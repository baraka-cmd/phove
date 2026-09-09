<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration (créer la table structures_territoriales).
     */
    public function up(): void
    {
        Schema::create('structures_territoriales', function (Blueprint $table) {
            $table->id();

            // Nom de la structure (ex: PHOVE Goma, PHOVE Rwanda, PHOVE Nyiragongo)
            $table->string('nom', 255);

            // Type de structure : Station (pays), Base (province), Antenne (territoire/commune)
            $table->enum('type', ['station', 'base', 'antenne'])
                  ->comment('Station = pays, Base = province, Antenne = territoire/commune');

            // Pays (pour les stations)
            $table->string('pays', 100)->nullable();

            // Province (pour les bases)
            $table->string('province', 100)->nullable();

            // Territoire ou Commune (pour les antennes)
            $table->string('territoire', 100)->nullable();

            // Adresse physique complète
            $table->string('adresse', 255)->nullable();

            // Description de la structure
            $table->text('description')->nullable();

            // Structure parente (hiérarchie : Station -> Base -> Antenne)
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('structures_territoriales')
                  ->onDelete('set null')
                  ->comment('Structure parente dans la hiérarchie');

            // Statut
            $table->enum('statut', ['actif', 'inactif'])->default('actif');

            // Timestamps
            $table->timestamps();

            // Index pour la recherche rapide
            $table->index('type');
            $table->index('pays');
            $table->index('province');
            $table->index('territoire');
            $table->index('statut');
        });
    }

    /**
     * Annuler la migration (supprimer la table).
     */
    public function down(): void
    {
        Schema::dropIfExists('structures_territoriales');
    }
};