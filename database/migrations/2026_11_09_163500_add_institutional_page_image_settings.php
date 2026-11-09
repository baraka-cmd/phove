<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ajoute les paramètres d'images des pages institutionnelles.
     *
     * Les valeurs restent nulles au départ afin de conserver les images
     * de secours déjà présentes dans les vues jusqu'au premier téléversement.
     */
    public function up(): void
    {
        $now = now();

        DB::table('settings')->insertOrIgnore([
            [
                'key' => 'page_assemblee_generale_image',
                'value' => null,
                'type' => 'image',
                'group' => 'pages',
                'label' => 'Image — Assemblée Générale',
                'description' => 'Photo affichée dans la section Assemblée Générale de la page Gouvernance. JPG, PNG ou WebP, 2 Mo maximum.',
                'ordre' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'page_mission_image',
                'value' => null,
                'type' => 'image',
                'group' => 'pages',
                'label' => 'Image — Mission & Vision',
                'description' => 'Photo principale affichée sur la page Mission & Vision. JPG, PNG ou WebP, 2 Mo maximum.',
                'ordre' => 11,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'page_zones_intervention_image',
                'value' => null,
                'type' => 'image',
                'group' => 'pages',
                'label' => 'Image — Zones d’intervention',
                'description' => 'Photo affichée dans la section Zones d’intervention de la page Qui sommes-nous. JPG, PNG ou WebP, 2 Mo maximum.',
                'ordre' => 12,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'page_organisation_fonctionnement_image',
                'value' => null,
                'type' => 'image',
                'group' => 'pages',
                'label' => 'Image — Fonctionnement de l’organisation',
                'description' => 'Photo affichée à côté de la section « Comment fonctionne notre organisation » sur la page Organigramme. JPG, PNG ou WebP, 2 Mo maximum.',
                'ordre' => 13,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'page_assemblee_generale_image',
            'page_mission_image',
            'page_zones_intervention_image',
            'page_organisation_fonctionnement_image',
        ])->delete();
    }
};
