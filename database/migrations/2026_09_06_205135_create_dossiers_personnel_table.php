<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration (créer la table dossiers_personnel).
     */
    public function up(): void
    {
        Schema::create('dossiers_personnel', function (Blueprint $table) {
            $table->id();

            // Lien vers le membre (personnel)
            $table->foreignId('membre_id')
                  ->constrained('membres')
                  ->onDelete('cascade')
                  ->comment('Membre concerné par ce dossier');

            // Statut du dossier
            $table->enum('statut', ['complet', 'incomplet', 'en_attente'])
                  ->default('en_attente')
                  ->comment('État de complétude du dossier');

            // Date de création du dossier
            $table->date('date_creation')
                  ->comment('Date de création du dossier');

            // Date de validation du dossier
            $table->date('date_validation')
                  ->nullable()
                  ->comment('Date de validation finale du dossier');

            // Utilisateur qui a validé le dossier
            $table->foreignId('valide_par')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Admin qui a validé le dossier');

            // Notes internes
            $table->text('notes')
                  ->nullable()
                  ->comment('Notes internes sur le dossier');

            // Timestamps
            $table->timestamps();

            // Index pour la recherche rapide
            $table->index('statut');
            $table->index('date_creation');
        });
    }

    /**
     * Annuler la migration (supprimer la table).
     */
    public function down(): void
    {
        Schema::dropIfExists('dossiers_personnel');
    }
};