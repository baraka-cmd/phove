<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration (créer la table mouvements_stocks).
     */
    public function up(): void
    {
        Schema::create('mouvements_stocks', function (Blueprint $table) {
            $table->id();

            // Lien vers le stock
            $table->foreignId('stock_id')
                  ->constrained('stocks')
                  ->onDelete('cascade')
                  ->comment('Produit concerné par le mouvement');

            // Type de mouvement
            $table->enum('type_mouvement', ['entree', 'sortie'])
                  ->comment('Entrée ou sortie de stock');

            // Quantité déplacée
            $table->decimal('quantite', 12, 2)
                  ->comment('Quantité déplacée');

            // Date du mouvement
            $table->date('date_mouvement')
                  ->comment('Date du mouvement');

            // Référence du document (bon de sortie, bordereau d\'entrée)
            $table->string('reference_document', 50)
                  ->nullable()
                  ->comment('N° de bon ou bordereau');

            // Motif du mouvement
            $table->string('motif', 255)
                  ->nullable()
                  ->comment('Motif : don, achat, distribution, etc.');

            // Destinataire ou fournisseur
            $table->string('destinataire', 255)
                  ->nullable()
                  ->comment('Bénéficiaire ou fournisseur');

            // Projet lié (optionnel)
            $table->foreignId('projet_id')
                  ->nullable()
                  ->constrained('projets')
                  ->onDelete('set null')
                  ->comment('Projet associé au mouvement');

            // Utilisateur qui a effectué le mouvement
            $table->foreignId('effectue_par')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->comment('Admin qui a effectué le mouvement');

            // Notes
            $table->text('notes')
                  ->nullable()
                  ->comment('Notes sur le mouvement');

            // Timestamps
            $table->timestamps();

            // Index pour la recherche rapide
            $table->index('stock_id');
            $table->index('type_mouvement');
            $table->index('date_mouvement');
            $table->index('projet_id');
            $table->index('reference_document');
        });
    }

    /**
     * Annuler la migration (supprimer la table).
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements_stocks');
    }
};