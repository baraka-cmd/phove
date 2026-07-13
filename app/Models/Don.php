<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Don extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'dons';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'donateur_nom',
        'donateur_email',
        'donateur_telephone',
        'donateur_adresse',
        'type_donateur',
        'montant',
        'devise',
        'type_don',
        'projet_id',
        'mode_paiement',
        'reference_transaction',
        'date_don',
        'recu_genere',
        'numero_recu',
        'date_recu',
        'message_donateur',
        'statut',
        'enregistre_par',
    ];

    /**
     * Cast des attributs.
     */
    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
            'date_don' => 'date',
            'recu_genere' => 'boolean',
            'date_recu' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le projet auquel le don est affecté (optionnel).
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /**
     * L'admin qui a enregistré ce don.
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
     * Libellé du type de donateur en français.
     */
    public function typeDonateurLibelle(): string
    {
        return match($this->type_donateur) {
            'particulier' => 'Particulier',
            'organisation' => 'Organisation',
            'etat' => 'État',
            'anonyme' => 'Anonyme',
            default => $this->type_donateur,
        };
    }

    /**
     * Libellé du mode de paiement en français.
     */
    public function modePaiementLibelle(): string
    {
        return match($this->mode_paiement) {
            'especes' => 'Espèces',
            'virement_bancaire' => 'Virement bancaire',
            'mobile_money' => 'Mobile Money',
            'carte_bancaire' => 'Carte bancaire',
            'paypal' => 'PayPal',
            'autre' => 'Autre',
            default => $this->mode_paiement,
        };
    }

    /**
     * Vérifie si le don est confirmé.
     */
    public function estConfirme(): bool
    {
        return $this->statut === 'confirme';
    }

    /**
     * Génère un numéro de reçu unique.
     */
    public function genererNumeroRecu(): string
    {
        $annee = $this->date_don->format('Y');
        $numero = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        return 'AHD-RDC-' . $annee . '-' . $numero;
    }

    /**
     * Formate le montant avec la devise.
     */
    public function montantFormate(): string
    {
        return number_format($this->montant, 2) . ' ' . $this->devise;
    }
}