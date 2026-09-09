<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControleTrimestriel extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'controles_trimestriels';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'trimestre',
        'annee',
        'date_debut',
        'date_fin',
        'date_controle',
        'type_controle',
        'statut',
        'controleur',
        'controle_par',
        'total_entrees',
        'total_sorties',
        'ecart',
        'conformite',
        'documents_verifies',
        'observations',
        'recommandations',
        'valide_par',
        'date_validation',
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
            'date_debut' => 'date',
            'date_fin' => 'date',
            'date_controle' => 'date',
            'date_validation' => 'date',
            'total_entrees' => 'decimal:2',
            'total_sorties' => 'decimal:2',
            'ecart' => 'decimal:2',
            'conformite' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CONSTANTES
    |--------------------------------------------------------------------------
    */

    /**
     * Les 4 trimestres de l'année.
     *
     * @var array<string, string>
     */
    public const TRIMESTRES = [
        'T1' => '1er Trimestre (Janvier - Mars)',
        'T2' => '2ème Trimestre (Avril - Juin)',
        'T3' => '3ème Trimestre (Juillet - Septembre)',
        'T4' => '4ème Trimestre (Octobre - Décembre)',
    ];

    /**
     * Les types de contrôle.
     *
     * @var array<string, string>
     */
    public const TYPES = [
        'financier' => 'Contrôle Financier',
        'operationnel' => 'Contrôle Opérationnel',
        'global' => 'Contrôle Global',
    ];

    /**
     * Les statuts du contrôle.
     *
     * @var array<string, string>
     */
    public const STATUTS = [
        'planifie' => 'Planifié',
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
        'valide' => 'Validé',
        'rejete' => 'Rejeté',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * L'admin qui a effectué le contrôle.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function controlePar()
    {
        return $this->belongsTo(User::class, 'controle_par');
    }

    /**
     * L'admin qui a validé le rapport.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : contrôles planifiés.
     */
    public function scopePlanifies($query)
    {
        return $query->where('statut', 'planifie');
    }

    /**
     * Scope : contrôles en cours.
     */
    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    /**
     * Scope : contrôles terminés.
     */
    public function scopeTermines($query)
    {
        return $query->where('statut', 'termine');
    }

    /**
     * Scope : contrôles validés.
     */
    public function scopeValides($query)
    {
        return $query->where('statut', 'valide');
    }

    /**
     * Scope : contrôles rejetés.
     */
    public function scopeRejetes($query)
    {
        return $query->where('statut', 'rejete');
    }

    /**
     * Scope : filtrer par année.
     */
    public function scopeAnnee($query, int $annee)
    {
        return $query->where('annee', $annee);
    }

    /**
     * Scope : filtrer par trimestre.
     */
    public function scopeTrimestre($query, string $trimestre)
    {
        return $query->where('trimestre', $trimestre);
    }

    /**
     * Scope : contrôles non conformes.
     */
    public function scopeNonConformes($query)
    {
        return $query->where('conformite', false);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Libellé du trimestre en français.
     *
     * @return string
     */
    public function trimestreLibelle(): string
    {
        return self::TRIMESTRES[$this->trimestre] ?? $this->trimestre;
    }

    /**
     * Libellé du type de contrôle en français.
     *
     * @return string
     */
    public function typeLibelle(): string
    {
        return self::TYPES[$this->type_controle] ?? $this->type_controle;
    }

    /**
     * Libellé du statut en français.
     *
     * @return string
     */
    public function statutLibelle(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    /**
     * Vérifie si le contrôle est planifié.
     */
    public function estPlanifie(): bool
    {
        return $this->statut === 'planifie';
    }

    /**
     * Vérifie si le contrôle est en cours.
     */
    public function estEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }

    /**
     * Vérifie si le contrôle est terminé.
     */
    public function estTermine(): bool
    {
        return $this->statut === 'termine';
    }

    /**
     * Vérifie si le contrôle est validé.
     */
    public function estValide(): bool
    {
        return $this->statut === 'valide';
    }

    /**
     * Vérifie si le contrôle est rejeté.
     */
    public function estRejete(): bool
    {
        return $this->statut === 'rejete';
    }

    /**
     * Vérifie si le contrôle est conforme.
     */
    public function estConforme(): bool
    {
        return $this->conformite;
    }

    /**
     * Calcule l'écart automatiquement.
     *
     * @return float
     */
    public function calculerEcart(): float
    {
        $this->ecart = $this->total_entrees - $this->total_sorties;
        $this->save();
        return $this->ecart;
    }

    /**
     * Valide le rapport de contrôle.
     *
     * @param int $adminId
     * @return void
     */
    public function valider(int $adminId): void
    {
        $this->update([
            'statut' => 'valide',
            'valide_par' => $adminId,
            'date_validation' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Rejette le rapport de contrôle.
     *
     * @param int $adminId
     * @param string|null $motif
     * @return void
     */
    public function rejeter(int $adminId, ?string $motif = null): void
    {
        $this->update([
            'statut' => 'rejete',
            'valide_par' => $adminId,
            'date_validation' => now()->format('Y-m-d'),
            'notes' => $motif ? $this->notes . "\nRejet : " . $motif : $this->notes,
        ]);
    }

    /**
     * Retourne le nom de l'admin qui a effectué le contrôle.
     *
     * @return string
     */
    public function nomControlePar(): string
    {
        return $this->controlePar ? $this->controlePar->nom : ($this->controleur ?? '—');
    }

    /**
     * Retourne le nom de l'admin qui a validé.
     *
     * @return string
     */
    public function nomValidePar(): string
    {
        return $this->validePar ? $this->validePar->nom : '—';
    }

    /**
     * Génère une référence unique.
     *
     * @param int $annee
     * @param string $trimestre
     * @return string
     */
    public static function genererReference(int $annee, string $trimestre): string
    {
        return 'CTRL-' . $annee . '-' . $trimestre;
    }

    /**
     * Retourne la période complète formatée.
     *
     * @return string
     */
    public function periodeFormatee(): string
    {
        return $this->date_debut->format('d/m/Y') . ' au ' . $this->date_fin->format('d/m/Y');
    }

    /**
     * Statistiques globales pour une année.
     *
     * @param int|null $annee
     * @return array
     */
    public static function statsAnnuelle(?int $annee = null): array
    {
        $query = self::query();
        if ($annee) {
            $query->where('annee', $annee);
        }

        $total = $query->count();
        $valides = (clone $query)->where('statut', 'valide')->count();
        $conformes = (clone $query)->where('conformite', true)->count();
        $nonConformes = $total - $conformes;

        return [
            'total' => $total,
            'valides' => $valides,
            'conformes' => $conformes,
            'non_conformes' => $nonConformes,
        ];
    }
}