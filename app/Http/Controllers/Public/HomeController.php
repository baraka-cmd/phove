<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Partenaire;
use App\Models\Media;
use App\Models\Projet;
use App\Models\Temoignage;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil publique.
     */
    public function index(): View
    {
        // --------------------------------------------
        // Articles récents
        // --------------------------------------------
        $articles = Article::where('statut', 'publie')
            ->where('date_publication', '<=', now())
            ->orderBy('epingle', 'desc')
            ->orderBy('date_publication', 'desc')
            ->take(3)
            ->get();

        // --------------------------------------------
        // Projets récents
        // --------------------------------------------
        $projets = Projet::where('publier', true)
            ->with('partenaires')
            ->orderByRaw("CASE WHEN statut = 'en_cours' THEN 0 ELSE 1 END")
            ->orderBy('date_debut', 'desc')
            ->take(3)
            ->get();

        // --------------------------------------------
        // Partenaires actifs
        // --------------------------------------------
        $partenaires = Partenaire::where('statut', 'actif')
            ->orderBy('nom')
            ->take(6)
            ->get();

        // --------------------------------------------
        // Les neuf photos publiées les plus récentes de la galerie.
        $galerieAccueil = Media::query()
            ->where('publier', true)
            ->where('type', 'image')
            ->whereNotNull('fichier')
            ->where('fichier', '!=', '')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(9)
            ->get();

        // Témoignages approuvés (visibles publiquement)
        // --------------------------------------------
        $temoignages = Temoignage::where('statut', 'approuve')
            ->with('projet:id,titre,slug')
            ->orderBy('ordre', 'asc')
            ->orderBy('date_validation', 'desc')
            ->take(3)
            ->get();

        // --------------------------------------------
        // Compteur global
        // --------------------------------------------
        $totalProjets = Projet::where('publier', true)->count();

        return view('public.home', compact(
            'articles',
            'projets',
            'partenaires',
            'galerieAccueil',
            'temoignages',
            'totalProjets'
        ));
    }
}