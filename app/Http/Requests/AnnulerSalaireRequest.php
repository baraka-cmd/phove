<?php

namespace App\Http\Requests;

use App\Models\Salaire;
use Illuminate\Foundation\Http\FormRequest;

class AnnulerSalaireRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé
     * à demander l'annulation du salaire.
     */
    public function authorize(): bool
    {
        $salaire = $this->route('salaire');

        if (!$salaire instanceof Salaire) {
            return false;
        }

        return auth()->check()
            && auth()->user()->estActif()
            && auth()->user()->can('cancel', $salaire);
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        return [
            /*
             * Le motif est obligatoire.
             *
             * Il constitue une information d'audit.
             */
            'motif_annulation' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [
            'motif_annulation.required' =>
                'Le motif d’annulation est obligatoire.',

            'motif_annulation.string' =>
                'Le motif d’annulation doit être du texte.',

            'motif_annulation.min' =>
                'Le motif d’annulation doit contenir au moins 10 caractères.',

            'motif_annulation.max' =>
                'Le motif d’annulation ne peut pas dépasser 2000 caractères.',
        ];
    }

    /**
     * Nettoyage et préparation des données.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'motif_annulation' => $this->filled('motif_annulation')
                ? trim((string) $this->input('motif_annulation'))
                : null,
        ]);
    }

    /**
     * Vérifications métier supplémentaires.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $salaire = $this->route('salaire');

            if (!$salaire instanceof Salaire) {
                return;
            }

            /*
             * Un salaire déjà annulé ne peut pas être
             * annulé une deuxième fois.
             */
            if ($salaire->estAnnule()) {
                $validator->errors()->add(
                    'salaire',
                    'Ce salaire est déjà annulé.'
                );

                return;
            }

            /*
             * Vérification de l'état métier.
             */
            if (!$salaire->peutEtreAnnule()) {
                $validator->errors()->add(
                    'salaire',
                    'Ce salaire ne peut pas être annulé dans son état actuel.'
                );
            }

            /*
             * Un salaire ayant déjà reçu un paiement ne doit
             * pas être annulé par la procédure simple.
             *
             * Dans une architecture financière complète,
             * une procédure spécifique de correction/annulation
             * des paiements devra être mise en place.
             */
            if ($salaire->paiements()->exists()) {
                $validator->errors()->add(
                    'salaire',
                    'Ce salaire possède déjà un ou plusieurs paiements. Une procédure financière de correction est nécessaire avant toute annulation.'
                );
            }
        });
    }

    /**
     * Données prêtes à transmettre au Service.
     */
    public function validatedData(): array
    {
        return [
            'motif_annulation' => $this->validated('motif_annulation'),
        ];
    }
}
