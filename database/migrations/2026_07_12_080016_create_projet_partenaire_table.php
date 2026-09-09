<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration (créer la table pivot).
     */
    public function up(): void
    {
        Schema::create('projet_partenaire', function (Blueprint $table) {
            $table->id();
            
            // Lien vers le projet
            $table->foreignId('projet_id')
                  ->constrained('projets')
                  ->onDelete('cascade');
            
            // Lien vers le partenaire
            $table->foreignId('partenaire_id')
                  ->constrained('partenaires')
                  ->onDelete('cascade');
            
            // Rôle du partenaire dans ce projet
            $table->enum('role', [
                'bailleur_principal',
                'co_financeur',
                'partenaire_technique',
                'partenaire_mise_en_oeuvre',
                'partenaire_institutionnel',
                'donateur',
                'sponsor',
                'conseil',
                'autre'
            ])->default('partenaire_technique');
            
            // Contribution financière éventuelle
            $table->decimal('contribution', 15, 2)->nullable();
            
            // Description du partenariat pour ce projet
            $table->text('description')->nullable();
            
            // Date de début et fin de la collaboration sur ce projet
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            
            // Horodatage
            $table->timestamps();
            
            // Unicité : un partenaire ne peut pas être lié deux fois au même projet
            $table->unique(['projet_id', 'partenaire_id']);
            
            // Index
            $table->index('projet_id');
            $table->index('partenaire_id');
            $table->index('role');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('projet_partenaire');
    }
};