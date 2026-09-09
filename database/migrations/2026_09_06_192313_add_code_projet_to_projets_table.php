<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration (ajouter la colonne code_projet).
     */
    public function up(): void
    {
        Schema::table('projets', function (Blueprint $table) {
            // Code officiel du projet (ex: UNJP/RDC/082/WFP)
            $table->string('code_projet', 50)
                  ->nullable()
                  ->after('slug')
                  ->comment('Code officiel du projet (ex: UNJP/RDC/082/WFP)');

            // Index pour la recherche rapide
            $table->index('code_projet');
        });
    }

    /**
     * Annuler la migration (supprimer la colonne code_projet).
     */
    public function down(): void
    {
        Schema::table('projets', function (Blueprint $table) {
            // Supprimer l'index d'abord
            $table->dropIndex(['code_projet']);

            // Supprimer la colonne
            $table->dropColumn('code_projet');
        });
    }
};