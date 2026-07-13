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
        Schema::create('membres', function (Blueprint $table) {
            $table->id();
            
            // Lien vers le compte utilisateur (optionnel)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            // Identité complète (format congolais)
            $table->string('nom');
            $table->string('post_nom');
            $table->string('prenom');
            $table->enum('sexe', ['M', 'F']);
            
            // Coordonnées
            $table->string('adresse');
            $table->string('telephone', 20);
            $table->string('email')->nullable();
            
            // Photo passeport (obligatoire selon le ROI)
            $table->string('photo')->nullable();
            
            // Catégorie de membre (statuts, article 13)
            $table->enum('categorie', [
                'initiateur',
                'fondateur',
                'adherent',
                'honneur',
                'sympathisant'
            ]);
            
            // Adhésion
            $table->date('date_adhesion');
            $table->decimal('frais_adhesion', 10, 2)->default(0.00);
            $table->decimal('cotisation_mensuelle', 10, 2)->default(50.00);
            
            // Statut du membre
            $table->enum('statut', [
                'actif',
                'suspendu',
                'exclu',
                'deserteur',
                'demissionnaire',
                'en_attente'
            ])->default('en_attente');
            
            // Sanctions
            $table->text('motif_sanction')->nullable();
            $table->date('date_sanction')->nullable();
            
            // Horodatage
            $table->timestamps();
            
            // Index pour accélérer les recherches fréquentes
            $table->index('categorie');
            $table->index('statut');
            $table->index('nom');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('membres');
    }
};