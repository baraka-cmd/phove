<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration (créer la table salaires).
     */
    public function up(): void
    {
        Schema::create('salaires', function (Blueprint $table) {
            $table->id();

            // Lien vers le membre (personnel rémunéré)
            $table->foreignId('membre_id')
                  ->constrained('membres')
                  ->onDelete('cascade')
                  ->comment('Membre (personnel) concerné par ce salaire');

            // Lien vers le poste occupé (organe)
            $table->foreignId('organe_id')
                  ->nullable()
                  ->constrained('organes')
                  ->onDelete('set null')
                  ->comment('Poste occupé dans l\'organigramme');

            // Période de paie (format YYYY-MM)
            $table->string('mois', 7)
                  ->comment('Période de paie (format YYYY-MM)');

            // Salaire de base
            $table->decimal('salaire_base', 12, 2)
                  ->default(0)
                  ->comment('Salaire de base');

            // Primes et indemnités
            $table->decimal('primes', 12, 2)
                  ->default(0)
                  ->comment('Primes, indemnités et avantages');

            // Retenues (impôts, avances, cotisations)
            $table->decimal('retenues', 12, 2)
                  ->default(0)
                  ->comment('Retenues : impôts, avances, cotisations');

            // Salaire net à payer
            $table->decimal('salaire_net', 12, 2)
                  ->default(0)
                  ->comment('Salaire net = base + primes - retenues');

            // Mode de paiement
            $table->enum('mode_paiement', [
                'especes',
                'virement',
                'mobile_money',
                'cheque',
            ])->default('especes')
              ->comment('Mode de paiement');

            // Date effective du paiement
            $table->date('date_paiement')
                  ->nullable()
                  ->comment('Date effective du paiement');

            // Statut du salaire
            $table->enum('statut', [
                'brouillon',
                'valide',
                'paye',
                'annule',
            ])->default('brouillon')
              ->comment('Statut : brouillon, valide, payé, annulé');

            // Référence unique du bulletin de salaire
            $table->string('reference', 50)
                  ->unique()
                  ->comment('Référence unique du bulletin (BUL-YYYYMM-XXXX)');

            // Admin qui a validé
            $table->foreignId('valide_par')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Admin qui a validé le salaire');

            // Admin qui a effectué le paiement
            $table->foreignId('paye_par')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Admin qui a effectué le paiement');

            // Notes
            $table->text('notes')
                  ->nullable()
                  ->comment('Notes sur ce salaire');

            // Timestamps
            $table->timestamps();

            // Index pour la recherche rapide
            $table->index('membre_id');
            $table->index('organe_id');
            $table->index('mois');
            $table->index('statut');
            $table->index('date_paiement');

            // Contrainte d'unicité : un seul salaire par membre par mois
            $table->unique(['membre_id', 'mois']);
        });
    }

    /**
     * Annuler la migration (supprimer la table).
     */
    public function down(): void
    {
        Schema::dropIfExists('salaires');
    }
};