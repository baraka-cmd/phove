<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Nombre d'articles par page.
     */
    protected int $perPage = 15;

    /**
     * Catégories d'articles disponibles.
     */
    protected array $categories = [
        'actualite' => 'Actualité',
        'projet' => 'Projet',
        'evenement' => 'Événement',
        'rapport' => 'Rapport',
        'temoignage' => 'Témoignage',
        'communique' => 'Communiqué',
    ];

    /**
     * Affiche la liste paginée et filtrée des articles.
     */
    public function index(Request $request)
    {
        $query = Article::with('auteur');

        // Filtre par catégorie
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Recherche
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('titre', 'like', '%' . $recherche . '%')
                  ->orWhere('contenu', 'like', '%' . $recherche . '%')
                  ->orWhere('extrait', 'like', '%' . $recherche . '%');
            });
        }

        $articles = $query->orderBy('date_publication', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        // Statistiques
        $totalArticles = Article::count();
        $articlesPublies = Article::where('statut', 'publie')->count();
        $articlesBrouillons = Article::where('statut', 'brouillon')->count();
        $articlesEpingles = Article::where('epingle', true)->count();

        return view('admin.articles.index', compact(
            'articles',
            'totalArticles',
            'articlesPublies',
            'articlesBrouillons',
            'articlesEpingles'
        ));
    }

    /**
     * Affiche le formulaire de création d'un article.
     */
    public function create()
    {
        $projets = Projet::orderBy('titre')->get();
        return view('admin.articles.create', compact('projets'));
    }

    /**
     * Enregistre un nouvel article.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'contenu' => ['required', 'string'],
            'extrait' => ['nullable', 'string', 'max:500'],
            'categorie' => ['required', 'in:actualite,projet,evenement,rapport,temoignage,communique'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'statut' => ['required', 'in:brouillon,publie'],
            'date_publication' => ['nullable', 'date'],
            'projet_id' => ['nullable', 'exists:projets,id'],
            'epingle' => ['nullable', 'boolean'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'contenu.required' => 'Le contenu est obligatoire.',
            'categorie.required' => 'La catégorie est obligatoire.',
        ]);

        // Générer le slug
        $validated['slug'] = Str::slug($validated['titre']);
        
        // Vérifier l'unicité du slug
        $count = 1;
        $originalSlug = $validated['slug'];
        while (Article::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        // Upload image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('articles/images', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['epingle'] = $request->boolean('epingle');

        // Date de publication
        if ($validated['statut'] === 'publie' && empty($validated['date_publication'])) {
            $validated['date_publication'] = now();
        }

        Article::create($validated);

        return redirect()->route('admin.articles.index')
            ->with('success', 'L\'article a été créé avec succès.');
    }

    /**
     * Affiche la fiche détaillée d'un article (prévisualisation admin).
     */
    public function show(Article $article)
    {
        $article->load('auteur', 'projet');
        return view('admin.articles.show', compact('article'));
    }

    /**
     * Affiche le formulaire de modification d'un article.
     */
    public function edit(Article $article)
    {
        $projets = Projet::orderBy('titre')->get();
        return view('admin.articles.edit', compact('article', 'projets'));
    }

    /**
     * Met à jour un article.
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'contenu' => ['required', 'string'],
            'extrait' => ['nullable', 'string', 'max:500'],
            'categorie' => ['required', 'in:actualite,projet,evenement,rapport,temoignage,communique'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'statut' => ['required', 'in:brouillon,publie'],
            'date_publication' => ['nullable', 'date'],
            'projet_id' => ['nullable', 'exists:projets,id'],
            'epingle' => ['nullable', 'boolean'],
        ]);

        // Mettre à jour le slug si le titre change
        if ($article->titre !== $validated['titre']) {
            $validated['slug'] = Str::slug($validated['titre']);
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Article::where('slug', $validated['slug'])->where('id', '!=', $article->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count;
                $count++;
            }
        }

        // Upload image
        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $validated['image'] = $request->file('image')->store('articles/images', 'public');
        }

        $validated['epingle'] = $request->boolean('epingle');

        // Date de publication
        if ($validated['statut'] === 'publie' && empty($validated['date_publication']) && !$article->date_publication) {
            $validated['date_publication'] = now();
        }

        $article->update($validated);

        return redirect()->route('admin.articles.index')
            ->with('success', 'L\'article a été mis à jour avec succès.');
    }

    /**
     * Supprime un article.
     */
    public function destroy(Article $article)
    {
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'L\'article a été supprimé définitivement.');
    }

    /**
     * Publie ou dépublie un article.
     */
    public function togglePublier(Article $article)
    {
        if ($article->statut === 'publie') {
            $article->update(['statut' => 'brouillon']);
            $message = 'L\'article a été dépublié.';
        } else {
            $article->update([
                'statut' => 'publie',
                'date_publication' => $article->date_publication ?? now(),
            ]);
            $message = 'L\'article a été publié.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Épingle ou désépingle un article.
     */
    public function toggleEpingler(Article $article)
    {
        $article->update(['epingle' => !$article->epingle]);
        $message = $article->epingle ? 'L\'article a été épinglé.' : 'L\'article a été désépinglé.';

        return redirect()->back()->with('success', $message);
    }
}