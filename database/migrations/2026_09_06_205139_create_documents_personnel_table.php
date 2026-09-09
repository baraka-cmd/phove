<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration (créer la table documents_personnel).
     */
    public function up(): void
    {
        Schema::create('documents_personnel', function (Blueprint $table) {
            $table->id();

            // Lien vers le dossier
            $table->foreignId('dossier_id')
                  ->constrained('dossiers_personnel')
                  ->onDelete('cascade')
                  ->comment('Dossier auquel appartient ce document');

            // Type de pièce (12 types définis par l'Article 65 des Statuts)
            $table->enum('type_document', [
                'demande_motivation',
                'cv',
                'titres_scolaires',
                'attestations_services',
                'attestation_nationalite',
                'attestations_autres',
                'carte_nationalite',
                'carte_hygiene',
                'photos_passeport',
                'carte_vocation',
                'photos_famille',
                'classeur',
            ])->comment('Type de pièce exigée (Article 65)');

            // Chemin du fichier uploadé
            $table->string('fichier', 255)
                  ->nullable()
                  ->comment('Chemin du fichier uploadé');

            // Date d'upload
            $table->date('date_upload')
                  ->nullable()
                  ->comment('Date de l\'upload du document');

            // Utilisateur qui a uploadé
            $table->foreignId('upload_par')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Admin qui a uploadé le document');

            // Statut du document
            $table->enum('statut', ['fourni', 'manquant', 'en_attente'])
                  ->default('manquant')
                  ->comment('Statut de la pièce');

            // Notes
            $table->text('notes')
                  ->nullable()
                  ->comment('Notes sur ce document');

            // Timestamps
            $table->timestamps();

            // Index pour la recherche rapide
            $table->index('dossier_id');
            $table->index('type_document');
            $table->index('statut');

            // Contrainte d'unicité : un seul document par type pour un dossier
            $table->unique(['dossier_id', 'type_document']);
        });
    }

    /**
     * Annuler la migration (supprimer la table).
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_personnel');
    }
};