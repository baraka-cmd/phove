<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter la migration.
     */
    public function up(): void
    {
        // Étape 1 : Rendre projet_id nullable pour les indicateurs globaux par domaine
        Schema::table('indicateurs_projet', function (Blueprint $table) {
            $table->foreignId('projet_id')
                  ->nullable()
                  ->change();
        });

        // Étape 2 : Ajouter la colonne domaine
        Schema::table('indicateurs_projet', function (Blueprint $table) {
            $table->enum('domaine', [
                'sante_nutrition',
                'education',
                'protection',
                'agriculture',
                'infrastructure',
                'economique',
                'spirituel',
                'psychosocial',
                'judiciaire',
            ])->nullable()
              ->after('projet_id')
              ->comment('Domaine d\'intervention PHOVE associé à cet indicateur');

            $table->index('domaine');
        });

        // Étape 3 : Migrer les indicateurs existants vers les nouveaux domaines
        DB::statement("
            UPDATE indicateurs_projet ip
            INNER JOIN projets p ON ip.projet_id = p.id
            SET ip.domaine = CASE
                WHEN p.domaine = 'sante_nutrition' THEN 'sante_nutrition'
                WHEN p.domaine = 'education' THEN 'education'
                WHEN p.domaine = 'protection' THEN 'protection'
                WHEN p.domaine = 'securite_alimentaire' THEN 'agriculture'
                WHEN p.domaine = 'infrastructure' THEN 'infrastructure'
                WHEN p.domaine = 'economique' THEN 'economique'
                WHEN p.domaine = 'spirituel' THEN 'spirituel'
                WHEN p.domaine = 'judiciaire' THEN 'judiciaire'
                WHEN p.domaine = 'wash' THEN 'sante_nutrition'
                WHEN p.domaine = 'environnement' THEN 'agriculture'
                WHEN p.domaine = 'paix' THEN 'protection'
                WHEN p.domaine = 'genre' THEN 'protection'
                ELSE NULL
            END
            WHERE ip.projet_id IS NOT NULL
        ");
    }

    /**
     * Annuler la migration.
     */
    public function down(): void
    {
        // Supprimer le domaine
        Schema::table('indicateurs_projet', function (Blueprint $table) {
            $table->dropIndex(['domaine']);
            $table->dropColumn('domaine');
        });

        // Remettre projet_id NOT NULL
        Schema::table('indicateurs_projet', function (Blueprint $table) {
            $table->foreignId('projet_id')
                  ->nullable(false)
                  ->change();
        });
    }
};