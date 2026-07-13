<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RapportFinancier extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'rapports_financiers';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'titre',
        'type',
        'periode',
        'date_debut',
        'date_fin',
        'total_entrees',
        'total_sorties',
        'solde',
        'dons_recus',
        'cotisations_recues',
        'subventions_recues',
        'autres_entrees',
        'depenses_projets',
        'salaires',
        'logistique',
        'administratif',
        'autres_sorties',
        'fichier',
        'commentaire',
        'public',
        'statut',
        'prepare_par',
        'date_validation',
    ];

    /**
     * Cast des attributs.
     */
    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'date_validation' => 'date',
            'public' => 'boolean',
            'total_entrees' => 'decimal:2',
            'total_sorties' => 'decimal:2',
            'solde' => 'decimal:2',
            'dons_recus' => 'decimal:2',
            'cotisations_recues' => 'decimal:2',
            'subventions_recues' => 'decimal:2',
            'autres_entrees' => 'decimal:2',
            'depenses_projets' => 'decimal:2',
            'salaires' => 'decimal:2',
            'logistique' => 'decimal:2',
            'administratif' => 'decimal:2',
            'autres_sorties' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * L'admin qui a préparé ce rapport.
     */
    public function preparePar()
    {
        return $this->belongsTo(User::class, 'prepare_par');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : uniquement les rapports publics.
     */
    public function scopePublics($query)
    {
        return $query->where('public', true);
    }

    /**
     * Scope : filtrer par type.
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope : filtrer par année.
     */
    public function scopeAnnee($query, int $annee)
    {
        return $query->where('periode', 'like', $annee . '%');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Calcule le solde automatiquement.
     */
    public function calculerSolde(): void
    {
        $this->solde = $this->total_entrees - $this->total_sorties;
    }

    /**
     * Libellé du type de rapport.
     */
    public function typeLibelle(): string
    {
        return match($this->type) {
            'mensuel' => 'Rapport mensuel',
            'trimestriel' => 'Rapport trimestriel',
            'annuel' => 'Rapport annuel',
            'projet' => 'Rapport de projet',
            'audit' => 'Rapport d\'audit',
            default => $this->type,
        };
    }

    /**
     * Libellé du statut.
     */
    public function statutLibelle(): string
    {
        return match($this->statut) {
            'brouillon' => 'Brouillon',
            'finalise' => 'Finalisé',
            'publie' => 'Publié',
            'archive' => 'Archivé',
            default => $this->statut,
        };
    }

    /**
     * Vérifie si le rapport est publié publiquement.
     */
    public function estPublic(): bool
    {
        return $this->public && $this->statut === 'publie';
    }

    /**
     * Formate un montant pour l'affichage.
     */
    public static function formaterMontant(float $montant): string
    {
        return number_format($montant, 2, ',', ' ') . ' $';
    }
}