<?php

namespace App\Services;

use App\Models\PaiementSalaire;
use App\Models\Salaire;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class PaiementSalaireService
{
    /*
    |--------------------------------------------------------------------------
    | CRÉATION D'UN PAIEMENT
    |--------------------------------------------------------------------------
    */

    /**
     * Enregistre un paiement sur un salaire validé.
     *
     * Règles métier :
     *
     * - le salaire doit exister ;
     * - le salaire doit être validé ;
     * - le salaire ne doit pas être annulé ;
     * - le montant doit être supérieur à zéro ;
     * - le montant ne doit pas dépasser le reste à payer ;
     * - le paiement est créé dans une transaction ;
     * - le salaire est verrouillé pendant le traitement ;
     * - le statut du salaire est synchronisé après le paiement.
     *
     * @param Salaire $salaire
     * @param array $data
     * @param int|null $userId
     * @return PaiementSalaire
     *
     * @throws Throwable
     */
    public function enregistrer(
        Salaire $salaire,
        array $data,
        ?int $userId = null
    ): PaiementSalaire {
        return DB::transaction(function () use (
            $salaire,
            $data,
            $userId
        ) {

            /*
             * IMPORTANT :
             *
             * On recharge le salaire avec un verrou SQL.
             *
             * Cela empêche deux utilisateurs de traiter
             * simultanément le même solde.
             */
            $salaire = Salaire::query()
                ->lockForUpdate()
                ->find($salaire->id);

            if (!$salaire) {
                throw ValidationException::withMessages([
                    'salaire' =>
                        'Le salaire demandé n’existe plus.',
                ]);
            }

            /*
             * Vérification du statut.
             */
            if ($salaire->estAnnule()) {
                throw ValidationException::withMessages([
                    'salaire' =>
                        'Impossible d’effectuer un paiement '
                        . 'sur un salaire annulé.',
                ]);
            }

            if (!$salaire->estValide() && !$salaire->estPartiellementPaye()) {
                throw ValidationException::withMessages([
                    'salaire' =>
                        'Le salaire doit être validé avant '
                        . 'de pouvoir recevoir un paiement.',
                ]);
            }

            /*
             * Normalisation du montant.
             */
            $montant = $this->normaliserMontant(
                $data['montant'] ?? null
            );

            /*
             * Le montant doit être strictement positif.
             */
            if ($montant <= 0) {
                throw ValidationException::withMessages([
                    'montant' =>
                        'Le montant du paiement doit être supérieur à zéro.',
                ]);
            }

            /*
             * Calcul du total déjà payé.
             *
             * Le verrou posé sur le salaire protège le calcul
             * contre les paiements concurrents.
             */
            $totalPaye = (float) $salaire
                ->paiements()
                ->sum('montant');

            /*
             * Calcul du reste réel.
             */
            $resteAPayer = round(
                (float) $salaire->salaire_net - $totalPaye,
                2
            );

            /*
             * Protection contre un éventuel état incohérent.
             */
            if ($resteAPayer < 0) {
                throw ValidationException::withMessages([
                    'salaire' =>
                        'Le salaire présente déjà un dépassement '
                        . 'de paiement. Une intervention administrative '
                        . 'est nécessaire.',
                ]);
            }

            /*
             * Le salaire est déjà entièrement payé.
             */
            if ($resteAPayer <= 0) {
                throw ValidationException::withMessages([
                    'montant' =>
                        'Ce salaire est déjà entièrement payé.',
                ]);
            }

            /*
             * INTERDICTION DU SURPAIEMENT.
             */
            if ($montant > $resteAPayer) {
                throw ValidationException::withMessages([
                    'montant' => sprintf(
                        'Le montant demandé (%s) dépasse le reste '
                        . 'à payer (%s).',
                        $this->formaterMontant($montant),
                        $this->formaterMontant($resteAPayer)
                    ),
                ]);
            }

            /*
             * Vérification du mode de paiement.
             */
            $modePaiement = $data['mode_paiement'] ?? null;

            if (
                !$modePaiement
                || !in_array(
                    $modePaiement,
                    PaiementSalaire::MODES_PAIEMENT,
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'mode_paiement' =>
                        'Le mode de paiement sélectionné '
                        . 'n’est pas valide.',
                ]);
            }

            /*
             * Date du paiement.
             *
             * Si elle n'est pas fournie, on utilise aujourd'hui.
             */
            $datePaiement = $data['date_paiement']
                ?? now()->toDateString();

            /*
             * Génération de la référence.
             */
            $reference = $this->genererReferenceUnique(
                $salaire->mois
            );

            /*
             * Création du paiement.
             */
            $paiement = PaiementSalaire::create([
                'salaire_id' => $salaire->id,

                'montant' => $montant,

                'date_paiement' => $datePaiement,

                'mode_paiement' => $modePaiement,

                'reference' => $reference,

                'paye_par' => $userId,

                'notes' => $data['notes'] ?? null,
            ]);

            /*
             * Mise à jour du statut du salaire.
             *
             * Le total est recalculé après création du paiement.
             */
            $this->synchroniserStatut(
                $salaire
            );

            /*
             * Recharge les relations utiles.
             */
            return $paiement->fresh([
                'salaire.membre',
                'salaire.organe',
                'payePar',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | SYNCHRONISATION DU STATUT
    |--------------------------------------------------------------------------
    */

    /**
     * Synchronise automatiquement le statut du salaire
     * avec les paiements réellement enregistrés.
     *
     * Règles :
     *
     * aucun paiement
     *      ↓
     * VALIDE
     *
     * paiement partiel
     *      ↓
     * PARTIELLEMENT_PAYE
     *
     * totalité payée
     *      ↓
     * PAYE
     *
     * @param Salaire $salaire
     * @return Salaire
     */
    public function synchroniserStatut(
        Salaire $salaire
    ): Salaire {
        /*
         * Un brouillon ou un salaire annulé ne doit pas
         * être transformé automatiquement.
         */
        if (
            $salaire->estBrouillon()
            || $salaire->estAnnule()
        ) {
            return $salaire;
        }

        $totalPaye = (float) $salaire
            ->paiements()
            ->sum('montant');

        $salaireNet = (float) $salaire->salaire_net;

        /*
         * Aucun paiement.
         */
        if ($totalPaye <= 0) {
            $salaire->statut = Salaire::STATUT_VALIDE;
        }

        /*
         * Paiement partiel.
         */
        elseif ($totalPaye < $salaireNet) {
            $salaire->statut =
                Salaire::STATUT_PARTIELLEMENT_PAYE;
        }

        /*
         * Salaire totalement payé.
         */
        else {
            $salaire->statut =
                Salaire::STATUT_PAYE;
        }

        $salaire->save();

        return $salaire;
    }

    /*
    |--------------------------------------------------------------------------
    | ANNULATION D'UN PAIEMENT
    |--------------------------------------------------------------------------
    */

    /**
     * Annule un paiement existant.
     *
     * ATTENTION :
     *
     * Nous ne supprimons pas physiquement le paiement.
     *
     * Dans un système financier professionnel,
     * supprimer une opération financière est dangereux.
     *
     * Pour cette raison, cette méthode suppose que la table
     * sera ultérieurement enrichie avec :
     *
     * - annule
     * - annule_par
     * - annule_at
     * - motif_annulation
     *
     * Si ces colonnes ne sont pas encore présentes,
     * cette méthode ne doit pas encore être activée.
     */
    public function annuler(
        PaiementSalaire $paiement,
        int $userId,
        string $motif
    ): PaiementSalaire {
        return DB::transaction(function () use (
            $paiement,
            $userId,
            $motif
        ) {

            $motif = trim($motif);

            if ($motif === '') {
                throw ValidationException::withMessages([
                    'motif_annulation' =>
                        'Le motif d’annulation est obligatoire.',
                ]);
            }

            /*
             * Pour l'instant, nous ne procédons pas à une suppression.
             *
             * Cette fonctionnalité sera activée lorsque nous aurons
             * ajouté les champs d'annulation à la migration.
             */
            throw ValidationException::withMessages([
                'paiement' =>
                    'L’annulation sécurisée des paiements sera '
                    . 'activée après l’ajout de l’historique '
                    . 'd’annulation dans la base de données.',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CONSULTATION
    |--------------------------------------------------------------------------
    */

    /**
     * Récupère tous les paiements d'un salaire.
     */
    public function paiementsDuSalaire(
        Salaire $salaire
    ): Collection {
        return $salaire
            ->paiements()
            ->with([
                'payePar',
            ])
            ->orderByDesc('date_paiement')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Récupère les paiements récents.
     */
    public function paiementsRecents(
        int $limite = 20
    ): Collection {
        return PaiementSalaire::query()
            ->with([
                'salaire.membre',
                'salaire.organe',
                'payePar',
            ])
            ->orderByDesc('date_paiement')
            ->orderByDesc('id')
            ->limit($limite)
            ->get();
    }

    /**
     * Recherche des paiements.
     */
    public function requete(
        ?string $mois = null,
        ?int $membreId = null,
        ?string $modePaiement = null,
        ?string $recherche = null,
        ?string $dateDebut = null,
        ?string $dateFin = null
    ): Builder {
        $query = PaiementSalaire::query()
            ->with([
                'salaire.membre',
                'salaire.organe',
                'payePar',
            ]);

        /*
         * Mois du salaire.
         */
        if ($mois !== null && $mois !== '') {
            $query->duMois($mois);
        }

        /*
         * Membre.
         */
        if ($membreId !== null) {
            $query->duMembre($membreId);
        }

        /*
         * Mode de paiement.
         */
        if (
            $modePaiement !== null
            && $modePaiement !== ''
        ) {
            $query->mode($modePaiement);
        }

        /*
         * Recherche par référence.
         */
        if (
            $recherche !== null
            && trim($recherche) !== ''
        ) {
            $query->recherche(
                trim($recherche)
            );
        }

        /*
         * Période de paiement.
         */
        if (
            $dateDebut !== null
            && $dateFin !== null
        ) {
            $query->entreDates(
                $dateDebut,
                $dateFin
            );
        }

        return $query
            ->orderByDesc('date_paiement')
            ->orderByDesc('id');
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULS
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne le total payé pour un salaire.
     */
    public function totalPaye(
        Salaire $salaire
    ): float {
        return (float) $salaire
            ->paiements()
            ->sum('montant');
    }

    /**
     * Retourne le reste à payer.
     */
    public function resteAPayer(
        Salaire $salaire
    ): float {
        $totalPaye = $this->totalPaye(
            $salaire
        );

        return max(
            0,
            round(
                (float) $salaire->salaire_net
                - $totalPaye,
                2
            )
        );
    }

    /**
     * Retourne le pourcentage payé.
     */
    public function pourcentagePaye(
        Salaire $salaire
    ): float {
        $net = (float) $salaire->salaire_net;

        if ($net <= 0) {
            return 0.0;
        }

        $totalPaye = $this->totalPaye(
            $salaire
        );

        return round(
            min(
                100,
                ($totalPaye / $net) * 100
            ),
            2
        );
    }

    /**
     * Indique si le salaire est entièrement payé.
     */
    public function estEntierementPaye(
        Salaire $salaire
    ): bool {
        return $this->resteAPayer($salaire) <= 0;
    }

    /**
     * Indique si le salaire est partiellement payé.
     */
    public function estPartiellementPaye(
        Salaire $salaire
    ): bool {
        $totalPaye = $this->totalPaye(
            $salaire
        );

        return $totalPaye > 0
            && $this->resteAPayer($salaire) > 0;
    }

    /*
    |--------------------------------------------------------------------------
    | STATISTIQUES
    |--------------------------------------------------------------------------
    */

    /**
     * Total des paiements pour un mois salarial.
     */
    public function totalPayeDuMois(
        string $mois
    ): float {
        return (float) PaiementSalaire::query()
            ->duMois($mois)
            ->sum('montant');
    }

    /**
     * Total payé selon un mode de paiement.
     */
    public function totalParMode(
        string $modePaiement,
        ?string $dateDebut = null,
        ?string $dateFin = null
    ): float {
        $query = PaiementSalaire::query()
            ->mode($modePaiement);

        if (
            $dateDebut !== null
            && $dateFin !== null
        ) {
            $query->entreDates(
                $dateDebut,
                $dateFin
            );
        }

        return (float) $query->sum('montant');
    }

    /**
     * Nombre de paiements d'un mois.
     */
    public function nombrePaiementsDuMois(
        string $mois
    ): int {
        return PaiementSalaire::query()
            ->duMois($mois)
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | RÉFÉRENCE
    |--------------------------------------------------------------------------
    */

    /**
     * Génère une référence unique de paiement.
     *
     * Format :
     *
     * PAY-202609-0001
     *
     * La contrainte UNIQUE de la base reste
     * la protection définitive.
     */
    public function genererReferenceUnique(
        string $mois
    ): string {
        /*
         * Normalisation du mois.
         *
         * 2026-09 → 202609
         */
        $moisNormalise = str_replace(
            '-',
            '',
            $mois
        );

        $prefixe =
            'PAY-' . $moisNormalise . '-';

        /*
         * Cherche la dernière référence.
         */
        $dernier = PaiementSalaire::query()
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

            $dernierNumero =
                (int) end($parties);

            $numero =
                $dernierNumero + 1;
        }

        /*
         * Protection supplémentaire contre une référence
         * déjà existante.
         */
        do {
            $reference =
                $prefixe
                . str_pad(
                    (string) $numero,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

            $existe = PaiementSalaire::query()
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
    | VALIDATION INTERNE
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si un mode de paiement est autorisé.
     */
    public function modePaiementValide(
        ?string $modePaiement
    ): bool {
        return $modePaiement !== null
            && in_array(
                $modePaiement,
                PaiementSalaire::MODES_PAIEMENT,
                true
            );
    }

    /**
     * Vérifie qu'un montant ne dépasse pas le reste.
     */
    public function montantAutorise(
        Salaire $salaire,
        float|int|string $montant
    ): bool {
        $montant = $this->normaliserMontant(
            $montant
        );

        return $montant > 0
            && $montant <= $this->resteAPayer(
                $salaire
            );
    }

    /*
    |--------------------------------------------------------------------------
    | OUTILS INTERNES
    |--------------------------------------------------------------------------
    */

    /**
     * Normalise un montant financier.
     */
    protected function normaliserMontant(
        float|int|string|null $montant
    ): float {
        if (
            $montant === null
            || $montant === ''
        ) {
            return 0.0;
        }

        /*
         * Accepte les valeurs numériques.
         */
        if (!is_numeric($montant)) {
            throw ValidationException::withMessages([
                'montant' =>
                    'Le montant doit être une valeur numérique.',
            ]);
        }

        $montant = (float) $montant;

        /*
         * Arrondi monétaire à deux décimales.
         */
        return round(
            $montant,
            2
        );
    }

    /**
     * Formate un montant pour les messages.
     */
    protected function formaterMontant(
        float $montant
    ): string {
        return number_format(
            $montant,
            2,
            ',',
            ' '
        ) . ' $';
    }
}