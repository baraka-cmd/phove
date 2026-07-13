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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            
            // Titre et slug pour l'URL
            $table->string('titre');
            $table->string('slug')->unique();
            
            // Contenu principal
            $table->longText('contenu');
            $table->text('extrait')->nullable();
            
            // Catégorie de l'article
            $table->enum('categorie', [
                'actualite',
                'projet',
                'evenement',
                'rapport',
                'temoignage',
                'communique'
            ])->default('actualite');
            
            // Image de couverture
            $table->string('image')->nullable();
            
            // Statut de publication
            $table->enum('statut', [
                'brouillon',
                'publie',
                'archive'
            ])->default('brouillon');
            
            // Auteur
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            // Dates
            $table->dateTime('date_publication')->nullable();
            
            // Référence éventuelle à un projet
            $table->foreignId('projet_id')
                  ->nullable()
                  ->constrained('projets')
                  ->onDelete('set null');
            
            // Mise en avant (épingler en page d'accueil)
            $table->boolean('epingle')->default(false);
            
            // Nombre de vues
            $table->integer('vues')->default(0);
            
            // Horodatage
            $table->timestamps();
            
            // Index
            $table->index('categorie');
            $table->index('statut');
            $table->index('date_publication');
            $table->index('epingle');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};