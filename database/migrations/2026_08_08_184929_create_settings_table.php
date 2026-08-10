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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Clé unique du paramètre
            $table->string('key', 100)->unique();

            // Valeur du paramètre
            $table->text('value')->nullable();

            // Type de champ (détermine l'affichage dans le formulaire)
            $table->enum('type', [
                'text',
                'textarea',
                'image',
                'url',
                'email',
                'phone',
                'number',
                'color',
                'switch',
            ])->default('text');

            // Groupe de paramètres (onglet dans l'interface)
            $table->enum('group', [
                'identite',
                'contact',
                'social',
                'pages',
                'seo',
                'maintenance',
            ])->default('identite');

            // Libellé affiché dans le formulaire
            $table->string('label', 100);

            // Description / texte d'aide
            $table->string('description', 255)->nullable();

            // Ordre d'affichage dans le groupe
            $table->integer('ordre')->default(0);

            // Horodatage
            $table->timestamps();

            // Index
            $table->index('group');
            $table->index('ordre');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};