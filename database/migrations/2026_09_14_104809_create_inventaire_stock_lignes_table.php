<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lignes de comptage d'un inventaire.
     *
     * Une ligne = un article compté dans un inventaire donné.
     *
     * Elle porte les trois valeurs clés :
     *
     *   quantite_theorique  → figée à la création de l'inventaire
     *   quantite_comptee    → saisie lors du comptage physique
     *   ecart               → quantite_comptee - quantite_theorique
     *
     * Invariants métier :
     *   - la ligne est créée dès la création de l'inventaire,
     *     avec quantite_theorique figée ;
     *   - quantite_comptee peut rester NULL tant que le comptage
     *     n'a pas eu lieu ;
     *   - ecart est recalculé à chaque modification du comptage.
     *
     * IMPORTANT :
     *   quantite_theorique NE DOIT JAMAIS être modifiée après
     *   création. C'est la photographie du stock au démarrage
     *   de l'inventaire.
     *
     *   C'est cette invariabilité qui permet, à la validation,
     *   de garantir que le stock n'a pas changé entretemps,
     *   et de rejeter l'inventaire dans le cas contraire.
     */
    public function up(): void
    {
        Schema::create('inventaire_stock_lignes', function (Blueprint $table) {

            $table->id();

            /*
             * Inventaire parent.
             *
             * cascadeOnDelete : une ligne n'a aucun sens
             * sans inventaire.
             */
            $table->foreignId('inventaire_stock_id')
                ->constrained('inventaires_stocks')
                ->cascadeOnDelete()
                ->comment('Inventaire auquel appartient la ligne');

            /*
             * Article compté.
             *
             * restrictOnDelete : on ne supprime jamais un article
             * qui a participé à un inventaire.
             */
            $table->foreignId('stock_id')
                ->constrained('stocks')
                ->restrictOnDelete()
                ->comment('Article compté dans cet inventaire');

            /*
             * -----------------------------------------------------------------
             * QUANTITÉS
             * -----------------------------------------------------------------
             *
             * Précision decimal(15,3) cohérente avec tout le module.
             */

            /*
             * Quantité théorique : somme des quantités actuelles
             * des lots actifs/bloqués de l'article, calculée
             * au moment de la création de la ligne.
             *
             * IMMUABLE après création.
             */
            $table->decimal('quantite_theorique', 15, 3)
                ->default(0)
                ->comment('Quantité système figée au démarrage');

            /*
             * Quantité physiquement comptée.
             *
             * NULL tant que le comptage n'a pas été saisi.
             */
            $table->decimal('quantite_comptee', 15, 3)
                ->nullable()
                ->comment('Quantité physiquement constatée');

            /*
             * Écart calculé : quantite_comptee - quantite_theorique.
             *
             * Peut être NULL tant que le comptage n'a pas eu lieu.
             * Recalculé à chaque saisie/modification du comptage.
             */
            $table->decimal('ecart', 15, 3)
                ->nullable()
                ->comment('Écart entre compté et théorique');

            /*
             * -----------------------------------------------------------------
             * CONTEXTE
             * -----------------------------------------------------------------
             */

            /*
             * Unité figée au moment de la ligne.
             */
            $table->string('unite', 20)
                ->comment('Unité de mesure utilisée sur la ligne');

            /*
             * Notes spécifiques à la ligne (ex : pourquoi l'écart).
             */
            $table->text('notes')
                ->nullable()
                ->comment('Notes spécifiques à cette ligne');

            $table->timestamps();

            /*
             * -----------------------------------------------------------------
             * INDEX
             * -----------------------------------------------------------------
             *
             * Les index FK (inventaire_stock_id, stock_id) sont créés
             * automatiquement par les contraintes.
             */

            /*
             * Contrainte métier :
             * Un article ne peut apparaître qu'UNE seule fois
             * dans un inventaire donné.
             */
            $table->unique(
                ['inventaire_stock_id', 'stock_id'],
                'inventaire_stock_lignes_unique'
            );

            /*
             * Index pour retrouver rapidement les lignes avec écart
             * (analyse post-inventaire).
             *
             * NOTE : MySQL n'autorise pas d'index partiel sur
             * `ecart != 0`, on utilise donc un index sur la colonne
             * brute. Les requêtes filtreront ensuite en SQL.
             */
            $table->index('ecart');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaire_stock_lignes');
    }
};