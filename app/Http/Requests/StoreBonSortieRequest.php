<?php

namespace App\Http\Requests;

use App\Models\BonSortie;
use App\Models\Stock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation de la création d'un bon de sortie.
 *
 * SÉMANTIQUE :
 *
 *   Un bon de sortie est un DOCUMENT. Sa création ne
 *   modifie AUCUN stock : elle fixe uniquement la demande.
 *
 *   Ce FormRequest valide :
 *     - les métadonnées du bon (dates, contexte) ;
 *     - la liste des lignes demandées (article + quantité).
 *
 * CHAMPS OBLIGATOIRES :
 *
 *   - date_demande       : date de la demande
 *   - lignes             : au moins 1 ligne
 *   - lignes.*.stock_id  : article ciblé
 *   - lignes.*.quantite_demandee : > 0
 *
 * CHAMPS OPTIONNELS :
 *
 *   - reference          : référence documentaire
 *   - demandeur_id       : par défaut l'utilisateur connecté
 *   - projet_id
 *   - destinataire
 *   - motif
 *   - notes
 *   - lignes.*.unite
 *   - lignes.*.notes
 *
 * AUTORISATION :
 *
 *   Déléguée à BonSortiePolicy::create().
 *
 * RÈGLES MÉTIER VÉRIFIÉES ICI :
 *
 *   - pas de doublon d'article dans les lignes ;
 *   - la quantité demandée > 0 ;
 *   - l'article existe et est actif.
 */
class StoreBonSortieRequest extends FormRequest
{
    /*
    |--------------------------------------------------------------------------
    | AUTORISATION
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        return $this->user()->can('create', BonSortie::class);
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
             * Date de la demande.
             */
            'date_demande' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            /*
             * Référence documentaire libre.
             */
            'reference' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
             * Demandeur : par défaut l'utilisateur connecté.
             * Contrôlé côté Service si absent.
             */
            'demandeur_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],

            /*
             * Projet éventuellement concerné.
             */
            'projet_id' => [
                'nullable',
                'integer',
                Rule::exists('projets', 'id'),
            ],

            /*
             * Destinataire libre.
             */
            'destinataire' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
             * Motif général du bon.
             */
            'motif' => [
                'nullable',
                'string',
                'max:500',
            ],

            /*
             * Notes complémentaires.
             */
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            /*
             * Lignes demandées.
             */
            'lignes' => [
                'required',
                'array',
                'min:1',
            ],

            'lignes.*.stock_id' => [
                'required',
                'integer',
                Rule::exists('stocks', 'id'),
            ],

            'lignes.*.quantite_demandee' => [
                'required',
                'numeric',
                'gt:0',
                'max:999999999999.999',
            ],

            'lignes.*.unite' => [
                'nullable',
                'string',
                'max:20',
            ],

