<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table des salaires.
     *
     * Architecture :
     * - Un salaire représente le montant dû à un membre pour un mois.
     * - Les paiements réels seront enregistrés séparément dans
     *   la table paiements_salaires.
     * - Un salaire peut donc être payé en plusieurs fois.
     */
    public function up(): void
    {
        Schema::create('salaires', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | IDENTIFIANT
            |--------------------------------------------------------------------------
            */

            $table->id();


            /*
            |--------------------------------------------------------------------------
            | PERSONNEL CONCERNÉ
            |--------------------------------------------------------------------------
            */

            $table->foreignId('membre_id')
                ->constrained('membres')
                ->restrictOnDelete()
                ->comment(
                    'Membre concerné par le salaire. ' .
                    'La suppression du membre est bloquée si un historique salarial existe.'
                );


            /*
            |--------------------------------------------------------------------------
            | POSTE / ORGANE
            |--------------------------------------------------------------------------
            */

            $table->foreignId('organe_id')
                ->nullable()
                ->constrained('organes')
                ->nullOnDelete()
                ->comment(
                    'Organe ou poste occupé par le membre au moment de la paie.'
                );


            /*
            |--------------------------------------------------------------------------
            | PÉRIODE DE PAIE
            |--------------------------------------------------------------------------
            |
            | Format attendu : YYYY-MM
            | Exemple : 2026-09
            |
            | On conserve une chaîne de 7 caractères plutôt qu'une date complète,
            | car un salaire correspond à un mois comptable et non à un jour précis.
            |
            */

            $table->string('mois', 7)
                ->comment(
                    'Période salariale au format YYYY-MM.'
                );


            /*
            |--------------------------------------------------------------------------
            | COMPOSANTES DU SALAIRE
            |--------------------------------------------------------------------------
            */

            $table->decimal('salaire_base', 12, 2)
                ->default(0)
                ->comment(
                    'Salaire de base du membre pour la période.'
                );

            $table->decimal('primes', 12, 2)
                ->default(0)
                ->comment(
                    'Total des primes et avantages ajoutés au salaire.'
                );

            $table->decimal('retenues', 12, 2)
                ->default(0)
                ->comment(
                    'Total des retenues appliquées au salaire.'
                );

            $table->decimal('salaire_net', 12, 2)
                ->default(0)
                ->comment(
                    'Montant net dû au membre : salaire_base + primes - retenues.'
                );


            /*
            |--------------------------------------------------------------------------
            | ÉTAT DU SALAIRE
            |--------------------------------------------------------------------------
            |
            | brouillon
            |     Le salaire est en préparation.
            |
            | valide
            |     Le salaire a été approuvé et peut être payé.
            |
            | partiellement_paye
            |     Une partie du salaire a déjà été versée.
            |
            | paye
            |     Le salaire a été intégralement payé.
            |
            | annule
            |     Le salaire a été annulé et ne doit plus être payé.
            |
            */

            $table->enum('statut', [
                'brouillon',
                'valide',
                'partiellement_paye',
                'paye',
                'annule',
            ])
                ->default('brouillon')
                ->comment(
                    'État du salaire dans le cycle de traitement.'
                );


            /*
            |--------------------------------------------------------------------------
            | RÉFÉRENCE DU BULLETIN
            |--------------------------------------------------------------------------
            |
            | Exemple :
            | BUL-202609-0001
            |
            | Cette référence identifie le salaire lui-même.
            | Elle est différente des références de paiement.
            |
            */

            $table->string('reference', 50)
                ->unique()
                ->comment(
                    'Référence unique du bulletin salarial.'
                );


            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */

            $table->foreignId('valide_par')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment(
                    'Utilisateur ayant validé le salaire.'
                );

            $table->timestamp('valide_at')
                ->nullable()
                ->comment(
                    'Date et heure exactes de validation du salaire.'
                );


            /*
            |--------------------------------------------------------------------------
            | ANNULATION
            |--------------------------------------------------------------------------
            */

            $table->foreignId('annule_par')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment(
                    'Utilisateur ayant annulé le salaire.'
                );

            $table->timestamp('annule_at')
                ->nullable()
                ->comment(
                    'Date et heure exactes de l’annulation.'
                );

            $table->text('motif_annulation')
                ->nullable()
                ->comment(
                    'Justification obligatoire lors de l’annulation.'
                );


            /*
            |--------------------------------------------------------------------------
            | CRÉATION / RESPONSABILITÉ
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment(
                    'Utilisateur ayant créé le salaire.'
                );


            /*
            |--------------------------------------------------------------------------
            | NOTES
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
                ->nullable()
                ->comment(
                    'Observations ou informations complémentaires concernant le salaire.'
                );


            /*
            |--------------------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------------------
            */

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            |
            | Ces index améliorent les recherches utilisées par :
            | - la liste des salaires ;
            | - les filtres par mois ;
            | - les filtres par statut ;
            | - les recherches par membre ;
            | - les tableaux financiers.
            |
            */

            $table->index(
                ['mois', 'statut'],
                'salaires_mois_statut_index'
            );

            $table->index(
                ['membre_id', 'mois'],
                'salaires_membre_mois_index'
            );

            $table->index(
                ['organe_id', 'mois'],
                'salaires_organe_mois_index'
            );

            $table->index(
                'valide_par',
                'salaires_valide_par_index'
            );

            $table->index(
                'created_by',
                'salaires_created_by_index'
            );


            /*
            |--------------------------------------------------------------------------
            | CONTRAINTE MÉTIER
            |--------------------------------------------------------------------------
            |
            | Un membre ne peut avoir qu'un seul salaire pour un même mois.
            |
            | Exemple :
            |
            | Membre 15 + 2026-09 = UN SEUL salaire
            |
            | Cela évite les doublons accidentels.
            |
            */

            $table->unique(
                ['membre_id', 'mois'],
                'salaires_membre_mois_unique'
            );
        });
    }


    /**
     * Supprime la table des salaires.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaires');
    }
};