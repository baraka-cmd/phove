<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salaire extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'salaires';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'membre_id',
        'organe_id',
        'mois',
        'salaire_base',
        'primes',
        'retenues',
        'salaire_net',
        'mode_paiement',
        'date_paiement',
        'statut',
        'reference',
        'valide_par',
        'paye_par',
        'notes',
    ];

    /**
     * Cast des attributs.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'salaire_base' => 'decimal:2',
            'primes' => 'decimal:2',
            'retenues' => 'decimal:2',
            'salaire_net' => 'decimal:2',
            'date_paiement' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le membre (personnel) concerné par ce salaire.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function membre()
    {
        return $this->belongsTo(Membre::class, 'membre_id');
    }

    /**
     * Le poste occupé dans l'organigramme.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organe()
    {
        return $this->belongsTo(Organe::class, 'organe_id');
    }

    /**
     * L'admin qui a validé le salaire.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    /**
     * L'admin qui a effectué le paiement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payePar()
    {
        return $this->belongsTo(User::class, 'paye_par');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : salaires brouillons.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBrouillon($query)
    {
        return $query->where('statut', 'brouillon');
    }

    /**
     * Scope : salaires validés.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeValides($query)
    {
        return $query->where('statut', 'valide');
    }

    /**
     * Scope : salaires payés.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePayes($query)
    {
        return $query->where('statut', 'paye');
    }

    /**
     * Scope : salaires annulés.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAnnules($query)
    {
        return $query->where('statut', 'annule');
    }

    /**
     * Scope : filtrer par mois.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $mois Format YYYY-MM
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDuMois($query, string $mois)
    {
        return $query->where('mois', $mois);
    }

    /**
     * Scope : filtrer par membre.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $membreId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDuMembre($query, int $membreId)
    {
        return $query->where('membre_id', $membreId);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Libellé du mode de paiement en français.
     *
     * @return string
     */
    public function modePaiementLibelle(): string
    {
        return match($this->mode_paiement) {
            'especes' => 'Espèces',
            'virement' => 'Virement bancaire',
            'mobile_money' => 'Mobile Money',
            'cheque' => 'Chèque',
            default => $this->mode_paiement,
        };
    }

    /**
     * Libellé du statut en français.
     *
     * @return string
     */
    public function statutLibelle(): string
    {
        return match($this->statut) {
            'brouillon' => 'Brouillon',
            'valide' => 'Validé',
            'paye' => 'Payé',
            'annule' => 'Annulé',
            default => $this->statut,
        };
    }

    /**
     * Calcule le salaire net automatiquement.
     *
     * @return float
     */
    public function calculerSalaireNet(): float
    {
        $this->salaire_net = $this->salaire_base + $this->primes - $this->retenues;
        $this->save();
        return $this->salaire_net;
    }

    /**
     * Vérifie si le salaire est en brouillon.
     *
     * @return bool
     */
    public function estBrouillon(): bool
    {
        return $this->statut === 'brouillon';
    }

    /**
     * Vérifie si le salaire est validé.
     *
     * @return bool
     */
    public function estValide(): bool
    {
        return $this->statut === 'valide';
    }

    /**
     * Vérifie si le salaire est payé.
     *
     * @return bool
     */
    public function estPaye(): bool
    {
        return $this->statut === 'paye';
    }

    /**
     * Valide le salaire.
     *
     * @param int $adminId
     * @return void
     */
    public function valider(int $adminId): void
    {
        $this->update([
            'statut' => 'valide',
            'valide_par' => $adminId,
        ]);
    }

    /**
     * Marque le salaire comme payé.
     *
     * @param int $adminId
     * @param string|null $datePaiement
     * @return void
     */
    public function marquerPaye(int $adminId, ?string $datePaiement = null): void
    {
        $this->update([
            'statut' => 'paye',
            'paye_par' => $adminId,
            'date_paiement' => $datePaiement ?? now()->format('Y-m-d'),
        ]);
    }

    /**
     * Annule le salaire.
     *
     * @return void
     */
    public function annuler(): void
    {
        $this->update(['statut' => 'annule']);
    }

    /**
     * Retourne le nom complet du membre concerné.
     *
     * @return string
     */
    public function nomMembre(): string
    {
        return $this->membre ? $this->membre->nomComplet() : '—';
    }

    /**
     * Retourne le nom du poste occupé.
     *
     * @return string
     */
    public function nomPoste(): string
    {
        return $this->organe ? $this->organe->poste : '—';
    }

    /**
     * Formate un montant pour l'affichage.
     *
     * @param float $montant
     * @return string
     */
    public static function formaterMontant(float $montant): string
    {
        return number_format($montant, 2, ',', ' ') . ' $';
    }

    /**
     * Génère une référence unique de bulletin.
     *
     * @param string $mois Format YYYY-MM
     * @return string
     */
    public static function genererReference(string $mois): string
    {
        $prefix = 'BUL-' . str_replace('-', '', $mois) . '-';
        $dernier = self::where('reference', 'like', $prefix . '%')
            ->orderBy('reference', 'desc')
            ->first();

        if ($dernier) {
            $numero = (int) substr($dernier->reference, -4) + 1;
        } else {
            $numero = 1;
        }

        return $prefix . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Retourne le total des salaires payés pour un mois donné.
     *
     * @param string $mois Format YYYY-MM
     * @return float
     */
    public static function totalPayeDuMois(string $mois): float
    {
        return self::where('mois', $mois)
            ->where('statut', 'paye')
            ->sum('salaire_net');
    }

    /**
     * Retourne le total des salaires validés mais non payés pour un mois.
     *
     * @param string $mois Format YYYY-MM
     * @return float
     */
    public static function totalValideDuMois(string $mois): float
    {
        return self::where('mois', $mois)
            ->where('statut', 'valide')
            ->sum('salaire_net');
    }
}