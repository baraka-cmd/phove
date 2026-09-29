<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute la migration.
     *
     * Cette table représente l'article géré dans le système
     * de gestion des stocks.
     *
     * IMPORTANT :
     * Les quantités physiques ne sont volontairement pas
     * stockées directement dans cette table.
     *
     * Elles seront gérées par les opérations de stock,
     * les lots et les mouvements.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            /*
             * Identifiant technique.
             */
            $table->id();

            /*
             * Référence métier unique de l'article.
             *
             * Exemple :
             * STK-2026-0001
             */
            $table->string('reference', 50)
                ->unique()
                ->comment('Référence métier unique de l’article');

            /*
             * Désignation de l'article.
             *
             * Exemple :
             * Riz blanc
             * Paracétamol 500 mg
             * Tôle ondulée
             */
            $table->string('designation', 255)
                ->comment('Nom ou désignation de l’article');

            /*
             * Catégorie de l'article.
             *
             * Les valeurs existantes sont conservées afin
             * de rester compatibles avec le domaine PHOVE.
             */
            $table->enum('categorie', [
                'vivres',
                'medicaments',
                'habillement',
                'abris',
                'materiel',
                'semences',
                'outils',
                'autre',
            ])->comment('Catégorie de l’article');

            /*
             * Unité de mesure.
             *
             * Exemples :
             * kg, litre, pièce, sac, boîte, carton...
             */
            $table->string('unite', 20)
                ->comment('Unité de mesure de l’article');

            /*
             * Seuil d'alerte.
             *
             * NULL signifie qu'aucun seuil spécifique
             * n'est configuré pour cet article.
             */
            $table->decimal('seuil_alerte', 12, 2)
                ->nullable()
                ->comment('Seuil minimum déclenchant une alerte');

            /*
             * Emplacement principal de l'article.
             *
             * La table emplacements_stocks doit exister
             * avant l'exécution de cette migration.
             *
             * Si l'emplacement est supprimé, on conserve
             * l'article mais on supprime uniquement la référence
             * vers cet emplacement.
             */
            $table->foreignId('emplacement_principal_id')
                ->nullable()
                ->constrained('emplacements_stocks')
                ->nullOnDelete()
                ->comment('Emplacement principal de stockage');

            /*
             * Description libre.
             */
            $table->text('description')
                ->nullable()
                ->comment('Description complémentaire de l’article');

            /*
             * Statut administratif de l'article.
             *
             * IMPORTANT :
             * Ce champ ne représente PAS la quantité disponible.
             *
             * actif   = article utilisable
             * inactif = article temporairement désactivé
             * archive = article retiré du catalogue actif
             */
            $table->enum('statut', [
                'actif',
                'inactif',
                'archive',
            ])->default('actif')
                ->comment('État administratif de l’article');

            /*
             * Timestamps Laravel.
             */
            $table->timestamps();

            /*
             * Index.
             *
             * reference possède déjà un index UNIQUE.
             */
            $table->index('categorie');
            $table->index('statut');
            $table->index('designation');
            $table->index('emplacement_principal_id');
        });
    }

    /**
     * Annule la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};