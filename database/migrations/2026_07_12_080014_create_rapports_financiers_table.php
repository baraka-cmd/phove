<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration (créer la table).
     */
    public function up(): void
    {
        Schema::create('rapports_financiers', function (Blueprint $table) {
            $table->id();
            
            // Titre du rapport
            $table->string('titre');
            
            // Type de rapport
            $table->enum('type', [
                'mensuel',
                'trimestriel',
                'annuel',
                'projet',
                'audit'
            ]);
            
            // Période couverte
            $table->string('periode');  // Format : YYYY-MM pour mensuel, YYYY pour annuel
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            
            // Totaux financiers
            $table->decimal('total_entrees', 15, 2)->default(0.00);
            $table->decimal('total_sorties', 15, 2)->default(0.00);
            $table->decimal('solde', 15, 2)->default(0.00);
            
            // Détail des entrées
            $table->decimal('dons_recus', 15, 2)->default(0.00);
            $table->decimal('cotisations_recues', 15, 2)->default(0.00);
            $table->decimal('subventions_recues', 15, 2)->default(0.00);
            $table->decimal('autres_entrees', 15, 2)->default(0.00);
            
            // Détail des sorties
            $table->decimal('depenses_projets', 15, 2)->default(0.00);
            $table->decimal('salaires', 15, 2)->default(0.00);
            $table->decimal('logistique', 15, 2)->default(0.00);
            $table->decimal('administratif', 15, 2)->default(0.00);
            $table->decimal('autres_sorties', 15, 2)->default(0.00);
            
            // Fichier PDF du rapport
            $table->string('fichier')->nullable();
            
            // Contenu textuel
            $table->text('commentaire')->nullable();
            
            // Visibilité
            $table->boolean('public')->default(false);
            
            // Statut
            $table->enum('statut', [
                'brouillon',
                'finalise',
                'publie',
                'archive'
            ])->default('brouillon');
            
            // Validation
            $table->foreignId('prepare_par')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            $table->date('date_validation')->nullable();
            
            // Horodatage
            $table->timestamps();
            
            // Index
            $table->index('type');
            $table->index('periode');
            $table->index('statut');
            $table->index('public');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapports_financiers');
    }
};