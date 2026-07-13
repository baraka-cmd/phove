<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Membre;
use App\Models\Partenaire;
use App\Models\Projet;
use App\Models\Organe;
use Illuminate\Support\Facades\Hash;

class AHDSeeder extends Seeder
{
    /**
     * Exécuter les seeds.
     */
    public function run(): void
    {
        // =============================================
        // 1. SUPER ADMIN
        // =============================================
        $superAdmin = User::create([
            'nom' => 'Dr AKSANTI DUNIA Steeng',
            'email' => 'aksanti@ahd-rdc.org',
            'telephone' => '+243 994656250',
            'mot_de_passe' => Hash::make('AhdRdc@2026'),
            'role' => 'super_admin',
            'statut' => 'actif',
        ]);

        $this->command->info('✅ Super Admin créé : aksanti@ahd-rdc.org / AhdRdc@2026');

        // =============================================
        // 2. LES 7 MEMBRES FONDATEURS
        // Source : Statuts signés le 7 janvier 2017 à Minova
        // =============================================

        $fondateurs = [
            [
                'nom' => 'AKSANTI',
                'post_nom' => 'DUNIA',
                'prenom' => 'Steeng',
                'sexe' => 'M',
                'adresse' => 'Minova',
                'telephone' => '+243 994656250',
                'email' => 'aksanti@ahd-rdc.org',
                'categorie' => 'initiateur',
            ],
            [
                'nom' => 'BISIMWA',
                'post_nom' => 'BASHONGA',
                'prenom' => 'Riphin',
                'sexe' => 'M',
                'adresse' => 'Minova',
                'telephone' => '+243 973307807',
                'email' => null,
                'categorie' => 'fondateur',
            ],
            [
                'nom' => 'CHRISTIAN',
                'post_nom' => 'MIRINDI',
                'prenom' => '',
                'sexe' => 'M',
                'adresse' => 'Goma',
                'telephone' => '+243 999210576',
                'email' => null,
                'categorie' => 'fondateur',
            ],
            [
                'nom' => 'MAJINA',
                'post_nom' => 'DUNIA',
                'prenom' => 'Judith',
                'sexe' => 'F',
                'adresse' => 'Goma',
                'telephone' => '+243 972059604',
                'email' => null,
                'categorie' => 'fondateur',
            ],
            [
                'nom' => 'MUGARUKA',
                'post_nom' => 'BASHANGWA',
                'prenom' => 'Léon',
                'sexe' => 'M',
                'adresse' => 'Minova',
                'telephone' => '+243 975105996',
                'email' => null,
                'categorie' => 'fondateur',
            ],
            [
                'nom' => 'OLEMBE',
                'post_nom' => 'MUSANGI',
                'prenom' => 'Grace',
                'sexe' => 'F',
                'adresse' => 'Goma',
                'telephone' => '+243 971346492',
                'email' => null,
                'categorie' => 'fondateur',
            ],
            [
                'nom' => 'NATHALIE',
                'post_nom' => 'BISIMWA',
                'prenom' => 'Julienne',
                'sexe' => 'F',
                'adresse' => 'Minova',
                'telephone' => '+243 970138592',
                'email' => null,
                'categorie' => 'fondateur',
            ],
        ];

        $membres = [];
        foreach ($fondateurs as $index => $data) {
            $membres[$index] = Membre::create([
                'user_id' => $index === 0 ? $superAdmin->id : null,
                'nom' => $data['nom'],
                'post_nom' => $data['post_nom'],
                'prenom' => $data['prenom'],
                'sexe' => $data['sexe'],
                'adresse' => $data['adresse'],
                'telephone' => $data['telephone'],
                'email' => $data['email'],
                'categorie' => $data['categorie'],
                'date_adhesion' => '2017-01-07',
                'frais_adhesion' => 50.00,
                'cotisation_mensuelle' => 50.00,
                'statut' => 'actif',
            ]);
        }

        $this->command->info('✅ 7 membres fondateurs créés');

        // =============================================
        // 3. ORGANES DE GOUVERNANCE
        // Source : PV d'élection du 7 janvier 2017
        // =============================================

        // Conseil d'Administration
        Organe::create([
            'membre_id' => $membres[3]->id, // Judith MAJINA DUNIA
            'organe' => 'conseil_administration',
            'poste' => 'Présidente du Conseil d\'Administration',
            'date_debut' => '2017-01-07',
            'date_fin' => null,
            'actif' => true,
            'ordre' => 1,
        ]);

        Organe::create([
            'membre_id' => $membres[2]->id, // CHRISTIAN MIRINDI
            'organe' => 'conseil_administration',
            'poste' => 'Vice-Président du Conseil d\'Administration',
            'date_debut' => '2017-01-07',
            'date_fin' => null,
            'actif' => true,
            'ordre' => 2,
        ]);

        Organe::create([
            'membre_id' => $membres[1]->id, // BISIMWA BASHONGA Riphin
            'organe' => 'conseil_administration',
            'poste' => 'Membre du Conseil d\'Administration',
            'date_debut' => '2017-01-07',
            'date_fin' => null,
            'actif' => true,
            'ordre' => 3,
        ]);

        Organe::create([
            'membre_id' => $membres[5]->id, // OLEMBE MUSANGI Grace
            'organe' => 'conseil_administration',
            'poste' => 'Membre du Conseil d\'Administration',
            'date_debut' => '2017-01-07',
            'date_fin' => null,
            'actif' => true,
            'ordre' => 4,
        ]);

        // Direction Exécutive
        Organe::create([
            'membre_id' => $membres[0]->id, // Dr AKSANTI DUNIA Steeng
            'organe' => 'direction_executive',
            'poste' => 'Coordonnateur National',
            'date_debut' => '2017-01-07',
            'date_fin' => null,
            'actif' => true,
            'ordre' => 1,
        ]);

        // Commission de Contrôle
        Organe::create([
            'membre_id' => $membres[6]->id, // NATHALIE BISIMWA Julienne
            'organe' => 'commission_controle',
            'poste' => 'Présidente de la Commission de Contrôle',
            'date_debut' => '2017-01-07',
            'date_fin' => null,
            'actif' => true,
            'ordre' => 1,
        ]);

        Organe::create([
            'membre_id' => $membres[4]->id, // MUGARUKA BASHANGWA Léon
            'organe' => 'commission_controle',
            'poste' => 'Secrétaire de la Commission de Contrôle',
            'date_debut' => '2017-01-07',
            'date_fin' => null,
            'actif' => true,
            'ordre' => 2,
        ]);

        $this->command->info('✅ Organes de gouvernance créés');

        // =============================================
        // 4. PARTENAIRES
        // =============================================

        $ca = Partenaire::create([
            'nom' => 'Conseil d\'Administration AHD-RDC',
            'type' => 'local',
            'description' => 'Autofinancement du Conseil d\'Administration',
            'statut' => 'actif',
            'date_debut' => '2017-01-07',
        ]);

        $dmp = Partenaire::create([
            'nom' => 'DMP-RDC',
            'type' => 'local',
            'description' => 'Partenaire technique dans la mise en œuvre des projets de sensibilisation sanitaire',
            'statut' => 'actif',
        ]);

        $this->command->info('✅ Partenaires créés');

        // =============================================
        // 5. PROJETS
        // Source : Section 11 des documents AHD-RDC
        // =============================================

        $projet1 = Projet::create([
            'titre' => 'Soutien à la production maraîchère pour la sécurité alimentaire et la lutte contre les maladies d\'origine hydrique',
            'slug' => 'soutien-production-maraichere-securite-alimentaire',
            'domaine' => 'securite_alimentaire',
            'zone_geographique' => 'Minova et Kirotshe',
            'province' => 'Nord-Kivu et Sud-Kivu',
            'date_debut' => '2026-01-01',
            'date_fin' => null,
            'budget_total' => 15000.00,
            'budget_utilise' => 7500.00,
            'bailleur' => 'Autofinancement du Conseil d\'Administration',
            'source_financement' => 'Autofinancement',
            'statut' => 'en_cours',
            'description' => 'Ce projet vise à soutenir la production maraîchère dans les zones de santé de Minova et Kirotshe pour améliorer la sécurité alimentaire des ménages vulnérables tout en luttant contre les maladies d\'origine hydrique.',
            'objectifs' => 'Améliorer la sécurité alimentaire de 500 ménages. Réduire les maladies hydriques par la sensibilisation.',
            'publier' => true,
        ]);

        $projet2 = Projet::create([
            'titre' => 'Sensibilisation contre le choléra et Ebola',
            'slug' => 'sensibilisation-cholera-ebola-2026',
            'domaine' => 'sante_nutrition',
            'zone_geographique' => 'Minova et Kirotshe',
            'province' => 'Nord-Kivu et Sud-Kivu',
            'date_debut' => '2026-01-01',
            'date_fin' => null,
            'budget_total' => 8000.00,
            'budget_utilise' => 3200.00,
            'bailleur' => 'Autofinancement du Conseil d\'Administration et DMP-RDC',
            'source_financement' => 'Partenariat',
            'statut' => 'en_cours',
            'description' => 'Campagne de sensibilisation des communautés de Minova et Kirotshe sur les mesures de prévention contre le choléra et la maladie à virus Ebola.',
            'objectifs' => 'Sensibiliser 10 000 personnes. Distribuer 2 000 kits d\'hygiène.',
            'publier' => true,
        ]);

        $projet3 = Projet::create([
            'titre' => 'Projet de sécurité alimentaire',
            'slug' => 'projet-securite-alimentaire-2025',
            'domaine' => 'securite_alimentaire',
            'zone_geographique' => 'Kalehe',
            'province' => 'Sud-Kivu',
            'date_debut' => '2025-01-01',
            'date_fin' => '2025-12-31',
            'budget_total' => 12000.00,
            'budget_utilise' => 12000.00,
            'bailleur' => 'Autofinancement du Conseil d\'Administration',
            'source_financement' => 'Autofinancement',
            'statut' => 'termine',
            'description' => 'Projet de renforcement de la sécurité alimentaire des ménages vulnérables dans le territoire de Kalehe.',
            'objectifs' => 'Améliorer la sécurité alimentaire de 400 ménages.',
            'publier' => true,
        ]);

        $projet4 = Projet::create([
            'titre' => 'Promotion de l\'hygiène communautaire',
            'slug' => 'promotion-hygiene-communautaire-2024',
            'domaine' => 'wash',
            'zone_geographique' => 'Minova',
            'province' => 'Sud-Kivu',
            'date_debut' => '2024-01-01',
            'date_fin' => '2024-12-31',
            'budget_total' => 6000.00,
            'budget_utilise' => 6000.00,
            'bailleur' => 'Autofinancement du Conseil d\'Administration',
            'source_financement' => 'Autofinancement',
            'statut' => 'termine',
            'description' => 'Promotion des bonnes pratiques d\'hygiène dans la communauté de Minova pour prévenir les maladies hydriques.',
            'objectifs' => 'Former 200 ménages aux pratiques d\'hygiène. Aménager 10 points d\'eau.',
            'publier' => true,
        ]);

        $projet5 = Projet::create([
            'titre' => 'Sensibilisation contre le choléra et le COVID-19',
            'slug' => 'sensibilisation-cholera-covid19-2020',
            'domaine' => 'sante_nutrition',
            'zone_geographique' => 'Minova',
            'province' => 'Sud-Kivu',
            'date_debut' => '2020-01-01',
            'date_fin' => '2020-12-31',
            'budget_total' => 5000.00,
            'budget_utilise' => 5000.00,
            'bailleur' => 'DMP-RDC',
            'source_financement' => 'Partenariat',
            'statut' => 'termine',
            'description' => 'Campagne de sensibilisation conjointe contre le choléra et la pandémie de COVID-19 dans la zone de Minova.',
            'objectifs' => 'Sensibiliser 8 000 personnes. Distribuer des masques et du savon.',
            'publier' => true,
        ]);

        $this->command->info('✅ 5 projets créés');

        // =============================================
        // 6. ASSOCIATION PROJETS ↔ PARTENAIRES
        // =============================================

        $projet1->partenaires()->attach($ca->id, [
            'role' => 'bailleur_principal',
            'contribution' => 15000.00,
            'date_debut' => '2026-01-01',
        ]);

        $projet2->partenaires()->attach($ca->id, [
            'role' => 'bailleur_principal',
            'contribution' => 5000.00,
            'date_debut' => '2026-01-01',
        ]);

        $projet2->partenaires()->attach($dmp->id, [
            'role' => 'partenaire_technique',
            'contribution' => 3000.00,
            'date_debut' => '2026-01-01',
        ]);

        $projet3->partenaires()->attach($ca->id, [
            'role' => 'bailleur_principal',
            'contribution' => 12000.00,
            'date_debut' => '2025-01-01',
            'date_fin' => '2025-12-31',
        ]);

        $projet4->partenaires()->attach($ca->id, [
            'role' => 'bailleur_principal',
            'contribution' => 6000.00,
            'date_debut' => '2024-01-01',
            'date_fin' => '2024-12-31',
        ]);

        $projet5->partenaires()->attach($dmp->id, [
            'role' => 'partenaire_mise_en_oeuvre',
            'contribution' => 5000.00,
            'date_debut' => '2020-01-01',
            'date_fin' => '2020-12-31',
        ]);

        $this->command->info('✅ Associations projets ↔ partenaires créées');

        $this->command->info('');
        $this->command->info('🎉 SEEDER AHD-RDC TERMINÉ AVEC SUCCÈS !');
    }
}