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
        Schema::create('indicateurs_projet', function (Blueprint $table) {
            $table->id();
            
            // Lien vers le projet
            $table->foreignId('projet_id')
                  ->constrained('projets')
                  ->onDelete('cascade');
            
            // Nom de l'indicateur
            $table->string('nom_indicateur');
            
            // Valeurs cible et actuelle
            $table->integer('valeur_cible')->default(0);
            $table->integer('valeur_actuelle')->default(0);
            
            // Unité de mesure
            $table->string('unite');  // personnes, ménages, tonnes, litres, points d'eau, repas, etc.
            
            // Description facultative
            $table->text('description')->nullable();
            
            // Date de dernière mise à jour
            $table->date('date_mise_a_jour')->nullable();
            
            // Horodatage
            $table->timestamps();
            
            // Index
            $table->index('projet_id');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicateurs_projet');
    }
};