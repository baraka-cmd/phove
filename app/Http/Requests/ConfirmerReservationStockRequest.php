<?php

namespace App\Http\Requests;

use App\Models\ReservationStock;
use App\Models\Stock;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation de la confirmation d'une réservation de stock.
 *
 * SÉMANTIQUE :
 *
 *   La confirmation est l'étape charnière : c'est le moment
 *   où les quantités sont RÉELLEMENT bloquées dans le stock.
 *
 *   Avant : réservation = BROUILLON, aucune quantité bloquée.
 *   Après : réservation = CONFIRMEE, quantités réservées actives.
 *
 *   À partir de ce moment, les quantités réservées sont
 *   retirées de la disponibilité des articles concernés.
 *
 * CHAMPS OBLIGATOIRES :
 *
 *   - lignes                    : au moins une ligne
 *   - lignes.*.ligne_id         : ligne de la réservation
 *   - lignes.*.quantite_reservee : >= 0, <= quantite_demandee
 *
 * CHAMPS OPTIONNELS :
 *
 *   - commentaire               : note de confirmation
 *
 * AUTORISATION :
 *
 *   Déléguée à ReservationStockPolicy::confirm() sur la réservation.
 *
 * RÈGLES MÉTIER VÉRIFIÉES ICI :
 *
 *   - toutes les lignes de la réservation doivent être présentes ;
 *   - aucune ligne n'appartient à une autre réservation ;
 *   - quantite_reservee <= quantite_demandee ;
 *   - au moins une quantité réservée > 0.
 *
 *   La VÉRIFICATION DE DISPONIBILITÉ RÉELLE (stock physique,
 *   verrouillage transactionnel) est faite EXCLUSIVEMENT par
 *   ReservationStockService::confirmer(), dans la transaction.
 */
class ConfirmerReservationStockRequest extends FormRequest
{
    /*
    |--------------------------------------------------------------------------
    | AUTORISATION
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        /** @var ReservationStock $reservation */
        $reservation = $this->route('reservation');

        return $this->user()->can('confirm', $reservation);
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
             * Lignes à confirmer.
             */
            'lignes' => [
                'required',
                'array',
                'min:1',
            ],

            'lignes.*.ligne_id' => [
                'required',
                'integer',
                'exists:lignes_reservations_stocks,id',
            ],

            'lignes.*.quantite_reservee' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.999',
            ],

            /*
             * Commentaire de confirmation.
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

            /** @var ReservationStock $reservation */
            $reservation = $this->route('reservation');

            if (!$reservation) {
                return;
            }

            $lignesSaisies = $this->input('lignes', []);

            if (!is_array($lignesSaisies)) {
                return;
            }

            /*
             * Récupération des lignes réelles.
             */
            $lignesReservation = $reservation->lignes()
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

            $idsAttendus = $lignesReservation->keys()->all();

            $idsManquants = array_diff($idsAttendus, $idsSaisis);

            if (!empty($idsManquants)) {
                $validator->errors()->add(
                    'lignes',
                    sprintf(
                        'Toutes les lignes de la réservation doivent être confirmées (%d ligne(s) manquante(s)).',
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

                if (!$lignesReservation->has($ligneId)) {
                    $validator->errors()->add(
                        "lignes.{$index}.ligne_id",
                        'Cette ligne n’appartient pas à cette réservation.'
                    );
                    continue;
                }

                $ligneReelle = $lignesReservation->get($ligneId);

                $quantiteReservee = $ligneSaisie['quantite_reservee'] ?? null;

                if ($quantiteReservee === null) {
                    continue;
                }

                $quantiteDemandee = (float) $ligneReelle->quantite_demandee;

                /*
                 * Ne pas dépasser la quantité demandée.
                 */
                if ((float) $quantiteReservee > $quantiteDemandee) {
                    $validator->errors()->add(
                        "lignes.{$index}.quantite_reservee",
                        sprintf(
                            'La quantité réservée (%s) ne peut pas dépasser la quantité demandée (%s).',
                            $quantiteReservee,
                            $quantiteDemandee
                        )
                    );
                }
            }

            /*
             * Au moins une quantité > 0.
             */
            $totalReserve = collect($lignesSaisies)
                ->sum(fn ($l) => (float) ($l['quantite_reservee'] ?? 0));

            if ($totalReserve <= 0) {
                $validator->errors()->add(
                    'lignes',
                    'Au moins une ligne doit avoir une quantité réservée strictement positive.'
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
            'lignes.required' => 'Les lignes de confirmation sont obligatoires.',
            'lignes.array'    => 'Les lignes de confirmation sont invalides.',
            'lignes.min'      => 'Au moins une ligne doit être confirmée.',

            'lignes.*.ligne_id.required' => 'L’identifiant de ligne est obligatoire.',
            'lignes.*.ligne_id.integer'  => 'L’identifiant de ligne est invalide.',
            'lignes.*.ligne_id.exists'   => 'Une ligne référencée n’existe pas.',

            'lignes.*.quantite_reservee.required' => 'La quantité réservée est obligatoire.',
            'lignes.*.quantite_reservee.numeric'  => 'La quantité réservée doit être un nombre.',
            'lignes.*.quantite_reservee.min'      => 'La quantité réservée ne peut pas être négative.',

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
     * Retourne les données prêtes pour
     * ReservationStockService::confirmer().
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
                'quantite_reservee' => $ligne['quantite_reservee'],
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

        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}