<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Article;
use App\Models\Partenaire;
use App\Models\Projet;
use App\Models\Temoignage;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Génère le sitemap XML du site public.
     */
    public function index(): Response
    {
        $urls = [];

        // ═══════════════════════════════════════════
        // 1. Pages statiques principales
        // ═══════════════════════════════════════════
        $pagesStatiques = [
            ['url' => url('/'),                    'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => url('/qui-sommes-nous'),     'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => url('/mission-vision'),      'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => url('/nos-valeurs'),         'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => url('/gouvernance'),         'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => url('/organigramme'),        'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => url('/partenaires'),         'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => url('/domaines-intervention'), 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => url('/projets'),             'priority' => '1.0', 'changefreq' => 'weekly'],
            ['url' => url('/actualites'),          'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => url('/galerie'),             'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => url('/temoignages'),         'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => url('/transparence'),        'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => url('/faire-un-don'),        'priority' => '1.0', 'changefreq' => 'monthly'],
            ['url' => url('/devenir-membre'),      'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => url('/devenir-benevole'),    'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => url('/devenir-partenaire'),  'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => url('/contactez-nous'),      'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => url('/mentions-legales'),    'priority' => '0.5', 'changefreq' => 'yearly'],
        ];

        foreach ($pagesStatiques as $page) {
            $urls[] = [
                'loc'        => $page['url'],
                'lastmod'    => now()->toAtomString(),
                'changefreq' => $page['changefreq'],
                'priority'   => $page['priority'],
            ];
        }

        // ═══════════════════════════════════════════
        // 2. Projets publics
        // ═══════════════════════════════════════════
        $projets = Projet::where('publier', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($projets as $projet) {
            $urls[] = [
                'loc'        => route('projets.show', $projet->slug),
                'lastmod'    => $projet->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority'   => '0.9',
            ];
        }

        // ═══════════════════════════════════════════
        // 3. Articles publics
        // ═══════════════════════════════════════════
        $articles = Article::where('statut', 'publie')
            ->where('date_publication', '<=', now())
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($articles as $article) {
            $urls[] = [
                'loc'        => route('actualites.show', $article->slug),
                'lastmod'    => $article->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority'   => '0.8',
            ];
        }

        // ═══════════════════════════════════════════
        // 4. Construction du XML
        // ═══════════════════════════════════════════
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '    <url>' . PHP_EOL;
            $xml .= '        <loc>' . htmlspecialchars($url['loc']) . '</loc>' . PHP_EOL;
            $xml .= '        <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            $xml .= '        <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
            $xml .= '        <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            $xml .= '    </url>' . PHP_EOL;
        }

        $xml .= '</urlset>' . PHP_EOL;

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}