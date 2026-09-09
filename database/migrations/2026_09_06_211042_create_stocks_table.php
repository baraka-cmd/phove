<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration (créer la table stocks).
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            // Référence unique du produit (code interne)
            $table->string('reference', 50)
                  ->unique()
                  ->comment('Référence unique du produit');

            // Désignation du produit
            $table->string('designation', 255)
                  ->comment('Nom ou désignation du produit');

            // Catégorie du produit
            $table->enum('categorie', [
                'vivres',
                'medicaments',
                'habillement',
                'abris',
                'materiel',
                'semences',
                'outils',
                'autre',
            ])->comment('Catégorie du produit');

            // Unité de mesure
            $table->string('unite', 20)
                  ->comment('Unité : kg, litre, pièce, sac, boîte, etc.');

            // Quantité initiale
            $table->decimal('quantite_initiale', 12, 2)
                  ->default(0)
                  ->comment('Quantité initiale en stock');

            // Quantité actuelle disponible
            $table->decimal('quantite_actuelle', 12, 2)
                  ->default(0)
                  ->comment('Quantité actuelle disponible');

            // Seuil d'alerte
            $table->decimal('seuil_alerte', 12, 2)
                  ->nullable()
                  ->comment('Seuil minimum avant alerte');

            // Emplacement de stockage
            $table->string('emplacement', 255)
                  ->nullable()
                  ->comment('Lieu de stockage');

            // Description
            $table->text('description')
                  ->nullable()
                  ->comment('Description du produit');

            // Statut calculé automatiquement
            $table->enum('statut', ['disponible', 'faible', 'rupture'])
                  ->default('disponible')
                  ->comment('Statut : disponible, faible, rupture');

            // Timestamps
            $table->timestamps();

            // Index pour la recherche rapide
            $table->index('categorie');
            $table->index('statut');
            $table->index('designation');
        });
    }

    /**
     * Annuler la migration (supprimer la table).
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};