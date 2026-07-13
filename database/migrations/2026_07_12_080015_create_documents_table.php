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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            
            // Titre du document
            $table->string('titre');
            
            // Type de document
            $table->enum('type', [
                'statuts',
                'roi',
                'proces_verbal',
                'convention',
                'rapport_annuel',
                'rapport_activite',
                'guide_procedure',
                'politique_interne',
                'communique_officiel',
                'autre'
            ]);
            
            // Fichier
            $table->string('fichier');
            $table->string('extension')->nullable();
            $table->bigInteger('taille')->nullable();  // en octets
            
            // Description
            $table->text('description')->nullable();
            
            // Dates
            $table->date('date_document')->nullable();
            $table->date('date_signature')->nullable();
            
            // Signataires
            $table->string('signataires')->nullable();
            
            // Version
            $table->string('version')->nullable();
            
            // Visibilité
            $table->boolean('public')->default(false);
            
            // Catégorie additionnelle
            $table->string('categorie')->nullable();
            
            // Téléchargements
            $table->integer('telechargements')->default(0);
            
            // Uploadé par
            $table->foreignId('upload_par')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            // Horodatage
            $table->timestamps();
            
            // Index
            $table->index('type');
            $table->index('public');
            $table->index('date_document');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};