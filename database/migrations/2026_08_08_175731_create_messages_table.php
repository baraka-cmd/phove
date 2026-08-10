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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            // Expéditeur
            $table->foreignId('expediteur_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Destinataire
            $table->foreignId('destinataire_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Sujet du message
            $table->string('sujet');

            // Contenu du message
            $table->text('contenu');

            // Lecture
            $table->boolean('lu')->default(false);
            $table->dateTime('date_lecture')->nullable();
            $table->foreignId('lu_par')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            // Réponse
            $table->boolean('repondu')->default(false);
            $table->dateTime('date_reponse')->nullable();

            // Archivage (chaque côté indépendamment)
            $table->boolean('archived_exp')->default(false);
            $table->boolean('archived_dest')->default(false);

            // Horodatage
            $table->timestamps();

            // Index
            $table->index('expediteur_id');
            $table->index('destinataire_id');
            $table->index('lu');
            $table->index('created_at');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};