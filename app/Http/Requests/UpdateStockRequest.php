<?php

namespace App\Http\Requests;

use App\Models\Stock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation de la modification d'un article de stock.
 *
 * SÉMANTIQUE :
 *
 *   Cette requête valide les données de la fiche catalogue.
 *
 *   Elle ne modifie JAMAIS :
 *     - le statut (actif / inactif / archive) ;
 *     - les quantités (qui n'existent pas sur cet article).
 *
 * AUTORISATION :
 *
 *   Déléguée à StockPolicy::update().
 *
 * SPÉCIFICITÉ :
 *
 *   La contrainte d'unicité sur `reference` exclut l'article
 *   en cours de modification.
 */
class UpdateStockRequest extends FormRequest
{
    /*
    |--------------------------------------------------------------------------
    | AUTORISATION
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        /** @var Stock $stock */
        $stock = $this->route('stock');

        return $this->user()->can('update', $stock);
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
        /** @var Stock $stock */
        $stock = $this->route('stock');

        return [
            'reference' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9\-_\/]+$/',
                Rule::unique('stocks', 'reference')->ignore($stock->id),
            ],

            'designation' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:255',
            ],

            'categorie' => [
                'sometimes',
                'required',
                'string',
                Rule::in([
                    'vivres',
                    'medicaments',
                    'habillement',
                    'abris',
                    'materiel',
                    'semences',
                    'outils',
                    'autre',
                ]),
            ],

            'unite' => [
                'sometimes',
                'required',
                'string',
                'max:20',
            ],

            'seuil_alerte' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
                'max:999999999999.999',
            ],

            'emplacement_principal_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:emplacements_stocks,id',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:5000',
            ],

            /*
             * Le statut ne se modifie JAMAIS via cette requête.
             * Il se modifie via activate/deactivate/archive.
             */
            'statut' => [
                'prohibited',
            ],
        ];
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
            'reference.required'   => 'La référence est obligatoire.',
            'reference.max'        => 'La référence ne peut pas dépasser 50 caractères.',
            'reference.regex'      => 'La référence ne peut contenir que des lettres, chiffres, tirets, underscores ou slashs.',
            'reference.unique'     => 'Cette référence est déjà utilisée par un autre article.',

            'designation.required' => 'La désignation est obligatoire.',
            'designation.min'      => 'La désignation doit contenir au moins 2 caractères.',
            'designation.max'      => 'La désignation ne peut pas dépasser 255 caractères.',

            'categorie.required'   => 'La catégorie est obligatoire.',
            'categorie.in'         => 'La catégorie sélectionnée est invalide.',

            'unite.required'       => 'L’unité de mesure est obligatoire.',
            'unite.max'            => 'L’unité ne peut pas dépasser 20 caractères.',

            'seuil_alerte.numeric' => 'Le seuil d’alerte doit être un nombre.',
            'seuil_alerte.min'     => 'Le seuil d’alerte ne peut pas être négatif.',

            'emplacement_principal_id.integer' => 'L’emplacement doit être un identifiant valide.',
            'emplacement_principal_id.exists'  => 'L’emplacement sélectionné n’existe pas.',

            'description.max'      => 'La description ne peut pas dépasser 5000 caractères.',

            'statut.prohibited'    => 'Le statut ne peut pas être modifié depuis ce formulaire.',
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
            'reference'                => 'référence',
            'designation'              => 'désignation',
            'categorie'                => 'catégorie',
            'unite'                    => 'unité',
            'seuil_alerte'             => 'seuil d’alerte',
            'emplacement_principal_id' => 'emplacement principal',
            'description'              => 'description',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRÉPARATION DES DONNÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne uniquement les champs réellement envoyés,
     * nettoyés, prêts pour StockService::modifier().
     *
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        $data = [];

        foreach ([
            'reference',
            'designation',
            'categorie',
            'unite',
            'seuil_alerte',
            'emplacement_principal_id',
            'description',
        ] as $champ) {
            if ($this->has($champ)) {
                $valeur = $this->input($champ);

                if (is_string($valeur)) {
                    $valeur = trim($valeur);
                }

                $data[$champ] = $valeur;
            }
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | PRÉPARATION AVANT VALIDATION
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['reference', 'designation', 'unite'] as $champ) {
            if ($this->has($champ) && is_string($this->input($champ))) {
                $merge[$champ] = trim($this->input($champ));
            }
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}