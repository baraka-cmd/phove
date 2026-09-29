<?php

namespace App\Http\Requests;

use App\Models\Salaire;
use Illuminate\Foundation\Http\FormRequest;

class ValiderSalaireRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé
     * à effectuer la validation.
     */
    public function authorize(): bool
    {
        $salaire = $this->route('salaire');

        if (!$salaire instanceof Salaire) {
            return false;
        }

        return auth()->check()
            && auth()->user()->estActif()
            && auth()->user()->can('validate', $salaire);
    }

    /**
     * Aucune donnée utilisateur n'est nécessaire
     * pour valider un salaire.
     *
     * La validation est une action métier.
     */
    public function rules(): array
    {
        return [
            /*
             * Le motif est facultatif ici.
             *
             * La validation ne nécessite pas de motif.
             */
            'notes' => [
                'sometimes',
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    /**
     * Vérifications métier complémentaires.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $salaire = $this->route('salaire');

            if (!$salaire instanceof Salaire) {
                return;
            }

            /*
             * Un salaire doit obligatoirement être un brouillon
             * avant de pouvoir être validé.
             */
            if (!$salaire->estBrouillon()) {
                $validator->errors()->add(
                    'salaire',
                    'Seul un salaire en brouillon peut être validé.'
                );
            }

            /*
             * Vérification du montant net.
             */
            $net = (float) $salaire->salaire_net;

            if ($net < 0) {
                $validator->errors()->add(
                    'salaire_net',
                    'Le salaire net ne peut pas être négatif.'
                );
            }

            /*
             * Un salaire sans montant dû n'est normalement
             * pas utile à valider.
             */
            if ($net <= 0) {
                $validator->errors()->add(
                    'salaire_net',
                    'Le salaire net doit être supérieur à zéro pour pouvoir être validé.'
                );
            }

            /*
             * Un salaire possédant déjà un paiement ne peut
             * pas revenir dans le processus de validation.
             */
            if ($salaire->paiements()->exists()) {
                $validator->errors()->add(
                    'salaire',
                    'Ce salaire possède déjà un paiement et ne peut plus être validé comme brouillon.'
                );
            }
        });
    }

    /**
     * Retourne les données utiles à transmettre au Service.
     */
    public function validatedData(): array
    {
        return $this->validated();
    }
}