            'lignes.*.notes' => [
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
     * Vérifie :
     *   - l'absence de doublons d'article ;
     *   - que chaque article existe et est actif.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            $lignes = $this->input('lignes', []);

            if (!is_array($lignes)) {
                return;
            }

            /*
             * Détection des doublons d'article.
             */
            $stockIdsVus = [];

            foreach ($lignes as $index => $ligne) {

                $stockId = $ligne['stock_id'] ?? null;

                if (!$stockId) {
                    continue;
                }

                if (isset($stockIdsVus[$stockId])) {
                    $validator->errors()->add(
                        "lignes.{$index}.stock_id",
                        'Un même article ne peut apparaître qu’une seule fois dans le bon.'
                    );
                }

                $stockIdsVus[$stockId] = true;
            }

            /*
             * Vérification de l'activité des articles.
             */
            if (!empty($stockIdsVus)) {

                $stocksInactifs = Stock::query()
                    ->whereIn('id', array_keys($stockIdsVus))
                    ->where('statut', '!=', \App\Enums\StatutStock::ACTIF->value)
                    ->pluck('designation', 'id');

                foreach ($stockIdsVus as $stockId => $_) {

                    if ($stocksInactifs->has($stockId)) {
                        $validator->errors()->add(
                            'lignes',
                            sprintf(
                                'L’article "%s" est inactif et ne peut pas être demandé.',
                                $stocksInactifs[$stockId]
                            )
                        );
                    }
                }
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
            'date_demande.required'         => 'La date de demande est obligatoire.',
            'date_demande.date'             => 'La date de demande doit être une date valide.',
            'date_demande.before_or_equal'  => 'La date de demande ne peut pas être dans le futur.',

            'reference.max'      => 'La référence ne peut pas dépasser 100 caractères.',

            'demandeur_id.integer' => 'Le demandeur doit être un identifiant valide.',
            'demandeur_id.exists'  => 'Le demandeur sélectionné n’existe pas.',

            'projet_id.integer' => 'Le projet doit être un identifiant valide.',
            'projet_id.exists'  => 'Le projet sélectionné n’existe pas.',

            'destinataire.max' => 'Le destinataire ne peut pas dépasser 255 caractères.',
            'motif.max'        => 'Le motif ne peut pas dépasser 500 caractères.',
            'notes.max'        => 'Les notes ne peuvent pas dépasser 5000 caractères.',

            'lignes.required'  => 'Le bon doit contenir au moins un article.',
            'lignes.array'     => 'Les lignes du bon sont invalides.',
            'lignes.min'       => 'Le bon doit contenir au moins un article.',

            'lignes.*.stock_id.required'   => 'L’article est obligatoire pour chaque ligne.',
            'lignes.*.stock_id.integer'    => 'L’article doit être un identifiant valide.',
            'lignes.*.stock_id.exists'     => 'L’article sélectionné n’existe pas.',

            'lignes.*.quantite_demandee.required' => 'La quantité demandée est obligatoire.',
            'lignes.*.quantite_demandee.numeric'  => 'La quantité demandée doit être un nombre.',
            'lignes.*.quantite_demandee.gt'       => 'La quantité demandée doit être strictement supérieure à zéro.',

            'lignes.*.unite.max'  => 'L’unité ne peut pas dépasser 20 caractères.',
            'lignes.*.notes.max'  => 'Les notes de ligne ne peuvent pas dépasser 5000 caractères.',
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
            'date_demande'      => 'date de demande',
            'reference'         => 'référence',
            'demandeur_id'      => 'demandeur',
            'projet_id'         => 'projet',
            'destinataire'      => 'destinataire',
            'motif'             => 'motif',
            'notes'             => 'notes',
            'lignes'            => 'lignes',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRÉPARATION DES DONNÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne les données prêtes pour
     * SortieStockService::creerBon().
     *
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        $data = [
            'date_demande' => $this->input('date_demande'),
            'reference'    => $this->input('reference') !== null
                ? trim((string) $this->input('reference'))
                : null,
            'projet_id'    => $this->input('projet_id'),
            'destinataire' => $this->input('destinataire') !== null
                ? trim((string) $this->input('destinataire'))
                : null,
            'motif'        => $this->input('motif'),
            'notes'        => $this->input('notes'),
            'lignes'       => [],
        ];

        /*
         * Le demandeur est par défaut l'utilisateur connecté.
         */
        $data['demandeur_id'] = $this->input('demandeur_id')
            ?? $this->user()->id;

        /*
         * Normalisation des lignes.
         */
        foreach ((array) $this->input('lignes', []) as $ligne) {

            if (!is_array($ligne)) {
                continue;
            }

            $data['lignes'][] = [
                'stock_id'          => (int) $ligne['stock_id'],
                'quantite_demandee' => $ligne['quantite_demandee'],
                'unite'             => isset($ligne['unite'])
                    ? trim((string) $ligne['unite'])
                    : null,
                'notes'             => $ligne['notes'] ?? null,
            ];
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

        /*
         * Trim sur les chaînes simples.
         */
        foreach (['reference', 'destinataire'] as $champ) {
            if ($this->has($champ) && is_string($this->input($champ))) {
                $merge[$champ] = trim($this->input($champ));
            }
        }

        /*
         * Conversion des chaînes vides en null.
         */
        foreach ([
            'reference',
            'projet_id',
            'destinataire',
            'motif',
            'notes',
        ] as $champ) {
            if ($this->input($champ) === '') {
                $merge[$champ] = null;
            }
        }

        /*
         * Normalisation des lignes.
         */
        $lignes = $this->input('lignes');

        if (is_array($lignes)) {
            $merge['lignes'] = array_map(function ($ligne) {
                if (!is_array($ligne)) {
                    return $ligne;
                }

                if (isset($ligne['unite']) && is_string($ligne['unite'])) {
                    $ligne['unite'] = trim($ligne['unite']);
                }

                if (isset($ligne['unite']) && $ligne['unite'] === '') {
                    $ligne['unite'] = null;
                }

                if (isset($ligne['notes']) && $ligne['notes'] === '') {
                    $ligne['notes'] = null;
                }

                return $ligne;
            }, $lignes);
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}