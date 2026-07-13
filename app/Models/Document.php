<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'documents';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'titre',
        'type',
        'fichier',
        'extension',
        'taille',
        'description',
        'date_document',
        'date_signature',
        'signataires',
        'version',
        'public',
        'categorie',
        'telechargements',
        'upload_par',
    ];

    /**
     * Cast des attributs.
     */
    protected function casts(): array
    {
        return [
            'date_document' => 'date',
            'date_signature' => 'date',
            'public' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * L'admin qui a uploadé le document.
     */
    public function uploadPar()
    {
        return $this->belongsTo(User::class, 'upload_par');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : uniquement les documents publics.
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

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Incrémente le compteur de téléchargements.
     */
    public function incrementerTelechargements(): void
    {
        $this->increment('telechargements');
    }

    /**
     * Retourne l'URL complète du fichier.
     */
    public function url(): string
    {
        return asset('storage/' . $this->fichier);
    }

    /**
     * Formate la taille du fichier en unités lisibles.
     */
    public function tailleLisible(): string
    {
        $taille = $this->taille;
        if (!$taille) return 'Inconnue';

        $unites = ['o', 'Ko', 'Mo', 'Go'];
        $i = 0;
        while ($taille >= 1024 && $i < count($unites) - 1) {
            $taille /= 1024;
            $i++;
        }
        return round($taille, 1) . ' ' . $unites[$i];
    }

    /**
     * Libellé du type de document.
     */
    public function typeLibelle(): string
    {
        return match($this->type) {
            'statuts' => 'Statuts',
            'roi' => 'Règlement d\'Ordre Intérieur',
            'proces_verbal' => 'Procès-verbal',
            'convention' => 'Convention de partenariat',
            'rapport_annuel' => 'Rapport annuel',
            'rapport_activite' => 'Rapport d\'activité',
            'guide_procedure' => 'Guide de procédure',
            'politique_interne' => 'Politique interne',
            'communique_officiel' => 'Communiqué officiel',
            'autre' => 'Autre document',
            default => $this->type,
        };
    }

    /**
     * Vérifie si le fichier est un PDF.
     */
    public function estPdf(): bool
    {
        return strtolower($this->extension) === 'pdf';
    }

    /**
     * Vérifie si le document est public.
     */
    public function estPublic(): bool
    {
        return $this->public;
    }
}