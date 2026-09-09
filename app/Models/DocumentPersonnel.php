<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentPersonnel extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'documents_personnel';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dossier_id',
        'type_document',
        'fichier',
        'date_upload',
        'upload_par',
        'statut',
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
            'date_upload' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le dossier auquel appartient ce document.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dossier()
    {
        return $this->belongsTo(DossierPersonnel::class, 'dossier_id');
    }

    /**
     * L'admin qui a uploadé ce document.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
     * Scope : documents fournis.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFournis($query)
    {
        return $query->where('statut', 'fourni');
    }

    /**
     * Scope : documents manquants.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeManquants($query)
    {
        return $query->where('statut', 'manquant');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne le libellé du type de document en français.
     *
     * @return string
     */
    public function typeLibelle(): string
    {
        $types = DossierPersonnel::typesDocuments();
        return $types[$this->type_document] ?? $this->type_document;
    }

    /**
     * Retourne le libellé du statut en français.
     *
     * @return string
     */
    public function statutLibelle(): string
    {
        return match($this->statut) {
            'fourni' => 'Fourni',
            'manquant' => 'Manquant',
            'en_attente' => 'En attente',
            default => $this->statut,
        };
    }

    /**
     * Vérifie si le document a été fourni.
     *
     * @return bool
     */
    public function estFourni(): bool
    {
        return $this->statut === 'fourni' && !empty($this->fichier);
    }

    /**
     * Retourne l'URL complète du fichier.
     *
     * @return string|null
     */
    public function urlFichier(): ?string
    {
        if ($this->fichier) {
            return asset('storage/' . $this->fichier);
        }
        return null;
    }

    /**
     * Retourne l'extension du fichier.
     *
     * @return string
     */
    public function extension(): string
    {
        if (!$this->fichier) {
            return '—';
        }
        return strtoupper(pathinfo($this->fichier, PATHINFO_EXTENSION));
    }
}