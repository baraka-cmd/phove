<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Inventaires de stock.
     *
     * Un inventaire est une opération de comptage physique
     * qui compare :
     *
     *   - la quantité THÉORIQUE (calculée depuis les lots) ;
     *   - la quantité PHYSIQUE (comptée sur le terrain).
     *
     * Ce qui produit un ÉCART, ensuite transformé en mouvement
     * d'ajustement par InventaireStockService::valider().
     *
     * IMPORTANT :
     *
     * Cette table NE contient PAS les lignes de comptage.
     * Les quantités comptées par article vivent dans
     * `inventaire_stock_lignes`.
     *
     * Cette table porte uniquement :
     *   - l'identité de l'inventaire ;
     *   - le périmètre (emplacement, statut) ;
     *   - les métadonnées du workflow (dates, acteurs, statut) ;
     *   - le verrou temporel (date de capture théorique).
     *
     * Workflow :
     *
     *   brouillon  → en_cours → termine → valide
     *                                    │
     *                                    └→ annule (possible
     *                                       depuis brouillon
     *                                       ou en_cours)
     */
    public function up(): void
    {
        Schema::create('inventaires_stocks', function (Blueprint $table) {

            $table->id();

            /*
             * Numéro métier unique.
             *
             * Format généré côté service :
             * INV-YYYY-NNNNNN
             */
            $table->string('numero', 50)
                ->unique()
                ->comment('Numéro unique de l’inventaire');

            /*
             * -----------------------------------------------------------------
             * PÉRIMÈTRE
             * -----------------------------------------------------------------
             *
             * Un inventaire peut cibler :
             *   - un emplacement précis (magasin, dépôt) ;
             *   - tous les emplacements (NULL).
             */
            $table->foreignId('emplacement_id')
                ->nullable()
                ->constrained('emplacements_stocks')
                ->restrictOnDelete()
                ->comment('Emplacement concerné (NULL = tous)');

            /*
             * -----------------------------------------------------------------
             * DATES DU WORKFLOW
             * -----------------------------------------------------------------
             */

            /*
             * Date officielle de début de l'inventaire.
             */
            $table->date('date_debut')
                ->comment('Date de début de l’inventaire');

            /*
             * Date de clôture du comptage.
             *
             * NULL tant que l'inventaire n'est pas passé à `termine`.
             */
            $table->date('date_cloture')
                ->nullable()
                ->comment('Date de clôture du comptage');

            /*
             * -----------------------------------------------------------------
             * ACTEURS
             * -----------------------------------------------------------------
             */

            /*
             * Utilisateur responsable de l'inventaire.
             *
             * restrictOnDelete : un utilisateur qui a porté
             * un inventaire ne doit jamais être supprimé.
             */
            $table->foreignId('responsable_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('Utilisateur responsable de l’inventaire');

            /*
             * Utilisateur qui a validé l'inventaire.
             *
             * NULL tant que l'inventaire n'est pas `valide`.
             */
            $table->foreignId('valide_par')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('Utilisateur ayant validé l’inventaire');

            /*
             * -----------------------------------------------------------------
             * WORKFLOW
             * -----------------------------------------------------------------
             */

            $table->enum('statut', [
                'brouillon',
                'en_cours',
                'termine',
                'valide',
                'annule',
            ])
                ->default('brouillon')
                ->comment('État du workflow de l’inventaire');

            /*
             * Date et heure de validation.
             */
            $table->timestamp('valide_at')
                ->nullable()
                ->comment('Date et heure de validation');

            /*
             * -----------------------------------------------------------------
             * NOTES
             * -----------------------------------------------------------------
             */

            $table->text('notes')
                ->nullable()
                ->comment('Informations complémentaires');

            $table->timestamps();

            /*
             * -----------------------------------------------------------------
             * INDEX
             * -----------------------------------------------------------------
             *
             * Les index FK emplacement_id, responsable_id, valide_par
             * sont créés automatiquement par les contraintes.
             */

            $table->index('date_debut');
            $table->index('date_cloture');
            $table->index('statut');

            /*
             * Index composé pour la requête la plus fréquente :
             *
             *   WHERE statut = ?
             *   ORDER BY date_debut DESC, id DESC
             */
            $table->index(
                ['statut', 'date_debut'],
                'inventaires_stocks_statut_date_index'
            );

            /*
             * Index composé pour trouver l'inventaire le plus
             * récent d'un emplacement donné.
             */
            $table->index(
                ['emplacement_id', 'date_debut'],
                'inventaires_stocks_emplacement_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaires_stocks');
    }
};