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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            
            // Expéditeur
            $table->string('nom');
            $table->string('email');
            $table->string('telephone', 20)->nullable();
            $table->string('organisation')->nullable();
            
            // Message
            $table->string('sujet');
            $table->text('message');
            
            // Nature du contact
            $table->enum('motif', [
                'partenariat',
                'don',
                'adhesion',
                'benevolat',
                'information',
                'presse',
                'plainte',
                'autre'
            ])->default('information');
            
            // Suivi
            $table->boolean('lu')->default(false);
            $table->dateTime('date_lecture')->nullable();
            $table->boolean('repondu')->default(false);
            $table->dateTime('date_reponse')->nullable();
            $table->text('note_interne')->nullable();
            
            // Qui a traité le message
            $table->foreignId('traite_par')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            // Statut
            $table->enum('statut', [
                'nouveau',
                'en_cours',
                'traite',
                'archive'
            ])->default('nouveau');
            
            // Horodatage
            $table->timestamps();
            
            // Index
            $table->index('lu');
            $table->index('statut');
            $table->index('motif');
            $table->index('created_at');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};