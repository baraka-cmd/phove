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
        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            
            // Informations principales
            $table->string('titre');
            $table->string('slug')->unique();
            
            // Domaine d'intervention (les 9 domaines des statuts, article 9)
            $table->enum('domaine', [
                'sante_nutrition',
                'education',
                'protection',
                'wash',
                'securite_alimentaire',
                'communication',
                'environnement',
                'paix',
                'genre'
            ]);
            
            // Localisation
            $table->string('zone_geographique');
            $table->string('province')->nullable();
            
            // Période
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            
            // Budget
            $table->decimal('budget_total', 15, 2)->default(0.00);
            $table->decimal('budget_utilise', 15, 2)->default(0.00);
            
            // Financement (déclaration de provenance des ressources)
            $table->string('bailleur')->nullable();
            $table->string('source_financement')->nullable();
            
            // Statut
            $table->enum('statut', [
                'en_preparation',
                'en_cours',
                'termine',
                'suspendu'
            ])->default('en_preparation');
            
            // Contenu détaillé
            $table->text('description');
            $table->text('objectifs')->nullable();
            $table->text('resultats_attendus')->nullable();
            
            // Image principale
            $table->string('image_principale')->nullable();
            
            // Visibilité sur le site public
            $table->boolean('publier')->default(true);
            
            // Horodatage
            $table->timestamps();
            
            // Index pour les filtres et recherches
            $table->index('domaine');
            $table->index('statut');
            $table->index('zone_geographique');
            $table->index('date_debut');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};