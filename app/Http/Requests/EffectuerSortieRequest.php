<?php

namespace App\Http\Requests;

use App\Models\BonSortie;
use App\Models\LigneBonSortie;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation de l'exécution physique d'un bon de sortie.
 *
 * SÉMANTIQUE :
 *
 *   L'exécution est l'opération CRITIQUE qui :
 *     1. sélectionne les lots FEFO pour chaque ligne ;
 *     2. décrémente les quantités physiques ;
 *     3. crée les mouvements de sortie ;
 *     4. passe le bon à `sorti`.
 *
 *   Cette opération est menée exclusivement par
 *   SortieStockService::effectuerSortie(), sous transaction
 *   et verrouillage.
 *
 * CHAMPS OBLIGATOIRES :
 *
 *   - lignes                  : au moins une ligne
 *   - lignes.*.ligne_id       : ligne à sortir
 *   - lignes.*.quantite_sortie : > 0, <= quantite_validee
 *
 * CHAMPS OPTIONNELS :
 *
 *   - commentaire             : note globale d'exécution
 *   - lignes.*.notes          : note par ligne
 *
 * AUTORISATION :
 *
 *   Déléguée à BonSortiePolicy::release() sur le bon.
 *
 * RÈGLES MÉTIER VÉRIFIÉES ICI :
 *
 *   - toutes les lignes du bon doivent être présentes ;
 *   - aucune ligne n'appartient à un autre bon ;
 *   - les quantités sorties sont <= validées ;
 *   - au moins une quantité > 0.
 *
 *   Le contrôle de DISPONIBILITÉ RÉELLE (stock physique,
 *   FEFO, verrouillage) est fait exclusivement par
 *   SortieStockService, dans la transaction.
 */
class EffectuerSortieRequest extends FormRequest
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

        return $this->user()->can('release', $bon);
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
             * Lignes à sortir.
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

            'lignes.*.quantite_sortie' => [
                'required',
                'numeric',
                'gt:0',
                'max:999999999999.999',
            ],

            'lignes.*.notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            /*
             * Commentaire global.
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
             * Récupération des lignes du bon.
             */
            $lignesBon = $bon->lignes()
                ->get()
                ->keyBy('id');

            /*
             * Vérification : toutes les lignes doivent être présentes.
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
                        'Toutes les lignes du bon doivent être traitées (%d ligne(s) manquante(s)).',
                        count($idsManquants)
                    )
                );
            }

            /*
             * Vérification ligne par ligne.
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

                $quantiteSortie  = $ligneSaisie['quantite_sortie'] ?? null;
                $quantiteValidee = (float) $ligneReelle->quantite_validee;

                if ($quantiteSortie === null) {
                    continue;
                }

                /*
                 * Interdiction de sortir plus que validé.
                 */
                if ((float) $quantiteSortie > $quantiteValidee) {
                    $validator->errors()->add(
                        "lignes.{$index}.quantite_sortie",
                        sprintf(
                            'La quantité sortie (%s) ne peut pas dépasser la quantité validée (%s).',
                            $quantiteSortie,
                            $quantiteValidee
                        )
                    );
                }
            }

            /*
             * Vérification : au moins une quantité > 0.
             */
            $totalSortie = collect($lignesSaisies)
                ->sum(fn ($l) => (float) ($l['quantite_sortie'] ?? 0));

            if ($totalSortie <= 0) {
                $validator->errors()->add(
                    'lignes',
                    'Au moins une ligne doit avoir une quantité sortie strictement positive.'
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
            'lignes.required' => 'Les lignes de sortie sont obligatoires.',
            'lignes.array'    => 'Les lignes de sortie sont invalides.',
            'lignes.min'      => 'Au moins une ligne doit être sortie.',

            'lignes.*.ligne_id.required' => 'L’identifiant de ligne est obligatoire.',
            'lignes.*.ligne_id.integer'  => 'L’identifiant de ligne est invalide.',
            'lignes.*.ligne_id.exists'   => 'Une ligne référencée n’existe pas.',

            'lignes.*.quantite_sortie.required' => 'La quantité sortie est obligatoire.',
            'lignes.*.quantite_sortie.numeric'  => 'La quantité sortie doit être un nombre.',
            'lignes.*.quantite_sortie.gt'       => 'La quantité sortie doit être strictement supérieure à zéro.',

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
     * SortieStockService::effectuerSortie().
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
                'quantite_sortie'  => $ligne['quantite_sortie'],
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