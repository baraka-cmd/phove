<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Paramètres par défaut de l'application PHOVE.
     *
     * PHOVE — Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies
     * Siège : Rue Georges BUSHU N°092, Q. Katoyi, Commune de Karisimbi, Goma, Nord-Kivu, RD Congo
     * Devise : « Compassion avec la population »
     */
    protected array $defaults = [
        // ============================================
        // IDENTITÉ
        // ============================================
        [
            'key' => 'site_name',
            'value' => 'PHOVE',
            'type' => 'text',
            'group' => 'identite',
            'label' => 'Nom du site',
            'description' => 'Le sigle officiel de l\'organisation affiché dans le header et le footer.',
            'ordre' => 1,
        ],
        [
            'key' => 'site_full_name',
            'value' => 'Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies',
            'type' => 'text',
            'group' => 'identite',
            'label' => 'Nom complet',
            'description' => 'La dénomination complète officielle de l\'ONG.',
            'ordre' => 2,
        ],
        [
            'key' => 'site_slogan',
            'value' => 'Compassion avec la population',
            'type' => 'text',
            'group' => 'identite',
            'label' => 'Devise / Slogan',
            'description' => 'La devise officielle de l\'organisation.',
            'ordre' => 3,
        ],
        [
            'key' => 'site_description',
            'value' => 'PHOVE (Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies) est une organisation non gouvernementale apolitique à caractère social et spirituel, créée le 30 août 2012 à Goma. Sa mission est de venir en aide aux personnes vulnérables : orphelins, veuves, personnes de 3ème âge et personnes démunies, en leur offrant un accompagnement spirituel, social, éducatif, sanitaire et économique.',
            'type' => 'textarea',
            'group' => 'identite',
            'label' => 'Description du site',
            'description' => 'Une description complète de l\'organisation pour le SEO et les partages.',
            'ordre' => 4,
        ],
        [
            'key' => 'site_logo',
            'value' => 'logo-phove.png',
            'type' => 'image',
            'group' => 'identite',
            'label' => 'Logo du site',
            'description' => 'Logo officiel PHOVE. Format recommandé : PNG, fond transparent.',
            'ordre' => 5,
        ],
        [
            'key' => 'site_favicon',
            'value' => 'favicon-phove.png',
            'type' => 'image',
            'group' => 'identite',
            'label' => 'Favicon',
            'description' => 'Icône du site affichée dans l\'onglet du navigateur. Format recommandé : PNG, 32×32 px.',
            'ordre' => 6,
        ],

        // ============================================
        // CONTACT
        // ============================================
        [
            'key' => 'contact_adresse',
            'value' => 'Rue Georges BUSHU N°092, Quartier Katoyi, Commune de Karisimbi, Goma, Nord-Kivu, République Démocratique du Congo',
            'type' => 'text',
            'group' => 'contact',
            'label' => 'Adresse',
            'description' => 'Adresse postale complète du siège social.',
            'ordre' => 1,
        ],
        [
            'key' => 'contact_repere',
            'value' => 'UACO / Maranata, entrée président',
            'type' => 'text',
            'group' => 'contact',
            'label' => 'Repère géographique',
            'description' => 'Point de repère pour faciliter la localisation du siège.',
            'ordre' => 2,
        ],
        [
            'key' => 'contact_telephone_1',
            'value' => '+243 970 872 910',
            'type' => 'phone',
            'group' => 'contact',
            'label' => 'Téléphone principal 1',
            'description' => 'Numéro de téléphone principal de l\'organisation.',
            'ordre' => 3,
        ],
        [
            'key' => 'contact_telephone_2',
            'value' => '+243 819 009 131',
            'type' => 'phone',
            'group' => 'contact',
            'label' => 'Téléphone principal 2',
            'description' => 'Numéro de téléphone secondaire.',
            'ordre' => 4,
        ],
        [
            'key' => 'contact_telephone_3',
            'value' => '+243 893 844 776',
            'type' => 'phone',
            'group' => 'contact',
            'label' => 'Téléphone secondaire 1',
            'description' => 'Numéro de téléphone supplémentaire.',
            'ordre' => 5,
        ],
        [
            'key' => 'contact_telephone_4',
            'value' => '+243 902 512 262',
            'type' => 'phone',
            'group' => 'contact',
            'label' => 'Téléphone secondaire 2',
            'description' => 'Numéro de téléphone supplémentaire.',
            'ordre' => 6,
        ],
        [
            'key' => 'contact_email',
            'value' => 'phovemonde@gmail.com',
            'type' => 'email',
            'group' => 'contact',
            'label' => 'Email de contact',
            'description' => 'Adresse email officielle affichée publiquement.',
            'ordre' => 7,
        ],
        [
            'key' => 'contact_bp',
            'value' => null,
            'type' => 'text',
            'group' => 'contact',
            'label' => 'Boîte Postale',
            'description' => 'Numéro de boîte postale (optionnel).',
            'ordre' => 8,
        ],

        // ============================================
        // RÉSEAUX SOCIAUX
        // ============================================
        [
            'key' => 'social_facebook',
            'value' => null,
            'type' => 'url',
            'group' => 'social',
            'label' => 'Facebook',
            'description' => 'URL de la page Facebook officielle.',
            'ordre' => 1,
        ],
        [
            'key' => 'social_twitter',
            'value' => null,
            'type' => 'url',
            'group' => 'social',
            'label' => 'Twitter / X',
            'description' => 'URL du compte Twitter/X officiel.',
            'ordre' => 2,
        ],
        [
            'key' => 'social_linkedin',
            'value' => null,
            'type' => 'url',
            'group' => 'social',
            'label' => 'LinkedIn',
            'description' => 'URL de la page LinkedIn officielle.',
            'ordre' => 3,
        ],
        [
            'key' => 'social_youtube',
            'value' => null,
            'type' => 'url',
            'group' => 'social',
            'label' => 'YouTube',
            'description' => 'URL de la chaîne YouTube officielle.',
            'ordre' => 4,
        ],
        [
            'key' => 'social_instagram',
            'value' => null,
            'type' => 'url',
            'group' => 'social',
            'label' => 'Instagram',
            'description' => 'URL du compte Instagram officiel.',
            'ordre' => 5,
        ],
        [
            'key' => 'social_whatsapp',
            'value' => '+243 970 872 910',
            'type' => 'phone',
            'group' => 'social',
            'label' => 'WhatsApp',
            'description' => 'Numéro WhatsApp officiel (format international).',
            'ordre' => 6,
        ],

        // ============================================
        // PAGES STATIQUES
        // ============================================
        [
            'key' => 'page_qui_sommes_nous',
            'value' => 'PHOVE (Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies) est une organisation non gouvernementale apolitique à caractère social et spirituel, créée le 30 août 2012 à Goma, en République Démocratique du Congo. Fondée par l\'Apôtre Philippe KABOY M. après 40 jours de jeûne et de prière sur la montagne KALAGANE à Bukavu, l\'organisation est née d\'une inspiration divine reçue le 20 avril 2012. Sa mission est de venir en aide aux personnes vulnérables : orphelins, veuves, personnes de 3ème âge et personnes démunies.',
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Page "Qui sommes-nous ?"',
            'description' => 'Contenu de la page institutionnelle.',
            'ordre' => 1,
        ],
        [
            'key' => 'page_mission_vision',
            'value' => 'Notre mission est d\'accomplir la mission de Dieu : « Aime ton prochain comme toi-même » (Romain 13:8). Nous encadrons et accompagnons les orphelins, veuves et personnes démunies dans les domaines spirituel et social tels que : éducation, agriculture, pêche, élevage, menuiserie, maçonnerie, mécanique, coupe et couture, art, esthétique, art culinaire, activités socio-économiques et sanitaires. Notre vision est de construire de bons citoyens qui servent leur pays et le monde entier.',
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Page "Mission & Vision"',
            'description' => 'Contenu de la page mission et vision.',
            'ordre' => 2,
        ],
        [
            'key' => 'page_nos_valeurs',
            'value' => 'Les valeurs fondamentales de PHOVE sont : la compassion, l\'amour du prochain, la charité, la solidarité, la foi en Dieu, l\'intégrité, la transparence et le service aux plus démunis. Notre devise est : « Compassion avec la population ».',
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Page "Nos valeurs"',
            'description' => 'Contenu de la page des valeurs.',
            'ordre' => 3,
        ],
        [
            'key' => 'page_mentions_legales',
            'value' => 'PHOVE (Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies) est une organisation non gouvernementale apolitique à caractère social, créée le 30 août 2012 à Goma, République Démocratique du Congo. Siège social : Rue Georges BUSHU N°092, Quartier Katoyi, Commune de Karisimbi, Goma, Nord-Kivu, RD Congo. Directeur Général et Coordonnateur : Apôtre Philippe KABOYI MUSAMUGUMA. Email : phovemonde@gmail.com.',
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Page "Mentions légales"',
            'description' => 'Contenu des mentions légales.',
            'ordre' => 4,
        ],

        // ============================================
        // CONTENU DE L'ACCUEIL ET PAGES ÉDITORIALES
        // Valeurs nulles jusqu'à leur configuration par l'administrateur.
        [
            'key' => 'home_hero_badge',
            'value' => null,
            'type' => 'text',
            'group' => 'pages',
            'label' => 'Accueil — Badge du bandeau principal',
            'description' => 'Courte accroche affichée au-dessus du titre principal.',
            'ordre' => 10,
        ],
        [
            'key' => 'home_hero_title',
            'value' => null,
            'type' => 'text',
            'group' => 'pages',
            'label' => 'Accueil — Titre principal',
            'description' => 'Titre principal du bandeau d’accueil.',
            'ordre' => 11,
        ],
        [
            'key' => 'home_hero_description',
            'value' => null,
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Accueil — Texte principal',
            'description' => 'Texte de présentation sous le titre principal.',
            'ordre' => 12,
        ],
        [
            'key' => 'home_hero_image_1',
            'value' => null,
            'type' => 'image',
            'group' => 'pages',
            'label' => 'Accueil — Image du bandeau 1',
            'description' => 'Première image du slider de la page d’accueil.',
            'ordre' => 13,
        ],
        [
            'key' => 'home_hero_image_2',
            'value' => null,
            'type' => 'image',
            'group' => 'pages',
            'label' => 'Accueil — Image du bandeau 2',
            'description' => 'Deuxième image du slider de la page d’accueil.',
            'ordre' => 14,
        ],
        [
            'key' => 'home_hero_image_3',
            'value' => null,
            'type' => 'image',
            'group' => 'pages',
            'label' => 'Accueil — Image du bandeau 3',
            'description' => 'Troisième image du slider de la page d’accueil.',
            'ordre' => 15,
        ],
        [
            'key' => 'home_about_title',
            'value' => null,
            'type' => 'text',
            'group' => 'pages',
            'label' => 'Accueil — Titre de présentation',
            'description' => 'Titre de la section Qui sommes-nous sur la page d’accueil.',
            'ordre' => 16,
        ],
        [
            'key' => 'home_about_description',
            'value' => null,
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Accueil — Présentation courte',
            'description' => 'Texte de présentation affiché sur la page d’accueil.',
            'ordre' => 17,
        ],
        [
            'key' => 'page_mission_content',
            'value' => null,
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Page Mission — Contenu',
            'description' => 'Texte éditorial de la mission de PHOVE.',
            'ordre' => 18,
        ],
        [
            'key' => 'page_vision_content',
            'value' => null,
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Page Vision — Contenu',
            'description' => 'Texte éditorial de la vision de PHOVE.',
            'ordre' => 19,
        ],

        [
            'key' => 'page_value_compassion',
            'value' => null,
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Valeurs — Compassion',
            'description' => 'Texte descriptif de la valeur compassion sur la page Nos valeurs.',
            'ordre' => 20,
        ],
        [
            'key' => 'page_value_solidarite',
            'value' => null,
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Valeurs — Solidarité',
            'description' => 'Texte descriptif de la valeur solidarité sur la page Nos valeurs.',
            'ordre' => 21,
        ],
        [
            'key' => 'page_value_foi',
            'value' => null,
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Valeurs — Foi en Dieu',
            'description' => 'Texte descriptif de la valeur foi en dieu sur la page Nos valeurs.',
            'ordre' => 22,
        ],
        [
            'key' => 'page_value_charite',
            'value' => null,
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Valeurs — Charité',
            'description' => 'Texte descriptif de la valeur charité sur la page Nos valeurs.',
            'ordre' => 23,
        ],
        [
            'key' => 'page_value_integrite',
            'value' => null,
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Valeurs — Intégrité',
            'description' => 'Texte descriptif de la valeur intégrité sur la page Nos valeurs.',
            'ordre' => 24,
        ],
        [
            'key' => 'page_value_transparence',
            'value' => null,
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Valeurs — Transparence',
            'description' => 'Texte descriptif de la valeur transparence sur la page Nos valeurs.',
            'ordre' => 25,
        ],

        // SEO
        // ============================================
        [
            'key' => 'meta_title',
            'value' => 'PHOVE — Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies',
            'type' => 'text',
            'group' => 'seo',
            'label' => 'Meta titre',
            'description' => 'Titre affiché dans les résultats de recherche (60-70 caractères recommandé).',
            'ordre' => 1,
        ],
        [
            'key' => 'meta_description',
            'value' => 'PHOVE est une ONG humanitaire congolaise qui vient en aide aux orphelins, veuves et personnes démunies : éducation, santé, agriculture, formation professionnelle, assistance sociale et spirituelle en RD Congo.',
            'type' => 'textarea',
            'group' => 'seo',
            'label' => 'Meta description',
            'description' => 'Description affichée dans les résultats de recherche (150-160 caractères recommandé).',
            'ordre' => 2,
        ],
        [
            'key' => 'meta_keywords',
            'value' => 'PHOVE, ONG, humanitaire, orphelins, veuves, personnes démunies, RDC, Congo, Goma, Nord-Kivu, aide sociale, agriculture, éducation, santé',
            'type' => 'text',
            'group' => 'seo',
            'label' => 'Mots-clés',
            'description' => 'Mots-clés séparés par des virgules.',
            'ordre' => 3,
        ],

        // ============================================
        // MAINTENANCE
        // ============================================
        [
            'key' => 'maintenance_mode',
            'value' => '0',
            'type' => 'switch',
            'group' => 'maintenance',
            'label' => 'Mode maintenance',
            'description' => 'Activez pour afficher une page de maintenance aux visiteurs.',
            'ordre' => 1,
        ],
        [
            'key' => 'maintenance_message',
            'value' => 'Notre site est actuellement en maintenance. Nous serons de retour très bientôt. Merci de votre patience. Que Dieu vous bénisse.',
            'type' => 'textarea',
            'group' => 'maintenance',
            'label' => 'Message de maintenance',
            'description' => 'Message affiché pendant la maintenance.',
            'ordre' => 2,
        ],
    ];

    /**
     * Exécuter le seeder.
     *
     * Crée uniquement les paramètres manquants sans écraser les valeurs existantes
     * dans la table `settings`.
     */
    public function run(): void
    {
        foreach ($this->defaults as $setting) {
            // Ne jamais écraser les valeurs éditées dans l'administration lors d'un reseed.
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Paramètres PHOVE créés avec succès (' . count($this->defaults) . ' paramètres).');
    }
}