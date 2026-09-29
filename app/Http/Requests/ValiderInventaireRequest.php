<?php

namespace App\Http\Requests;

use App\Models\InventaireStock;
use App\Models\InventaireStockLigne;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation de la validation finale d'un inventaire.
 *
 * SÉMANTIQUE :
 *
 *   La validation est l'étape charnière de l'inventaire :
 *   c'est le moment où les écarts constatés sont transformés
 *   en MOUVEMENTS D'AJUSTEMENT sur les lots.
 *
 *   Avant : inventaire terminé, écarts calculés, stock inchangé.
 *   Après : inventaire validé, stock ajusté, mouvements créés.
 *
 *   La validation est DÉFINITIVE : un inventaire validé
 *   ne peut plus être modifié ni annulé.
 *
 *   Cette opération est menée exclusivement par
 *   InventaireStockService::valider(), sous transaction
 *   et verrouillage.
 *
 * CHAMPS OPTIONNELS :
 *
 *   - lignes.*.ligne_id  : lignes spécifiques à ajuster
 *     (par défaut : toutes les lignes avec écart)
 *   - commentaire        : note de validation
 *
 * AUTORISATION :
 *
 *   Déléguée à InventaireStockPolicy::validate() sur l'inventaire.
 *
 * RÈGLES MÉTIER VÉRIFIÉES ICI :
 *
 *   - toutes les lignes du bon doivent être comptées ;
 *   - le stock n'a pas changé depuis le début ;
 *   - les quantités ajustées sont cohérentes avec les écarts.
 *
 *   Les deux premières règles sont vérifiées par le service
 *   (transaction + verrouillage). La troisième est vérifiée ici
 *   pour un message d'erreur clair.
 */
class ValiderInventaireRequest extends FormRequest
{
    /*
    |--------------------------------------------------------------------------
    | AUTORISATION
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        /** @var InventaireStock $inventaire */
        $inventaire = $this->route('inventaire');

        return $this->user()->can('validate', $inventaire);
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
             * Lignes à ajuster.
             *
             * Si vide, TOUTES les lignes avec un écart non nul
             * seront ajustées automatiquement.
             *
             * Si renseigné, seules ces lignes seront ajustées.
             */
            'lignes' => [
                'nullable',
                'array',
            ],

            'lignes.*.ligne_id' => [
                'required',
                'integer',
                'exists:inventaire_stock_lignes,id',
            ],

            /*
             * Commentaire de validation.
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

            /** @var InventaireStock $inventaire */
            $inventaire = $this->route('inventaire');

            if (!$inventaire) {
                return;
            }

            /*
             * Vérification : l'inventaire doit pouvoir être validé.
             */
            if (!$inventaire->peutEtreValide()) {
                $validator->errors()->add(
                    'inventaire',
                    sprintf(
                        'Cet inventaire ne peut pas être validé (statut : %s).',
                        $inventaire->statut
                    )
                );
                return;
            }

            /*
             * Vérification : toutes les lignes doivent être comptées.
             */
            $nombreNonComptees = $inventaire->lignes()
                ->whereNull('quantite_comptee')
                ->count();

            if ($nombreNonComptees > 0) {
                $validator->errors()->add(
                    'inventaire',
                    sprintf(
                        '%d ligne(s) ne sont pas encore comptées. Toutes les lignes doivent être comptées avant la validation.',
                        $nombreNonComptees
                    )
                );
            }

            /*
             * Si des lignes spécifiques sont envoyées,
             * vérification de leur appartenance.
             */
            $lignesSaisies = $this->input('lignes');

            if (!is_array($lignesSaisies) || empty($lignesSaisies)) {
                return;
            }

            $lignesInventaire = $inventaire->lignes()
                ->get()
                ->keyBy('id');

            $idsVus = [];

            foreach ($lignesSaisies as $index => $ligneSaisie) {

                $ligneId = $ligneSaisie['ligne_id'] ?? null;

                if (!$ligneId) {
                    continue;
                }

                /*
                 * Doublon.
                 */
                if (isset($idsVus[$ligneId])) {
                    $validator->errors()->add(
                        "lignes.{$index}.ligne_id",
                        'Cette ligne apparaît plusieurs fois.'
                    );
                    continue;
                }

                $idsVus[$ligneId] = true;

                /*
                 * Appartenance à l'inventaire.
                 */
                if (!$lignesInventaire->has($ligneId)) {
                    $validator->errors()->add(
                        "lignes.{$index}.ligne_id",
                        'Cette ligne n’appartient pas à cet inventaire.'
                    );
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
            'lignes.array' => 'La liste des lignes est invalide.',

            'lignes.*.ligne_id.required' => 'L’identifiant de ligne est obligatoire.',
            'lignes.*.ligne_id.integer'  => 'L’identifiant de ligne est invalide.',
            'lignes.*.ligne_id.exists'   => 'Une ligne référencée n’existe pas.',

            'commentaire.max' => 'Le commentaire ne peut pas dépasser 5000 caractères.',
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
     * Retourne les identifiants de lignes à ajuster.
     *
     * Si aucune ligne n'est spécifiée, retourne un tableau vide :
     * le service ajustera alors automatiquement TOUTES les lignes
     * avec un écart non nul.
     *
     * @return array<int, int>
     */
    public function lignesAAdjuster(): array
    {
        $lignes = $this->input('lignes');

        if (!is_array($lignes) || empty($lignes)) {
            return [];
        }

        return collect($lignes)
            ->pluck('ligne_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Retourne les données prêtes pour
     * InventaireStockService::valider().
     *
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        return [
            'lignes_a_ajuster' => $this->lignesAAdjuster(),
            'commentaire'      => $this->input('commentaire'),
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
         * Nettoyage des lignes vides.
         */
        $lignes = $this->input('lignes');

        if (is_array($lignes)) {
            $merge['lignes'] = array_values(
                array_filter(
                    $lignes,
                    fn ($l) => is_array($l) && !empty($l['ligne_id'])
                )
            );
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}