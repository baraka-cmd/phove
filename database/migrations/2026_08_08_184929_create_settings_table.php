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

            // Clé unique du paramètre (ex: site_name, slogan, email, phone_1)
            $table->string('key', 100)->unique();

            // Valeur du paramètre (texte long pour gérer les adresses, descriptions)
            $table->text('value')->nullable();

            // Type de champ (détermine l'affichage dans le formulaire admin)
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

            // Groupe de paramètres (onglet dans l'interface admin)
            $table->enum('group', [
                'identite',
                'contact',
                'social',
                'pages',
                'seo',
                'maintenance',
            ])->default('identite');

            // Libellé affiché dans le formulaire admin
            $table->string('label', 100);

            // Description / texte d'aide pour guider l'administrateur
            $table->string('description', 255)->nullable();

            // Ordre d'affichage dans le groupe
            $table->integer('ordre')->default(0);

            // Horodatage (created_at, updated_at)
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index('group');
            $table->index('ordre');
        });
    }

    /**
     * Annuler la migration (supprimer la table).
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};