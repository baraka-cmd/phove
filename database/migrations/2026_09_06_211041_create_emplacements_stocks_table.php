<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute la migration.
     *
     * Cette table représente les emplacements physiques
     * utilisés pour stocker les articles de PHOVE.
     *
     * Exemples :
     *
     * MAG-001 → Magasin principal
     * DEP-001 → Dépôt Nyiragongo
     * DEP-002 → Dépôt Minova
     */
    public function up(): void
    {
        Schema::create('emplacements_stocks', function (Blueprint $table) {
            /*
             * Identifiant technique.
             */
            $table->id();

            /*
             * Code métier unique de l'emplacement.
             *
             * Exemple :
             * MAG-001
             * DEP-001
             */
            $table->string('code', 30)
                ->unique()
                ->comment('Code unique de l’emplacement');

            /*
             * Nom de l'emplacement.
             *
             * Exemple :
             * Magasin principal
             * Dépôt Nyiragongo
             */
            $table->string('nom', 150)
                ->comment('Nom de l’emplacement');

            /*
             * Description complémentaire.
             *
             * Permet de préciser par exemple :
             * - bâtiment
             * - zone
             * - informations logistiques
             */
            $table->text('description')
                ->nullable()
                ->comment('Description de l’emplacement');

            /*
             * Activation / désactivation logique.
             *
             * TRUE  → emplacement utilisable
             * FALSE → emplacement désactivé
             *
             * On évite ainsi de supprimer physiquement
             * un emplacement pouvant déjà apparaître
             * dans l'historique des mouvements.
             */
            $table->boolean('actif')
                ->default(true)
                ->comment('Indique si l’emplacement est actif');

            /*
             * Timestamps Laravel.
             */
            $table->timestamps();

            /*
             * Index.
             *
             * Le champ code possède déjà un index UNIQUE.
             * L'index sur actif facilite les recherches
             * des emplacements actuellement disponibles.
             */
            $table->index('actif');
        });
    }

    /**
     * Annule la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('emplacements_stocks');
    }
};