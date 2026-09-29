<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Historique immuable des mouvements de stock.
     *
     * Cette table est un JOURNAL :
     *
     *   - une ligne = un fait
     *   - une ligne n'est JAMAIS modifiée après création
     *   - une ligne n'est JAMAIS supprimée (sauf purge légale)
     *
     * Les quantités de stock ne doivent JAMAIS être modifiées
     * directement depuis un Controller, une View ou un Job.
     *
     * Toute modification de quantité DOIT passer par un Service
     * métier qui, dans une transaction :
     *
     *   1. verrouille la ressource (stock, lot, emplacement),
     *   2. vérifie les invariants,
     *   3. écrit la modification,
     *   4. PUIS crée la ligne correspondante ici.
     *
     * Rôles de cette table :
     *
     *   - audit complet des mouvements (qui, quoi, quand, combien) ;
     *   - source pour reconstituer l'historique d'un article ;
     *   - source pour l'analyse (rapports, KPI, écarts) ;
     *   - preuve métier en cas de contrôle ou de litige.
     */
    public function up(): void
    {
        Schema::create('mouvements_stocks', function (Blueprint $table) {

            $table->id();

            /*
             * -----------------------------------------------------------------
             * RESSOURCES IMPACTÉES
             * -----------------------------------------------------------------
             */

            /*
             * Article concerné.
             *
             * restrictOnDelete : un article avec des mouvements
             * ne doit JAMAIS être supprimé. On archive, on ne
             * supprime pas.
             */
            $table->foreignId('stock_id')
                ->constrained('stocks')
                ->restrictOnDelete()
                ->comment('Article concerné par le mouvement');

            /*
             * Lot concerné.
             *
             * Nullable pour les mouvements non liés à un lot
             * (futur : stock global, corrections). Aujourd'hui
             * la grande majorité des mouvements porte un lot.
             *
             * restrictOnDelete : un lot avec mouvements ne peut
             * pas être supprimé.
             */
            $table->foreignId('lot_stock_id')
                ->nullable()
                ->constrained('lots_stocks')
                ->restrictOnDelete()
                ->comment('Lot concerné par le mouvement');

            /*
             * Emplacement concerné.
             *
             * Nullable pour les mouvements non localisés
             * (corrections globales, écritures historiques).
             */
            $table->foreignId('emplacement_id')
                ->nullable()
                ->constrained('emplacements_stocks')
                ->restrictOnDelete()
                ->comment('Emplacement concerné par le mouvement');

            /*
             * -----------------------------------------------------------------
             * CLASSIFICATION DU MOUVEMENT
             * -----------------------------------------------------------------
             *
             * `type` : mécanisme comptable du mouvement.
             * `nature` : motif métier du mouvement.
             *
             * Deux axes DISTINCTS, volontairement séparés.
             */

            $table->enum('type', [
                'entree',
                'sortie',
                'transfert',
                'ajustement',
            ])
                ->comment('Type comptable du mouvement');

            $table->enum('nature', [
                'achat',
                'don',
                'retour',
                'distribution',
                'perte',
                'peremption',
                'inventaire',
                'correction',
                'transfert_interne',
            ])
                ->comment('Nature métier du mouvement');

            /*
             * -----------------------------------------------------------------
             * QUANTITÉ
             * -----------------------------------------------------------------
             *
             * Toujours positive.
             *
             * Le SENS du mouvement est déterminé par `type` :
             *
             *   entree     → +quantite
             *   sortie     → -quantite
             *   transfert  → neutre globalement, ventilé par lot
             *   ajustement → signe porté par une convention du service
             *
             * Pour l'ajustement d'inventaire, le service encode
             * directement le signe dans une colonne dédiée
             * `sens` ci-dessous (voir plus bas).
             */
            $table->decimal('quantite', 15, 3)
                ->comment('Quantité du mouvement (toujours > 0)');

            /*
             * Sens du mouvement — permet d'exprimer proprement
             * les ajustements (+1 ou -1) sans ambiguïté.
             *
             * Pour entree  : +1
             * Pour sortie  : -1
             * Pour ajustement : selon l'écart (+1 surplus, -1 manquant)
             * Pour transfert : 0 (neutre, ventilé par lot)
             *
             * Cette colonne évite les hack de signe sur `quantite`.
             */
            $table->smallInteger('sens')
                ->default(1)
                ->comment('Sens du mouvement : +1, -1 ou 0');

            /*
             * -----------------------------------------------------------------
             * DATES
             * -----------------------------------------------------------------
             */

            /*
             * Date métier du mouvement (date de l'événement).
             *
             * Peut être différente de created_at (saisie rétroactive,
             * régularisation). C'est cette date qui est utilisée
             * pour l'audit et les rapports.
             */
            $table->date('date_mouvement')
                ->comment('Date métier du mouvement');

            /*
             * -----------------------------------------------------------------
             * DOCUMENTS SOURCES
             * -----------------------------------------------------------------
             *
             * Référence libre du document qui justifie le mouvement.
             *
             * Exemples :
             * BL-2026-0045
             * DON-2026-0015
             * INV-2026-0003
             */
            $table->string('reference_document', 100)
                ->nullable()
                ->comment('Référence du document source');

            /*
             * Bon de sortie associé (si applicable).
             *
             * restrictOnDelete : on ne supprime pas un bon de sortie
             * qui a généré des mouvements.
             */
            $table->foreignId('bon_sortie_id')
                ->nullable()
                ->constrained('bons_sortie')
                ->restrictOnDelete()
                ->comment('Bon de sortie à l’origine du mouvement');

            /*
             * Projet concerné (si applicable).
             *
             * nullOnDelete : un mouvement reste valable même si
             * le projet est supprimé.
             */
            $table->foreignId('projet_id')
                ->nullable()
                ->constrained('projets')
                ->nullOnDelete()
                ->comment('Projet concerné par le mouvement');

            /*
             * -----------------------------------------------------------------
             * ACTEUR
             * -----------------------------------------------------------------
             *
             * Utilisateur qui a effectué l'opération.
             *
             * restrictOnDelete : un utilisateur ayant écrit dans
             * l'historique ne doit JAMAIS être supprimé.
             */
            $table->foreignId('utilisateur_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('Utilisateur ayant effectué le mouvement');

            /*
             * -----------------------------------------------------------------
             * INFORMATIONS LIBRES
             * -----------------------------------------------------------------
             */

            $table->string('motif', 500)
                ->nullable()
                ->comment('Motif complémentaire du mouvement');

            $table->text('notes')
                ->nullable()
                ->comment('Notes complémentaires');

            /*
             * -----------------------------------------------------------------
             * STATUT
             * -----------------------------------------------------------------
             *
             * Un mouvement est créé en `valide` par défaut :
             * il ne doit pas exister de mouvements en brouillon
             * dans le journal.
             *
             * `annule` sert uniquement à marquer explicitement
             * qu'un mouvement a été contre-passé par une opération
             * ultérieure (jamais par modification directe).
             */
            $table->enum('statut', [
                'valide',
                'annule',
            ])
                ->default('valide')
                ->comment('État administratif du mouvement');

            $table->timestamps();

            /*
             * -----------------------------------------------------------------
             * INDEX
             * -----------------------------------------------------------------
             *
             * Les index FK (stock_id, lot_stock_id, emplacement_id,
             * bon_sortie_id, projet_id, utilisateur_id) sont créés
             * automatiquement par les contraintes.
             *
             * On ajoute uniquement les index métier nécessaires.
             */

            /*
             * Filtre principal : type de mouvement.
             */
            $table->index('type');

            /*
             * Filtre secondaire : nature du mouvement.
             */
            $table->index('nature');

            /*
             * Filtre date : très utilisé dans les rapports.
             */
            $table->index('date_mouvement');

            /*
             * Filtre statut (mouvements annulés vs valides).
             */
            $table->index('statut');

            /*
             * Index composé n°1 — historique d'un article.
             *
             *   WHERE stock_id = ?
             *   ORDER BY date_mouvement DESC, id DESC
             *
             * C'est LA requête de la fiche article.
             */
            $table->index(
                ['stock_id', 'date_mouvement', 'id'],
                'mouvements_stocks_stock_date_index'
            );

            /*
             * Index composé n°2 — historique d'un lot.
             *
             *   WHERE lot_stock_id = ?
             *   ORDER BY date_mouvement DESC, id DESC
             */
            $table->index(
                ['lot_stock_id', 'date_mouvement', 'id'],
                'mouvements_stocks_lot_date_index'
            );

            /*
             * Index composé n°3 — historique par utilisateur.
             *
             *   WHERE utilisateur_id = ?
             *   ORDER BY date_mouvement DESC
             *
             * Utile pour les audits internes.
             */
            $table->index(
                ['utilisateur_id', 'date_mouvement'],
                'mouvements_stocks_user_date_index'
            );

            /*
             * Index composé n°4 — mouvements liés à un bon de sortie.
             *
             *   WHERE bon_sortie_id = ?
             *   ORDER BY date_mouvement
             *
             * Utile sur la fiche d'un bon de sortie pour
             * afficher les mouvements générés.
             */
            $table->index(
                ['bon_sortie_id', 'date_mouvement'],
                'mouvements_stocks_bon_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_stocks');
    }
};