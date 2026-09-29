<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table des lots physiques.
     *
     * Un lot représente une quantité identifiable d'un article,
     * avec une date d'entrée, une éventuelle date d'expiration,
     * un fournisseur et un statut opérationnel.
     *
     * C'est la table qui porte réellement la quantité physique.
     * La table `stocks` ne contient AUCUNE quantité.
     *
     * Le lot est la brique de base pour :
     * - la gestion FEFO ;
     * - la traçabilité fournisseur ;
     * - l'historique via `mouvements_stocks`.
     */
    public function up(): void
    {
        Schema::create('lots_stocks', function (Blueprint $table) {

            $table->id();

            /*
             * Article auquel appartient ce lot.
             *
             * Un lot appartient à un et un seul article.
             * La suppression d'un article supprime ses lots
             * (cascade), car un lot sans article n'a aucun sens.
             */
            $table->foreignId('stock_id')
                ->constrained('stocks')
                ->cascadeOnDelete()
                ->comment('Article auquel appartient ce lot');

            /*
             * Numéro de lot fourni par le fabricant ou le fournisseur.
             *
             * Nullable car certains articles (dons, productions locales)
             * peuvent ne pas avoir de numéro de lot formel.
             *
             * L'unicité n'est PAS globale : deux articles différents
             * peuvent porter le même numéro de lot chez deux fabricants.
             * L'unicité par article sera assurée côté service.
             */
            $table->string('numero_lot', 100)
                ->nullable()
                ->comment('Numéro de lot fabricant ou fournisseur');

            /*
             * Dates de traçabilité produit.
             */
            $table->date('date_fabrication')
                ->nullable()
                ->comment('Date de fabrication du produit');

            /*
             * Date d'entrée physique en stock.
             *
             * Obligatoire : un lot sans date d'entrée est inexploitable
             * pour la gestion FEFO et pour l'audit.
             */
            $table->date('date_entree')
                ->comment('Date d’entrée physique du lot en stock');

            $table->date('date_expiration')
                ->nullable()
                ->comment('Date d’expiration si produit périssable');

            /*
             * Quantités.
             *
             * quantite_initiale : figée à la création du lot.
             *                     Ne doit JAMAIS être modifiée après.
             *
             * quantite_actuelle : vivante, modifiée uniquement
             *                     par les services métier dans
             *                     une transaction avec verrouillage.
             *
             * On utilise decimal(15,3) pour couvrir les unités
             * fractionnaires (kg, litre, m³) avec une précision
             * suffisante pour les denrées et matériaux.
             */
            $table->decimal('quantite_initiale', 15, 3)
                ->default(0)
                ->comment('Quantité initiale reçue (immuable)');

            $table->decimal('quantite_actuelle', 15, 3)
                ->default(0)
                ->comment('Quantité restante dans le lot');

            /*
             * Valorisation.
             *
             * NULL pour les dons sans valeur d'acquisition connue.
             */
            $table->decimal('cout_unitaire', 15, 2)
                ->nullable()
                ->comment('Coût unitaire du produit (nullable pour les dons)');

            /*
             * Traçabilité fournisseur.
             */
            $table->string('fournisseur', 255)
                ->nullable()
                ->comment('Fournisseur ou organisme donateur');

            /*
             * Référence du document d'entrée (BL, don, facture).
             *
             * Exemples :
             * BL-2026-0045
             * DON-2026-0015
             */
            $table->string('reference_document', 100)
                ->nullable()
                ->comment('Référence du document d’entrée du lot');

            /*
             * Statut opérationnel du lot.
             *
             * actif   → utilisable pour les sorties
             * epuise  → quantité actuelle à zéro
             * expire  → date d'expiration dépassée
             * bloque  → interdit manuellement (qualité, litige…)
             *
             * Une seule source de vérité : ce statut.
             * Il est maintenu par LotStockService.
             */
            $table->enum('statut', [
                'actif',
                'epuise',
                'expire',
                'bloque',
            ])
                ->default('actif')
                ->comment('État opérationnel du lot');

            $table->timestamps();

            /*
             * -----------------------------------------------------------------
             * INDEX
             * -----------------------------------------------------------------
             */

            /*
             * Index sur stock_id : déjà créé par la contrainte FK.
             * Pas besoin de le redéclarer.
             */

            /*
             * Index pour les requêtes FEFO et les alertes d'expiration.
             */
            $table->index('date_expiration');

            /*
             * Index pour filtrer rapidement par statut
             * (lots actifs, bloqués, expirés…).
             */
            $table->index('statut');

            /*
             * Index composé utilisé par le scope FEFO :
             *
             *   WHERE stock_id = ?
             *     AND statut = 'actif'
             *     AND quantite_actuelle > 0
             *   ORDER BY date_expiration ASC, date_entree ASC, id ASC
             *
             * C'est LA requête la plus chaude du module stock.
             * L'index composé dans cet ordre évite un filesort.
             */
            $table->index(
                ['stock_id', 'statut', 'date_expiration', 'date_entree'],
                'lots_stocks_fefo_index'
            );

            /*
             * Un même article ne peut pas avoir deux fois
             * le même numéro de lot.
             *
             * Contrainte UNIQUE composite : autorise plusieurs NULL
             * (un lot sans numéro reste possible), mais interdit
             * les doublons réels.
             */
            $table->unique(
                ['stock_id', 'numero_lot'],
                'lots_stocks_stock_numero_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots_stocks');
    }
};