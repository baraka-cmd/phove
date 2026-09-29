<?php

namespace App\Http\Requests;

use App\Models\Salaire;
use Illuminate\Foundation\Http\FormRequest;

class StoreSalaireRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé
     * à créer un salaire.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->estActif()
            && $user->can('create', Salaire::class);
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        return [
            'membre_id' => [
                'required',
                'integer',
                'exists:membres,id',
            ],

            'organe_id' => [
                'required',
                'integer',
                'exists:organes,id',
            ],

            'mois' => [
                'required',
                'date_format:Y-m',
            ],

            'reference' => [
                'required',
                'string',
                'max:50',
            ],

            'salaire_base' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'primes' => [
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'retenues' => [
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [
            'membre_id.required' => 'Veuillez sélectionner un membre.',
            'membre_id.integer' => 'Le membre sélectionné est invalide.',
            'membre_id.exists' => 'Le membre sélectionné est introuvable.',

            'organe_id.required' => 'Veuillez sélectionner un organe.',
            'organe_id.integer' => 'L’organe sélectionné est invalide.',
            'organe_id.exists' => 'L’organe sélectionné est introuvable.',

            'mois.required' => 'Veuillez sélectionner le mois du salaire.',
            'mois.date_format' => 'Le mois doit être au format AAAA-MM.',

            'reference.required' => 'La référence du salaire est obligatoire.',
            'reference.string' => 'La référence du salaire doit être du texte.',
            'reference.max' => 'La référence ne peut pas dépasser 50 caractères.',

            'salaire_base.required' => 'Le salaire de base est obligatoire.',
            'salaire_base.numeric' => 'Le salaire de base doit être un nombre.',
            'salaire_base.min' => 'Le salaire de base ne peut pas être négatif.',
            'salaire_base.decimal' => 'Le salaire de base doit comporter au maximum deux décimales.',

            'primes.numeric' => 'Les primes doivent être un nombre.',
            'primes.min' => 'Les primes ne peuvent pas être négatives.',
            'primes.decimal' => 'Les primes doivent comporter au maximum deux décimales.',

            'retenues.numeric' => 'Les retenues doivent être un nombre.',
            'retenues.min' => 'Les retenues ne peuvent pas être négatives.',
            'retenues.decimal' => 'Les retenues doivent comporter au maximum deux décimales.',

            'notes.string' => 'Les notes doivent être du texte.',
            'notes.max' => 'Les notes ne peuvent pas dépasser 1000 caractères.',
        ];
    }

    /**
     * Prépare les données avant validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'reference' => $this->filled('reference')
                ? trim((string) $this->input('reference'))
                : null,

            'mois' => $this->filled('mois')
                ? trim((string) $this->input('mois'))
                : null,

            'primes' => $this->filled('primes')
                ? $this->normaliserMontant($this->input('primes'))
                : 0,

            'retenues' => $this->filled('retenues')
                ? $this->normaliserMontant($this->input('retenues'))
                : 0,

            'salaire_base' => $this->filled('salaire_base')
                ? $this->normaliserMontant($this->input('salaire_base'))
                : null,

            'notes' => $this->filled('notes')
                ? trim((string) $this->input('notes'))
                : null,
        ]);
    }

    /**
     * Retourne les données validées destinées au service.
     *
     * Le SalaireController utilise volontairement
     * cette méthode au lieu de validated().
     */
    public function validatedData(): array
    {
        return $this->validated();
    }

    /**
     * Normalise un montant.
     *
     * Formats acceptés :
     * 500
     * 500.50
     * 500,50
     * 1 500,50
     * 1.500,50
     * 1,500.50
     */
    private function normaliserMontant(mixed $montant): mixed
    {
        if ($montant === null || $montant === '') {
            return $montant;
        }

        if (is_numeric($montant)) {
            return $montant;
        }

        if (!is_string($montant)) {
            return $montant;
        }

        $montant = trim($montant);

        /*
         * Suppression des espaces classiques
         * et espaces insécables.
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
         * Présence simultanée de "." et ",".
         */
        if (
            str_contains($montant, ',')
            && str_contains($montant, '.')
        ) {
            /*
             * Format européen :
             * 1.500,50
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
             * 1,500.50
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
         * Format avec uniquement une virgule :
         * 500,50
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