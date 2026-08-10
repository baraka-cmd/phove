<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Paramètres par défaut de l'application.
     */
    protected array $defaults = [
        // ============================================
        // IDENTITÉ
        // ============================================
        [
            'key' => 'site_name',
            'value' => 'AHD-RDC',
            'type' => 'text',
            'group' => 'identite',
            'label' => 'Nom du site',
            'description' => 'Le nom officiel de l\'organisation affiché dans le header et le footer.',
            'ordre' => 1,
        ],
        [
            'key' => 'site_slogan',
            'value' => 'Être ensemble pour le bien de la communauté',
            'type' => 'text',
            'group' => 'identite',
            'label' => 'Slogan',
            'description' => 'Le slogan de l\'organisation.',
            'ordre' => 2,
        ],
        [
            'key' => 'site_description',
            'value' => 'Action Humanitaire pour le Développement en République Démocratique du Congo (AHD-RDC) est une organisation non gouvernementale engagée dans le développement communautaire, la protection, la cohésion sociale et la promotion de la paix.',
            'type' => 'textarea',
            'group' => 'identite',
            'label' => 'Description du site',
            'description' => 'Une courte description de l\'organisation pour le SEO et les partages.',
            'ordre' => 3,
        ],
        [
            'key' => 'site_logo',
            'value' => null,
            'type' => 'image',
            'group' => 'identite',
            'label' => 'Logo du site',
            'description' => 'Logo principal affiché dans le header. Format recommandé : PNG, 200×60 px.',
            'ordre' => 4,
        ],
        [
            'key' => 'site_favicon',
            'value' => null,
            'type' => 'image',
            'group' => 'identite',
            'label' => 'Favicon',
            'description' => 'Icône du site affichée dans l\'onglet du navigateur. Format : PNG ou ICO, 32×32 px.',
            'ordre' => 5,
        ],

        // ============================================
        // CONTACT
        // ============================================
        [
            'key' => 'contact_adresse',
            'value' => 'Minova, Territoire de Kalehe, Province du Sud-Kivu, République Démocratique du Congo',
            'type' => 'text',
            'group' => 'contact',
            'label' => 'Adresse',
            'description' => 'Adresse postale complète du siège social.',
            'ordre' => 1,
        ],
        [
            'key' => 'contact_telephone',
            'value' => '+243 994 656 250',
            'type' => 'phone',
            'group' => 'contact',
            'label' => 'Téléphone',
            'description' => 'Numéro de téléphone principal.',
            'ordre' => 2,
        ],
        [
            'key' => 'contact_email',
            'value' => 'contact@ahd-rdc.org',
            'type' => 'email',
            'group' => 'contact',
            'label' => 'Email de contact',
            'description' => 'Adresse email affichée publiquement.',
            'ordre' => 3,
        ],
        [
            'key' => 'contact_bp',
            'value' => null,
            'type' => 'text',
            'group' => 'contact',
            'label' => 'Boîte Postale',
            'description' => 'Numéro de boîte postale (optionnel).',
            'ordre' => 4,
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
            'description' => 'URL de la page Facebook.',
            'ordre' => 1,
        ],
        [
            'key' => 'social_twitter',
            'value' => null,
            'type' => 'url',
            'group' => 'social',
            'label' => 'Twitter / X',
            'description' => 'URL du compte Twitter/X.',
            'ordre' => 2,
        ],
        [
            'key' => 'social_linkedin',
            'value' => null,
            'type' => 'url',
            'group' => 'social',
            'label' => 'LinkedIn',
            'description' => 'URL de la page LinkedIn.',
            'ordre' => 3,
        ],
        [
            'key' => 'social_youtube',
            'value' => null,
            'type' => 'url',
            'group' => 'social',
            'label' => 'YouTube',
            'description' => 'URL de la chaîne YouTube.',
            'ordre' => 4,
        ],
        [
            'key' => 'social_instagram',
            'value' => null,
            'type' => 'url',
            'group' => 'social',
            'label' => 'Instagram',
            'description' => 'URL du compte Instagram.',
            'ordre' => 5,
        ],
        [
            'key' => 'social_whatsapp',
            'value' => '+243 994 656 250',
            'type' => 'phone',
            'group' => 'social',
            'label' => 'WhatsApp',
            'description' => 'Numéro WhatsApp (format international).',
            'ordre' => 6,
        ],

        // ============================================
        // PAGES STATIQUES
        // ============================================
        [
            'key' => 'page_qui_sommes_nous',
            'value' => 'Action Humanitaire pour le Développement en République Démocratique du Congo « AHD-RDC » est une organisation non gouvernementale engagée dans le développement communautaire, la protection, la cohésion sociale et la promotion de la paix dans la partie Est de la RD Congo et dans tout le pays en général. Elle a été créée dans la région marquée par des conflits intercommunautaires, les guerres et les crises humanitaires, où elle travaille pour renforcer la résilience communautaire par l\'auto prise en charge communautaire.',
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Page "Qui sommes-nous ?"',
            'description' => 'Contenu de la page institutionnelle.',
            'ordre' => 1,
        ],
        [
            'key' => 'page_mission_vision',
            'value' => 'Notre mission est de contribuer à la résolution des conflits fonciers et au renforcement des mécanismes de consolidation de la paix entre les communautés, au relèvement communautaire et à la promotion des droits de la femme en République Démocratique du Congo. Notre vision est de vivre dans une société pacifiée sans conflits communautaires violents, où règne l\'équilibre social et où les droits des femmes sont respectés.',
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Page "Mission & Vision"',
            'description' => 'Contenu de la page mission et vision.',
            'ordre' => 2,
        ],
        [
            'key' => 'page_nos_valeurs',
            'value' => 'Les valeurs d\'AHD-RDC sont : le professionnalisme, la transparence, la redevabilité, la compassion et la sensibilité au genre.',
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Page "Nos valeurs"',
            'description' => 'Contenu de la page des valeurs.',
            'ordre' => 3,
        ],
        [
            'key' => 'page_mentions_legales',
            'value' => 'AHD-RDC est une association sans but lucratif de droit congolais créée le 7 janvier 2017 à Minova, Territoire de Kalehe, Province du Sud-Kivu, République Démocratique du Congo. Directeur de publication : Dr AKSANTI DUNIA Steeng. Hébergement : [à compléter].',
            'type' => 'textarea',
            'group' => 'pages',
            'label' => 'Page "Mentions légales"',
            'description' => 'Contenu des mentions légales.',
            'ordre' => 4,
        ],

        // ============================================
        // SEO
        // ============================================
        [
            'key' => 'meta_title',
            'value' => 'AHD-RDC — Action Humanitaire pour le Développement en RD Congo',
            'type' => 'text',
            'group' => 'seo',
            'label' => 'Meta titre',
            'description' => 'Titre affiché dans les résultats de recherche (60-70 caractères recommandé).',
            'ordre' => 1,
        ],
        [
            'key' => 'meta_description',
            'value' => 'AHD-RDC est une ONG humanitaire engagée dans le développement communautaire, la protection des femmes et enfants, la consolidation de la paix et la sécurité alimentaire en République Démocratique du Congo.',
            'type' => 'textarea',
            'group' => 'seo',
            'label' => 'Meta description',
            'description' => 'Description affichée dans les résultats de recherche (150-160 caractères recommandé).',
            'ordre' => 2,
        ],
        [
            'key' => 'meta_keywords',
            'value' => 'ONG, humanitaire, développement, RDC, Congo, paix, sécurité alimentaire, protection, femmes, enfants',
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
            'value' => 'Notre site est actuellement en maintenance. Nous serons de retour très bientôt. Merci de votre patience.',
            'type' => 'textarea',
            'group' => 'maintenance',
            'label' => 'Message de maintenance',
            'description' => 'Message affiché pendant la maintenance.',
            'ordre' => 2,
        ],
    ];

    /**
     * Exécuter le seeder.
     */
    public function run(): void
    {
        foreach ($this->defaults as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Paramètres par défaut créés avec succès (' . count($this->defaults) . ' paramètres).');
    }
}