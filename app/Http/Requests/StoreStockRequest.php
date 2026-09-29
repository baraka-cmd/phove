<?php

namespace App\Http\Requests;

use App\Enums\StatutStock;
use App\Models\Stock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation de la création d'un article de stock.
 *
 * SÉMANTIQUE :
 *
 *   Cette requête valide UNIQUEMENT les données de la fiche
 *   catalogue (référence, désignation, catégorie, unité,
 *   seuil d'alerte, emplacement principal, description).
 *
 *   Elle ne traite AUCUNE quantité : un article est créé
 *   sans stock. Les quantités viendront par les entrées.
 *
 * AUTORISATION :
 *
 *   Déléguée à StockPolicy::create().
 *
 * DONNÉES PRÉPARÉES :
 *
 *   `validatedData()` retourne un tableau prêt à être passé
 *   à StockService::creer().
 */
class StoreStockRequest extends FormRequest
{
    /*
    |--------------------------------------------------------------------------
    | AUTORISATION
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        return $this->user()->can('create', Stock::class);
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
             * Référence unique.
             */
            'reference' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9\-_\/]+$/',
                Rule::unique('stocks', 'reference'),
            ],

            /*
             * Désignation.
             */
            'designation' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],

            /*
             * Catégorie : alignée sur l'enum de la migration.
             */
            'categorie' => [
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

            /*
             * Unité.
             */
            'unite' => [
                'required',
                'string',
                'max:20',
            ],

            /*
             * Seuil d'alerte : optionnel, non négatif.
             */
            'seuil_alerte' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999999.999',
            ],

            /*
             * Emplacement principal : optionnel.
             */
            'emplacement_principal_id' => [
                'nullable',
                'integer',
                'exists:emplacements_stocks,id',
            ],

            /*
             * Description : optionnelle.
             */
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            /*
             * Statut : interdit à la création.
             *
             * Un article créé est TOUJOURS actif.
             * Le statut ne se modifie qu'après via
             * activate/deactivate/archive.
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
            'reference.string'     => 'La référence doit être une chaîne de caractères.',
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

            'statut.prohibited'    => 'Le statut ne peut pas être défini manuellement.',
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
     * Retourne les données prêtes pour StockService::creer().
     *
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        return [
            'reference'                => trim($this->input('reference')),
            'designation'              => trim($this->input('designation')),
            'categorie'                => $this->input('categorie'),
            'unite'                    => trim($this->input('unite')),
            'seuil_alerte'             => $this->input('seuil_alerte'),
            'emplacement_principal_id' => $this->input('emplacement_principal_id'),
            'description'              => $this->input('description'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRÉPARATION AVANT VALIDATION
    |--------------------------------------------------------------------------
    */

    /**
     * Normalise les entrées AVANT la validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'reference'   => is_string($this->reference)
                ? trim($this->reference)
                : $this->reference,
            'designation' => is_string($this->designation)
                ? trim($this->designation)
                : $this->designation,
            'unite'       => is_string($this->unite)
                ? trim($this->unite)
                : $this->unite,
        ]);
    }
}