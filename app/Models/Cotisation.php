<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotisation extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'cotisations';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'membre_id',
        'mois',
        'montant',
        'date_paiement',
        'mode_paiement',
        'reference',
        'observation',
        'enregistre_par',
    ];

    /**
     * Cast des attributs.
     */
    protected function casts(): array
    {
        return [
            'date_paiement' => 'date',
            'montant' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le membre qui a payé cette cotisation.
     */
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    /**
     * L'utilisateur (admin) qui a enregistré ce paiement.
     */
    public function enregistrePar()
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Libellé du mode de paiement en français.
     */
    public function modePaiementLibelle(): string
    {
        return match($this->mode_paiement) {
            'especes' => 'Espèces',
            'virement_bancaire' => 'Virement bancaire',
            'mobile_money' => 'Mobile Money',
            'autre' => 'Autre',
            default => $this->mode_paiement,
        };
    }

    /**
     * Vérifie si le paiement est en retard (après le 30 du mois).
     */
    public function estEnRetard(): bool
    {
        $dateLimite = \Carbon\Carbon::createFromFormat('Y-m', $this->mois)->endOfMonth();
        return $this->date_paiement->gt($dateLimite);
    }
}