<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Beneficiaire;
use App\Models\Document;
use App\Models\RapportFinancier;
use Illuminate\Http\Request;

class ActualiteController extends Controller
{
    /**
     * Nombre d'articles par page.
     */
    protected int $perPage = 6;

    /**
     * Affiche la liste paginée et filtrée des actualités.
     */
    public function index(Request $request)
    {
        // 1. Récupérer les paramètres de filtre
        $categorie = $request->input('categorie');
        $recherche = $request->input('recherche');

        // 2. Construire la requête de base
        $query = Article::query()
            ->where('statut', 'publie')
            ->where('date_publication', '<=', now())
            ->with('auteur');

        // 3. Appliquer les filtres
        if ($categorie) {
            $query->where('categorie', $categorie);
        }

        if ($recherche) {
            $query->where(function ($q) use ($recherche) {
                $q->where('titre', 'like', '%' . $recherche . '%')
                  ->orWhere('contenu', 'like', '%' . $recherche . '%')
                  ->orWhere('extrait', 'like', '%' . $recherche . '%');
            });
        }

        // 4. Trier par date de publication (plus récent d'abord)
        $query->orderBy('date_publication', 'desc');

        // 5. Paginer les résultats
        $articles = $query->paginate($this->perPage)->withQueryString();

        // 6. Articles épinglés (toujours en premier)
        $articlesEpingles = Article::where('statut', 'publie')
            ->where('epingle', true)
            ->where('date_publication', '<=', now())
            ->with('auteur')
            ->orderBy('date_publication', 'desc')
            ->get();

        // 7. Catégories pour le filtre
        $categories = [
            'actualite' => 'Actualités',
            'projet' => 'Projets',
            'evenement' => 'Événements',
            'rapport' => 'Rapports',
            'temoignage' => 'Témoignages',
            'communique' => 'Communiqués',
        ];

        // 8. Statistiques
        $totalArticles = Article::where('statut', 'publie')
            ->where('date_publication', '<=', now())
            ->count();

        return view('public.actualites.index', compact(
            'articles',
            'articlesEpingles',
            'categories',
            'categorie',
            'recherche',
            'totalArticles'
        ));
    }

    /**
     * Affiche le détail d'un article.
     */
    public function show(string $slug)
    {
        // 1. Trouver l'article par son slug
        $article = Article::where('slug', $slug)
            ->where('statut', 'publie')
            ->where('date_publication', '<=', now())
            ->with('auteur', 'projet')
            ->firstOrFail();

        // 2. Incrémenter le compteur de vues
        $article->increment('vues');

        // 3. Articles connexes (même catégorie, exclure l'article actuel)
        $articlesConnexes = Article::where('statut', 'publie')
            ->where('date_publication', '<=', now())
            ->where('id', '!=', $article->id)
            ->where('categorie', $article->categorie)
            ->with('auteur')
            ->orderBy('date_publication', 'desc')
            ->limit(3)
            ->get();

        return view('public.actualites.show', compact('article', 'articlesConnexes'));
    }

    /**
     * Affiche la page des témoignages.
     */
    public function temoignages(Request $request)
    {
        // 1. Récupérer le filtre de zone
        $zone = $request->input('zone');

        // 2. Construire la requête
        $query = Beneficiaire::where('temoignage_public', true)
            ->whereNotNull('temoignage')
            ->with('projet');

        // 3. Appliquer le filtre de zone
        if ($zone) {
            $query->where('localisation', 'like', '%' . $zone . '%');
        }

        // 4. Paginer
        $temoignages = $query->orderBy('created_at', 'desc')
            ->paginate(9)
            ->withQueryString();

        // 5. Zones distinctes pour le filtre
        $zones = Beneficiaire::where('temoignage_public', true)
            ->whereNotNull('temoignage')
            ->distinct()
            ->pluck('localisation')
            ->map(function ($loc) {
                return trim($loc);
            })
            ->unique()
            ->sort()
            ->values();

        return view('public.temoignages', compact('temoignages', 'zones', 'zone'));
    }

    /**
     * Affiche la page transparence.
     */
    public function transparence(Request $request)
    {
        // 1. Onglet actif
        $tab = $request->input('tab', 'financiers');

        // 2. Rapports financiers publics
        $rapportsFinanciers = RapportFinancier::where('public', true)
            ->where('statut', 'publie')
            ->orderBy('periode', 'desc')
            ->get();

        // 3. Rapports annuels (documents de type rapport_annuel)
        $rapportsAnnuels = Document::where('public', true)
            ->where('type', 'rapport_annuel')
            ->orderBy('date_document', 'desc')
            ->get();

        // 4. Documents officiels
        $documentsOfficiels = Document::where('public', true)
            ->whereIn('type', ['statuts', 'roi', 'proces_verbal', 'convention', 'guide_procedure', 'politique_interne', 'communique_officiel'])
            ->orderBy('date_document', 'desc')
            ->get();

        return view('public.transparence', compact(
            'tab',
            'rapportsFinanciers',
            'rapportsAnnuels',
            'documentsOfficiels'
        ));
    }
}