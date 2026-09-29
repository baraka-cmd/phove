<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaiementSalaire extends Model
{
    use HasFactory;

    /**
     * Nom de la table.
     */
    protected $table = 'paiements_salaires';

    /**
     * Champs pouvant être remplis.
     *
     * IMPORTANT :
     * La référence et certaines informations sensibles
     * devraient idéalement être générées côté Service.
     */
    protected $fillable = [
        'salaire_id',
        'montant',
        'date_paiement',
        'mode_paiement',
        'reference',
        'paye_par',
        'notes',
    ];

    /**
     * Conversion automatique des types.
     */
    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | CONSTANTES
    |--------------------------------------------------------------------------
    */

    /**
     * Modes de paiement supportés.
     *
     * On utilise des constantes afin d'éviter
     * de répéter des chaînes partout dans l'application.
     */
    public const MODE_ESPECES = 'especes';

    public const MODE_VIREMENT = 'virement';

    public const MODE_MOBILE_MONEY = 'mobile_money';

    public const MODE_CHEQUE = 'cheque';

    /**
     * Liste des modes de paiement autorisés.
     */
    public const MODES_PAIEMENT = [
        self::MODE_ESPECES,
        self::MODE_VIREMENT,
        self::MODE_MOBILE_MONEY,
        self::MODE_CHEQUE,
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le salaire concerné par ce paiement.
     */
    public function salaire(): BelongsTo
    {
        return $this->belongsTo(Salaire::class);
    }

    /**
     * Utilisateur ayant enregistré/effectué le paiement.
     */
    public function payePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paye_par');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Paiements d'un salaire donné.
     */
    public function scopeDuSalaire(
        Builder $query,
        int $salaireId
    ): Builder {
        return $query->where('salaire_id', $salaireId);
    }

    /**
     * Paiements d'un membre donné.
     *
     * Utilisation :
     * PaiementSalaire::duMembre($membreId)->get();
     */
    public function scopeDuMembre(
        Builder $query,
        int $membreId
    ): Builder {
        return $query->whereHas('salaire', function (Builder $q) use ($membreId) {
            $q->where('membre_id', $membreId);
        });
    }

    /**
     * Paiements d'un mois donné.
     *
     * Le mois correspond au mois du salaire,
     * pas nécessairement au mois de paiement.
     *
     * Exemple :
     * 2026-09
     */
    public function scopeDuMois(
        Builder $query,
        string $mois
    ): Builder {
        return $query->whereHas('salaire', function (Builder $q) use ($mois) {
            $q->where('mois', $mois);
        });
    }

    /**
     * Paiements effectués pendant une période.
     *
     * Exemple :
     *
     * PaiementSalaire::entreDates(
     *     '2026-09-01',
     *     '2026-09-30'
     * )->get();
     */
    public function scopeEntreDates(
        Builder $query,
        $dateDebut,
        $dateFin
    ): Builder {
        return $query->whereBetween(
            'date_paiement',
            [$dateDebut, $dateFin]
        );
    }

    /**
     * Paiements effectués avec un mode donné.
     */
    public function scopeMode(
        Builder $query,
        string $mode
    ): Builder {
        return $query->where('mode_paiement', $mode);
    }

    /**
     * Paiements enregistrés par un utilisateur.
     */
    public function scopePayePar(
        Builder $query,
        int $userId
    ): Builder {
        return $query->where('paye_par', $userId);
    }

    /**
     * Recherche par référence.
     */
    public function scopeRecherche(
        Builder $query,
        ?string $terme
    ): Builder {
        if (!$terme) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($terme) {
            $q->where('reference', 'like', '%' . $terme . '%')
              ->orWhereHas('salaire', function (Builder $salaire) use ($terme) {
                  $salaire->where('reference', 'like', '%' . $terme . '%');
              });
        });
    }

    /**
     * Tri du plus récent au plus ancien.
     */
    public function scopeRecents(Builder $query): Builder
    {
        return $query
            ->orderByDesc('date_paiement')
            ->orderByDesc('id');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie que le paiement est positif.
     *
     * La validation Form Request reste obligatoire.
     * Cette méthode constitue une deuxième protection.
     */
    public function estMontantValide(): bool
    {
        return (float) $this->montant > 0;
    }

    /**
     * Vérifie si le paiement appartient à un salaire valide.
     */
    public function salaireEstValide(): bool
    {
        if (!$this->relationLoaded('salaire')) {
            $this->load('salaire');
        }

        return $this->salaire !== null
            && $this->salaire->estValide();
    }

    /**
     * Vérifie si ce paiement est le dernier paiement
     * permettant de solder complètement le salaire.
     */
    public function soldeSalaire(): bool
    {
        if (!$this->relationLoaded('salaire')) {
            $this->load('salaire');
        }

        if (!$this->salaire) {
            return false;
        }

        return $this->salaire->resteAPayer() <= 0;
    }

    /**
     * Montant formaté.
     */
    public function montantFormate(): string
    {
        return number_format(
            (float) $this->montant,
            2,
            ',',
            ' '
        ) . ' $';
    }

    /**
     * Date formatée.
     */
    public function datePaiementFormatee(): string
    {
        return $this->date_paiement
            ? $this->date_paiement->format('d/m/Y')
            : '-';
    }

    /**
     * Libellé humain du mode de paiement.
     */
    public function modePaiementLabel(): string
    {
        return match ($this->mode_paiement) {
            self::MODE_ESPECES => 'Espèces',
            self::MODE_VIREMENT => 'Virement bancaire',
            self::MODE_MOBILE_MONEY => 'Mobile Money',
            self::MODE_CHEQUE => 'Chèque',
            default => ucfirst(
                str_replace('_', ' ', $this->mode_paiement)
            ),
        };
    }

    /**
     * Génère automatiquement une référence de paiement.
     *
     * Format :
     *
     * PAY-202609-0001
     *
     * IMPORTANT :
     * Cette méthode ne doit pas être utilisée seule pour garantir
     * l'unicité dans un environnement fortement concurrent.
     *
     * L'unicité réelle est garantie par la contrainte SQL UNIQUE.
     */
    public static function genererReference(
        string $mois
    ): string {
        $mois = preg_replace('/[^0-9\-]/', '', $mois);

        $prefixe = 'PAY-' . str_replace('-', '', $mois) . '-';

        $dernierPaiement = static::query()
            ->where('reference', 'like', $prefixe . '%')
            ->orderByDesc('id')
            ->first();

        if (!$dernierPaiement) {
            $numero = 1;
        } else {
            $parties = explode('-', $dernierPaiement->reference);

            $dernierNumero = (int) end($parties);

            $numero = $dernierNumero + 1;
        }

        return $prefixe . str_pad(
            (string) $numero,
            4,
            '0',
            STR_PAD_LEFT
        );
    }

    /*
    |--------------------------------------------------------------------------
    | INFORMATIONS SUR LE SALAIRE
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne le membre concerné.
     */
    public function nomMembre(): string
    {
        if (!$this->relationLoaded('salaire')) {
            $this->load('salaire.membre');
        } elseif ($this->salaire && !$this->salaire->relationLoaded('membre')) {
            $this->salaire->load('membre');
        }

        if (!$this->salaire || !$this->salaire->membre) {
            return 'Membre inconnu';
        }

        $membre = $this->salaire->membre;

        /*
         * On évite ici de supposer l'existence d'un prénom.
         * Le modèle Membre doit être vérifié avec la structure réelle
         * de votre application.
         */
        $nom = $membre->nom ?? '';
        $postNom = $membre->post_nom ?? '';

        return trim($nom . ' ' . $postNom);
    }

    /**
     * Retourne la référence du salaire.
     */
    public function referenceSalaire(): string
    {
        if (!$this->relationLoaded('salaire')) {
            $this->load('salaire');
        }

        return $this->salaire?->reference ?? '-';
    }

    /**
     * Retourne le mois salarial.
     */
    public function moisSalaire(): ?string
    {
        if (!$this->relationLoaded('salaire')) {
            $this->load('salaire');
        }

        return $this->salaire?->mois;
    }

    /*
    |--------------------------------------------------------------------------
    | AGRÉGATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Calcule le total payé pour le salaire associé.
     */
    public function totalPayeDuSalaire(): float
    {
        if (!$this->relationLoaded('salaire')) {
            $this->load('salaire');
        }

        if (!$this->salaire) {
            return 0.0;
        }

        return (float) $this->salaire
            ->paiements()
            ->sum('montant');
    }

    /**
     * Retourne le reste à payer après ce paiement.
     */
    public function resteApresPaiement(): float
    {
        if (!$this->relationLoaded('salaire')) {
            $this->load('salaire');
        }

        if (!$this->salaire) {
            return 0.0;
        }

        $totalPaye = $this->salaire
            ->paiements()
            ->sum('montant');

        return max(
            0,
            (float) $this->salaire->salaire_net - (float) $totalPaye
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LIBELLÉS
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne une description courte du paiement.
     */
    public function description(): string
    {
        return sprintf(
            '%s — %s — %s',
            $this->reference,
            $this->montantFormate(),
            $this->datePaiementFormatee()
        );
    }
}