<?php

namespace App\Http\Requests;

use App\Models\PaiementSalaire;
use App\Models\Salaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaiementSalaireRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé
     * à enregistrer un paiement.
     */
    public function authorize(): bool
    {
        $salaire = $this->route('salaire');

        if (!$salaire instanceof Salaire) {
            return false;
        }

        $user = $this->user();

        return $user !== null
            && $user->estActif()
            && $user->can('pay', $salaire);
    }

    /**
     * Règles de validation du paiement.
     */
    public function rules(): array
    {
        return [
            /*
             * Montant effectivement payé.
             *
             * Le montant doit être strictement positif.
             *
             * Le contrôle du reste à payer est également
             * effectué dans PaiementSalaireService sous
             * transaction avec verrouillage.
             */
            'montant' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
            ],

            /*
             * Date réelle du paiement.
             */
            'date_paiement' => [
                'required',
                'date',
                'date_format:Y-m-d',
            ],

            /*
             * Mode de paiement.
             *
             * La liste provient directement du modèle.
             */
            'mode_paiement' => [
                'required',
                'string',
                'max:30',
                Rule::in(PaiementSalaire::MODES_PAIEMENT),
            ],

            /*
             * La référence est générée côté serveur.
             *
             * Elle ne doit jamais être acceptée depuis
             * le formulaire utilisateur.
             */
            'reference' => [
                'prohibited',
            ],

            /*
             * Le responsable du paiement est déterminé
             * par l'utilisateur authentifié.
             */
            'paye_par' => [
                'prohibited',
            ],

            /*
             * Notes facultatives.
             */
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [
            /*
             * Montant
             */
            'montant.required' =>
                'Le montant du paiement est obligatoire.',

            'montant.numeric' =>
                'Le montant du paiement doit être un nombre.',

            'montant.gt' =>
                'Le montant du paiement doit être supérieur à zéro.',

            'montant.decimal' =>
                'Le montant doit comporter au maximum deux décimales.',

            /*
             * Date
             */
            'date_paiement.required' =>
                'La date du paiement est obligatoire.',

            'date_paiement.date' =>
                'La date du paiement est invalide.',

            'date_paiement.date_format' =>
                'La date doit être au format AAAA-MM-JJ.',

            /*
             * Mode de paiement
             */
            'mode_paiement.required' =>
                'Veuillez sélectionner un mode de paiement.',

            'mode_paiement.string' =>
                'Le mode de paiement est invalide.',

            'mode_paiement.max' =>
                'Le mode de paiement est trop long.',

            'mode_paiement.in' =>
                'Le mode de paiement sélectionné n’est pas autorisé.',

            /*
             * Champs protégés
             */
            'reference.prohibited' =>
                'La référence du paiement est générée automatiquement.',

            'paye_par.prohibited' =>
                'Le responsable du paiement est déterminé automatiquement.',

            /*
             * Notes
             */
            'notes.string' =>
                'Les notes doivent être du texte.',

            'notes.max' =>
                'Les notes ne peuvent pas dépasser 5000 caractères.',
        ];
    }

    /**
     * Prépare les données avant validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            /*
             * Normalisation du montant.
             */
            'montant' => $this->normaliserMontant(
                $this->input('montant')
            ),

            /*
             * Nettoyage du mode de paiement.
             */
            'mode_paiement' => $this->filled('mode_paiement')
                ? trim((string) $this->input('mode_paiement'))
                : null,

            /*
             * Nettoyage de la date.
             */
            'date_paiement' => $this->filled('date_paiement')
                ? trim((string) $this->input('date_paiement'))
                : null,

            /*
             * Nettoyage des notes.
             */
            'notes' => $this->filled('notes')
                ? trim((string) $this->input('notes'))
                : null,
        ]);
    }

    /**
     * Vérifications métier supplémentaires.
     *
     * Ces vérifications améliorent l'expérience utilisateur,
     * mais la vérification définitive doit rester dans
     * PaiementSalaireService sous transaction SQL.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $salaire = $this->route('salaire');

            if (!$salaire instanceof Salaire) {
                return;
            }

            /*
             * Un salaire annulé ne peut jamais recevoir
             * un nouveau paiement.
             */
            if ($salaire->estAnnule()) {
                $validator->errors()->add(
                    'salaire',
                    'Un salaire annulé ne peut pas recevoir de paiement.'
                );

                return;
            }

            /*
             * Le paiement doit concerner un salaire validé
             * ou partiellement payé.
             */
            if (
                !$salaire->estValide()
                && !$salaire->estPartiellementPaye()
            ) {
                $validator->errors()->add(
                    'salaire',
                    'Le salaire doit être validé avant d’enregistrer un paiement.'
                );

                return;
            }

            /*
             * Un salaire entièrement payé ne peut plus
             * recevoir de paiement.
             */
            if ($salaire->estPaye()) {
                $validator->errors()->add(
                    'montant',
                    'Ce salaire est déjà entièrement payé.'
                );

                return;
            }

            /*
             * Reste actuellement disponible.
             */
            $reste = $salaire->resteAPayer();

            if ($reste <= 0) {
                $validator->errors()->add(
                    'montant',
                    'Il ne reste aucun montant à payer pour ce salaire.'
                );

                return;
            }

            /*
             * Montant demandé.
             */
            $montant = (float) ($this->input('montant') ?? 0);

            /*
             * Le paiement ne peut pas dépasser
             * le reste à payer.
             */
            if ($montant > $reste) {
                $validator->errors()->add(
                    'montant',
                    sprintf(
                        'Le montant ne peut pas dépasser le reste à payer de %s.',
                        number_format(
                            $reste,
                            2,
                            ',',
                            ' '
                        ) . ' $'
                    )
                );
            }
        });
    }

    /**
     * Données propres à transmettre au service.
     *
     * La référence et paye_par sont volontairement
     * absents : ils sont générés/déterminés côté serveur.
     */
    public function validatedData(): array
    {
        $data = $this->validated();

        unset(
            $data['reference'],
            $data['paye_par']
        );

        return $data;
    }

    /**
     * Normalise les montants provenant du formulaire.
     *
     * Formats acceptés :
     *
     * 500
     * 500.50
     * 500,50
     * 1 500,50
     * 1.500,50
     * 1,500.50
     */
    private function normaliserMontant(mixed $montant): mixed
    {
        /*
         * Valeur absente.
         */
        if ($montant === null || $montant === '') {
            return $montant;
        }

        /*
         * Nombre déjà valide.
         */
        if (is_numeric($montant)) {
            return $montant;
        }

        /*
         * Type inattendu.
         */
        if (!is_string($montant)) {
            return $montant;
        }

        /*
         * Nettoyage.
         */
        $montant = trim($montant);

        /*
         * Suppression des espaces classiques
         * et insécables.
         */
        $montant = str_replace(
            [
                "\xc2\xa0",
                ' ',
            ],
            '',
            $montant
        );

        /*
         * Présence simultanée de "," et ".".
         */
        if (
            str_contains($montant, ',')
            && str_contains($montant, '.')
        ) {
            /*
             * Format européen :
             *
             * 1.500,50
             *
             * devient :
             *
             * 1500.50
             */
            if (
                strrpos($montant, ',')
                >
                strrpos($montant, '.')
            ) {
                $montant = str_replace(
                    '.',
                    '',
                    $montant
                );

                $montant = str_replace(
                    ',',
                    '.',
                    $montant
                );
            }

            /*
             * Format anglo-saxon :
             *
             * 1,500.50
             *
             * devient :
             *
             * 1500.50
             */
            else {
                $montant = str_replace(
                    ',',
                    '',
                    $montant
                );
            }
        }

        /*
         * Uniquement une virgule :
         *
         * 500,50
         *
         * devient :
         *
         * 500.50
         */
        elseif (str_contains($montant, ',')) {
            $montant = str_replace(
                ',',
                '.',
                $montant
            );
        }

        return $montant;
    }
}