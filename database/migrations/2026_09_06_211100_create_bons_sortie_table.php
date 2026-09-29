<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bons de sortie de stock.
     *
     * Un bon de sortie est le document métier qui porte le
     * workflow de sortie physique :
     *
     *   brouillon → soumis → valide → en_preparation → sorti
     *                                                    │
     *                                                    └→ annule (possible
     *                                                       depuis brouillon,
     *                                                       soumis, valide)
     *
     * IMPORTANT :
     * Le bon de sortie ne modifie JAMAIS directement les quantités.
     * C'est SortieStockService::effectuer() qui, à l'état
     * `en_preparation`, génère les mouvements de sortie et
     * décrémente les lots — sous transaction et verrouillage.
     *
     * La table porte :
     * - les acteurs du workflow (demandeur, validateur, préparateur) ;
     * - les dates des étapes ;
     * - l'état courant (statut) ;
     * - le contexte métier (destinataire, projet, motif, référence).
     */
    public function up(): void
    {
        Schema::create('bons_sortie', function (Blueprint $table) {

            $table->id();

            /*
             * Numéro métier unique du bon.
             *
             * Généré côté service au format :
             * BS-YYYYMMDD-XXXXXX
             */
            $table->string('numero', 50)
                ->unique()
                ->comment('Numéro unique du bon de sortie');

            /*
             * Référence documentaire libre fournie par le demandeur.
             *
             * Exemples :
             * REF-2026-0045
             * NOTE-SERVICE-12
             * DEMANDE-MAGASIN-08
             *
             * Nullable : un bon peut être créé sans référence externe.
             */
            $table->string('reference', 100)
                ->nullable()
                ->comment('Référence documentaire libre du bon');

            /*
             * -----------------------------------------------------------------
             * DATES DU WORKFLOW
             * -----------------------------------------------------------------
             */

            $table->date('date_demande')
                ->comment('Date de création de la demande');

            $table->date('date_validation')
                ->nullable()
                ->comment('Date de validation du bon');

            $table->date('date_sortie')
                ->nullable()
                ->comment('Date de sortie physique réelle');

            /*
             * -----------------------------------------------------------------
             * ACTEURS
             * -----------------------------------------------------------------
             *
             * Tous les FK sur `users` sont en restrictOnDelete :
             * on ne supprime JAMAIS un utilisateur qui a participé
             * à un workflow documentaire (traçabilité).
             */

            $table->foreignId('demandeur_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('Utilisateur ayant demandé la sortie');

            $table->foreignId('validateur_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('Utilisateur ayant validé le bon');

            $table->foreignId('preparateur_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('Utilisateur ayant préparé la sortie');

            /*
             * -----------------------------------------------------------------
             * CONTEXTE MÉTIER
             * -----------------------------------------------------------------
             */

            $table->string('destinataire', 255)
                ->nullable()
                ->comment('Bénéficiaire ou destinataire de la sortie');

            $table->foreignId('projet_id')
                ->nullable()
                ->constrained('projets')
                ->nullOnDelete()
                ->comment('Projet bénéficiaire de la sortie');

            $table->string('motif', 500)
                ->nullable()
                ->comment('Motif général de la sortie');

            /*
             * -----------------------------------------------------------------
             * WORKFLOW
             * -----------------------------------------------------------------
             */

            $table->enum('statut', [
                'brouillon',
                'soumis',
                'valide',
                'en_preparation',
                'sorti',
                'annule',
            ])
                ->default('brouillon')
                ->comment('État du workflow du bon de sortie');

            $table->text('notes')
                ->nullable()
                ->comment('Informations complémentaires');

            $table->timestamps();

            /*
             * -----------------------------------------------------------------
             * INDEX
             * -----------------------------------------------------------------
             *
             * Les index FK (demandeur_id, validateur_id, preparateur_id,
             * projet_id) sont créés automatiquement par les contraintes.
             */

            $table->index('date_demande');
            $table->index('date_validation');
            $table->index('date_sortie');
            $table->index('statut');

            $table->index(
                ['statut', 'date_demande'],
                'bons_sortie_statut_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bons_sortie');
    }
};