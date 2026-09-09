<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration (créer la table controles_trimestriels).
     */
    public function up(): void
    {
        Schema::create('controles_trimestriels', function (Blueprint $table) {
            $table->id();

            $table->string('reference', 30)->unique();
            $table->enum('trimestre', ['T1', 'T2', 'T3', 'T4']);
            $table->year('annee');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->date('date_controle');
            $table->enum('type_controle', ['financier', 'operationnel', 'global'])->default('global');
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'valide', 'rejete'])->default('planifie');
            $table->string('controleur', 255)->nullable();
            $table->foreignId('controle_par')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('total_entrees', 14, 2)->default(0);
            $table->decimal('total_sorties', 14, 2)->default(0);
            $table->decimal('ecart', 14, 2)->default(0);
            $table->boolean('conformite')->default(true);
            $table->text('documents_verifies')->nullable();
            $table->text('observations')->nullable();
            $table->text('recommandations')->nullable();
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            $table->date('date_validation')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('trimestre');
            $table->index('annee');
            $table->index('statut');
            $table->index('type_controle');
            $table->index('date_controle');
            $table->unique(['trimestre', 'annee']);
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('controles_trimestriels');
    }
};