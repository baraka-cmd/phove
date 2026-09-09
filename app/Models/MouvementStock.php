<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'mouvements_stocks';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stock_id',
        'type_mouvement',
        'quantite',
        'date_mouvement',
        'reference_document',
        'motif',
        'destinataire',
        'projet_id',
        'effectue_par',
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
            'quantite' => 'decimal:2',
            'date_mouvement' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le stock concerné par ce mouvement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    /**
     * L'admin qui a effectué le mouvement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function effectuePar()
    {
        return $this->belongsTo(User::class, 'effectue_par');
    }

    /**
     * Le projet lié au mouvement (optionnel).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : uniquement les entrées.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntrees($query)
    {
        return $query->where('type_mouvement', 'entree');
    }

    /**
     * Scope : uniquement les sorties.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSorties($query)
    {
        return $query->where('type_mouvement', 'sortie');
    }

    /**
     * Scope : filtrer par date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDuJour($query, string $date)
    {
        return $query->whereDate('date_mouvement', $date);
    }

    /**
     * Scope : filtrer par mois.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $mois Format Y-m
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDuMois($query, string $mois)
    {
        return $query->where('date_mouvement', 'like', $mois . '%');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Libellé du type de mouvement en français.
     *
     * @return string
     */
    public function typeLibelle(): string
    {
        return match($this->type_mouvement) {
            'entree' => 'Entrée',
            'sortie' => 'Sortie',
            default => $this->type_mouvement,
        };
    }

    /**
     * Vérifie si c'est une entrée.
     *
     * @return bool
     */
    public function estEntree(): bool
    {
        return $this->type_mouvement === 'entree';
    }

    /**
     * Vérifie si c'est une sortie.
     *
     * @return bool
     */
    public function estSortie(): bool
    {
        return $this->type_mouvement === 'sortie';
    }

    /**
     * Retourne la quantité formatée avec l'unité.
     *
     * @return string
     */
    public function quantiteFormatee(): string
    {
        $unite = $this->stock ? $this->stock->unite : '';
        $signe = $this->estEntree() ? '+' : '-';
        return $signe . number_format($this->quantite, 2, ',', ' ') . ' ' . $unite;
    }

    /**
     * Retourne le nom complet de l'admin qui a effectué le mouvement.
     *
     * @return string
     */
    public function nomEffectuePar(): string
    {
        return $this->effectuePar ? $this->effectuePar->nom : '—';
    }

    /**
     * Retourne le nom du projet lié.
     *
     * @return string
     */
    public function nomProjet(): string
    {
        return $this->projet ? $this->projet->titre : '—';
    }
}