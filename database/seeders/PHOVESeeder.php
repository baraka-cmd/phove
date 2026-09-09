<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Membre;
use App\Models\Partenaire;
use App\Models\Projet;
use App\Models\Organe;
use Illuminate\Support\Facades\Hash;
use App\Models\StructureTerritoriale;

class PHOVESeeder extends Seeder
{
    /**
     * Exécuter les seeds PHOVE.
     *
     * PHOVE — Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies
     * Créée le 30 août 2012 à Goma, Nord-Kivu, RD Congo
     * Fondateur : Apôtre Philippe KABOYI MUSAMUGUMA
     */
    public function run(): void
    {
        // =============================================
        // 1. SUPER ADMIN
        // =============================================
        $superAdmin = User::updateOrCreate(
            ['email' => 'phovemonde@gmail.com'],
            [
                'nom' => 'KABOYI MUSAMUGUMA Philippe',
                'telephone' => '+243 970 872 910',
                'mot_de_passe' => Hash::make('Phove@2026'),
                'role' => 'super_admin',
                'statut' => 'actif',
            ]
        );

        $this->command->info('✅ Super Admin créé : phovemonde@gmail.com / Phove@2026');

        // =============================================
// 2. LES 10 MEMBRES FONDATEURS
// Source : Statuts signés le 30 août 2012 à Goma
// =============================================

$fondateurs = [
    [
        'nom' => 'KABOYI',
        'post_nom' => 'MUSAMUGUMA',
        'prenom' => 'Philippe',
        'sexe' => 'M',
        'adresse' => 'Rue Georges BUSHU N°092, Q. Katoyi, Goma',
        'telephone' => '+243 970 872 910',
        'email' => 'phovemonde@gmail.com',
        'categorie' => 'fondateur',
        'profession' => 'Apôtre missionnaire',
        'fonction' => 'Directeur Général et Coordonnateur du Programme',
        'organe' => 'conseil_administration',
        'poste' => 'Directeur Général et Coordonnateur du Programme',
    ],
    [
        'nom' => 'LUMOO',
        'post_nom' => 'WETEMWAMI',
        'prenom' => 'Justine',
        'sexe' => 'F',
        'adresse' => 'Q. Katoyi, Goma',
        'telephone' => '+243 819 009 131',
        'email' => 'lumoo.justine@phove.org',
        'categorie' => 'fondateur',
        'profession' => 'Intercesseur',
        'fonction' => 'Chef de Mission',
        'organe' => 'conseil_administration',
        'poste' => 'Chef de Mission',
    ],
    [
        'nom' => 'KAMBALE',
        'post_nom' => 'KAMATHE',
        'prenom' => 'Nestor',
        'sexe' => 'M',
        'adresse' => 'Q. Katoyi, Goma',
        'telephone' => '+243 893 844 776',
        'email' => 'kambale.nestor@phove.org',
        'categorie' => 'fondateur',
        'profession' => 'Révérend Pasteur',
        'fonction' => 'Chef de Programme',
        'organe' => 'conseil_administration',
        'poste' => 'Chef de Programme',
    ],
    [
        'nom' => 'MUGISHO',
        'post_nom' => '',
        'prenom' => 'Christian',
        'sexe' => 'M',
        'adresse' => 'Q. Katoyi, Goma',
        'telephone' => '+243 902 512 262',
        'email' => 'mugisho.christian@phove.org',
        'categorie' => 'fondateur',
        'profession' => 'Pasteur',
        'fonction' => 'Secrétaire Administratif',
        'organe' => 'conseil_administration',
        'poste' => 'Secrétaire Administratif',
    ],
    [
        'nom' => 'KAHAMBU',
        'post_nom' => 'MUTANGI',
        'prenom' => 'Julienne',
        'sexe' => 'F',
        'adresse' => 'Q. Katoyi, Goma',
        'telephone' => '+243 970 000 105',
        'email' => 'kahambu.julienne@phove.org',
        'categorie' => 'fondateur',
        'profession' => 'Prophétesse',
        'fonction' => 'Chargé de Finance',
        'organe' => 'commission_controle',
        'poste' => 'Chargé de Finance',
    ],
    [
        'nom' => 'MUGOLI',
        'post_nom' => 'NKINDI',
        'prenom' => 'Lydie',
        'sexe' => 'F',
        'adresse' => 'Q. Katoyi, Goma',
        'telephone' => '+243 970 000 106',
        'email' => 'mugoli.lydie@phove.org',
        'categorie' => 'fondateur',
        'profession' => 'Évangéliste',
        'fonction' => 'Chargé de la Logistique',
        'organe' => 'commission_controle',
        'poste' => 'Chargé de la Logistique',
    ],
    [
        'nom' => 'REX',
        'post_nom' => 'MUNGUIKO',
        'prenom' => 'Lutgarde',
        'sexe' => 'F',
        'adresse' => 'Q. Katoyi, Goma',
        'telephone' => '+243 970 000 107',
        'email' => 'rex.lutgarde@phove.org',
        'categorie' => 'fondateur',
        'profession' => 'Pasteur',
        'fonction' => 'Chargé de Transport',
        'organe' => 'commission_controle',
        'poste' => 'Chargé de Transport',
    ],
    [
        'nom' => 'MWAYUMA',
        'post_nom' => 'BYAKITA',
        'prenom' => 'Lydia',
        'sexe' => 'F',
        'adresse' => 'Q. Katoyi, Goma',
        'telephone' => '+243 970 000 108',
        'email' => 'mwayuma.lydia@phove.org',
        'categorie' => 'fondateur',
        'profession' => 'Docteur',
        'fonction' => 'Chargé des Programmes Sanitaires',
        'organe' => 'direction_executive',
        'poste' => 'Chargé des Programmes Sanitaires',
    ],
    [
        'nom' => 'KUBURHANWA',
        'post_nom' => 'TSHENTWALI',
        'prenom' => 'Dénis',
        'sexe' => 'M',
        'adresse' => 'Q. Katoyi, Goma',
        'telephone' => '+243 970 000 109',
        'email' => 'kuburhanwa.denis@phove.org',
        'categorie' => 'fondateur',
        'profession' => 'Fonctionnaire',
        'fonction' => 'Chargé des Affaires Sociales',
        'organe' => 'direction_executive',
        'poste' => 'Chargé des Affaires Sociales',
    ],
    [
        'nom' => 'KISIMBA',
        'post_nom' => 'KIS',
        'prenom' => 'François',
        'sexe' => 'M',
        'adresse' => 'Q. Katoyi, Goma',
        'telephone' => '+243 970 000 110',
        'email' => 'kisimba.francois@phove.org',
        'categorie' => 'fondateur',
        'profession' => 'Fonctionnaire',
        'fonction' => 'Chargé des Affaires Juridiques',
        'organe' => 'direction_executive',
        'poste' => 'Chargé des Affaires Juridiques',
    ],
];

$membres = [];
foreach ($fondateurs as $index => $data) {
    $membres[$index] = Membre::updateOrCreate(
        [
            'nom' => $data['nom'],
            'post_nom' => $data['post_nom'],
            'prenom' => $data['prenom'],
        ],
        [
            'user_id' => $index === 0 ? $superAdmin->id : null,
            'sexe' => $data['sexe'],
            'adresse' => $data['adresse'],
            'telephone' => $data['telephone'],
            'email' => $data['email'],
            'categorie' => $data['categorie'],
            'date_adhesion' => '2012-08-30',
            'frais_adhesion' => 10.00,
            'cotisation_mensuelle' => 0.00,
            'statut' => 'actif',
        ]
    );

    // Créer l'organe de gouvernance correspondant
    Organe::updateOrCreate(
        [
            'membre_id' => $membres[$index]->id,
            'organe' => $data['organe'],
            'poste' => $data['poste'],
        ],
        [
            'date_debut' => '2012-08-30',
            'date_fin' => null,
            'actif' => true,
            'ordre' => $index + 1,
        ]
    );
}

$this->command->info('✅ 10 membres fondateurs créés avec leurs organes de gouvernance');

        // =============================================
        // 3. PARTENAIRES INTERNATIONAUX
        // Source : Bannières officielles des projets PHOVE
        // =============================================

        $fao = Partenaire::updateOrCreate(
            ['sigle' => 'FAO'],
            [
                'nom' => 'Food and Agriculture Organization of the United Nations',
                'type' => 'international',
                'email' => 'fao@fao.org',
                'site_web' => 'https://www.fao.org',
                'description' => 'Organisation des Nations Unies pour l\'alimentation et l\'agriculture. Partenaire du projet d\'assistance alimentaire et agricole à Muja.',
                'statut' => 'actif',
                'date_debut' => '2024-01-01',
            ]
        );

        $wfp = Partenaire::updateOrCreate(
            ['sigle' => 'WFP'],
            [
                'nom' => 'World Food Programme',
                'type' => 'international',
                'email' => 'wfp@wfp.org',
                'site_web' => 'https://www.wfp.org',
                'description' => 'Programme Alimentaire Mondial (PAM). Partenaire du projet de résilience UNJP/RDC/082/WFP.',
                'statut' => 'actif',
                'date_debut' => '2024-01-01',
            ]
        );

        $ocha = Partenaire::updateOrCreate(
            ['sigle' => 'OCHA'],
            [
                'nom' => 'Office for the Coordination of Humanitarian Affairs',
                'type' => 'international',
                'email' => 'ocha@un.org',
                'site_web' => 'https://www.unocha.org',
                'description' => 'Bureau de la coordination des affaires humanitaires des Nations Unies. Partenaire de l\'atelier de formation des agents humanitaires.',
                'statut' => 'actif',
                'date_debut' => '2025-01-01',
            ]
        );

        $unesco = Partenaire::updateOrCreate(
            ['sigle' => 'UNESCO'],
            [
                'nom' => 'UNESCO',
                'type' => 'international',
                'email' => 'unesco@unesco.org',
                'site_web' => 'https://www.unesco.org',
                'description' => 'Organisation des Nations Unies pour l\'éducation, la science et la culture. Partenaire de l\'atelier de formation des agents humanitaires.',
                'statut' => 'actif',
                'date_debut' => '2025-01-01',
            ]
        );

        $gpe = Partenaire::updateOrCreate(
            ['sigle' => 'GPE'],
            [
                'nom' => 'Global Partnership for Education',
                'type' => 'international',
                'email' => 'info@globalpartnership.org',
                'site_web' => 'https://www.globalpartnership.org',
                'description' => 'Partenariat Mondial pour l\'Éducation. Partenaire de l\'atelier de formation des agents humanitaires.',
                'statut' => 'actif',
                'date_debut' => '2025-01-01',
            ]
        );

        $rdc = Partenaire::updateOrCreate(
            ['sigle' => 'RDC'],
            [
                'nom' => 'République Démocratique du Congo',
                'type' => 'institutionnel',
                'email' => null,
                'site_web' => null,
                'description' => 'État congolais. Partenaire institutionnel de tous les projets PHOVE.',
                'statut' => 'actif',
                'date_debut' => '2012-08-30',
            ]
        );

        $this->command->info('✅ 6 partenaires créés : FAO, WFP, OCHA, UNESCO, GPE, RDC');

        // =============================================
        // 4. PROJETS RÉELS PHOVE
        // Source : Bannières officielles des projets
        // =============================================

        $projet1 = Projet::updateOrCreate(
            ['code_projet' => 'PHOVE-NUT-001'],
            [
                'titre' => 'Appui nutritionnel aux orphelins du village Kiziba I',
                'slug' => 'appui-nutritionnel-orphelins-kiziba',
                'domaine' => 'sante_nutrition',
                'zone_geographique' => 'Village Kiziba I, Territoire de Nyiragongo',
                'province' => 'Nord-Kivu',
                'date_debut' => '2025-01-01',
                'date_fin' => null,
                'budget_total' => 5000.00,
                'budget_utilise' => 2500.00,
                'bailleur' => 'PHOVE',
                'source_financement' => 'Autofinancement',
                'statut' => 'en_cours',
                'description' => 'Distribution de vivres et appui nutritionnel aux orphelins vulnérables du village Kiziba I, dans le territoire de Nyiragongo, Province du Nord-Kivu.',
                'objectifs' => 'Améliorer l\'état nutritionnel de 200 orphelins. Réduire la malnutrition infantile.',
                'resultats_attendus' => '200 orphelins pris en charge nutritionnellement.',
                'publier' => true,
            ]
        );

        $projet2 = Projet::updateOrCreate(
            ['code_projet' => 'FAO-MUJA-2025'],
            [
                'titre' => 'Assistance alimentaire aux orphelins, veuves et personnes démunies du village Muja',
                'slug' => 'assistance-alimentaire-muja-fao',
                'domaine' => 'securite_alimentaire',
                'zone_geographique' => 'Village Muja, Territoire de Nyiragongo',
                'province' => 'Nord-Kivu',
                'date_debut' => '2025-01-01',
                'date_fin' => null,
                'budget_total' => 15000.00,
                'budget_utilise' => 7500.00,
                'bailleur' => 'FAO',
                'source_financement' => 'Partenariat international',
                'statut' => 'en_cours',
                'description' => 'Assistance alimentaire aux orphelins, veuves et personnes démunies du village Muja dans le cadre du projet d\'agriculture appuyé par la FAO.',
                'objectifs' => 'Assurer la sécurité alimentaire de 500 ménages vulnérables. Renforcer la production agricole locale.',
                'resultats_attendus' => '500 ménages assistés. Production agricole augmentée de 30%.',
                'publier' => true,
            ]
        );

        $projet3 = Projet::updateOrCreate(
            ['code_projet' => 'UNJP/RDC/082/WFP'],
            [
                'titre' => 'Projet de résilience UNJP/RDC/082/WFP',
                'slug' => 'projet-resilience-unjp-rdc-082-wfp',
                'domaine' => 'securite_alimentaire',
                'zone_geographique' => 'Territoire de Nyiragongo',
                'province' => 'Nord-Kivu',
                'date_debut' => '2025-06-01',
                'date_fin' => null,
                'budget_total' => 50000.00,
                'budget_utilise' => 20000.00,
                'bailleur' => 'WFP',
                'source_financement' => 'Partenariat international',
                'statut' => 'en_cours',
                'description' => 'Projet de résilience (UNJP/RDC/082/WFP) : Lancement de la distribution des semences vivrières, maraîchères et des outils aratoires dans la Province du Nord-Kivu, territoire de Nyiragongo.',
                'objectifs' => 'Distribuer des semences à 1000 ménages. Fournir des outils aratoires. Renforcer la résilience agricole.',
                'resultats_attendus' => '1000 ménages dotés en semences et outils aratoires.',
                'publier' => true,
            ]
        );

        $projet4 = Projet::updateOrCreate(
            ['code_projet' => 'FORM-AGENTS-2025'],
            [
                'titre' => 'Atelier de formation et capacitation des agents humanitaires',
                'slug' => 'atelier-formation-agents-humanitaires-goma',
                'domaine' => 'education',
                'zone_geographique' => 'Bureau PHOVE de Goma, Karisimbi, Katoyi',
                'province' => 'Nord-Kivu',
                'date_debut' => '2025-03-01',
                'date_fin' => '2025-03-31',
                'budget_total' => 8000.00,
                'budget_utilise' => 8000.00,
                'bailleur' => 'OCHA, UNESCO, GPE',
                'source_financement' => 'Partenariat international',
                'statut' => 'termine',
                'description' => 'Atelier de formation et de capacitation des agents humanitaires au bureau PHOVE de Goma (Karisimbi, Katoyi).',
                'objectifs' => 'Former 50 agents humanitaires. Renforcer les capacités opérationnelles.',
                'resultats_attendus' => '50 agents formés et certifiés.',
                'publier' => true,
            ]
        );

        $this->command->info('✅ 4 projets réels créés avec codes officiels');

        // =============================================
        // 5. ASSOCIATION PROJETS ↔ PARTENAIRES
        // =============================================

        // Projet 1 : Appui nutritionnel — RDC
        $projet1->partenaires()->syncWithoutDetaching([
            $rdc->id => [
                'role' => 'partenaire_institutionnel',
                'contribution' => 0.00,
                'date_debut' => '2025-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Projet 2 : Assistance alimentaire Muja — FAO + RDC
        $projet2->partenaires()->syncWithoutDetaching([
            $fao->id => [
                'role' => 'bailleur_principal',
                'contribution' => 15000.00,
                'date_debut' => '2025-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            $rdc->id => [
                'role' => 'partenaire_institutionnel',
                'contribution' => 0.00,
                'date_debut' => '2025-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Projet 3 : Résilience WFP — WFP + RDC
        $projet3->partenaires()->syncWithoutDetaching([
            $wfp->id => [
                'role' => 'bailleur_principal',
                'contribution' => 50000.00,
                'date_debut' => '2025-06-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            $rdc->id => [
                'role' => 'partenaire_institutionnel',
                'contribution' => 0.00,
                'date_debut' => '2025-06-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Projet 4 : Formation — OCHA + UNESCO + GPE + RDC
        $projet4->partenaires()->syncWithoutDetaching([
            $ocha->id => [
                'role' => 'partenaire_technique',
                'contribution' => 3000.00,
                'date_debut' => '2025-03-01',
                'date_fin' => '2025-03-31',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            $unesco->id => [
                'role' => 'partenaire_technique',
                'contribution' => 3000.00,
                'date_debut' => '2025-03-01',
                'date_fin' => '2025-03-31',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            $gpe->id => [
                'role' => 'partenaire_technique',
                'contribution' => 2000.00,
                'date_debut' => '2025-03-01',
                'date_fin' => '2025-03-31',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            $rdc->id => [
                'role' => 'partenaire_institutionnel',
                'contribution' => 0.00,
                'date_debut' => '2025-03-01',
                'date_fin' => '2025-03-31',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('✅ Associations projets ↔ partenaires créées');


        // =============================================
        // 6b. INDICATEURS PAR DOMAINE PHOVE
        // Source : Articles 8-9 des Statuts
        // =============================================

        $indicateursParDomaine = [
            // ============ SANTÉ & NUTRITION ============
            [
                'projet_id' => null,
                'domaine' => 'sante_nutrition',
                'nom_indicateur' => 'Orphelins pris en charge nutritionnellement',
                'valeur_cible' => 500,
                'valeur_actuelle' => 200,
                'unite' => 'orphelins',
                'description' => 'Nombre d\'orphelins bénéficiant d\'un appui nutritionnel régulier',
            ],
            [
                'projet_id' => null,
                'domaine' => 'sante_nutrition',
                'nom_indicateur' => 'Consultations médicales réalisées',
                'valeur_cible' => 2000,
                'valeur_actuelle' => 850,
                'unite' => 'consultations',
                'description' => 'Nombre de consultations médicales effectuées par les médecins PHOVE',
            ],
            [
                'projet_id' => null,
                'domaine' => 'sante_nutrition',
                'nom_indicateur' => 'Patients traumatisés accompagnés',
                'valeur_cible' => 300,
                'valeur_actuelle' => 120,
                'unite' => 'patients',
                'description' => 'Patients pris en charge par les psychologues PHOVE',
            ],

            // ============ ÉDUCATION & FORMATION ============
            [
                'projet_id' => null,
                'domaine' => 'education',
                'nom_indicateur' => 'Orphelins scolarisés',
                'valeur_cible' => 1000,
                'valeur_actuelle' => 450,
                'unite' => 'orphelins',
                'description' => 'Nombre d\'orphelins inscrits et suivis à l\'école',
            ],
            [
                'projet_id' => null,
                'domaine' => 'education',
                'nom_indicateur' => 'Personnes alphabétisées',
                'valeur_cible' => 500,
                'valeur_actuelle' => 180,
                'unite' => 'personnes',
                'description' => 'Adultes ayant suivi le programme d\'alphabétisation',
            ],
            [
                'projet_id' => null,
                'domaine' => 'education',
                'nom_indicateur' => 'Formations professionnelles délivrées',
                'valeur_cible' => 300,
                'valeur_actuelle' => 95,
                'unite' => 'formations',
                'description' => 'Attestations de formation professionnelle délivrées',
            ],

            // ============ PROTECTION SOCIALE ============
            [
                'projet_id' => null,
                'domaine' => 'protection',
                'nom_indicateur' => 'Veuves assistées',
                'valeur_cible' => 800,
                'valeur_actuelle' => 320,
                'unite' => 'veuves',
                'description' => 'Veuves bénéficiant d\'une assistance sociale régulière',
            ],
            [
                'projet_id' => null,
                'domaine' => 'protection',
                'nom_indicateur' => 'Logements octroyés',
                'valeur_cible' => 100,
                'valeur_actuelle' => 35,
                'unite' => 'logements',
                'description' => 'Logements construits ou réhabilités pour les personnes vulnérables',
            ],
            [
                'projet_id' => null,
                'domaine' => 'protection',
                'nom_indicateur' => 'Dossiers d\'assistance judiciaire traités',
                'valeur_cible' => 150,
                'valeur_actuelle' => 48,
                'unite' => 'dossiers',
                'description' => 'Affaires juridiques accompagnées par PHOVE',
            ],

            // ============ AGRICULTURE & ÉLEVAGE ============
            [
                'projet_id' => null,
                'domaine' => 'agriculture',
                'nom_indicateur' => 'Ménages agricoles soutenus',
                'valeur_cible' => 2000,
                'valeur_actuelle' => 750,
                'unite' => 'ménages',
                'description' => 'Ménages bénéficiant de semences et outils aratoires',
            ],
            [
                'projet_id' => null,
                'domaine' => 'agriculture',
                'nom_indicateur' => 'Semences distribuées',
                'valeur_cible' => 100,
                'valeur_actuelle' => 45,
                'unite' => 'tonnes',
                'description' => 'Tonnes de semences vivrières et maraîchères distribuées',
            ],
            [
                'projet_id' => null,
                'domaine' => 'agriculture',
                'nom_indicateur' => 'Outils aratoires distribués',
                'valeur_cible' => 5000,
                'valeur_actuelle' => 1800,
                'unite' => 'outils',
                'description' => 'Nombre d\'outils aratoires distribués aux ménages',
            ],

            // ============ INFRASTRUCTURES ============
            [
                'projet_id' => null,
                'domaine' => 'infrastructure',
                'nom_indicateur' => 'Routes réhabilitées',
                'valeur_cible' => 50,
                'valeur_actuelle' => 12,
                'unite' => 'km',
                'description' => 'Kilomètres de routes de desserte agricole réhabilitées',
            ],
            [
                'projet_id' => null,
                'domaine' => 'infrastructure',
                'nom_indicateur' => 'Écoles construites ou réhabilitées',
                'valeur_cible' => 20,
                'valeur_actuelle' => 5,
                'unite' => 'écoles',
                'description' => 'Infrastructures scolaires construites ou rénovées',
            ],
            [
                'projet_id' => null,
                'domaine' => 'infrastructure',
                'nom_indicateur' => 'Centres de santé construits',
                'valeur_cible' => 10,
                'valeur_actuelle' => 3,
                'unite' => 'centres de santé',
                'description' => 'Infrastructures sanitaires construites ou réhabilitées',
            ],

            // ============ ACTIVITÉS SOCIO-ÉCONOMIQUES ============
            [
                'projet_id' => null,
                'domaine' => 'economique',
                'nom_indicateur' => 'AGR créées',
                'valeur_cible' => 400,
                'valeur_actuelle' => 130,
                'unite' => 'AGR',
                'description' => 'Activités Génératrices de Revenus créées par les bénéficiaires',
            ],
            [
                'projet_id' => null,
                'domaine' => 'economique',
                'nom_indicateur' => 'Personnes insérées professionnellement',
                'valeur_cible' => 600,
                'valeur_actuelle' => 210,
                'unite' => 'personnes',
                'description' => 'Personnes formées et insérées dans le marché du travail',
            ],

            // ============ ACCOMPAGNEMENT SPIRITUEL ============
            [
                'projet_id' => null,
                'domaine' => 'spirituel',
                'nom_indicateur' => 'Fidèles encadrés spirituellement',
                'valeur_cible' => 5000,
                'valeur_actuelle' => 2000,
                'unite' => 'fidèles',
                'description' => 'Fidèles bénéficiant d\'un accompagnement spirituel régulier',
            ],
            [
                'projet_id' => null,
                'domaine' => 'spirituel',
                'nom_indicateur' => 'Sessions de formation biblique',
                'valeur_cible' => 200,
                'valeur_actuelle' => 75,
                'unite' => 'formations',
                'description' => 'Sessions de formation biblique organisées',
            ],
            [
                'projet_id' => null,
                'domaine' => 'spirituel',
                'nom_indicateur' => 'Prédicateurs actifs',
                'valeur_cible' => 50,
                'valeur_actuelle' => 18,
                'unite' => 'prédicateurs',
                'description' => 'Prédicateurs bénévoles actifs dans l\'organisation',
            ],

            // ============ ACCOMPAGNEMENT PSYCHOSOCIAL ============
            [
                'projet_id' => null,
                'domaine' => 'psychosocial',
                'nom_indicateur' => 'Personnes traumatisées accompagnées',
                'valeur_cible' => 500,
                'valeur_actuelle' => 185,
                'unite' => 'personnes',
                'description' => 'Victimes de guerre et de violences accompagnées psychologiquement',
            ],
            [
                'projet_id' => null,
                'domaine' => 'psychosocial',
                'nom_indicateur' => 'Groupes de parole organisés',
                'valeur_cible' => 100,
                'valeur_actuelle' => 32,
                'unite' => 'groupes',
                'description' => 'Groupes de parole pour la stabilisation mentale des victimes',
            ],

            // ============ ASSISTANCE JUDICIAIRE ============
            [
                'projet_id' => null,
                'domaine' => 'judiciaire',
                'nom_indicateur' => 'Dossiers juridiques traités',
                'valeur_cible' => 200,
                'valeur_actuelle' => 58,
                'unite' => 'dossiers',
                'description' => 'Dossiers d\'assistance judiciaire traités',
            ],
            [
                'projet_id' => null,
                'domaine' => 'judiciaire',
                'nom_indicateur' => 'Personnes défendues',
                'valeur_cible' => 300,
                'valeur_actuelle' => 92,
                'unite' => 'personnes',
                'description' => 'Personnes vulnérables défendues juridiquement',
            ],
        ];

        foreach ($indicateursParDomaine as $indicateur) {
            \App\Models\IndicateurProjet::updateOrCreate(
                [
                    'domaine' => $indicateur['domaine'],
                    'nom_indicateur' => $indicateur['nom_indicateur'],
                    'projet_id' => null,
                ],
                $indicateur + ['date_mise_a_jour' => now()->format('Y-m-d')]
            );
        }

        $this->command->info('✅ ' . count($indicateursParDomaine) . ' indicateurs par domaine créés');

        // =============================================
        // 6. STRUCTURES TERRITORIALES
        // Source : Articles 2c et 11d-g des Statuts PHOVE
        // =============================================

        // Station RDC (Pays)
        $stationRDC = \App\Models\StructureTerritoriale::updateOrCreate(
            [
                'nom' => 'PHOVE RDC',
                'type' => 'station',
            ],
            [
                'pays' => 'République Démocratique du Congo',
                'province' => null,
                'territoire' => null,
                'adresse' => 'Rue Georges BUSHU N°092, Quartier Katoyi, Commune de Karisimbi, Goma',
                'description' => 'Station principale de PHOVE en République Démocratique du Congo. Coordonne toutes les activités au niveau national.',
                'parent_id' => null,
                'statut' => 'actif',
            ]
        );

        // Base Nord-Kivu (Province)
        $baseNordKivu = \App\Models\StructureTerritoriale::updateOrCreate(
            [
                'nom' => 'PHOVE Nord-Kivu',
                'type' => 'base',
            ],
            [
                'pays' => 'République Démocratique du Congo',
                'province' => 'Nord-Kivu',
                'territoire' => null,
                'adresse' => 'Goma, Nord-Kivu',
                'description' => 'Base provinciale de PHOVE au Nord-Kivu. Supervise les activités dans la province.',
                'parent_id' => $stationRDC->id,
                'statut' => 'actif',
            ]
        );

        // Antenne Goma (Commune)
        $antenneGoma = \App\Models\StructureTerritoriale::updateOrCreate(
            [
                'nom' => 'PHOVE Goma',
                'type' => 'antenne',
            ],
            [
                'pays' => 'République Démocratique du Congo',
                'province' => 'Nord-Kivu',
                'territoire' => 'Commune de Karisimbi',
                'adresse' => 'Rue Georges BUSHU N°092, Quartier Katoyi',
                'description' => 'Antenne communale de PHOVE à Goma. Bureau principal et siège social.',
                'parent_id' => $baseNordKivu->id,
                'statut' => 'actif',
            ]
        );

        // Antenne Nyiragongo (Territoire)
        $antenneNyiragongo = \App\Models\StructureTerritoriale::updateOrCreate(
            [
                'nom' => 'PHOVE Nyiragongo',
                'type' => 'antenne',
            ],
            [
                'pays' => 'République Démocratique du Congo',
                'province' => 'Nord-Kivu',
                'territoire' => 'Territoire de Nyiragongo',
                'adresse' => 'Territoire de Nyiragongo, Nord-Kivu',
                'description' => 'Antenne territoriale de PHOVE à Nyiragongo. Zone d\'intervention des projets agricoles et nutritionnels.',
                'parent_id' => $baseNordKivu->id,
                'statut' => 'actif',
            ]
        );

        // Station Rwanda (Pays — extension internationale)
        $stationRwanda = \App\Models\StructureTerritoriale::updateOrCreate(
            [
                'nom' => 'PHOVE Rwanda',
                'type' => 'station',
            ],
            [
                'pays' => 'Rwanda',
                'province' => null,
                'territoire' => null,
                'adresse' => null,
                'description' => 'Station internationale de PHOVE au Rwanda. Extension des activités humanitaires.',
                'parent_id' => null,
                'statut' => 'actif',
            ]
        );

        $this->command->info('✅ 5 structures territoriales créées : 2 stations, 1 base, 2 antennes');

        $this->command->info('');
        $this->command->info('🎉 SEEDER PHOVE TERMINÉ AVEC SUCCÈS !');
    }
}