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
        Schema::create('partenaires', function (Blueprint $table) {
            $table->id();
            
            // Informations sur le partenaire
            $table->string('nom');
            $table->enum('type', [
                'bailleur_fonds',
                'technique',
                'local',
                'international',
                'institutionnel'
            ]);
            
            // Coordonnées
            $table->string('email')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('adresse')->nullable();
            $table->string('site_web')->nullable();
            
            // Logo pour affichage sur le site public
            $table->string('logo')->nullable();
            
            // Description du partenariat
            $table->text('description')->nullable();
            
            // Statut de la collaboration
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            
            // Dates de collaboration
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            
            // Horodatage
            $table->timestamps();
            
            // Index
            $table->index('type');
            $table->index('statut');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('partenaires');
    }
};