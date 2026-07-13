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
        Schema::create('dons', function (Blueprint $table) {
            $table->id();
            
            // Informations sur le donateur
            $table->string('donateur_nom');
            $table->string('donateur_email')->nullable();
            $table->string('donateur_telephone', 20)->nullable();
            $table->string('donateur_adresse')->nullable();
            
            // Type de donateur
            $table->enum('type_donateur', [
                'particulier',
                'organisation',
                'etat',
                'anonyme'
            ])->default('particulier');
            
            // Montant et devise
            $table->decimal('montant', 15, 2);
            $table->string('devise')->default('USD');
            
            // Type de don
            $table->enum('type_don', [
                'ponctuel',
                'recurrent'
            ])->default('ponctuel');
            
            // Affectation du don
            $table->foreignId('projet_id')
                  ->nullable()
                  ->constrained('projets')
                  ->onDelete('set null');
            
            // Mode de paiement
            $table->enum('mode_paiement', [
                'especes',
                'virement_bancaire',
                'mobile_money',
                'carte_bancaire',
                'paypal',
                'autre'
            ])->default('especes');
            
            // Référence de transaction
            $table->string('reference_transaction')->nullable();
            
            // Date du don
            $table->date('date_don');
            
            // Reçu fiscal
            $table->boolean('recu_genere')->default(false);
            $table->string('numero_recu')->nullable();
            $table->date('date_recu')->nullable();
            
            // Message du donateur
            $table->text('message_donateur')->nullable();
            
            // Statut
            $table->enum('statut', [
                'confirme',
                'en_attente',
                'annule'
            ])->default('confirme');
            
            // Qui a enregistré le don (si saisi manuellement par l'admin)
            $table->foreignId('enregistre_par')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            // Horodatage
            $table->timestamps();
            
            // Index
            $table->index('date_don');
            $table->index('type_don');
            $table->index('statut');
            $table->index('projet_id');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('dons');
    }
};