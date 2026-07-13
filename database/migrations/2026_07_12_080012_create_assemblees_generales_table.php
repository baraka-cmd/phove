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
        Schema::create('assemblees_generales', function (Blueprint $table) {
            $table->id();
            
            // Type d'AG (article 25 des statuts)
            $table->enum('type', [
                'ordinaire',
                'extraordinaire'
            ]);
            
            // Date et lieu
            $table->date('date_reunion');
            $table->string('lieu');
            
            // Ordre du jour
            $table->text('ordre_du_jour')->nullable();
            
            // Participation (article 25 : majorité simple = quorum)
            $table->integer('membres_total')->default(0);
            $table->integer('membres_presents')->default(0);
            $table->integer('membres_absents')->default(0);
            $table->boolean('quorum_atteint')->default(false);
            
            // Décisions et résolutions
            $table->text('decisions')->nullable();
            $table->text('resolutions')->nullable();
            
            // Procès-verbal
            $table->string('pv_fichier')->nullable();
            
            // Statut
            $table->enum('statut', [
                'planifiee',
                'tenue',
                'annulee',
                'reportee'
            ])->default('planifiee');
            
            // Horodatage
            $table->timestamps();
            
            // Index
            $table->index('date_reunion');
            $table->index('type');
            $table->index('statut');
        });
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('assemblees_generales');
    }
};