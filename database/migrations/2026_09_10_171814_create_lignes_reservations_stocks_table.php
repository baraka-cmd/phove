<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lignes d'une réservation de stock.
     *
     * Une ligne = un article réservé dans une réservation donnée.
     *
     * Cycle des quantités d'une ligne :
     *
     *   quantite_demandee     → saisie utilisateur
     *   quantite_reservee     → effectivement bloquée (peut être < demandée)
     *   quantite_consommee    → cumul converti en sortie physique
     *
     * Invariants métier :
     *   quantite_reservee   <= quantite_demandee
     *   quantite_consommee  <= quantite_reservee
     *
     * Le solde réellement actif d'une ligne est :
     *
     *   reste = quantite_reservee - quantite_consommee
     *
     * C'est ce reste qui entre dans le calcul de la quantité
     * réservée globale d'un article.
     *
     * IMPORTANT :
     * La quantité réservée n'est comptée dans le calcul
     * de la disponibilité QUE si la réservation parente
     * est dans un statut actif (confirmee | partielle).
     * Cette règle est appliquée au niveau des services.
     */
    public function up(): void
    {
        Schema::create('lignes_reservations_stocks', function (Blueprint $table) {

            $table->id();

            /*
             * Réservation parente.
             *
             * cascadeOnDelete : une ligne n'a aucun sens
             * sans sa réservation.
             */
            $table->foreignId('reservation_stock_id')
                ->constrained('reservations_stocks')
                ->cascadeOnDelete()
                ->comment('Réservation à laquelle appartient la ligne');

            /*
             * Article réservé.
             *
             * restrictOnDelete : on ne supprime jamais un article
             * qui apparaît dans une réservation (traçabilité).
             */
            $table->foreignId('stock_id')
                ->constrained('stocks')
                ->restrictOnDelete()
                ->comment('Article concerné par la réservation');

            /*
             * -----------------------------------------------------------------
             * QUANTITÉS
             * -----------------------------------------------------------------
             *
             * Précision decimal(15,3) cohérente avec lots_stocks,
             * lignes_bons_sortie, etc.
             */

            /*
             * Quantité initialement demandée par l'utilisateur.
             */
            $table->decimal('quantite_demandee', 15, 3)
                ->comment('Quantité demandée par l’utilisateur');

            /*
             * Quantité effectivement réservée.
             *
             * Peut être inférieure à la quantité demandée
             * lorsqu'une réservation partielle est acceptée.
             * Reste à 0 tant que la réservation n'est pas confirmée.
             */
            $table->decimal('quantite_reservee', 15, 3)
                ->default(0)
                ->comment('Quantité réellement bloquée dans le stock');

            /*
             * Quantité déjà consommée.
             *
             * Incrémentée uniquement par ReservationStockService::consommer().
             * Le solde actif d'une ligne est :
             *
             *   reste = quantite_reservee - quantite_consommee
             */
            $table->decimal('quantite_consommee', 15, 3)
                ->default(0)
                ->comment('Quantité déjà consommée par les sorties');

            /*
             * -----------------------------------------------------------------
             * CONTEXTE
             * -----------------------------------------------------------------
             */

            /*
             * Unité figée au moment de la ligne.
             *
             * Dupliquée depuis stocks.unite pour préserver
             * l'historique documentaire même si l'article
             * change d'unité plus tard.
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
             * Les index FK (reservation_stock_id, stock_id) sont
             * créés automatiquement par les contraintes.
             */

            /*
             * Contrainte métier :
             * Un article ne peut apparaître qu'UNE seule fois
             * dans une réservation donnée.
             *
             * Remplace toute vérification applicative fragile.
             */
            $table->unique(
                ['reservation_stock_id', 'stock_id'],
                'lignes_reservations_reservation_stock_unique'
            );

            /*
             * Index utilisé par les calculs de disponibilité :
             *
             *   SELECT SUM(quantite_reservee - quantite_consommee)
             *   FROM lignes_reservations_stocks
             *   WHERE stock_id = ?
             *     AND reservation_stock_id IN (...)
             *
             * L'index sur stock_id (déjà créé par FK)
             * est suffisant en pratique.
             */
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lignes_reservations_stocks');
    }
};