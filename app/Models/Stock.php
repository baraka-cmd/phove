<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'stocks';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'designation',
        'categorie',
        'unite',
        'quantite_initiale',
        'quantite_actuelle',
        'seuil_alerte',
        'emplacement',
        'description',
        'statut',
    ];

    /**
     * Cast des attributs.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantite_initiale' => 'decimal:2',
            'quantite_actuelle' => 'decimal:2',
            'seuil_alerte' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Les mouvements de ce stock (entrées et sorties).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class, 'stock_id');
    }

    /**
     * Les entrées de ce stock.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entrees()
    {
        return $this->hasMany(MouvementStock::class, 'stock_id')->where('type_mouvement', 'entree');
    }

    /**
     * Les sorties de ce stock.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sorties()
    {
        return $this->hasMany(MouvementStock::class, 'stock_id')->where('type_mouvement', 'sortie');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : produits disponibles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDisponible($query)
    {
        return $query->where('statut', 'disponible');
    }

    /**
     * Scope : produits en stock faible.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFaible($query)
    {
        return $query->where('statut', 'faible');
    }

    /**
     * Scope : produits en rupture.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRupture($query)
    {
        return $query->where('statut', 'rupture');
    }

    /**
     * Scope : filtrer par catégorie.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $categorie
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategorie($query, string $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Libellé de la catégorie en français.
     *
     * @return string
     */
    public function categorieLibelle(): string
    {
        return match($this->categorie) {
            'vivres' => 'Vivres',
            'medicaments' => 'Médicaments',
            'habillement' => 'Habillement',
            'abris' => 'Abris',
            'materiel' => 'Matériel',
            'semences' => 'Semences',
            'outils' => 'Outils aratoires',
            'autre' => 'Autre',
            default => $this->categorie,
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
            'disponible' => 'Disponible',
            'faible' => 'Stock faible',
            'rupture' => 'Rupture de stock',
            default => $this->statut,
        };
    }

    /**
     * Calcule et met à jour le statut en fonction de la quantité actuelle.
     *
     * @return string
     */
    public function calculerStatut(): string
    {
        if ($this->quantite_actuelle <= 0) {
            $this->statut = 'rupture';
        } elseif ($this->seuil_alerte !== null && $this->quantite_actuelle <= $this->seuil_alerte) {
            $this->statut = 'faible';
        } else {
            $this->statut = 'disponible';
        }

        $this->save();
        return $this->statut;
    }

    /**
     * Enregistre une entrée de stock.
     *
     * @param float $quantite
     * @param string|null $motif
     * @param string|null $destinataire
     * @param string|null $referenceDocument
     * @param int|null $projetId
     * @param string|null $notes
     * @return MouvementStock
     */
    public function entrer(
        float $quantite,
        ?string $motif = null,
        ?string $destinataire = null,
        ?string $referenceDocument = null,
        ?int $projetId = null,
        ?string $notes = null
    ): MouvementStock {
        // Créer le mouvement
        $mouvement = MouvementStock::create([
            'stock_id' => $this->id,
            'type_mouvement' => 'entree',
            'quantite' => $quantite,
            'date_mouvement' => now()->format('Y-m-d'),
            'reference_document' => $referenceDocument,
            'motif' => $motif,
            'destinataire' => $destinataire,
            'projet_id' => $projetId,
            'effectue_par' => auth()->id(),
            'notes' => $notes,
        ]);

        // Mettre à jour la quantité
        $this->quantite_actuelle += $quantite;
        $this->save();

        // Recalculer le statut
        $this->calculerStatut();

        return $mouvement;
    }

    /**
     * Enregistre une sortie de stock.
     *
     * @param float $quantite
     * @param string|null $motif
     * @param string|null $destinataire
     * @param string|null $referenceDocument
     * @param int|null $projetId
     * @param string|null $notes
     * @return MouvementStock
     * @throws \Exception Si la quantité demandée dépasse le stock disponible
     */
    public function sortir(
        float $quantite,
        ?string $motif = null,
        ?string $destinataire = null,
        ?string $referenceDocument = null,
        ?int $projetId = null,
        ?string $notes = null
    ): MouvementStock {
        // Vérifier que la quantité est suffisante
        if ($quantite > $this->quantite_actuelle) {
            throw new \Exception(
                'Quantité insuffisante en stock. Disponible : ' . $this->quantite_actuelle . ' ' . $this->unite
            );
        }

        // Créer le mouvement
        $mouvement = MouvementStock::create([
            'stock_id' => $this->id,
            'type_mouvement' => 'sortie',
            'quantite' => $quantite,
            'date_mouvement' => now()->format('Y-m-d'),
            'reference_document' => $referenceDocument,
            'motif' => $motif,
            'destinataire' => $destinataire,
            'projet_id' => $projetId,
            'effectue_par' => auth()->id(),
            'notes' => $notes,
        ]);

        // Mettre à jour la quantité
        $this->quantite_actuelle -= $quantite;
        $this->save();

        // Recalculer le statut
        $this->calculerStatut();

        return $mouvement;
    }

    /**
     * Retourne le total des entrées.
     *
     * @return float
     */
    public function totalEntrees(): float
    {
        return $this->entrees()->sum('quantite');
    }

    /**
     * Retourne le total des sorties.
     *
     * @return float
     */
    public function totalSorties(): float
    {
        return $this->sorties()->sum('quantite');
    }

    /**
     * Retourne la valeur formatée avec l'unité.
     *
     * @param float $quantite
     * @return string
     */
    public function formaterQuantite(float $quantite): string
    {
        return number_format($quantite, 2, ',', ' ') . ' ' . $this->unite;
    }

    /**
     * Vérifie si le stock est en dessous du seuil d'alerte.
     *
     * @return bool
     */
    public function estSousSeuil(): bool
    {
        return $this->seuil_alerte !== null && $this->quantite_actuelle <= $this->seuil_alerte;
    }

    /**
     * Vérifie si le stock est en rupture.
     *
     * @return bool
     */
    public function estEnRupture(): bool
    {
        return $this->quantite_actuelle <= 0;
    }

    /**
     * Génère une référence unique si non fournie.
     *
     * @return string
     */
    public static function genererReference(): string
    {
        $annee = date('Y');
        $mois = date('m');
        $prefix = 'STK-' . $annee . $mois . '-';
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
}