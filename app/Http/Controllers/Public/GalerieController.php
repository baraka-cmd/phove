<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Projet;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Contrôleur public — Galerie Média.
 *
 * Affiche la galerie publique des médias (photos et vidéos) marqués
 * comme publiés par l'administration. Permet un filtrage par type,
 * album, projet et une recherche textuelle.
 *
 * @package App\Http\Controllers\Public
 */
class GalerieController extends Controller
{
    /**
     * Nombre de médias affichés par page.
     */
    protected const PER_PAGE = 24;

    /**
     * Type : image.
     */
    protected const TYPE_IMAGE = 'image';

    /**
     * Type : vidéo.
     */
    protected const TYPE_VIDEO = 'video';

    /**
     * Type de tri : plus récents d'abord (par défaut).
     */
    protected const ORDRE_RECENT = 'recent';

    /**
     * Type de tri : plus anciens d'abord.
     */
    protected const ORDRE_ANCIEN = 'ancien';

    /**
     * Type de tri : alphabétique par titre.
     */
    protected const ORDRE_TITRE = 'titre';

    /**
     * Type de tri : regroupement par album.
     */
    protected const ORDRE_ALBUM = 'album';

    /**
     * Durée du cache des compteurs (en secondes).
     */
    protected const CACHE_STATS_TTL = 300;

    /**
     * Types de médias autorisés (utilisé pour valider le filtre "type").
     *
     * @var array<int, string>
     */
    protected const TYPES_AUTORISES = [
        self::TYPE_IMAGE,
        self::TYPE_VIDEO,
    ];

    /**
     * Ordres de tri autorisés (utilisé pour valider le filtre "ordre").
     *
     * @var array<int, string>
     */
    protected const ORDRES_AUTORISES = [
        self::ORDRE_RECENT,
        self::ORDRE_ANCIEN,
        self::ORDRE_TITRE,
        self::ORDRE_ALBUM,
    ];

