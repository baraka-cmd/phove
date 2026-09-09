<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Exécuter tous les seeders de l'application PHOVE.
     */
    public function run(): void
    {
        $this->call([
            PHOVESeeder::class,        // Contenu par défaut (projets, partenaires, membres, articles, etc.)
            SettingSeeder::class,    // Paramètres PHOVE (identité, contact, social, pages, SEO, maintenance)
        ]);
    }
}