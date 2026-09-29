<?php

namespace App\Http\Requests;

use App\Enums\NatureMouvement;
use App\Models\Stock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation de l'enregistrement d'une entrée de stock.
 *
 * SÉMANTIQUE :
 *
 *   Une entrée augmente la quantité d'un lot existant ou
 *   en crée un nouveau. Elle génère également une ligne
 *   dans le journal des mouvements.
 *
 * CHAMPS OBLIGATOIRES :
 *
 *   - stock_id        : article ciblé
 *   - quantite        : > 0
 *   - numero_lot      : traçabilité
 *   - nature          : achat, don ou retour
 *   - date_entree     : date d'entrée du lot (par défaut aujourd'hui)
 *
 * CHAMPS OPTIONNELS :
 *
 *   - emplacement_id  : lieu physique de réception
 *   - date_mouvement  : date du mouvement (par défaut aujourd'hui)
 *   - date_fabrication
 *   - date_expiration
 *   - cout_unitaire
 *   - fournisseur
 *   - reference_document
 *   - projet_id
 *   - motif
 *   - notes
 *
 * AUTORISATION :
 *
 *   Déléguée à StockPolicy::enter() sur l'article ciblé.
 */
class StoreEntreeStockRequest extends FormRequest
{
    /*
    |--------------------------------------------------------------------------
    | AUTORISATION
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        $stockId = (int) $this->input('stock_id');

        if ($stockId <= 0) {
            return false;
        }

        $stock = Stock::find($stockId);

        if (!$stock) {
            return false;
        }

        return $this->user()->can('enter', $stock);
    }

    /*
    |--------------------------------------------------------------------------
    | RÈGLES DE VALIDATION
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            /*
             * Article ciblé.
             */
            'stock_id' => [
                'required',
                'integer',
                Rule::exists('stocks', 'id'),
            ],

            /*
             * Quantité entrée : strictement positive.
             */
            'quantite' => [
                'required',
                'numeric',
                'gt:0',
                'max:999999999999.999',
            ],

            /*
             * Numéro de lot : obligatoire pour garantir la traçabilité.
             *
             * L'unicité par article est vérifiée dans le Service
             * (car elle dépend du couple stock_id + numero_lot).
             */
            'numero_lot' => [
                'required',
                'string',
                'max:100',
            ],

            /*
             * Nature de l'entrée : seule une nature d'entrée est acceptée.
             *
             * Les valeurs autorisées sont définies par
             * NatureMouvement::naturesEntree() : achat, don, retour.
             */
            'nature' => [
                'required',
                'string',
                Rule::in(NatureMouvement::naturesEntree()),
            ],

            /*
             * Date d'entrée du lot (défaut aujourd'hui).
             */
            'date_entree' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            /*
             * Date du mouvement (défaut aujourd'hui).
             */
            'date_mouvement' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            /*
             * Date de fabrication (optionnelle).
             */
            'date_fabrication' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            /*
             * Date d'expiration (optionnelle).
             *
             * Doit être postérieure à la date de fabrication
             * ET à la date d'entrée si elles sont fournies.
             * Les contrôles croisés se font dans `withValidator()`.
             */
            'date_expiration' => [
                'nullable',
                'date',
            ],

            /*
             * Coût unitaire (optionnel).
             */
            'cout_unitaire' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],

            /*
             * Fournisseur ou organisme donateur (optionnel).
             */
            'fournisseur' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
             * Référence du document d'entrée (optionnel).
             */
            'reference_document' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
             * Emplacement physique de réception (optionnel).
             */
            'emplacement_id' => [
                'nullable',
                'integer',
                Rule::exists('emplacements_stocks', 'id')
                    ->where('actif', true),
            ],

            /*
             * Projet éventuellement concerné (optionnel).
             */
            'projet_id' => [
                'nullable',
                'integer',
                Rule::exists('projets', 'id'),
            ],

            /*
             * Motif complémentaire (optionnel).
             */
            'motif' => [
                'nullable',
                'string',
                'max:500',
            ],

            /*
             * Notes complémentaires (optionnel).
             */
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION CROISÉE
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie la cohérence des dates entre elles.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            $fabrication = $this->input('date_fabrication');
            $entree      = $this->input('date_entree');
            $expiration  = $this->input('date_expiration');

            /*
             * Expiration > Fabrication.
             */
            if ($fabrication && $expiration
                && strtotime($expiration) <= strtotime($fabrication)
            ) {
                $validator->errors()->add(
                    'date_expiration',
                    'La date d’expiration doit être postérieure à la date de fabrication.'
                );
            }

            /*
             * Expiration > Entrée (si les deux sont fournies).
             */
            if ($entree && $expiration
                && strtotime($expiration) <= strtotime($entree)
            ) {
                $validator->errors()->add(
                    'date_expiration',
                    'La date d’expiration doit être postérieure à la date d’entrée.'
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MESSAGES
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'stock_id.required'   => 'L’article est obligatoire.',
            'stock_id.integer'    => 'L’article doit être un identifiant valide.',
            'stock_id.exists'     => 'L’article sélectionné n’existe pas.',

            'quantite.required'   => 'La quantité est obligatoire.',
            'quantite.numeric'    => 'La quantité doit être un nombre.',
            'quantite.gt'         => 'La quantité doit être strictement supérieure à zéro.',

            'numero_lot.required' => 'Le numéro de lot est obligatoire pour une entrée de stock.',
            'numero_lot.max'      => 'Le numéro de lot ne peut pas dépasser 100 caractères.',

            'nature.required'     => 'La nature de l’entrée est obligatoire.',
            'nature.in'           => 'La nature de l’entrée n’est pas valide. Valeurs autorisées : achat, don, retour.',

            'date_entree.date'             => 'La date d’entrée doit être une date valide.',
            'date_entree.before_or_equal'  => 'La date d’entrée ne peut pas être dans le futur.',

            'date_mouvement.date'            => 'La date du mouvement doit être une date valide.',
            'date_mouvement.before_or_equal' => 'La date du mouvement ne peut pas être dans le futur.',

            'date_fabrication.date'            => 'La date de fabrication doit être une date valide.',
            'date_fabrication.before_or_equal' => 'La date de fabrication ne peut pas être dans le futur.',

            'date_expiration.date' => 'La date d’expiration doit être une date valide.',

            'cout_unitaire.numeric' => 'Le coût unitaire doit être un nombre.',
            'cout_unitaire.min'     => 'Le coût unitaire ne peut pas être négatif.',

            'fournisseur.max'          => 'Le nom du fournisseur ne peut pas dépasser 255 caractères.',
            'reference_document.max'   => 'La référence du document ne peut pas dépasser 100 caractères.',

            'emplacement_id.integer' => 'L’emplacement doit être un identifiant valide.',
            'emplacement_id.exists'  => 'L’emplacement sélectionné n’existe pas ou est inactif.',

            'projet_id.integer' => 'Le projet doit être un identifiant valide.',
            'projet_id.exists'  => 'Le projet sélectionné n’existe pas.',

            'motif.max'         => 'Le motif ne peut pas dépasser 500 caractères.',
            'notes.max'         => 'Les notes ne peuvent pas dépasser 5000 caractères.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | NOMS DES CHAMPS
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'stock_id'           => 'article',
            'quantite'           => 'quantité',
            'numero_lot'         => 'numéro de lot',
            'nature'             => 'nature',
            'date_entree'        => 'date d’entrée',
            'date_mouvement'     => 'date du mouvement',
            'date_fabrication'   => 'date de fabrication',
            'date_expiration'    => 'date d’expiration',
            'cout_unitaire'      => 'coût unitaire',
            'fournisseur'        => 'fournisseur',
            'reference_document' => 'référence document',
            'emplacement_id'     => 'emplacement',
            'projet_id'          => 'projet',
            'motif'              => 'motif',
            'notes'              => 'notes',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRÉPARATION DES DONNÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne les données prêtes pour EntreeStockService::enregistrer().
     *
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        return [
            'stock_id'           => (int) $this->input('stock_id'),
            'quantite'           => $this->input('quantite'),

            'numero_lot'         => trim((string) $this->input('numero_lot')),
            'nature'             => $this->input('nature'),

            'date_entree'        => $this->input('date_entree'),
            'date_mouvement'     => $this->input('date_mouvement'),

            'date_fabrication'   => $this->input('date_fabrication'),
            'date_expiration'    => $this->input('date_expiration'),

            'cout_unitaire'      => $this->input('cout_unitaire'),
            'fournisseur'        => $this->input('fournisseur') !== null
                ? trim((string) $this->input('fournisseur'))
                : null,
            'reference_document' => $this->input('reference_document') !== null
                ? trim((string) $this->input('reference_document'))
                : null,

            'emplacement_id'     => $this->input('emplacement_id'),
            'projet_id'          => $this->input('projet_id'),

            'motif'              => $this->input('motif'),
            'notes'              => $this->input('notes'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRÉPARATION AVANT VALIDATION
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $merge = [];

        /*
         * Trim sur les chaînes.
         */
        foreach ([
            'numero_lot',
            'fournisseur',
            'reference_document',
            'motif',
        ] as $champ) {
            if ($this->has($champ) && is_string($this->input($champ))) {
                $merge[$champ] = trim($this->input($champ));
            }
        }

        /*
         * Convertit les chaînes vides en NULL
         * pour les champs optionnels.
         */
        foreach ([
            'date_fabrication',
            'date_expiration',
            'cout_unitaire',
            'emplacement_id',
            'projet_id',
            'motif',
            'notes',
        ] as $champ) {
            if ($this->input($champ) === '') {
                $merge[$champ] = null;
            }
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}