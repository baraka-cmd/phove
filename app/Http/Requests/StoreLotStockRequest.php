<?php

namespace App\Http\Requests;

use App\Enums\StatutLot;
use App\Models\LotStock;
use App\Models\Stock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation de la création ADMINISTRATIVE d'un lot.
 *
 * ⚠️ ATTENTION — USAGE EXCEPTIONNEL :
 *
 *   Dans le workflow normal, un lot est créé AUTOMATIQUEMENT
 *   par EntreeStockService::obtenirOuCreerLot() lors d'une
 *   entrée de stock. Aucun utilisateur ne devrait créer
 *   un lot manuellement.
 *
 *   Ce FormRequest n'existe que pour couvrir des cas rares :
 *     - migration de données historiques ;
 *     - correction administrative après audit ;
 *     - pré-création de lots pour des entrées planifiées.
 *
 *   Les créations normales passent par :
 *     POST /admin/stocks/entrees
 *     → EntreeStockService::enregistrer()
 *     → obtenirOuCreerLot()
 *
 * CHAMPS OBLIGATOIRES :
 *
 *   - stock_id         : article auquel appartient le lot
 *   - numero_lot       : identifiant du lot chez le fabricant
 *   - date_entree      : date d'entrée physique
 *   - quantite_initiale : >= 0
 *
 * CHAMPS OPTIONNELS :
 *
 *   - date_fabrication
 *   - date_expiration
 *   - cout_unitaire
 *   - fournisseur
 *   - reference_document
 *   - statut (par défaut : actif)
 *
 * AUTORISATION :
 *
 *   Déléguée à LotStockPolicy::create().
 *
 * RÈGLES MÉTIER VÉRIFIÉES ICI :
 *
 *   - date_expiration > date_fabrication ;
 *   - date_expiration > date_entree ;
 *   - date_fabrication <= date_entree ;
 *   - l'article existe et est actif.
 */
class StoreLotStockRequest extends FormRequest
{
    /*
    |--------------------------------------------------------------------------
    | AUTORISATION
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        return $this->user()->can('create', LotStock::class);
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
             * Article auquel appartient ce lot.
             */
            'stock_id' => [
                'required',
                'integer',
                Rule::exists('stocks', 'id'),
            ],

            /*
             * Numéro de lot.
             *
             * L'unicité est vérifiée sur le couple
             * (stock_id, numero_lot) — voir withValidator.
             */
            'numero_lot' => [
                'required',
                'string',
                'max:100',
            ],

