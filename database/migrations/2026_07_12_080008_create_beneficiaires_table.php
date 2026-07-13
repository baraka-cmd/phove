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
        Schema::create('beneficiaires', function (Blueprint $table) {
            $table->id();
            
            // Lien vers le projet
            $table->foreignId('projet_id')
                  ->constrained('projets')
                  ->onDelete('cascade');
            
            // Identité du bénéficiaire
            $table->string('nom');
            $table->string('post_nom')->nullable();
            $table->string('prenom')->nullable();
            $table->enum('sexe', ['M', 'F'])->nullable();
            $table->integer('age')->nullable();
            
            // Catégorie de vulnérabilité (section 10 des documents)
            $table->enum('categorie', [
                'femme',
                'enfant',
                'handicape',
                'troisieme_age',
                'orphelin',
                'deplace_interne',
                'refugie',
                'rapatrie',
                'autre'
            ]);
            
            // Localisation
            $table->string('localisation');
            $table->string('province')->nullable();
            
            // Aide reçue
            $table->string('type_aide');
            $table->date('date_aide');
            $table->text('description_aide')->nullable();
            
            // Contact (si disponible et consentement)
            $table->string('telephone', 20)->nullable();
            
            // Témoignage éventuel
            $table->text('temoignage')->nullable();
            $table->boolean('temoignage_public')->default(false);
            
            // Horodatage
            $table->timestamps();
            
            // Index
            $table->index('categorie');
            $table->index('localisation');
            $table->index('projet_id');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaires');
    }
};