<?php

namespace App\Services;

use App\Models\Membre;
use App\Models\Salaire;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class SalaireService
{
    /*
    |--------------------------------------------------------------------------
    | CRÉATION
    |--------------------------------------------------------------------------
    */

    /**
     * Crée un nouveau salaire sous forme de brouillon.
     *
     * IMPORTANT :
     * Un salaire nouvellement créé ne peut pas être directement
     * "validé" ou "payé".
     *
     * Le cycle normal est :
     *
     * création → brouillon → validation → paiement
     *
     * @param array $data
     * @param int|null $userId
     * @return Salaire
     *
     * @throws Throwable
     */
    public function creer(
        array $data,
        ?int $userId = null
    ): Salaire {
        return DB::transaction(function () use ($data, $userId) {

            $this->verifierDonneesMetier($data);

            $membreId = (int) $data['membre_id'];
            $mois = $data['mois'];

            /*
             * Empêche la création de deux salaires
             * pour le même membre et le même mois.
             *
             * La contrainte UNIQUE en base reste la protection
             * définitive contre les doublons.
             */
            $this->verifierSalaireExistant(
                $membreId,
                $mois
            );

            /*
             * Récupération du membre.
             *
             * On utilise lockForUpdate() afin d'éviter qu'une
             * opération concurrente modifie certaines informations
             * pendant notre traitement.
             */
            $membre = Membre::query()
                ->lockForUpdate()
                ->find($membreId);

            if (!$membre) {
                throw ValidationException::withMessages([
                    'membre_id' => 'Le membre sélectionné n’existe pas.',
                ]);
            }

            /*
             * Calcul des montants.
             */
            $salaireBase = $this->montant(
                $data['salaire_base'] ?? 0
            );

            $primes = $this->montant(
                $data['primes'] ?? 0
            );

            $retenues = $this->montant(
                $data['retenues'] ?? 0
            );

            $salaireNet = $this->calculerNet(
                $salaireBase,
                $primes,
                $retenues
            );

            /*
             * Le salaire net ne peut jamais être négatif.
             */
            if ($salaireNet < 0) {
                throw ValidationException::withMessages([
                    'retenues' =>
                        'Les retenues ne peuvent pas dépasser '
                        . 'le total du salaire de base et des primes.',
                ]);
            }

            /*
             * Référence générée côté serveur.
             *
             * On ne fait pas confiance à une référence envoyée
             * depuis le formulaire.
             */
            $reference = $this->genererReferenceUnique($mois);

            /*
             * Création du salaire.
             */
            $salaire = Salaire::create([
                'membre_id' => $membreId,
                'organe_id' => $data['organe_id'] ?? null,

                'mois' => $mois,

                'salaire_base' => $salaireBase,
                'primes' => $primes,
                'retenues' => $retenues,
                'salaire_net' => $salaireNet,

                'statut' => Salaire::STATUT_BROUILLON,

                'reference' => $reference,

                'created_by' => $userId,

                'notes' => $data['notes'] ?? null,
            ]);

            return $salaire;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MODIFICATION
    |--------------------------------------------------------------------------
    */

    /**
     * Modifie un salaire.
     *
     * Seul un salaire en brouillon peut être modifié.
     *
     * @param Salaire $salaire
     * @param array $data
     * @return Salaire
     *
     * @throws Throwable
     */
    public function modifier(
        Salaire $salaire,
        array $data
    ): Salaire {
        return DB::transaction(function () use ($salaire, $data) {

            /*
             * Recharge la ligne avec verrou.
             */
            $salaire = Salaire::query()
                ->lockForUpdate()
                ->find($salaire->id);

            if (!$salaire) {
                throw ValidationException::withMessages([
                    'salaire' => 'Le salaire demandé n’existe plus.',
                ]);
            }

            /*
             * Protection contre la modification
             * d'un salaire déjà validé.
             */
            if (!$salaire->peutEtreModifie()) {
                throw ValidationException::withMessages([
                    'salaire' =>
                        'Ce salaire ne peut plus être modifié '
                        . 'car il n’est plus au statut brouillon.',
                ]);
            }

            $this->verifierDonneesMetier(
                $data,
                $salaire->id
            );

            /*
             * Si le mois ou le membre change,
             * nous devons vérifier le doublon.
             */
            $membreId = (int) (
                $data['membre_id'] ?? $salaire->membre_id
            );

            $mois = $data['mois'] ?? $salaire->mois;

            $this->verifierSalaireExistant(
                $membreId,
                $mois,
                $salaire->id
            );

            /*
             * Calcul des nouveaux montants.
             */
            $salaireBase = $this->montant(
                $data['salaire_base'] ?? $salaire->salaire_base
            );

            $primes = $this->montant(
                $data['primes'] ?? $salaire->primes
            );

            $retenues = $this->montant(
                $data['retenues'] ?? $salaire->retenues
            );

            $salaireNet = $this->calculerNet(
                $salaireBase,
                $primes,
                $retenues
            );

            if ($salaireNet < 0) {
                throw ValidationException::withMessages([
                    'retenues' =>
                        'Les retenues ne peuvent pas dépasser '
                        . 'le total du salaire de base et des primes.',
                ]);
            }

            /*
             * Mise à jour.
             */
            $salaire->membre_id = $membreId;

            if (array_key_exists('organe_id', $data)) {
                $salaire->organe_id = $data['organe_id'];
            }

            $salaire->mois = $mois;

            $salaire->salaire_base = $salaireBase;
            $salaire->primes = $primes;
            $salaire->retenues = $retenues;
            $salaire->salaire_net = $salaireNet;

            if (array_key_exists('notes', $data)) {
                $salaire->notes = $data['notes'];
            }

            /*
             * Le statut reste brouillon.
             */
            $salaire->statut = Salaire::STATUT_BROUILLON;

            $salaire->save();

            return $salaire->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CALCUL
    |--------------------------------------------------------------------------
    */

    /**
     * Calcule le salaire net.
     *
     * Formule :
     *
     * salaire net =
     * salaire de base + primes - retenues
     */
    public function calculerNet(
        float|int|string $salaireBase,
        float|int|string $primes,
        float|int|string $retenues
    ): float {
        $base = $this->montant($salaireBase);
        $prime = $this->montant($primes);
        $retenue = $this->montant($retenues);

        return round(
            $base + $prime - $retenue,
            2
        );
    }

    /**
     * Recalcule le salaire net d'un salaire existant.
     *
     * Cette méthode est utile lorsqu'une opération interne
     * nécessite de recalculer les montants.
     *
     * @param Salaire $salaire
     * @return Salaire
     *
     * @throws Throwable
     */
    public function recalculerNet(
        Salaire $salaire
    ): Salaire {
        return DB::transaction(function () use ($salaire) {

            $salaire = Salaire::query()
                ->lockForUpdate()
                ->find($salaire->id);

            if (!$salaire) {
                throw ValidationException::withMessages([
                    'salaire' => 'Le salaire n’existe plus.',
                ]);
            }

            /*
             * On ne permet pas de modifier financièrement
             * un salaire déjà payé ou partiellement payé.
             */
            if (
                $salaire->estPartiellementPaye()
                || $salaire->estPaye()
                || $salaire->estAnnule()
            ) {
                throw ValidationException::withMessages([
                    'salaire' =>
                        'Le montant d’un salaire déjà traité '
                        . 'ne peut plus être recalculé.',
                ]);
            }

            $net = $this->calculerNet(
                $salaire->salaire_base,
                $salaire->primes,
                $salaire->retenues
            );

            if ($net < 0) {
                throw ValidationException::withMessages([
                    'salaire' =>
                        'Le salaire net ne peut pas être négatif.',
                ]);
            }

            $salaire->salaire_net = $net;
            $salaire->save();

            return $salaire->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    /**
     * Valide un salaire.
     *
     * Transition autorisée :
     *
     * BROUILLON → VALIDE
     *
     * @param Salaire $salaire
     * @param int $userId
     * @return Salaire
     *
     * @throws Throwable
     */
    public function valider(
        Salaire $salaire,
        int $userId
    ): Salaire {
        return DB::transaction(function () use ($salaire, $userId) {

            $salaire = Salaire::query()
                ->lockForUpdate()
                ->find($salaire->id);

            if (!$salaire) {
                throw ValidationException::withMessages([
                    'salaire' => 'Le salaire n’existe plus.',
                ]);
            }

            /*
             * Le salaire doit être un brouillon.
             */
            if (!$salaire->peutEtreValide()) {
                throw ValidationException::withMessages([
                    'salaire' =>
                        'Seul un salaire en brouillon peut être validé.',
                ]);
            }

            /*
             * Vérification de sécurité supplémentaire.
             */
            if ((float) $salaire->salaire_net < 0) {
                throw ValidationException::withMessages([
                    'salaire' =>
                        'Impossible de valider un salaire '
                        . 'avec un montant net négatif.',
                ]);
            }

            /*
             * Validation.
             */
            $salaire->valider($userId);

            return $salaire->fresh([
                'membre',
                'organe',
                'validePar',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ANNULATION
    |--------------------------------------------------------------------------
    */

    /**
     * Annule un salaire.
     *
     * Transitions :
     *
     * BROUILLON → ANNULÉ
     * VALIDE → ANNULÉ
     *
     * Un salaire déjà payé ou partiellement payé
     * ne peut pas être annulé directement.
     *
     * @param Salaire $salaire
     * @param int $userId
     * @param string $motif
     * @return Salaire
     *
     * @throws Throwable
     */
    public function annuler(
        Salaire $salaire,
        int $userId,
        string $motif
    ): Salaire {
        return DB::transaction(function () use (
            $salaire,
            $userId,
            $motif
        ) {

            $salaire = Salaire::query()
                ->lockForUpdate()
                ->find($salaire->id);

            if (!$salaire) {
                throw ValidationException::withMessages([
                    'salaire' => 'Le salaire n’existe plus.',
                ]);
            }

            $motif = trim($motif);

            if ($motif === '') {
                throw ValidationException::withMessages([
                    'motif_annulation' =>
                        'Le motif d’annulation est obligatoire.',
                ]);
            }

            if (!$salaire->peutEtreAnnule()) {
                throw ValidationException::withMessages([
                    'salaire' =>
                        'Ce salaire ne peut pas être annulé '
                        . 'dans son état actuel.',
                ]);
            }

            $salaire->annuler(
                $userId,
                $motif
            );

            return $salaire->fresh([
                'membre',
                'organe',
                'annulePar',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RECHERCHE
    |--------------------------------------------------------------------------
    */

    /**
     * Construit une requête de recherche de salaires.
     *
     * Cette méthode ne lance pas immédiatement la requête.
     *
     * Elle permet au contrôleur de faire ensuite :
     *
     * $query = $service->requete(...);
     * $salaires = $query->paginate(20);
     */
    public function requete(
        ?string $mois = null,
        ?string $statut = null,
        ?int $membreId = null,
        ?int $organeId = null,
        ?string $recherche = null
    ): Builder {
        $query = Salaire::query()
            ->with([
                'membre',
                'organe',
            ]);

        if ($mois !== null && $mois !== '') {
            $query->where('mois', $mois);
        }

        if ($statut !== null && $statut !== '') {
            $query->where('statut', $statut);
        }

        if ($membreId !== null) {
            $query->where('membre_id', $membreId);
        }

        if ($organeId !== null) {
            $query->where('organe_id', $organeId);
        }

        if ($recherche !== null && trim($recherche) !== '') {
            $query->recherche(
                trim($recherche)
            );
        }

        return $query
            ->orderByDesc('mois')
            ->orderByDesc('id');
    }

    /**
     * Récupère les salaires d'un membre pour un mois.
     *
     * Normalement il n'y aura qu'un résultat grâce à
     * UNIQUE(membre_id, mois).
     */
    public function salaireDuMois(
        int $membreId,
        string $mois
    ): ?Salaire {
        return Salaire::query()
            ->with([
                'membre',
                'organe',
                'paiements',
            ])
            ->where('membre_id', $membreId)
            ->where('mois', $mois)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | STATISTIQUES
    |--------------------------------------------------------------------------
    */

    /**
     * Total des salaires nets d'un mois.
     */
    public function totalDuMois(
        string $mois
    ): float {
        return (float) Salaire::query()
            ->where('mois', $mois)
            ->where(
                'statut',
                '!=',
                Salaire::STATUT_ANNULE
            )
            ->sum('salaire_net');
    }

    /**
     * Total des salaires validés d'un mois.
     *
     * Ici on considère :
     *
     * valide
     * partiellement_paye
     * paye
     */
    public function totalValideDuMois(
        string $mois
    ): float {
        return (float) Salaire::query()
            ->where('mois', $mois)
            ->whereIn('statut', [
                Salaire::STATUT_VALIDE,
                Salaire::STATUT_PARTIELLEMENT_PAYE,
                Salaire::STATUT_PAYE,
            ])
            ->sum('salaire_net');
    }

    /**
     * Nombre de salaires d'un mois.
     */
    public function nombreDuMois(
        string $mois
    ): int {
        return Salaire::query()
            ->where('mois', $mois)
            ->where(
                'statut',
                '!=',
                Salaire::STATUT_ANNULE
            )
            ->count();
    }

    /**
     * Retourne les salaires restant à payer.
     *
     * On récupère les salaires non soldés.
     */
    public function salairesAvecReste(
        ?string $mois = null
    ): Collection {
        $query = Salaire::query()
            ->with([
                'membre',
                'organe',
                'paiements',
            ])
            ->whereIn('statut', [
                Salaire::STATUT_VALIDE,
                Salaire::STATUT_PARTIELLEMENT_PAYE,
            ]);

        if ($mois !== null && $mois !== '') {
            $query->where('mois', $mois);
        }

        return $query
            ->orderBy('mois')
            ->orderBy('id')
            ->get()
            ->filter(function (Salaire $salaire) {
                return $salaire->resteAPayer() > 0;
            })
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | VÉRIFICATIONS MÉTIER
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie les données métier principales.
     *
     * La Form Request reste responsable de la validation
     * de format :
     *
     * required
     * numeric
     * date_format
     * etc.
     *
     * Cette méthode vérifie plutôt les règles métier.
     */
    protected function verifierDonneesMetier(
        array $data,
        ?int $salaireId = null
    ): void {
        $base = $this->montant(
            $data['salaire_base'] ?? 0
        );

        $primes = $this->montant(
            $data['primes'] ?? 0
        );

        $retenues = $this->montant(
            $data['retenues'] ?? 0
        );

        if ($base < 0) {
            throw ValidationException::withMessages([
                'salaire_base' =>
                    'Le salaire de base ne peut pas être négatif.',
            ]);
        }

        if ($primes < 0) {
            throw ValidationException::withMessages([
                'primes' =>
                    'Les primes ne peuvent pas être négatives.',
            ]);
        }

        if ($retenues < 0) {
            throw ValidationException::withMessages([
                'retenues' =>
                    'Les retenues ne peuvent pas être négatives.',
            ]);
        }

        if (
            isset($data['mois'])
            && !preg_match(
                '/^\d{4}-(0[1-9]|1[0-2])$/',
                $data['mois']
            )
        ) {
            throw ValidationException::withMessages([
                'mois' =>
                    'Le mois doit être au format YYYY-MM.',
            ]);
        }
    }

    /**
     * Vérifie qu'un salaire n'existe pas déjà.
     *
     * @param int $membreId
     * @param string $mois
     * @param int|null $exceptId
     */
    protected function verifierSalaireExistant(
        int $membreId,
        string $mois,
        ?int $exceptId = null
    ): void {
        $query = Salaire::query()
            ->where('membre_id', $membreId)
            ->where('mois', $mois);

        if ($exceptId !== null) {
            $query->whereKeyNot($exceptId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'mois' =>
                    'Un salaire existe déjà pour ce membre '
                    . 'et cette période.',
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RÉFÉRENCE
    |--------------------------------------------------------------------------
    */

    /**
     * Génère une référence unique de salaire.
     *
     * Format :
     *
     * BUL-202609-0001
     */
    public function genererReferenceUnique(
        string $mois
    ): string {
        $moisNormalise = str_replace(
            '-',
            '',
            $mois
        );

        $prefixe = 'BUL-' . $moisNormalise . '-';

        /*
         * On récupère la dernière référence.
         */
        $dernier = Salaire::query()
            ->where(
                'reference',
                'like',
                $prefixe . '%'
            )
            ->orderByDesc('id')
            ->first();

        if (!$dernier) {
            $numero = 1;
        } else {
            $parties = explode(
                '-',
                $dernier->reference
            );

            $dernierNumero = (int) end($parties);

            $numero = $dernierNumero + 1;
        }

        /*
         * Vérification supplémentaire.
         *
         * La contrainte UNIQUE de la base reste
         * la protection définitive.
         */
        do {
            $reference = $prefixe . str_pad(
                (string) $numero,
                4,
                '0',
                STR_PAD_LEFT
            );

            $existe = Salaire::query()
                ->where(
                    'reference',
                    $reference
                )
                ->exists();

            if ($existe) {
                $numero++;
            }
        } while ($existe);

        return $reference;
    }

    /*
    |--------------------------------------------------------------------------
    | OUTILS INTERNES
    |--------------------------------------------------------------------------
    */

    /**
     * Normalise un montant monétaire.
     */
    protected function montant(
        float|int|string|null $montant
    ): float {
        if ($montant === null || $montant === '') {
            return 0.0;
        }

        /*
         * Permet également de recevoir :
         *
         * 1500
         * 1500.50
         * "1500.50"
         *
         * mais on rejette les valeurs non numériques.
         */
        if (!is_numeric($montant)) {
            throw ValidationException::withMessages([
                'montant' =>
                    'Un montant financier doit être numérique.',
            ]);
        }

        return round(
            (float) $montant,
            2
        );
    }
}