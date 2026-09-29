<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Emplacement physique de stockage.
 *
 * Un emplacement peut être :
 *   - un magasin principal ;
 *   - un dépôt secondaire ;
 *   - une zone interne.
 *
 * L'emplacement est une RÉFÉRENCE ORGANISATIONNELLE :
 *
 *   - il est utilisé comme emplacement PRINCIPAL d'un article ;
 *   - il est utilisé comme localisation d'un mouvement ;
 *   - il peut servir de périmètre à un inventaire.
 *
 * IMPORTANT :
 *   Un emplacement n'est JAMAIS supprimé physiquement s'il
 *   apparaît dans l'historique (mouvements). On le désactive
 *   avec `actif = false`.
 */
class EmplacementStock extends Model
{
    use HasFactory;

    protected $table = 'emplacements_stocks';

    /**
     * Attributs assignables en masse.
     */
    protected $fillable = [
        'code',
        'nom',
        'description',
        'actif',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'actif'      => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Articles dont cet emplacement est l'emplacement principal.
     */
    public function stocksPrincipaux(): HasMany
    {
        return $this->hasMany(
            Stock::class,
            'emplacement_principal_id'
        );
    }

    /**
     * Mouvements localisés dans cet emplacement.
     */
    public function mouvements(): HasMany
    {
        return $this->hasMany(
            MouvementStock::class,
            'emplacement_id'
        );
    }

    /**
     * Inventaires ciblant cet emplacement.
     */
    public function inventaires(): HasMany
    {
        return $this->hasMany(
            InventaireStock::class,
            'emplacement_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER
    |--------------------------------------------------------------------------
    */

    public function estActif(): bool
    {
        return $this->actif === true;
    }

    public function estInactif(): bool
    {
        return $this->actif === false;
    }

    /**
     * L'emplacement peut-il être utilisé dans une nouvelle
     * opération (entrée, inventaire, mouvement) ?
     */
    public function peutEtreUtilise(): bool
    {
        return $this->estActif();
    }

    /**
     * Représentation courte : "MAG-001 — Magasin principal".
     */
    public function libelle(): string
    {
        return trim(
            ($this->code ?? '') . ' — ' . ($this->nom ?? '')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('actif', true);
    }

    public function scopeRecherche(
        Builder $query,
        ?string $recherche
    ): Builder {
        $recherche = $recherche !== null
            ? trim($recherche)
            : null;

        if ($recherche === null || $recherche === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($recherche) {
            $q->where('code', 'like', "%{$recherche}%")
              ->orWhere('nom', 'like', "%{$recherche}%");
        });
    }
}