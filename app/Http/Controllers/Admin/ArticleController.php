<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Media;
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

    /*
    |--------------------------------------------------------------------------
    | INDEX / CREATE / STORE
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche la liste paginée et filtrée des articles.
     */
    public function index(Request $request)
    {
        $query = Article::with('auteur');

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

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

        $totalArticles      = Article::count();
        $articlesPublies    = Article::where('statut', 'publie')->count();
        $articlesBrouillons = Article::where('statut', 'brouillon')->count();
        $articlesEpingles   = Article::where('epingle', true)->count();

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

            // ─────────────────────────────────────────
            // GALERIE MÉDIA
            // ─────────────────────────────────────────
            'galerie' => ['nullable', 'array', 'max:20'],
            'galerie.*' => [
                'file',
                'mimes:jpeg,jpg,png,webp,mp4,webm,ogg',
                'max:51200', // 50 Mo
            ],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'contenu.required' => 'Le contenu est obligatoire.',
            'categorie.required' => 'La catégorie est obligatoire.',
            'galerie.array' => 'La galerie doit être un ensemble de fichiers.',
            'galerie.max' => 'Vous ne pouvez pas uploader plus de 20 fichiers à la fois.',
            'galerie.*.file' => 'Chaque élément doit être un fichier valide.',
            'galerie.*.mimes' => 'Les fichiers doivent être au format JPG, PNG, WebP, MP4, WebM ou OGG.',
            'galerie.*.max' => 'Chaque fichier ne doit pas dépasser 50 Mo.',
        ]);

        // Slug unique
        $validated['slug'] = $this->genererSlugUnique($validated['titre']);

        // Upload image de couverture
        if ($request->hasFile('image')) {
            $validated['image'] = media_store($request->file('image'), 'articles/images');
        }

        $validated['user_id'] = auth()->id();
        $validated['epingle'] = $request->boolean('epingle');

        if ($validated['statut'] === 'publie' && empty($validated['date_publication'])) {
            $validated['date_publication'] = now();
        }

        // Créer l'article
        $article = Article::create($validated);

        // ─────────────────────────────────────────
        // GALERIE MÉDIA
        // ─────────────────────────────────────────
        if ($request->hasFile('galerie')) {
            $this->uploadGalerie($request, $article);
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'L\'article a été créé avec succès.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW / EDIT / UPDATE
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche la fiche détaillée d'un article.
     */
    public function show(Article $article)
    {
        $article->load([
            'auteur',
            'projet',
            'medias' => function ($q) {
                $q->orderBy('ordre')->orderBy('created_at', 'desc');
            },
        ]);

        return view('admin.articles.show', compact('article'));
    }

    /**
     * Affiche le formulaire de modification d'un article.
     */
    public function edit(Article $article)
    {
        $projets = Projet::orderBy('titre')->get();

        $article->load(['medias' => function ($q) {
            $q->orderBy('ordre')->orderBy('created_at', 'desc');
        }]);

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

            // ─────────────────────────────────────────
            // GALERIE MÉDIA
            // ─────────────────────────────────────────
            'galerie' => ['nullable', 'array', 'max:20'],
            'galerie.*' => [
                'file',
                'mimes:jpeg,jpg,png,webp,mp4,webm,ogg',
                'max:51200',
            ],
            'medias_supprimer' => ['nullable', 'array'],
            'medias_supprimer.*' => ['integer', 'exists:medias,id'],
        ]);

        // Slug unique (si le titre change)
        if ($article->titre !== $validated['titre']) {
            $validated['slug'] = $this->genererSlugUnique($validated['titre'], $article->id);
        }

        // Upload image de couverture
        if ($request->hasFile('image')) {
            if ($article->image) {
                media_delete($article->image);
            }
            $validated['image'] = media_store($request->file('image'), 'articles/images');
        }

        $validated['epingle'] = $request->boolean('epingle');

        if ($validated['statut'] === 'publie' && empty($validated['date_publication']) && !$article->date_publication) {
            $validated['date_publication'] = now();
        }

        // Mettre à jour l'article
        $article->update($validated);

        // ─────────────────────────────────────────
        // SUPPRESSION DES MÉDIAS COCHÉS
        // ─────────────────────────────────────────
        if ($request->filled('medias_supprimer')) {
            $mediasASupprimer = Media::whereIn('id', $request->input('medias_supprimer'))
                ->where('article_id', $article->id)
                ->get();

            foreach ($mediasASupprimer as $media) {
                if ($media->fichier) {
                    media_delete($media->fichier);
                }
                if ($media->miniature) {
                    media_delete($media->miniature);
                }
                $media->delete();
            }
        }

        // ─────────────────────────────────────────
        // AJOUT DES NOUVEAUX MÉDIAS
        // ─────────────────────────────────────────
        if ($request->hasFile('galerie')) {
            $this->uploadGalerie($request, $article);
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'L\'article a été mis à jour avec succès.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY / TOGGLES
    |--------------------------------------------------------------------------
    */

    /**
     * Supprime un article.
     */
    public function destroy(Article $article)
    {
        // Supprimer l'image de couverture
        if ($article->image) {
            media_delete($article->image);
        }

        // Supprimer les médias liés
        foreach ($article->medias as $media) {
            if ($media->fichier) {
                media_delete($media->fichier);
            }
            if ($media->miniature) {
                media_delete($media->miniature);
            }
            $media->delete();
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

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES PRIVÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Génère un slug unique.
     */
    protected function genererSlugUnique(string $titre, ?int $excludeId = null): string
    {
        $slug = Str::slug($titre);
        $originalSlug = $slug;
        $count = 1;

        $query = Article::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;

            $query = Article::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Upload une galerie de fichiers (images et vidéos) liée à un article.
     */
    /**
 * Upload une galerie de fichiers (images et vidéos) liée à un article.
 */
protected function uploadGalerie(Request $request, Article $article): void
{
    $formatsImages = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $formatsVideos = ['mp4', 'webm', 'ogg'];

    $ordre = Media::where('article_id', $article->id)->max('ordre') ?? 0;

    foreach ($request->file('galerie') as $fichier) {
        try {
            $extension = strtolower($fichier->getClientOriginalExtension());
            $type      = in_array($extension, $formatsImages) ? 'image' : 'video';

            $dossier = $type === 'image' ? 'medias/images' : 'medias/videos';

            // Upload via helper (Cloudinary en prod, local en dev)
            $chemin = media_store($fichier, $dossier);

            if (!$chemin) {
                continue; // échec d'upload → on saute
            }

            $ordre++;

            Media::create([
                'titre'       => pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME),
                'type'        => $type,
                'fichier'     => $chemin,
                'miniature'   => null, // Cloudinary génère les miniatures à la volée
                'taille'      => $fichier->getSize(),
                'article_id'  => $article->id,
                'album'       => 'Article : ' . Str::limit($article->titre, 60),
                'publier'     => true,
                'ordre'       => $ordre,
                'user_id'     => auth()->id(),
                'auteur'      => auth()->user()->nom ?? 'Admin',
            ]);
        } catch (\Throwable $e) {
            // Silencieux : on continue avec les fichiers suivants
        }
    }
}

}