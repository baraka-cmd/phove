<?php

namespace App\Http\Requests;

use App\Models\BonSortie;
use App\Models\LigneBonSortie;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation de la validation d'un bon de sortie.
 *
 * SÉMANTIQUE :
 *
 *   Le validateur confirme la quantité qu'il accepte pour
 *   chaque ligne, avant le passage du bon au statut `valide`.
 *
 *   C'est le moment où la quantité VALIDÉE est fixée :
 *
 *     quantite_validee <= quantite_demandee
 *
 *   La validation ne modifie AUCUN stock. Elle est purement
 *   documentaire : elle fige la quantité autorisée à sortir.
 *
 * CHAMPS OBLIGATOIRES :
 *
 *   - lignes                    : au moins une ligne
 *   - lignes.*.ligne_id         : ligne du bon à valider
 *   - lignes.*.quantite_validee : >= 0, <= quantite_demandee
 *
 * CHAMPS OPTIONNELS :
 *
 *   - commentaire               : note globale de validation
 *   - lignes.*.notes            : note par ligne
 *
 * AUTORISATION :
 *
 *   Déléguée à BonSortiePolicy::validate() sur le bon.
 *
 * RÈGLES MÉTIER VÉRIFIÉES ICI :
 *
 *   - toutes les lignes du bon doivent être présentes ;
 *   - aucune ligne n'appartient à un autre bon ;
 *   - les quantités validées sont <= demandées.
 *
 *   Le contrôle du statut du bon (SOUMIS) est fait par
 *   SortieStockService::validerBon().
 */
class ValiderBonSortieRequest extends FormRequest
{
    /*
    |--------------------------------------------------------------------------
    | AUTORISATION
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        /** @var BonSortie $bon */
        $bon = $this->route('bonSortie');

        return $this->user()->can('validate', $bon);
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
             * Lignes à valider.
             */
            'lignes' => [
                'required',
                'array',
                'min:1',
            ],

            'lignes.*.ligne_id' => [
                'required',
                'integer',
                'exists:lignes_bons_sortie,id',
            ],

            'lignes.*.quantite_validee' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.999',
            ],

            'lignes.*.notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            /*
             * Commentaire global de validation.
             */
            'commentaire' => [
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

            /** @var BonSortie $bon */
            $bon = $this->route('bonSortie');

            if (!$bon) {
                return;
            }

            $lignesSaisies = $this->input('lignes', []);

            if (!is_array($lignesSaisies)) {
                return;
            }

            /*
             * Récupération des lignes réelles du bon.
             */
            $lignesBon = $bon->lignes()
                ->get()
                ->keyBy('id');

            /*
             * Vérification : toutes les lignes du bon doivent
             * être présentes dans la requête.
             */
            $idsSaisis = collect($lignesSaisies)
                ->pluck('ligne_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $idsAttendus = $lignesBon->keys()->all();

            $idsManquants = array_diff($idsAttendus, $idsSaisis);

            if (!empty($idsManquants)) {
                $validator->errors()->add(
                    'lignes',
                    sprintf(
                        'Toutes les lignes du bon doivent être validées (%d ligne(s) manquante(s)).',
                        count($idsManquants)
                    )
                );
            }

            /*
             * Vérification : aucune ligne étrangère.
             */
            foreach ($lignesSaisies as $index => $ligneSaisie) {

                $ligneId = $ligneSaisie['ligne_id'] ?? null;

                if (!$ligneId) {
                    continue;
                }

                if (!$lignesBon->has($ligneId)) {
                    $validator->errors()->add(
                        "lignes.{$index}.ligne_id",
                        'Cette ligne n’appartient pas à ce bon.'
                    );
                    continue;
                }

                /** @var LigneBonSortie $ligneReelle */
                $ligneReelle = $lignesBon->get($ligneId);

                $quantiteValidee = $ligneSaisie['quantite_validee'] ?? null;

                if ($quantiteValidee === null) {
                    continue;
                }

                $quantiteDemandee = (float) $ligneReelle->quantite_demandee;

                if ((float) $quantiteValidee > $quantiteDemandee) {
                    $validator->errors()->add(
                        "lignes.{$index}.quantite_validee",
                        sprintf(
                            'La quantité validée (%s) ne peut pas dépasser la quantité demandée (%s).',
                            $quantiteValidee,
                            $quantiteDemandee
                        )
                    );
                }
            }

            /*
             * Vérification : au moins une quantité validée > 0.
             */
            $totalValide = collect($lignesSaisies)
                ->sum(fn ($l) => (float) ($l['quantite_validee'] ?? 0));

            if ($totalValide <= 0) {
                $validator->errors()->add(
                    'lignes',
                    'Au moins une ligne doit avoir une quantité validée strictement positive.'
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
            'lignes.required' => 'Les lignes de validation sont obligatoires.',
            'lignes.array'    => 'Les lignes de validation sont invalides.',
            'lignes.min'      => 'Au moins une ligne doit être validée.',

            'lignes.*.ligne_id.required' => 'L’identifiant de ligne est obligatoire.',
            'lignes.*.ligne_id.integer'  => 'L’identifiant de ligne est invalide.',
            'lignes.*.ligne_id.exists'   => 'Une ligne référencée n’existe pas.',

            'lignes.*.quantite_validee.required' => 'La quantité validée est obligatoire.',
            'lignes.*.quantite_validee.numeric'  => 'La quantité validée doit être un nombre.',
            'lignes.*.quantite_validee.min'      => 'La quantité validée ne peut pas être négative.',

            'lignes.*.notes.max' => 'Les notes de ligne ne peuvent pas dépasser 5000 caractères.',
            'commentaire.max'    => 'Le commentaire ne peut pas dépasser 5000 caractères.',
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
            'lignes'      => 'lignes',
            'commentaire' => 'commentaire',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRÉPARATION DES DONNÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne les données prêtes pour
     * SortieStockService::validerBon().
     *
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        $lignes = [];

        foreach ((array) $this->input('lignes', []) as $ligne) {

            if (!is_array($ligne)) {
                continue;
            }

            $lignes[] = [
                'ligne_id'         => (int) $ligne['ligne_id'],
                'quantite_validee' => $ligne['quantite_validee'],
                'notes'            => $ligne['notes'] ?? null,
            ];
        }

        return [
            'lignes'      => $lignes,
            'commentaire' => $this->input('commentaire'),
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
         * Trim sur le commentaire.
         */
        if (is_string($this->input('commentaire'))) {
            $merge['commentaire'] = trim($this->input('commentaire'));
        }

        if ($this->input('commentaire') === '') {
            $merge['commentaire'] = null;
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

                if (isset($ligne['notes']) && is_string($ligne['notes'])) {
                    $ligne['notes'] = trim($ligne['notes']);
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