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
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            
            // Titre du média
            $table->string('titre');
            
            // Type de média
            $table->enum('type', [
                'image',
                'video'
            ]);
            
            // Fichier
            $table->string('fichier');
            $table->string('miniature')->nullable();
            
            // Lien vers un projet (optionnel)
            $table->foreignId('projet_id')
                  ->nullable()
                  ->constrained('projets')
                  ->onDelete('set null');
            
            // Organisation en albums
            $table->string('album')->nullable();
            
            // Description / légende
            $table->text('description')->nullable();
            
            // Date de la prise de vue
            $table->date('date_prise')->nullable();
            
            // Lieu de la prise de vue
            $table->string('lieu')->nullable();
            
            // Photographe / source
            $table->string('auteur')->nullable();
            
            // Visibilité
            $table->boolean('publier')->default(true);
            
            // Ordre d'affichage dans la galerie
            $table->integer('ordre')->default(0);
            
            // Uploadé par
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            // Horodatage
            $table->timestamps();
            
            // Index
            $table->index('type');
            $table->index('album');
            $table->index('projet_id');
            $table->index('publier');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('medias');
    }
};