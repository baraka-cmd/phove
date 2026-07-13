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
        Schema::create('organes', function (Blueprint $table) {
            $table->id();
            
            // Lien vers le membre
            $table->foreignId('membre_id')
                  ->constrained('membres')
                  ->onDelete('cascade');
            
            // Quel organe (article 22 des statuts)
            $table->enum('organe', [
                'assemblee_generale',
                'conseil_administration',
                'direction_executive',
                'commission_controle'
            ]);
            
            // Poste occupé
            $table->string('poste');
            
            // Mandat (article 27 : 3 ans renouvelable une fois)
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->boolean('actif')->default(true);
            
            // Ordre d'affichage dans l'organigramme
            $table->integer('ordre')->default(0);
            
            // Horodatage
            $table->timestamps();
            
            // Index
            $table->index('organe');
            $table->index('actif');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('organes');
    }
};