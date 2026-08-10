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
        Schema::create('temoignages', function (Blueprint $table) {
            $table->id();

            // Projet concerné
            $table->foreignId('projet_id')
                  ->nullable()
                  ->constrained('projets')
                  ->onDelete('set null');

            // Informations du bénéficiaire
            $table->string('nom', 100);
            $table->string('post_nom', 100)->nullable();
            $table->string('prenom', 100)->nullable();
            $table->enum('sexe', ['M', 'F']);
            $table->unsignedTinyInteger('age')->nullable();

            // Catégorie du bénéficiaire
            $table->enum('categorie', [
                'femme',
                'enfant',
                'handicape',
                'troisieme_age',
                'orphelin',
                'deplace_interne',
                'refugie',
                'rapatrie',
                'autre'
            ]);

            // Localisation
            $table->string('province', 100)->nullable();
            $table->string('localisation', 255)->nullable();

            // Contenu du témoignage
            $table->text('contenu');

            // Photo du bénéficiaire (optionnelle)
            $table->string('photo')->nullable();

            // Statut de modération
            $table->enum('statut', [
                'en_attente',
                'approuve',
                'rejete',
                'archive'
            ])->default('en_attente');

            // Motif de rejet
            $table->text('motif_rejet')->nullable();

            // Dates
            $table->dateTime('date_soumission')->nullable();
            $table->dateTime('date_validation')->nullable();

            // Admin qui a traité le témoignage
            $table->foreignId('traite_par')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            // Ordre d'affichage
            $table->integer('ordre')->default(0);

            // Horodatage
            $table->timestamps();

            // Index
            $table->index('statut');
            $table->index('projet_id');
            $table->index('categorie');
            $table->index('province');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('temoignages');
    }
};