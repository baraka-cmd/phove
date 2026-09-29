<?php

namespace App\Http\Requests;

use App\Models\InventaireStock;
use App\Models\InventaireStockLigne;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation du comptage physique d'un inventaire.
 *
 * SÉMANTIQUE :
 *
 *   Le comptage consiste à saisir la quantité PHYSIQUE
 *   réellement constatée pour une ou plusieurs lignes
 *   d'un inventaire.
 *
 *   À ce stade :
 *     - AUCUN stock n'est modifié ;
 *     - l'écart est simplement calculé et enregistré
 *       dans la ligne d'inventaire ;
 *     - l'ajustement effectif aura lieu à la validation
 *       de l'inventaire (InventaireStockService::valider()).
 *
 * CHAMPS OBLIGATOIRES :
 *
 *   - comptages                  : au moins une ligne à compter
 *   - comptages.*.ligne_id       : ligne d'inventaire ciblée
 *   - comptages.*.quantite_comptee : >= 0
 *
 * CHAMPS OPTIONNELS :
 *
 *   - comptages.*.notes          : note par ligne
 *
 * AUTORISATION :
 *
 *   Déléguée à InventaireStockPolicy::perform() sur l'inventaire.
 *
 * RÈGLES MÉTIER VÉRIFIÉES ICI :
 *
 *   - toutes les lignes référencées appartiennent à l'inventaire ;
 *   - aucune ligne dupliquée ;
 *   - quantités >= 0.
 *
 *   Le contrôle de l'état de l'inventaire (brouillon ou en_cours)
 *   est fait par InventaireStockService::compterLot().
 */
class ComptageInventaireRequest extends FormRequest
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

        return $this->user()->can('perform', $inventaire);
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
             * Comptages à enregistrer.
             */
            'comptages' => [
                'required',
                'array',
                'min:1',
            ],

            'comptages.*.ligne_id' => [
                'required',
                'integer',
                'exists:inventaire_stock_lignes,id',
            ],

            'comptages.*.quantite_comptee' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.999',
            ],

            'comptages.*.notes' => [
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

            $comptages = $this->input('comptages', []);

            if (!is_array($comptages)) {
                return;
            }

            /*
             * Récupération des lignes réelles de l'inventaire.
             */
            $lignesInventaire = $inventaire->lignes()
                ->get()
                ->keyBy('id');

            /*
             * Détection des doublons.
             */
            $idsVus = [];

            foreach ($comptages as $index => $comptage) {

                $ligneId = $comptage['ligne_id'] ?? null;

                if (!$ligneId) {
                    continue;
                }

                /*
                 * Doublon dans la requête.
                 */
                if (isset($idsVus[$ligneId])) {
                    $validator->errors()->add(
                        "comptages.{$index}.ligne_id",
                        'Cette ligne apparaît plusieurs fois dans la requête.'
                    );
                    continue;
                }

                $idsVus[$ligneId] = true;

                /*
                 * La ligne doit appartenir à cet inventaire.
                 */
                if (!$lignesInventaire->has($ligneId)) {
                    $validator->errors()->add(
                        "comptages.{$index}.ligne_id",
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
            'comptages.required' => 'Au moins un comptage est obligatoire.',
            'comptages.array'    => 'Les comptages sont invalides.',
            'comptages.min'      => 'Au moins une ligne doit être comptée.',

            'comptages.*.ligne_id.required' => 'L’identifiant de ligne est obligatoire.',
            'comptages.*.ligne_id.integer'  => 'L’identifiant de ligne est invalide.',
            'comptages.*.ligne_id.exists'   => 'Une ligne référencée n’existe pas.',

            'comptages.*.quantite_comptee.required' => 'La quantité comptée est obligatoire.',
            'comptages.*.quantite_comptee.numeric'  => 'La quantité comptée doit être un nombre.',
            'comptages.*.quantite_comptee.min'      => 'La quantité comptée ne peut pas être négative.',

            'comptages.*.notes.max' => 'Les notes de ligne ne peuvent pas dépasser 5000 caractères.',
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
            'comptages' => 'comptages',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRÉPARATION DES DONNÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne les données prêtes pour
     * InventaireStockService::compterLot().
     *
     * Format attendu par le service :
     *     [ligne_id => quantite_comptee, ...]
     *
     * @return array<int, float>
     */
    public function validatedData(): array
    {
        $comptages = [];

        foreach ((array) $this->input('comptages', []) as $comptage) {

            if (!is_array($comptage)) {
                continue;
            }

            $ligneId = (int) ($comptage['ligne_id'] ?? 0);

            if ($ligneId <= 0) {
                continue;
            }

            $comptages[$ligneId] = $comptage['quantite_comptee'];
        }

        return $comptages;
    }

    /**
     * Retourne les notes par ligne, si nécessaire
     * pour un traitement complémentaire.
     *
     * @return array<int, string|null>
     */
    public function notesParLigne(): array
    {
        $notes = [];

        foreach ((array) $this->input('comptages', []) as $comptage) {

            if (!is_array($comptage)) {
                continue;
            }

            $ligneId = (int) ($comptage['ligne_id'] ?? 0);

            if ($ligneId <= 0) {
                continue;
            }

            $notes[$ligneId] = $comptage['notes'] ?? null;
        }

        return $notes;
    }

    /*
    |--------------------------------------------------------------------------
    | PRÉPARATION AVANT VALIDATION
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $comptages = $this->input('comptages');

        if (!is_array($comptages)) {
            return;
        }

        $comptages = array_map(function ($comptage) {
            if (!is_array($comptage)) {
                return $comptage;
            }

            if (isset($comptage['notes']) && is_string($comptage['notes'])) {
                $comptage['notes'] = trim($comptage['notes']);
            }

            if (isset($comptage['notes']) && $comptage['notes'] === '') {
                $comptage['notes'] = null;
            }

            return $comptage;
        }, $comptages);

        $this->merge(['comptages' => $comptages]);
    }
}