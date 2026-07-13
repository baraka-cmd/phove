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
        Schema::create('cotisations', function (Blueprint $table) {
            $table->id();
            
            // Lien vers le membre
            $table->foreignId('membre_id')
                  ->constrained('membres')
                  ->onDelete('cascade');
            
            // Période de cotisation
            $table->string('mois', 7);  // Format : YYYY-MM (ex : 2026-07)
            
            // Montant payé (50$ selon article 39 du ROI)
            $table->decimal('montant', 10, 2)->default(50.00);
            
            // Date du paiement
            $table->date('date_paiement');
            
            // Mode de paiement
            $table->enum('mode_paiement', [
                'especes',
                'virement_bancaire',
                'mobile_money',
                'autre'
            ])->default('especes');
            
            // Référence du paiement (numéro de transaction, bordereau)
            $table->string('reference')->nullable();
            
            // Commentaire éventuel
            $table->text('observation')->nullable();
            
            // Qui a enregistré le paiement
            $table->foreignId('enregistre_par')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            // Horodatage
            $table->timestamps();
            
            // Index pour les recherches fréquentes
            $table->index('membre_id');
            $table->index('mois');
            $table->index('date_paiement');
            
            // Unicité : un seul paiement par membre et par mois
            $table->unique(['membre_id', 'mois']);
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotisations');
    }
};