    /*
    |--------------------------------------------------------------------------
    | ACTION PRINCIPALE
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche la galerie publique paginée et filtrable.
     *
     * Filtres disponibles (query string) :
     * - recherche   : recherche textuelle (titre, description, lieu, album)
     * - type        : "image" | "video"
     * - album       : nom exact de l'album
     * - projet_id   : identifiant du projet associé
     * - ordre       : "recent" | "ancien" | "titre" | "album"
     *
     * @param  Request  $request
     * @return View
     */
    public function index(Request $request): View
    {
        // ---------------------------------------------------------------------
        // 1. Construction de la requête de base
        // ---------------------------------------------------------------------
        $query = Media::query()
            ->where('publier', true)
            ->with([
                'projet:id,titre,slug',
            ]);

        // ---------------------------------------------------------------------
        // 2. Application des filtres
        // ---------------------------------------------------------------------
        $this->appliquerFiltreType($query, $request);
        $this->appliquerFiltreAlbum($query, $request);
        $this->appliquerFiltreProjet($query, $request);
        $this->appliquerFiltreRecherche($query, $request);

        // ---------------------------------------------------------------------
        // 3. Application du tri
        // ---------------------------------------------------------------------
        $this->appliquerTri($query, $request);

        // ---------------------------------------------------------------------
        // 4. Pagination
        // ---------------------------------------------------------------------
        $medias = $query
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // ---------------------------------------------------------------------
        // 5. Données annexes pour les filtres et statistiques
        // ---------------------------------------------------------------------
        $albums  = $this->recupererAlbums();
        $projets = $this->recupererProjetsAvecMedias();
        $stats   = $this->recupererStatistiques();

        // ---------------------------------------------------------------------
        // 6. Dictionnaires pour les vues
        // ---------------------------------------------------------------------
        $types = [
            self::TYPE_IMAGE => 'Photos',
            self::TYPE_VIDEO => 'Vidéos',
        ];

        $ordres = [
            self::ORDRE_RECENT => 'Plus récents',
            self::ORDRE_ANCIEN => 'Plus anciens',
            self::ORDRE_TITRE  => 'Par titre',
            self::ORDRE_ALBUM  => 'Par album',
        ];

        // ---------------------------------------------------------------------
        // 7. Rendu de la vue
        // ---------------------------------------------------------------------
        return view('public.galerie.index', [
            'medias'       => $medias,
            'albums'       => $albums,
            'projets'      => $projets,
            'totalMedias'  => $stats['total'],
            'totalImages'  => $stats['images'],
            'totalVideos'  => $stats['videos'],
            'types'        => $types,
            'ordres'       => $ordres,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTRES
    |--------------------------------------------------------------------------
    */

    /**
     * Applique le filtre par type (image / vidéo).
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Media>  $query
     * @param  Request  $request
     * @return void
     */
    protected function appliquerFiltreType($query, Request $request): void
    {
        if (!$request->filled('type')) {
            return;
        }

        $type = (string) $request->input('type');

        if (in_array($type, self::TYPES_AUTORISES, true)) {
            $query->where('type', $type);
        }
    }

    /**
     * Applique le filtre par album (nom exact).
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Media>  $query
     * @param  Request  $request
     * @return void
     */
    protected function appliquerFiltreAlbum($query, Request $request): void
    {
        if (!$request->filled('album')) {
            return;
        }

        $album = trim((string) $request->input('album'));

        if ($album !== '') {
            $query->where('album', $album);
        }
    }

    /**
     * Applique le filtre par projet.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Media>  $query
     * @param  Request  $request
     * @return void
     */
    protected function appliquerFiltreProjet($query, Request $request): void
    {
        if (!$request->filled('projet_id')) {
            return;
        }

        $projetId = (int) $request->input('projet_id');

        if ($projetId > 0) {
            $query->where('projet_id', $projetId);
        }
    }

    /**
     * Applique le filtre de recherche textuelle.
     *
     * Recherche sur : titre, description, lieu, album, auteur.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Media>  $query
     * @param  Request  $request
     * @return void
     */
    protected function appliquerFiltreRecherche($query, Request $request): void
    {
        if (!$request->filled('recherche')) {
            return;
        }

        $recherche = trim((string) $request->input('recherche'));

        if ($recherche === '') {
            return;
        }

        $like = '%' . $recherche . '%';

        $query->where(function ($q) use ($like) {
            $q->where('titre', 'like', $like)
              ->orWhere('description', 'like', $like)
              ->orWhere('lieu', 'like', $like)
              ->orWhere('album', 'like', $like)
              ->orWhere('auteur', 'like', $like);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | TRI
    |--------------------------------------------------------------------------
    */

    /**
     * Applique le tri selon le paramètre "ordre".
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Media>  $query
     * @param  Request  $request
     * @return void
     */
    protected function appliquerTri($query, Request $request): void
    {
        $ordre = (string) $request->input('ordre', self::ORDRE_RECENT);

        if (!in_array($ordre, self::ORDRES_AUTORISES, true)) {
            $ordre = self::ORDRE_RECENT;
        }

        switch ($ordre) {
            case self::ORDRE_ANCIEN:
                $query->orderBy('created_at', 'asc');
                break;

            case self::ORDRE_TITRE:
                $query->orderBy('titre', 'asc');
                break;

            case self::ORDRE_ALBUM:
                $query->orderByRaw('album IS NULL, album ASC')
                      ->orderBy('ordre', 'asc')
                      ->orderBy('created_at', 'desc');
                break;

            case self::ORDRE_RECENT:
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DONNÉES ANNEXES
    |--------------------------------------------------------------------------
    */

    /**
     * Récupère la liste des albums distincts (uniquement ceux contenant
     * au moins un média publié). Tri alphabétique, sans valeurs vides.
     *
     * @return Collection<int, string>
     */
    protected function recupererAlbums(): Collection
    {
        return Media::query()
            ->where('publier', true)
            ->whereNotNull('album')
            ->where('album', '!=', '')
            ->distinct()
            ->orderBy('album')
            ->pluck('album')
            ->filter(fn ($album) => is_string($album) && trim($album) !== '')
            ->values();
    }

    /**
     * Récupère les projets qui possèdent au moins un média publié.
     *
     * On limite les colonnes pour éviter de charger des champs inutiles.
     *
     * @return Collection<int, Projet>
     */
    protected function recupererProjetsAvecMedias(): Collection
    {
        return Projet::query()
            ->select(['id', 'titre', 'slug'])
            ->whereHas('medias', function ($q) {
                $q->where('publier', true);
            })
            ->orderBy('titre')
            ->get();
    }

    /**
     * Récupère les statistiques globales (avec cache pour la performance).
     *
     * @return array{total:int, images:int, videos:int}
     */
    protected function recupererStatistiques(): array
    {
        return Cache::remember(
            'galerie.public.stats',
            self::CACHE_STATS_TTL,
            function (): array {
                $base = Media::query()->where('publier', true);

                return [
                    'total'  => (clone $base)->count(),
                    'images' => (clone $base)->where('type', self::TYPE_IMAGE)->count(),
                    'videos' => (clone $base)->where('type', self::TYPE_VIDEO)->count(),
                ];
            }
        );
    }
}