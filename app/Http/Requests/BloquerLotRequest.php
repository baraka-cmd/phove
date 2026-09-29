<?php

namespace App\Http\Requests;

use App\Models\LotStock;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation du blocage d'un lot de stock.
 *
 * SÉMANTIQUE :
 *
 *   Un lot bloqué reste physiquement présent, mais n'est
 *   plus sélectionnable pour une sortie normale.
 *
 *   Le blocage est une DÉCISION MANUELLE :
 *     - suspicion de qualité ;
 *     - contrôle en cours ;
 *     - décision administrative ;
 *     - litige fournisseur.
 *
 *   Un lot bloqué ne peut être débloqué que par une action
 *   explicite (LotStockService::debloquer()).
 *
 * CHAMPS OPTIONNELS :
 *
 *   - motif : raison du blocage (recommandé mais non obligatoire)
 *
 * AUTORISATION :
 *
 *   Déléguée à LotStockPolicy::block() sur le lot.
 *
 * RÈGLES MÉTIER VÉRIFIÉES ICI :
 *
 *   - le lot doit être dans un état permettant le blocage
 *     (ACTIF).
 *
 *   La règle métier complète est vérifiée par
 *   LotStockService::bloquer(), qui réalise le verrouillage
 *   transactionnel et le contrôle de cohérence final.
 */
class BloquerLotRequest extends FormRequest
{
    /*
    |--------------------------------------------------------------------------
    | AUTORISATION
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        /** @var LotStock $lot */
        $lot = $this->route('lot');

        return $this->user()->can('block', $lot);
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
             * Motif du blocage.
             *
             * Recommandé pour la traçabilité mais non obligatoire.
             * Le motif est concaténé ou stocké selon la stratégie
             * retenue (notes, journal d'audit, ou table dédiée
             * ultérieurement).
             */
            'motif' => [
                'nullable',
                'string',
                'max:500',
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
            'motif.max' => 'Le motif ne peut pas dépasser 500 caractères.',
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
            'motif' => 'motif',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRÉPARATION DES DONNÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne les données prêtes pour
     * LotStockService::bloquer().
     *
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        return [
            'motif' => $this->input('motif') !== null
                ? trim((string) $this->input('motif'))
                : null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRÉPARATION AVANT VALIDATION
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('motif'))) {
            $motif = trim($this->input('motif'));

            $this->merge([
                'motif' => $motif === '' ? null : $motif,
            ]);
        }
    }
}