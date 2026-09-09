<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration (ajouter structure_id et niveau à organes).
     */
    public function up(): void
    {
        Schema::table('organes', function (Blueprint $table) {
            // Lien vers la structure territoriale
            $table->foreignId('structure_id')
                  ->nullable()
                  ->after('membre_id')
                  ->constrained('structures_territoriales')
                  ->onDelete('set null')
                  ->comment('Structure territoriale associée à ce poste');

            // Niveau hiérarchique du poste
            $table->enum('niveau', ['international', 'national', 'provincial', 'local'])
                  ->nullable()
                  ->after('structure_id')
                  ->comment('Niveau hiérarchique du poste dans l\'organigramme');

            // Index pour la recherche rapide
            $table->index('structure_id');
            $table->index('niveau');
        });
    }

    /**
     * Annuler la migration (supprimer les colonnes ajoutées).
     */
    public function down(): void
    {
        Schema::table('organes', function (Blueprint $table) {
            // Supprimer les index d'abord
            $table->dropIndex(['structure_id']);
            $table->dropIndex(['niveau']);

            // Supprimer la contrainte étrangère
            $table->dropForeign(['structure_id']);

            // Supprimer les colonnes
            $table->dropColumn(['structure_id', 'niveau']);
        });
    }
};