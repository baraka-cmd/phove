<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration (créer la table users avec les 9 rôles PHOVE).
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Informations personnelles
            $table->string('nom');
            $table->string('email')->unique();
            $table->string('telephone', 20)->nullable();
            
            // Authentification
            $table->string('mot_de_passe');
            $table->rememberToken();
            
            // Rôle et statut — 9 rôles PHOVE directement inclus
            $table->enum('role', [
                'super_admin',
                'dg',
                'chef_mission',
                'charge_programme',
                'secretaire',
                'comptable',
                'caissier',
                'editeur',
                'utilisateur',
            ])->default('utilisateur');
            
            $table->enum('statut', ['actif', 'inactif', 'suspendu'])
                  ->default('actif');
            
            // Photo de profil
            $table->string('photo_profil')->nullable();
            
            // Dernière connexion
            $table->timestamp('derniere_connexion')->nullable();
            
            // Horodatage
            $table->timestamps();
            
            // Index
            $table->index('role');
        });
    }

    /**
     * Annuler la migration (supprimer la table).
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};