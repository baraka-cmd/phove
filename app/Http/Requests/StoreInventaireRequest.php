<?php

namespace App\Http\Requests;

use App\Models\InventaireStock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation de la création d'un inventaire de stock.
 *
 * SÉMANTIQUE :
 *
 *   Un inventaire fige la photographie du stock pour chaque
 *   article ciblé. La quantité théorique est enregistrée
 *   dans chaque ligne. La quantité physique sera saisie
 *   ultérieurement via des comptages.
 *
 *   À la création, AUCUN stock n'est modifié.
 *
 * CHAMPS OBLIGATOIRES :
 *
 *   - date_debut : date de début de l'inventaire
 *
 * CHAMPS OPTIONNELS :
 *
 *   - emplacement_id  : périmètre (null = tous)
 *   - responsable_id  : par défaut l'utilisateur connecté
 *   - stock_ids       : articles ciblés (vide = tous les articles du périmètre)
 *   - notes
 *
 * AUTORISATION :
 *
 *   Déléguée à InventaireStockPolicy::create().
 *
 * RÈGLES MÉTIER VÉRIFIÉES ICI :
 *
 *   - l'emplacement ciblé existe et est actif ;
 *   - les articles ciblés existent et sont actifs ;
 *   - au moins un article sera inclus (soit par stock_ids,
 *     soit par le périmètre de l'emplacement).
 *
 *   La vérification du nombre effectif d'articles inclus
 *   est faite par le service InventaireStockService::creer().
 */
class StoreInventaireRequest extends FormRequest
{
    /*
    |--------------------------------------------------------------------------
    | AUTORISATION
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        return $this->user()->can('create', InventaireStock::class);
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
             * Date de début de l'inventaire.
             */
            'date_debut' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            /*
             * Périmètre : emplacement ciblé (optionnel).
             *
             * Si null, l'inventaire porte sur tous les
             * emplacements.
             */
            'emplacement_id' => [
                'nullable',
                'integer',
                Rule::exists('emplacements_stocks', 'id')
                    ->where('actif', true),
            ],

            /*
             * Responsable de l'inventaire.
             *
             * Par défaut l'utilisateur connecté.
             */
            'responsable_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],

            /*
             * Articles ciblés.
             *
             * Si vide, tous les articles actifs du périmètre
             * (emplacement) seront inclus.
             *
             * Si renseigné, seuls ces articles seront inclus.
             */
            'stock_ids' => [
                'nullable',
                'array',
            ],

            'stock_ids.*' => [
                'integer',
                Rule::exists('stocks', 'id'),
            ],

            /*
             * Notes complémentaires.
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            $stockIds = $this->input('stock_ids');

            if (!is_array($stockIds) || empty($stockIds)) {
                /*
                 * Cas "inventaire global" : on ne vérifie rien
                 * ici. Le service s'assurera qu'au moins un
                 * article est inclus.
                 */
                return;
            }

            /*
             * Vérification de l'absence de doublons.
             */
            $uniques = array_unique($stockIds);

            if (count($uniques) !== count($stockIds)) {
                $validator->errors()->add(
                    'stock_ids',
                    'La liste des articles ne doit pas contenir de doublons.'
                );
            }

            /*
             * Vérification de l'activité des articles.
             */
            $stocksInactifs = \App\Models\Stock::query()
                ->whereIn('id', $uniques)
                ->where('statut', '!=', \App\Enums\StatutStock::ACTIF->value)
                ->pluck('designation', 'id');

            if ($stocksInactifs->isNotEmpty()) {
                $validator->errors()->add(
                    'stock_ids',
                    sprintf(
                        '%d article(s) sélectionné(s) sont inactifs ou archivés.',
                        $stocksInactifs->count()
                    )
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
            'date_debut.required'        => 'La date de début de l’inventaire est obligatoire.',
            'date_debut.date'            => 'La date de début doit être une date valide.',
            'date_debut.before_or_equal' => 'La date de début ne peut pas être dans le futur.',

            'emplacement_id.integer' => 'L’emplacement doit être un identifiant valide.',
            'emplacement_id.exists'  => 'L’emplacement sélectionné n’existe pas ou est inactif.',

            'responsable_id.integer' => 'Le responsable doit être un identifiant valide.',
            'responsable_id.exists'  => 'Le responsable sélectionné n’existe pas.',

            'stock_ids.array'   => 'La liste des articles est invalide.',
            'stock_ids.*.integer' => 'Chaque article doit être un identifiant valide.',
            'stock_ids.*.exists'  => 'Un article sélectionné n’existe pas.',

            'notes.max' => 'Les notes ne peuvent pas dépasser 5000 caractères.',
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
            'date_debut'     => 'date de début',
            'emplacement_id' => 'emplacement',
            'responsable_id' => 'responsable',
            'stock_ids'      => 'articles ciblés',
            'notes'          => 'notes',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRÉPARATION DES DONNÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne les données prêtes pour
     * InventaireStockService::creer().
     *
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        $stockIds = $this->input('stock_ids');

        /*
         * Normalisation : tableau d'entiers uniques.
         */
        if (is_array($stockIds)) {
            $stockIds = array_values(
                array_unique(
                    array_map('intval', $stockIds)
                )
            );
        } else {
            $stockIds = [];
        }

        return [
            'date_debut'     => $this->input('date_debut'),
            'emplacement_id' => $this->input('emplacement_id'),
            'responsable_id' => $this->input('responsable_id')
                ?? $this->user()->id,
            'stock_ids'      => $stockIds,
            'notes'          => $this->input('notes'),
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
         * Conversion des chaînes vides en null.
         */
        foreach ([
            'emplacement_id',
            'responsable_id',
            'notes',
        ] as $champ) {
            if ($this->input($champ) === '') {
                $merge[$champ] = null;
            }
        }

        /*
         * Normalisation de stock_ids : on filtre les valeurs vides.
         */
        $stockIds = $this->input('stock_ids');

        if (is_array($stockIds)) {
            $merge['stock_ids'] = array_values(
                array_filter(
                    $stockIds,
                    fn ($id) => $id !== null
                        && $id !== ''
                        && $id !== 0
                        && $id !== '0'
                )
            );
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}