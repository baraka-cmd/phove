<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salaire extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    protected $table = 'salaires';


    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTS MASS-ASSIGNABLES
    |--------------------------------------------------------------------------
    |
    | Les champs liés aux paiements ne figurent volontairement PAS ici.
    |
    | Un paiement est enregistré dans PaiementSalaire.
    |
    */

    protected $fillable = [
        'membre_id',
        'organe_id',
        'mois',
        'salaire_base',
        'primes',
        'retenues',
        'salaire_net',
        'statut',
        'reference',
        'valide_par',
        'valide_at',
        'annule_par',
        'annule_at',
        'motif_annulation',
        'created_by',
        'notes',
    ];


    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'salaire_base' => 'decimal:2',
            'primes'       => 'decimal:2',
            'retenues'     => 'decimal:2',
            'salaire_net'  => 'decimal:2',

            'valide_at'    => 'datetime',
            'annule_at'    => 'datetime',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | CONSTANTES MÉTIER
    |--------------------------------------------------------------------------
    |
    | Éviter de disperser les chaînes "brouillon", "valide", etc.
    | dans toute l'application.
    |
    */

    public const STATUT_BROUILLON = 'brouillon';

    public const STATUT_VALIDE = 'valide';

    public const STATUT_PARTIELLEMENT_PAYE = 'partiellement_paye';

    public const STATUT_PAYE = 'paye';

    public const STATUT_ANNULE = 'annule';


    /*
    |--------------------------------------------------------------------------
    | MODES DE PAIEMENT
    |--------------------------------------------------------------------------
    |
    | Ces valeurs seront principalement utilisées par
    | PaiementSalaire.
    |
    | Elles sont centralisées ici pour éviter les incohérences.
    |
    */

    public const MODE_ESPECES = 'especes';

    public const MODE_VIREMENT = 'virement';

    public const MODE_MOBILE_MONEY = 'mobile_money';

    public const MODE_CHEQUE = 'cheque';


    /*
    |--------------------------------------------------------------------------
    | RELATION : MEMBRE
    |--------------------------------------------------------------------------
    */

    public function membre(): BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }


    /*
    |--------------------------------------------------------------------------
    | RELATION : ORGANE
    |--------------------------------------------------------------------------
    */

    public function organe(): BelongsTo
    {
        return $this->belongsTo(Organe::class);
    }


    /*
    |--------------------------------------------------------------------------
    | RELATION : UTILISATEUR AYANT CRÉÉ LE SALAIRE
    |--------------------------------------------------------------------------
    */

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    /*
    |--------------------------------------------------------------------------
    | RELATION : UTILISATEUR AYANT VALIDÉ
    |--------------------------------------------------------------------------
    */

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }


    /*
    |--------------------------------------------------------------------------
    | RELATION : UTILISATEUR AYANT ANNULÉ
    |--------------------------------------------------------------------------
    */

    public function annulePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'annule_par');
    }


    /*
    |--------------------------------------------------------------------------
    | RELATION : PAIEMENTS
    |--------------------------------------------------------------------------
    |
    | Un salaire peut avoir :
    |
    | 0 paiement
    | 1 paiement
    | plusieurs paiements
    |
    */

    public function paiements(): HasMany
    {
        return $this->hasMany(PaiementSalaire::class, 'salaire_id');
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPE : BROUILLONS
    |--------------------------------------------------------------------------
    */

    public function scopeBrouillons(Builder $query): Builder
    {
        return $query->where(
            'statut',
            self::STATUT_BROUILLON
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPE : VALIDÉS
    |--------------------------------------------------------------------------
    */

    public function scopeValides(Builder $query): Builder
    {
        return $query->where(
            'statut',
            self::STATUT_VALIDE
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPE : PARTIELLEMENT PAYÉS
    |--------------------------------------------------------------------------
    */

    public function scopePartiellementPayes(Builder $query): Builder
    {
        return $query->where(
            'statut',
            self::STATUT_PARTIELLEMENT_PAYE
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPE : PAYÉS
    |--------------------------------------------------------------------------
    */

    public function scopePayes(Builder $query): Builder
    {
        return $query->where(
            'statut',
            self::STATUT_PAYE
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPE : ANNULÉS
    |--------------------------------------------------------------------------
    */

    public function scopeAnnules(Builder $query): Builder
    {
        return $query->where(
            'statut',
            self::STATUT_ANNULE
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPE : MOIS
    |--------------------------------------------------------------------------
    */

    public function scopeDuMois(
        Builder $query,
        string $mois
    ): Builder {
        return $query->where('mois', $mois);
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPE : MEMBRE
    |--------------------------------------------------------------------------
    */

    public function scopeDuMembre(
        Builder $query,
        int $membreId
    ): Builder {
        return $query->where('membre_id', $membreId);
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPE : RECHERCHE
    |--------------------------------------------------------------------------
    */

    public function scopeRecherche(
        Builder $query,
        ?string $recherche
    ): Builder {
        if (!$recherche) {
            return $query;
        }

        $recherche = trim($recherche);

        if ($recherche === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($recherche) {

            $q->where('reference', 'like', "%{$recherche}%")

                ->orWhereHas('membre', function (Builder $membre) use ($recherche) {

                    $membre->where('nom', 'like', "%{$recherche}%")
                        ->orWhere('post_nom', 'like', "%{$recherche}%")
                        ->orWhere('prenom', 'like', "%{$recherche}%")
                        ->orWhere('matricule', 'like', "%{$recherche}%");
                });
        });
    }


    /*
    |--------------------------------------------------------------------------
    | CALCUL DU SALAIRE NET
    |--------------------------------------------------------------------------
    |
    | salaire_net = salaire_base + primes - retenues
    |
    */

    public function calculerNet(): float
    {
        return round(
            (float) $this->salaire_base
            + (float) $this->primes
            - (float) $this->retenues,
            2
        );
    }


    /*
    |--------------------------------------------------------------------------
    | METTRE À JOUR LE SALAIRE NET
    |--------------------------------------------------------------------------
    */

    public function recalculerNet(): float
    {
        $net = $this->calculerNet();

        $this->salaire_net = $net;

        return $net;
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL PAYÉ
    |--------------------------------------------------------------------------
    |
    | Le montant réellement payé provient exclusivement
    | de la table paiements_salaires.
    |
    */

    public function getTotalPayeAttribute(): float
    {
        if ($this->relationLoaded('paiements')) {
            return round(
                (float) $this->paiements->sum('montant'),
                2
            );
        }

        return round(
            (float) $this->paiements()->sum('montant'),
            2
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RESTE À PAYER
    |--------------------------------------------------------------------------
    */

    public function getResteAPayerAttribute(): float
    {
        return max(
            0,
            round(
                (float) $this->salaire_net
                - $this->total_paye,
                2
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | POURCENTAGE PAYÉ
    |--------------------------------------------------------------------------
    */

    public function getPourcentagePayeAttribute(): float
    {
        $net = (float) $this->salaire_net;

        if ($net <= 0) {
            return 0;
        }

        $pourcentage = (
            $this->total_paye / $net
        ) * 100;

        return round(
            min(100, max(0, $pourcentage)),
            2
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RESTE-T-IL QUELQUE CHOSE À PAYER ?
    |--------------------------------------------------------------------------
    */

    public function resteAPayer(): bool
    {
        return $this->reste_a_payer > 0;
    }


    /*
    |--------------------------------------------------------------------------
    | SALAIRE ENTIÈREMENT PAYÉ ?
    |--------------------------------------------------------------------------
    */

    public function estPaye(): bool
    {
        return $this->statut === self::STATUT_PAYE;
    }


    /*
    |--------------------------------------------------------------------------
    | SALAIRE PARTIELLEMENT PAYÉ ?
    |--------------------------------------------------------------------------
    */

    public function estPartiellementPaye(): bool
    {
        return $this->statut === self::STATUT_PARTIELLEMENT_PAYE;
    }


    /*
    |--------------------------------------------------------------------------
    | SALAIRE VALIDÉ ?
    |--------------------------------------------------------------------------
    */

    public function estValide(): bool
    {
        return $this->statut === self::STATUT_VALIDE
            || $this->statut === self::STATUT_PARTIELLEMENT_PAYE
            || $this->statut === self::STATUT_PAYE;
    }


    /*
    |--------------------------------------------------------------------------
    | BROUILLON ?
    |--------------------------------------------------------------------------
    */

    public function estBrouillon(): bool
    {
        return $this->statut === self::STATUT_BROUILLON;
    }


    /*
    |--------------------------------------------------------------------------
    | ANNULÉ ?
    |--------------------------------------------------------------------------
    */

    public function estAnnule(): bool
    {
        return $this->statut === self::STATUT_ANNULE;
    }


    /*
    |--------------------------------------------------------------------------
    | PEUT ÊTRE MODIFIÉ ?
    |--------------------------------------------------------------------------
    |
    | Une fois le salaire validé, ses montants financiers ne doivent
    | plus être librement modifiés.
    |
    */

    public function peutEtreModifie(): bool
    {
        return $this->statut === self::STATUT_BROUILLON;
    }


    /*
    |--------------------------------------------------------------------------
    | PEUT ÊTRE VALIDÉ ?
    |--------------------------------------------------------------------------
    */

    public function peutEtreValide(): bool
    {
        return $this->statut === self::STATUT_BROUILLON;
    }


    /*
    |--------------------------------------------------------------------------
    | PEUT RECEVOIR UN PAIEMENT ?
    |--------------------------------------------------------------------------
    */

    public function peutRecevoirPaiement(): bool
    {
        return in_array(
            $this->statut,
            [
                self::STATUT_VALIDE,
                self::STATUT_PARTIELLEMENT_PAYE,
            ],
            true
        ) && $this->resteAPayer();
    }


    /*
    |--------------------------------------------------------------------------
    | PEUT ÊTRE ANNULÉ ?
    |--------------------------------------------------------------------------
    */

    public function peutEtreAnnule(): bool
    {
        return in_array(
            $this->statut,
            [
                self::STATUT_BROUILLON,
                self::STATUT_VALIDE,
            ],
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    |
    | Cette méthode ne doit être appelée que par le service métier
    | après vérification des autorisations.
    |
    */

    public function valider(int $userId): bool
    {
        if (!$this->peutEtreValide()) {
            return false;
        }

        $this->statut = self::STATUT_VALIDE;
        $this->valide_par = $userId;
        $this->valide_at = now();

        return $this->save();
    }


    /*
    |--------------------------------------------------------------------------
    | ANNULATION
    |--------------------------------------------------------------------------
    */

    public function annuler(
        int $userId,
        string $motif
    ): bool {
        if (!$this->peutEtreAnnule()) {
            return false;
        }

        $motif = trim($motif);

        if ($motif === '') {
            return false;
        }

        $this->statut = self::STATUT_ANNULE;
        $this->annule_par = $userId;
        $this->annule_at = now();
        $this->motif_annulation = $motif;

        return $this->save();
    }


    /*
    |--------------------------------------------------------------------------
    | SYNCHRONISATION DU STATUT DE PAIEMENT
    |--------------------------------------------------------------------------
    |
    | Cette méthode permet de déterminer le statut financier
    | à partir des paiements réellement enregistrés.
    |
    */

    public function synchroniserStatutPaiement(): bool
    {
        /*
         * Un salaire annulé ne doit jamais redevenir payable.
         */
        if ($this->estAnnule()) {
            return false;
        }

        /*
         * Un salaire en brouillon ne doit pas recevoir de paiement.
         */
        if ($this->estBrouillon()) {
            return false;
        }

        $totalPaye = $this->total_paye;
        $net = (float) $this->salaire_net;

        /*
         * Aucun paiement.
         */
        if ($totalPaye <= 0) {
            $this->statut = self::STATUT_VALIDE;
        }

        /*
         * Paiement partiel.
         */
        elseif ($totalPaye < $net) {
            $this->statut = self::STATUT_PARTIELLEMENT_PAYE;
        }

        /*
         * Paiement complet.
         */
        else {
            $this->statut = self::STATUT_PAYE;
        }

        return $this->save();
    }


    /*
    |--------------------------------------------------------------------------
    | LIBELLÉ DU STATUT
    |--------------------------------------------------------------------------
    */

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {

            self::STATUT_BROUILLON =>
                'Brouillon',

            self::STATUT_VALIDE =>
                'Validé',

            self::STATUT_PARTIELLEMENT_PAYE =>
                'Partiellement payé',

            self::STATUT_PAYE =>
                'Payé',

            self::STATUT_ANNULE =>
                'Annulé',

            default =>
                ucfirst((string) $this->statut),
        };
    }


    /*
    |--------------------------------------------------------------------------
    | CLASSE CSS DU STATUT
    |--------------------------------------------------------------------------
    */

    public function getStatutBadgeAttribute(): string
    {
        return match ($this->statut) {

            self::STATUT_BROUILLON =>
                'warning',

            self::STATUT_VALIDE =>
                'info',

            self::STATUT_PARTIELLEMENT_PAYE =>
                'primary',

            self::STATUT_PAYE =>
                'success',

            self::STATUT_ANNULE =>
                'danger',

            default =>
                'secondary',
        };
    }


    /*
    |--------------------------------------------------------------------------
    | LIBELLÉ DU MODE DE PAIEMENT
    |--------------------------------------------------------------------------
    |
    | Utile lorsque le mode provient d'un PaiementSalaire.
    |
    */

    public static function modePaiementLabel(
        ?string $mode
    ): string {
        return match ($mode) {

            self::MODE_ESPECES =>
                'Espèces',

            self::MODE_VIREMENT =>
                'Virement bancaire',

            self::MODE_MOBILE_MONEY =>
                'Mobile Money',

            self::MODE_CHEQUE =>
                'Chèque',

            default =>
                $mode
                    ? ucfirst(str_replace('_', ' ', $mode))
                    : 'Non défini',
        };
    }


    /*
    |--------------------------------------------------------------------------
    | NOM COMPLET DU MEMBRE
    |--------------------------------------------------------------------------
    */

    public function getNomMembreAttribute(): string
    {
        if (!$this->membre) {
            return 'Membre inconnu';
        }

        /*
         * On utilise les attributs disponibles sans imposer
         * une structure supplémentaire au modèle Membre.
         */

        $nom = trim(
            collect([
                $this->membre->prenom ?? null,
                $this->membre->nom ?? null,
                $this->membre->post_nom ?? null,
            ])
                ->filter()
                ->implode(' ')
        );

        return $nom !== ''
            ? $nom
            : 'Membre #' . $this->membre_id;
    }


    /*
    |--------------------------------------------------------------------------
    | NOM DU POSTE / ORGANE
    |--------------------------------------------------------------------------
    */

    public function getNomPosteAttribute(): string
    {
        if (!$this->organe) {
            return 'Non défini';
        }

        return $this->organe->nom
            ?? $this->organe->libelle
            ?? 'Organe #' . $this->organe_id;
    }


    /*
    |--------------------------------------------------------------------------
    | FORMATAGE DU MONTANT
    |--------------------------------------------------------------------------
    */

    public function formaterMontant(
        float|int|string $montant
    ): string {
        return number_format(
            (float) $montant,
            2,
            ',',
            ' '
        ) . ' $';
    }


    /*
    |--------------------------------------------------------------------------
    | MONTANT NET FORMATÉ
    |--------------------------------------------------------------------------
    */

    public function getSalaireNetFormateAttribute(): string
    {
        return $this->formaterMontant(
            $this->salaire_net
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL PAYÉ FORMATÉ
    |--------------------------------------------------------------------------
    */

    public function getTotalPayeFormateAttribute(): string
    {
        return $this->formaterMontant(
            $this->total_paye
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RESTE À PAYER FORMATÉ
    |--------------------------------------------------------------------------
    */

    public function getResteAPayerFormateAttribute(): string
    {
        return $this->formaterMontant(
            $this->reste_a_payer
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GÉNÉRATION D'UNE RÉFÉRENCE
    |--------------------------------------------------------------------------
    |
    | Exemple :
    |
    | BUL-202609-0001
    |
    | Cette méthode sert à proposer une référence.
    |
    | La contrainte UNIQUE de la base reste la véritable protection
    | contre les doublons.
    |
    */

    public static function genererReference(
        string $mois
    ): string {
        $prefixe = 'BUL-' . str_replace('-', '', $mois) . '-';

        $dernier = static::query()
            ->where('reference', 'like', $prefixe . '%')
            ->orderByDesc('id')
            ->value('reference');

        if (!$dernier) {
            return $prefixe . '0001';
        }

        $numero = (int) substr(
            $dernier,
            -4
        );

        return $prefixe . str_pad(
            $numero + 1,
            4,
            '0',
            STR_PAD_LEFT
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL DES SALAIRES PAYÉS POUR UN MOIS
    |--------------------------------------------------------------------------
    */

    public static function totalPayeDuMois(
        string $mois
    ): float {
        return round(
            (float) static::query()
                ->where('mois', $mois)
                ->where('statut', self::STATUT_PAYE)
                ->withSum('paiements', 'montant')
                ->get()
                ->sum('paiements_sum_montant'),
            2
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL DES SALAIRES DUS POUR UN MOIS
    |--------------------------------------------------------------------------
    */

    public static function totalDuMois(
        string $mois
    ): float {
        return round(
            (float) static::query()
                ->where('mois', $mois)
                ->whereNot(
                    'statut',
                    self::STATUT_ANNULE
                )
                ->sum('salaire_net'),
            2
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL RESTANT À PAYER POUR UN MOIS
    |--------------------------------------------------------------------------
    */

    public static function totalResteDuMois(
        string $mois
    ): float {
        $salaires = static::query()
            ->where('mois', $mois)
            ->whereNot(
                'statut',
                self::STATUT_ANNULE
            )
            ->withSum('paiements', 'montant')
            ->get();

        return round(
            (float) $salaires->sum(
                function (Salaire $salaire) {
                    return max(
                        0,
                        (float) $salaire->salaire_net
                        - (float) ($salaire->paiements_sum_montant ?? 0)
                    );
                }
            ),
            2
        );
    }
}