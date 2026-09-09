<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DossierPersonnel extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'dossiers_personnel';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'membre_id',
        'statut',
        'date_creation',
        'date_validation',
        'valide_par',
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
            'date_creation' => 'date',
            'date_validation' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le membre concerné par ce dossier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function membre()
    {
        return $this->belongsTo(Membre::class, 'membre_id');
    }

    /**
     * L'admin qui a validé ce dossier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    /**
     * Les documents (pièces) de ce dossier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(DocumentPersonnel::class, 'dossier_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : dossiers complets.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeComplets($query)
    {
        return $query->where('statut', 'complet');
    }

    /**
     * Scope : dossiers incomplets.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncomplets($query)
    {
        return $query->where('statut', 'incomplet');
    }

    /**
     * Scope : dossiers en attente.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Les 12 types de pièces exigées (Article 65 des Statuts).
     *
     * @var array<string, string>
     */
    public static function typesDocuments(): array
    {
        return [
            'demande_motivation' => 'Demande de motivation',
            'cv' => 'CV actualisé',
            'titres_scolaires' => 'Photocopies des titres scolaires',
            'attestations_services' => 'Attestations des services rendus',
            'attestation_nationalite' => 'Attestation de nationalité',
            'attestations_autres' => 'Attestations autres (naissance, célibat, mariage, résidence, composition familiale, vie et mœurs, aptitude physique)',
            'carte_nationalite' => 'Carte de nationalité, passeport, permis de conduire',
            'carte_hygiene' => 'Carte d\'hygiène',
            'photos_passeport' => '4 Photos passeport en couleur',
            'carte_vocation' => 'Photocopie de la carte de vocation (serviteurs de Dieu)',
            'photos_famille' => 'Photos passeport de la famille',
            'classeur' => 'Un classeur petit format',
        ];
    }

    /**
     * Retourne le libellé du statut en français.
     *
     * @return string
     */
    public function statutLibelle(): string
    {
        return match($this->statut) {
            'complet' => 'Dossier complet',
            'incomplet' => 'Dossier incomplet',
            'en_attente' => 'En attente de validation',
            default => $this->statut,
        };
    }

    /**
     * Calcule le pourcentage de complétude du dossier.
     *
     * @return float
     */
    public function pourcentageCompletion(): float
    {
        $total = count(self::typesDocuments());
        if ($total === 0) {
            return 0;
        }

        $fournis = $this->documents()->where('statut', 'fourni')->count();
        return round(($fournis / $total) * 100, 1);
    }

    /**
     * Retourne le nombre de documents fournis.
     *
     * @return int
     */
    public function nombreDocumentsFournis(): int
    {
        return $this->documents()->where('statut', 'fourni')->count();
    }

    /**
     * Retourne le nombre de documents manquants.
     *
     * @return int
     */
    public function nombreDocumentsManquants(): int
    {
        $total = count(self::typesDocuments());
        return $total - $this->nombreDocumentsFournis();
    }

    /**
     * Vérifie si le dossier est complet.
     *
     * @return bool
     */
    public function estComplet(): bool
    {
        return $this->statut === 'complet';
    }

    /**
     * Met à jour le statut du dossier en fonction des documents fournis.
     *
     * @return void
     */
    public function mettreAJourStatut(): void
    {
        $fournis = $this->nombreDocumentsFournis();
        $total = count(self::typesDocuments());

        if ($fournis === $total) {
            $this->update([
                'statut' => 'complet',
                'date_validation' => now()->format('Y-m-d'),
                'valide_par' => auth()->id(),
            ]);
        } elseif ($fournis > 0) {
            $this->update(['statut' => 'incomplet']);
        } else {
            $this->update(['statut' => 'en_attente']);
        }
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
}