            /*
             * Date d'entrée physique du lot.
             */
            'date_entree' => [
                'required',
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
             */
            'date_expiration' => [
                'nullable',
                'date',
            ],

            /*
             * Quantité initiale du lot.
             *
             * Cette valeur fixe `quantite_initiale` ET
             * `quantite_actuelle` du nouveau lot.
             *
             * Peut être 0 pour un lot "vide" à alimenter
             * plus tard par des entrées.
             */
            'quantite_initiale' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.999',
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
             * Fournisseur (optionnel).
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
             * Statut initial.
             *
             * Par défaut : actif.
             * Les autres statuts sont autorisés dans ce contexte
             * administratif, mais doivent rester cohérents avec
             * les invariants métier.
             */
            'statut' => [
                'nullable',
                'string',
                Rule::in(StatutLot::valeurs()),
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION CROISÉE
    |--------------------------------------------------------------------------
    */

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            /*
             * Vérification de l'unicité (stock_id, numero_lot).
             */
            $stockId  = $this->input('stock_id');
            $numeroLot = trim((string) $this->input('numero_lot'));

            if ($stockId && $numeroLot !== '') {
                $existe = LotStock::query()
                    ->where('stock_id', $stockId)
                    ->where('numero_lot', $numeroLot)
                    ->exists();

                if ($existe) {
                    $validator->errors()->add(
                        'numero_lot',
                        sprintf(
                            'Le numéro de lot "%s" existe déjà pour cet article.',
                            $numeroLot
                        )
                    );
                }
            }

            /*
             * Vérification de l'activité de l'article.
             */
            if ($stockId) {
                $stock = Stock::find($stockId);

                if ($stock && !$stock->peutEtreUtilise()) {
                    $validator->errors()->add(
                        'stock_id',
                        sprintf(
                            'L’article "%s" est inactif et ne peut pas recevoir un nouveau lot.',
                            $stock->designation
                        )
                    );
                }
            }

            /*
             * Vérification des dates.
             */
            $fabrication = $this->input('date_fabrication');
            $entree      = $this->input('date_entree');
            $expiration  = $this->input('date_expiration');

            /*
             * Fabrication <= Entrée.
             */
            if ($fabrication && $entree
                && strtotime($fabrication) > strtotime($entree)
            ) {
                $validator->errors()->add(
                    'date_fabrication',
                    'La date de fabrication ne peut pas être postérieure à la date d’entrée.'
                );
            }

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
             * Expiration > Entrée.
             */
            if ($entree && $expiration
                && strtotime($expiration) <= strtotime($entree)
            ) {
                $validator->errors()->add(
                    'date_expiration',
                    'La date d’expiration doit être postérieure à la date d’entrée.'
                );
            }

            /*
             * Cohérence : un lot avec quantité > 0 ne peut pas
             * être créé directement avec un statut EPUISE ou EXPIRE.
             */
            $quantite = (float) $this->input('quantite_initiale', 0);
            $statut   = $this->input('statut');

            if ($quantite > 0 && in_array($statut, [
                StatutLot::EPUISE->value,
                StatutLot::EXPIRE->value,
            ], true)) {
                $validator->errors()->add(
                    'statut',
                    'Un lot avec une quantité positive ne peut pas être créé avec un statut épuisé ou expiré.'
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

            'numero_lot.required' => 'Le numéro de lot est obligatoire.',
            'numero_lot.max'      => 'Le numéro de lot ne peut pas dépasser 100 caractères.',

            'date_entree.required'        => 'La date d’entrée est obligatoire.',
            'date_entree.date'            => 'La date d’entrée doit être une date valide.',
            'date_entree.before_or_equal' => 'La date d’entrée ne peut pas être dans le futur.',

            'date_fabrication.date'            => 'La date de fabrication doit être une date valide.',
            'date_fabrication.before_or_equal' => 'La date de fabrication ne peut pas être dans le futur.',

            'date_expiration.date' => 'La date d’expiration doit être une date valide.',

            'quantite_initiale.required' => 'La quantité initiale est obligatoire.',
            'quantite_initiale.numeric'  => 'La quantité initiale doit être un nombre.',
            'quantite_initiale.min'      => 'La quantité initiale ne peut pas être négative.',

            'cout_unitaire.numeric' => 'Le coût unitaire doit être un nombre.',
            'cout_unitaire.min'     => 'Le coût unitaire ne peut pas être négatif.',

            'fournisseur.max'        => 'Le nom du fournisseur ne peut pas dépasser 255 caractères.',
            'reference_document.max' => 'La référence du document ne peut pas dépasser 100 caractères.',

            'statut.in' => 'Le statut sélectionné est invalide.',
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
            'numero_lot'         => 'numéro de lot',
            'date_entree'        => 'date d’entrée',
            'date_fabrication'   => 'date de fabrication',
            'date_expiration'    => 'date d’expiration',
            'quantite_initiale'  => 'quantité initiale',
            'cout_unitaire'      => 'coût unitaire',
            'fournisseur'        => 'fournisseur',
            'reference_document' => 'référence document',
            'statut'             => 'statut',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRÉPARATION DES DONNÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne les données prêtes pour LotStockService::creer()
     * (si cette méthode est créée).
     *
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        return [
            'stock_id'           => (int) $this->input('stock_id'),
            'numero_lot'         => trim((string) $this->input('numero_lot')),
            'date_entree'        => $this->input('date_entree'),
            'date_fabrication'   => $this->input('date_fabrication'),
            'date_expiration'    => $this->input('date_expiration'),
            'quantite_initiale'  => $this->input('quantite_initiale'),
            'cout_unitaire'      => $this->input('cout_unitaire'),
            'fournisseur'        => $this->input('fournisseur') !== null
                ? trim((string) $this->input('fournisseur'))
                : null,
            'reference_document' => $this->input('reference_document') !== null
                ? trim((string) $this->input('reference_document'))
                : null,
            'statut'             => $this->input('statut')
                ?? StatutLot::ACTIF->value,
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
        ] as $champ) {
            if ($this->has($champ) && is_string($this->input($champ))) {
                $merge[$champ] = trim($this->input($champ));
            }
        }

        /*
         * Conversion des chaînes vides en null.
         */
        foreach ([
            'date_fabrication',
            'date_expiration',
            'cout_unitaire',
            'fournisseur',
            'reference_document',
            'statut',
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