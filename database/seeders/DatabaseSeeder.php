<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Exécuter tous les seeders.
     */
    public function run(): void
    {
        $this->call([
            AHDSeeder::class,
        ]);
    }
}