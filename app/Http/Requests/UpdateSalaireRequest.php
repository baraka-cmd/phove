<?php

namespace App\Http\Requests;

use App\Models\Salaire;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSalaireRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur peut effectuer la requête.
     *
     * L'autorisation est également vérifiée dans SalairePolicy
     * et dans SalaireService.
     */
    public function authorize(): bool
    {
        $salaire = $this->route('salaire');

        if (!$salaire instanceof Salaire) {
            return false;
        }

        return auth()->check()
            && auth()->user()->estActif()
            && auth()->user()->can('update', $salaire);
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        return [
            /*
             * Le membre peut être changé uniquement tant que
             * le salaire est encore un brouillon.
             */
            'membre_id' => [
                'required',
                'integer',
                'exists:membres,id',
            ],

            /*
             * Organe/poste facultatif.
             */
            'organe_id' => [
                'nullable',
                'integer',
                'exists:organes,id',
            ],

            /*
             * Période salariale.
             */
            'mois' => [
                'required',
                'date_format:Y-m',
            ],

            /*
             * Salaire de base.
             */
            'salaire_base' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            /*
             * Primes.
             */
            'primes' => [
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            /*
             * Retenues.
             */
            'retenues' => [
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            /*
             * Ces champs sont volontairement interdits.
             *
             * Ils ne doivent jamais être modifiés depuis
             * le formulaire d'édition.
             */
            'salaire_net' => [
                'prohibited',
            ],

            'statut' => [
                'prohibited',
            ],

            'reference' => [
                'prohibited',
            ],

            'mode_paiement' => [
                'prohibited',
            ],

            'date_paiement' => [
                'prohibited',
            ],

            'paye_par' => [
                'prohibited',
            ],

            'valide_par' => [
                'prohibited',
            ],

            'valide_at' => [
                'prohibited',
            ],

            'annule_par' => [
                'prohibited',
            ],

            'annule_at' => [
                'prohibited',
            ],

            'motif_annulation' => [
                'prohibited',
            ],

            /*
             * Notes.
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
            'membre_id.required' => 'Veuillez sélectionner un membre.',
            'membre_id.integer' => 'Le membre sélectionné est invalide.',
            'membre_id.exists' => 'Le membre sélectionné n’existe pas.',

            'organe_id.integer' => 'L’organe sélectionné est invalide.',
            'organe_id.exists' => 'L’organe sélectionné n’existe pas.',

            'mois.required' => 'La période salariale est obligatoire.',
            'mois.date_format' => 'La période doit être au format AAAA-MM.',

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
            'notes.max' => 'Les notes ne peuvent pas dépasser 5000 caractères.',

            'salaire_net.prohibited' => 'Le salaire net est calculé automatiquement.',
            'statut.prohibited' => 'Le statut est géré automatiquement.',
            'reference.prohibited' => 'La référence ne peut pas être modifiée.',
            'mode_paiement.prohibited' => 'Le mode de paiement est géré séparément.',
            'date_paiement.prohibited' => 'La date de paiement est gérée séparément.',
            'paye_par.prohibited' => 'Le responsable du paiement est géré automatiquement.',
            'valide_par.prohibited' => 'La validation est gérée séparément.',
            'valide_at.prohibited' => 'La date de validation est gérée automatiquement.',
            'annule_par.prohibited' => 'L’annulation est gérée séparément.',
            'annule_at.prohibited' => 'La date d’annulation est gérée automatiquement.',
            'motif_annulation.prohibited' => 'Le motif d’annulation est géré séparément.',
        ];
    }

    /**
     * Prépare les données avant validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'organe_id' => $this->filled('organe_id')
                ? (int) $this->input('organe_id')
                : null,

            'membre_id' => $this->filled('membre_id')
                ? (int) $this->input('membre_id')
                : null,

            'salaire_base' => $this->normaliserMontant(
                $this->input('salaire_base')
            ),

            'primes' => $this->normaliserMontant(
                $this->input('primes', 0)
            ),

            'retenues' => $this->normaliserMontant(
                $this->input('retenues', 0)
            ),
        ]);
    }

    /**
     * Vérifications supplémentaires après la validation
     * des règles standards.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $salaire = $this->route('salaire');

            if (!$salaire instanceof Salaire) {
                return;
            }

            /*
             * Protection supplémentaire :
             * même si quelqu'un tente de contourner l'interface,
             * une requête de modification ne doit concerner
             * qu'un brouillon.
             */
            if (!$salaire->estBrouillon()) {
                $validator->errors()->add(
                    'salaire',
                    'Ce salaire ne peut plus être modifié car il n’est plus à l’état de brouillon.'
                );
            }

            /*
             * Protection contre une incohérence éventuelle
             * de l'état du salaire.
             */
            if ($salaire->paiements()->exists()) {
                $validator->errors()->add(
                    'salaire',
                    'Ce salaire possède déjà un historique de paiement et ne peut plus être modifié.'
                );
            }

            /*
             * Vérification du montant net calculé.
             *
             * Le net ne doit jamais être négatif.
             */
            $base = (float) ($this->input('salaire_base') ?? 0);
            $primes = (float) ($this->input('primes') ?? 0);
            $retenues = (float) ($this->input('retenues') ?? 0);

            $net = round($base + $primes - $retenues, 2);

            if ($net < 0) {
                $validator->errors()->add(
                    'retenues',
                    'Le total des retenues ne peut pas être supérieur au salaire brut.'
                );
            }
        });
    }

    /**
     * Données propres à transmettre au SalaireService.
     *
     * Le Service reste responsable des règles métier
     * et du recalcul final du salaire net.
     */
    public function validatedData(): array
    {
        $data = $this->validated();

        unset(
            $data['salaire_net'],
            $data['statut'],
            $data['reference'],
            $data['mode_paiement'],
            $data['date_paiement'],
            $data['paye_par'],
            $data['valide_par'],
            $data['valide_at'],
            $data['annule_par'],
            $data['annule_at'],
            $data['motif_annulation']
        );

        $data['primes'] = $data['primes'] ?? 0;
        $data['retenues'] = $data['retenues'] ?? 0;

        return $data;
    }

    /**
     * Normalise les montants provenant du formulaire.
     */
    private function normaliserMontant(mixed $montant): mixed
    {
        if ($montant === null || $montant === '') {
            return 0;
        }

        if (is_numeric($montant)) {
            return $montant;
        }

        if (!is_string($montant)) {
            return $montant;
        }

        $montant = trim($montant);
        $montant = str_replace(["\xc2\xa0", ' '], '', $montant);

        if (str_contains($montant, ',') && str_contains($montant, '.')) {
            if (strrpos($montant, ',') > strrpos($montant, '.')) {
                $montant = str_replace('.', '', $montant);
                $montant = str_replace(',', '.', $montant);
            } else {
                $montant = str_replace(',', '', $montant);
            }
        } elseif (str_contains($montant, ',')) {
            $montant = str_replace(',', '.', $montant);
        }

        return $montant;
    }
}