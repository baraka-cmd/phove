<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lignes d'un bon de sortie.
     *
     * Une ligne = un article demandé dans un bon de sortie.
     *
     * Cycle des quantités d'une ligne :
     *
     *   quantite_demandee  → saisie initiale (brouillon)
     *   quantite_validee   → ajustée à la validation
     *   quantite_sortie    → cumul réellement sorti (FEFO)
     *
     * IMPORTANT :
     *   quantite_sortie ne doit être incrémentée QUE par
     *   SortieStockService::effectuer(), sous transaction
     *   et verrouillage.
     *
     * Contrainte d'unicité :
     *   Un même article ne peut apparaître qu'UNE seule fois
     *   dans un même bon de sortie.
     */
    public function up(): void
    {
        Schema::create('lignes_bons_sortie', function (Blueprint $table) {

            $table->id();

            /*
             * Bon de sortie parent.
             *
             * cascadeOnDelete : si on supprime le bon (ce qui n'arrive
             * qu'en brouillon, côté service), ses lignes disparaissent
             * aussi. Pas de ligne orpheline.
             */
            $table->foreignId('bon_sortie_id')
                ->constrained('bons_sortie')
                ->cascadeOnDelete()
                ->comment('Bon de sortie auquel appartient la ligne');

            /*
             * Article concerné.
             *
             * restrictOnDelete : on n'autorise JAMAIS la suppression
             * d'un article qui apparaît dans un document de sortie,
             * même historique. La traçabilité prime.
             */
            $table->foreignId('stock_id')
                ->constrained('stocks')
                ->restrictOnDelete()
                ->comment('Article demandé dans cette ligne');

            /*
             * -----------------------------------------------------------------
             * QUANTITÉS
             * -----------------------------------------------------------------
             *
             * Précision decimal(15,3) cohérente avec lots_stocks.
             */

            /*
             * Quantité demandée à la création du brouillon.
             * Strictement > 0 (contrôlé côté service).
             */
            $table->decimal('quantite_demandee', 15, 3)
                ->comment('Quantité initialement demandée');

            /*
             * Quantité validée.
             *
             * Peut être inférieure à la quantité demandée
             * (validation partielle autorisée).
             *
             * Par défaut = quantite_demandee à la création,
             * puis ajustée à la validation.
             */
            $table->decimal('quantite_validee', 15, 3)
                ->default(0)
                ->comment('Quantité validée (peut être < demandée)');

            /*
             * Quantité réellement sortie du stock.
             *
             * Cumul des sorties FEFO effectives.
             * Reste à 0 tant que le bon n'est pas `sorti`.
             */
            $table->decimal('quantite_sortie', 15, 3)
                ->default(0)
                ->comment('Quantité réellement sortie du stock');

            /*
             * -----------------------------------------------------------------
             * CONTEXTE
             * -----------------------------------------------------------------
             */

            /*
             * Unité figée au moment de la création de la ligne.
             *
             * On la duplique volontairement depuis stocks.unite
             * pour préserver l'historique documentaire même si
             * l'unité de l'article change plus tard.
             */
            $table->string('unite', 20)
                ->comment('Unité de mesure utilisée sur la ligne');

            /*
             * Notes spécifiques à la ligne.
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
             * Les index FK bon_sortie_id et stock_id sont créés
             * automatiquement par les contraintes. Pas besoin
             * de les redéclarer.
             */

            /*
             * Contrainte métier : un article ne peut apparaître
             * qu'une seule fois dans un même bon.
             *
             * Cette contrainte remplace toute vérification
             * applicative fragile.
             */
            $table->unique(
                ['bon_sortie_id', 'stock_id'],
                'lignes_bons_sortie_bon_stock_unique'
            );

            /*
             * Index pour reconstituer rapidement
             * l'historique des sorties d'un article donné.
             */
            $table->index(
                ['stock_id', 'bon_sortie_id'],
                'lignes_bons_sortie_stock_bon_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lignes_bons_sortie');
    }
};