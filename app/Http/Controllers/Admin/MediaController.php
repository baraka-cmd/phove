<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\CloudinaryService;

class MediaController extends Controller
{
    /**
     * Nombre de médias par page.
     */
    protected int $perPage = 24;

    /**
     * Types de médias acceptés.
     */
    protected array $types = [
        'image' => 'Image',
        'video' => 'Vidéo',
    ];

    /**
     * Formats d'image acceptés.
     */
    protected array $formatsImages = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /**
     * Formats vidéo acceptés.
     */
    protected array $formatsVideos = ['mp4', 'webm', 'ogg'];

    /**
     * Taille maximale pour les images (10 Mo, en Ko).
     */
    protected int $tailleMaxImage = 10240;

    /**
     * Taille maximale pour les vidéos (50 Mo, en Ko).
     */
    protected int $tailleMaxVideo = 51200;

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche la galerie de médias avec KPI, filtres et grille.
     */
    public function index(Request $request)
    {
        $query = Media::with('projet', 'uploadPar');

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par album
        if ($request->filled('album')) {
            $query->where('album', $request->album);
        }

        // Filtre par projet
        if ($request->filled('projet_id')) {
            $query->where('projet_id', $request->projet_id);
        }

        // Filtre par statut de publication
        if ($request->filled('publier')) {
            $query->where('publier', $request->publier === 'oui');
        }

        // Recherche textuelle
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('titre', 'like', '%' . $recherche . '%')
                  ->orWhere('description', 'like', '%' . $recherche . '%')
                  ->orWhere('lieu', 'like', '%' . $recherche . '%')
                  ->orWhere('auteur', 'like', '%' . $recherche . '%')
                  ->orWhere('album', 'like', '%' . $recherche . '%');
            });
        }

        // Tri
        $ordre = $request->input('ordre', 'recent');
        switch ($ordre) {
            case 'ancien':
                $query->orderBy('created_at', 'asc');
                break;
            case 'titre':
                $query->orderBy('titre', 'asc');
                break;
            case 'album':
                $query->orderBy('album', 'asc')->orderBy('ordre', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $medias = $query->paginate($this->perPage)->withQueryString();

        // ============================================
        // STATISTIQUES KPI
        // ============================================
        $totalMedias   = Media::count();
        $totalImages   = Media::where('type', 'image')->count();
        $totalVideos   = Media::where('type', 'video')->count();
        $totalPublies  = Media::where('publier', true)->count();
        $totalPrives   = Media::where('publier', false)->count();

        // Espace disque utilisé
        $espaceDisque         = Media::sum('taille');
        $espaceDisqueFormate  = $this->formaterTaille((int) $espaceDisque);

        // Albums distincts
        $albums = Media::select('album')
            ->whereNotNull('album')
            ->where('album', '!=', '')
            ->distinct()
            ->orderBy('album')
            ->pluck('album');

        // Projets pour le filtre
        $projets = Projet::orderBy('titre')->get();

        // Types pour le filtre
        $types = $this->types;

        // Ordres de tri disponibles
        $ordres = [
            'recent' => 'Plus récents',
            'ancien' => 'Plus anciens',
            'titre'  => 'Par titre',
            'album'  => 'Par album',
        ];

        return view('admin.medias.index', compact(
            'medias',
            'totalMedias',
            'totalImages',
            'totalVideos',
            'totalPublies',
            'totalPrives',
            'espaceDisqueFormate',
            'albums',
            'projets',
            'types',
            'ordres'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE / STORE
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche le formulaire d'upload de médias.
     */
    public function create()
    {
        $projets = Projet::orderBy('titre')->get();
        $albums  = Media::select('album')
            ->whereNotNull('album')
            ->where('album', '!=', '')
            ->distinct()
            ->orderBy('album')
            ->pluck('album');

        return view('admin.medias.create', compact('projets', 'albums'));
    }

    /**
     * Enregistre un ou plusieurs médias.
     */
    public function store(Request $request)
    {
        // Déterminer si les fichiers arrivent via "fichiers[]" ou "fichier" (single)
        $hasDirectFiles = $request->hasFile('fichiers');
        $hasSingleFile  = $request->hasFile('fichier');

        if (!$hasDirectFiles && !$hasSingleFile) {
            return redirect()->back()
                ->with('error', 'Veuillez sélectionner au moins un fichier.');
        }

        // Récupérer les fichiers sous forme de tableau
        $fichiers = $hasDirectFiles
            ? $request->file('fichiers')
            : [$request->file('fichier')];

        // S'assurer qu'on a bien un tableau
        if (!is_array($fichiers)) {
            $fichiers = [$fichiers];
        }

        // Construire les règles de validation en fonction de l'input utilisé
        $rules = [
            'projet_id' => ['nullable', 'exists:projets,id'],
            'album'     => ['nullable', 'string', 'max:255'],
            'publier'   => ['nullable', 'boolean'],
        ];

        if ($hasDirectFiles) {
            $rules['fichiers']   = ['required', 'array', 'min:1', 'max:20'];
            $rules['fichiers.*'] = [$this->regleFichier()];
        } else {
            $rules['fichier'] = ['required', 'file', $this->regleFichier()];
        }

        $request->validate($rules, [
            'fichiers.required' => 'Veuillez sélectionner au moins un fichier.',
            'fichiers.max'      => 'Vous ne pouvez pas uploader plus de 20 fichiers à la fois.',
            'fichier.required'  => 'Veuillez sélectionner un fichier.',
        ]);

        $mediasEnregistres = [];
        $erreurs           = [];

        foreach ($fichiers as $fichier) {
            try {
                $extension = strtolower($fichier->getClientOriginalExtension());
                $type      = in_array($extension, $this->formatsImages) ? 'image' : 'video';

                // Nom unique
                $nomFichier = Str::uuid() . '.' . $extension;

                // Dossier de stockage
                $dossier = $type === 'image' ? 'medias/images' : 'medias/videos';
                $chemin = media_store($fichier, $dossier);

                // Cloudinary génère les miniatures à la volée (transformations d'URL)
                $cheminMiniature = null;

                // Calcul de l'ordre : par album s'il existe, sinon global
                $ordre = (int) Media::when(
                    $request->filled('album'),
                    fn ($q) => $q->where('album', $request->album),
                    fn ($q) => $q->whereNull('album')
                )->max('ordre') + 1;

                Media::create([
                    'titre'       => pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME),
                    'type'        => $type,
                    'fichier'     => $chemin,
                    'miniature'   => $cheminMiniature,
                    'taille'      => $fichier->getSize(),
                    'projet_id'   => $request->projet_id,
                    'album'       => $request->album,
                    'description' => null,
                    'date_prise'  => now(),
                    'lieu'        => null,
                    'auteur'      => auth()->user()->nom ?? 'Admin',
                    'publier'     => $request->boolean('publier', true),
                    'ordre'       => $ordre,
                    'user_id'     => auth()->id(),
                ]);

                $mediasEnregistres[] = true;
            } catch (\Exception $e) {
                $erreurs[] = $fichier->getClientOriginalName() . ' : ' . $e->getMessage();
            }
        }

        $count   = count($mediasEnregistres);
        $message = $count . ' média(s) enregistré(s) avec succès.';

        if (count($erreurs) > 0) {
            return redirect()->route('admin.medias.index')
                ->with('warning', $message . ' ' . count($erreurs) . ' erreur(s).');
        }

        return redirect()->route('admin.medias.index')
            ->with('success', $message);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW / EDIT / UPDATE
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche le détail d'un média.
     */
    public function show(Media $media)
    {
        $media->load('projet', 'uploadPar');

        $precedent = Media::where('id', '<', $media->id)
            ->when($media->album, fn ($q) => $q->where('album', $media->album))
            ->orderBy('id', 'desc')
            ->first();

        $suivant = Media::where('id', '>', $media->id)
            ->when($media->album, fn ($q) => $q->where('album', $media->album))
            ->orderBy('id', 'asc')
            ->first();

        return view('admin.medias.show', compact('media', 'precedent', 'suivant'));
    }

    /**
     * Affiche le formulaire d'édition d'un média.
     */
    public function edit(Media $media)
    {
        $projets = Projet::orderBy('titre')->get();
        $albums  = Media::select('album')
            ->whereNotNull('album')
            ->where('album', '!=', '')
            ->distinct()
            ->orderBy('album')
            ->pluck('album');

        return view('admin.medias.edit', compact('media', 'projets', 'albums'));
    }

    /**
     * Met à jour les métadonnées d'un média.
     */
    public function update(Request $request, Media $media)
    {
        $validated = $request->validate([
            'titre'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'album'       => ['nullable', 'string', 'max:255'],
            'projet_id'   => ['nullable', 'exists:projets,id'],
            'lieu'        => ['nullable', 'string', 'max:255'],
            'auteur'      => ['nullable', 'string', 'max:255'],
            'date_prise'  => ['nullable', 'date'],
            'publier'     => ['nullable', 'boolean'],
            'ordre'       => ['nullable', 'integer', 'min:0'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
        ]);

        $validated['publier'] = $request->boolean('publier');

        $media->update($validated);

        return redirect()->route('admin.medias.index')
            ->with('success', 'Le média a été mis à jour avec succès.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY / BATCH
    |--------------------------------------------------------------------------
    */

    /**
     * Supprime un média.
     */
    public function destroy(Media $media)
    {
        $this->supprimerFichiersPhysiques($media);
        $media->delete();

        return redirect()->route('admin.medias.index')
            ->with('success', 'Le média a été supprimé définitivement.');
    }

    /**
     * Action batch : supprimer plusieurs médias.
     */
    public function batchDelete(Request $request)
    {
        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:medias,id'],
        ], [
            'ids.required' => 'Veuillez sélectionner au moins un média.',
        ]);

        $medias = Media::whereIn('id', $request->ids)->get();
        $count  = 0;

        foreach ($medias as $media) {
            $this->supprimerFichiersPhysiques($media);
            $media->delete();
            $count++;
        }

        return redirect()->route('admin.medias.index')
            ->with('success', $count . ' média(s) supprimé(s) avec succès.');
    }

    /**
     * Action batch : changer l'album de plusieurs médias.
     */
    public function batchChangeAlbum(Request $request)
    {
        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:medias,id'],
            'album' => ['required', 'string', 'max:255'],
        ], [
            'ids.required'   => 'Veuillez sélectionner au moins un média.',
            'album.required' => 'Le nom de l\'album est obligatoire.',
        ]);

        $count = Media::whereIn('id', $request->ids)
            ->update(['album' => $request->album]);

        return redirect()->route('admin.medias.index')
            ->with('success', $count . ' média(s) déplacé(s) vers l\'album "' . $request->album . '".');
    }

    /**
     * Action batch : changer le statut de publication.
     */
    public function batchTogglePublier(Request $request)
    {
        $request->validate([
            'ids'     => ['required', 'array', 'min:1'],
            'ids.*'   => ['required', 'integer', 'exists:medias,id'],
            'publier' => ['required', 'boolean'],
        ]);

        $count = Media::whereIn('id', $request->ids)
            ->update(['publier' => $request->publier]);

        $etat = $request->publier ? 'publié(s)' : 'dépublié(s)';

        return redirect()->route('admin.medias.index')
            ->with('success', $count . ' média(s) ' . $etat . ' avec succès.');
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIONS UNITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Publie ou dépublie un média.
     */
    public function togglePublier(Media $media)
    {
        $media->update(['publier' => !$media->publier]);
        $etat = $media->publier ? 'publié' : 'dépublié';

        return redirect()->back()
            ->with('success', 'Le média a été ' . $etat . ' avec succès.');
    }

    /**
     * Remplace le fichier d'un média.
     *
     * ⚠️ Corrigé : accepte soit "fichier" (single) soit "fichiers[]" (multi),
     * et ne prend que le premier fichier reçu.
     */
    public function replaceFile(Request $request, Media $media)
    {
        // Déterminer quel input est utilisé
        $fichier = $request->file('fichier')
            ?? (is_array($request->file('fichiers'))
                ? $request->file('fichiers')[0]
                : $request->file('fichiers'));

        if (!$fichier) {
            return redirect()->back()
                ->with('error', 'Aucun fichier fourni pour le remplacement.');
        }

        // Valider le fichier unique
        $request->validate([
            'fichier'   => ['nullable', 'file', $this->regleFichier()],
            'fichiers'  => ['nullable', 'array', 'max:1'],
            'projet_id' => ['nullable', 'exists:projets,id'],
            'album'     => ['nullable', 'string', 'max:255'],
            'publier'   => ['nullable', 'boolean'],
        ]);

        $extension = strtolower($fichier->getClientOriginalExtension());
        $type      = in_array($extension, $this->formatsImages) ? 'image' : 'video';

        // Stocker et valider le nouveau fichier AVANT de supprimer l'ancien.
        // Ainsi, un échec Cloudinary ne détruit jamais le média actuellement disponible.
        $dossier = $type === 'image' ? 'medias/images' : 'medias/videos';
        $chemin = media_store($fichier, $dossier);

        if (!$chemin) {
            return redirect()->back()
                ->with('error', "Le nouveau fichier n'a pas pu être stocké. L'ancien média a été conservé.");
        }

        // Cloudinary génère les miniatures à la volée (transformations d'URL)
        $cheminMiniature = null;

        $ancienFichier = $media->fichier;
        $ancienneMiniature = $media->miniature;

        $media->update([
            'type'      => $type,
            'fichier'   => $chemin,
            'miniature' => $cheminMiniature,
            'taille'    => $fichier->getSize(),
        ]);

        // Supprimer les anciens fichiers uniquement après la mise à jour réussie.
        if ($ancienFichier && $ancienFichier !== $chemin) {
            media_delete($ancienFichier);
        }
        if ($ancienneMiniature && $ancienneMiniature !== $cheminMiniature) {
            media_delete($ancienneMiniature);
        }

        return redirect()->back()
            ->with('success', 'Le fichier a été remplacé avec succès.');
    }

    /**
     * Incrémente le compteur de vues (appelé depuis le front).
     */
    public function incrementerVues(Media $media)
    {
        $media->increment('vues');

        return response()->json(['vues' => $media->vues]);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES PRIVÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Construit la closure de validation commune à un fichier (image ou vidéo).
     *
     * @return \Closure
     */
    private function regleFichier(): \Closure
    {
        return function ($attribute, $value, $fail) {
            $extension      = strtolower($value->getClientOriginalExtension());
            $formatsAcceptes = array_merge($this->formatsImages, $this->formatsVideos);

            if (!in_array($extension, $formatsAcceptes)) {
                $fail('Le format "' . $extension . '" n\'est pas accepté. Formats acceptés : ' . implode(', ', $formatsAcceptes));
                return;
            }

            $tailleMax   = in_array($extension, $this->formatsImages)
                ? $this->tailleMaxImage
                : $this->tailleMaxVideo;
            $tailleMaxMo = $tailleMax / 1024;

            if ($value->getSize() > $tailleMax * 1024) {
                $fail('Le fichier "' . $value->getClientOriginalName() . '" dépasse la taille maximale de ' . $tailleMaxMo . ' Mo.');
            }
        };
    }

    /**
     * Supprime les fichiers physiques associés à un média (fichier + miniature).
     */
    private function supprimerFichiersPhysiques(Media $media): void
    {
        if ($media->fichier) {
            media_delete($media->fichier);
        }

        if ($media->miniature) {
            media_delete($media->miniature);
        }
    }

    

    /**
     * Convertit des coordonnées GPS EXIF en chaîne lisible.
     */
    private function convertirGps(array $lat, array $lon): string
    {
        $latDeg = $this->gpsToDecimal($lat);
        $lonDeg = $this->gpsToDecimal($lon);

        if ($latDeg === null || $lonDeg === null) {
            return '';
        }

        return round($latDeg, 6) . ', ' . round($lonDeg, 6);
    }

    /**
     * Convertit une coordonnée GPS EXIF en degrés décimaux.
     */
    private function gpsToDecimal(array $coord): ?float
    {
        if (count($coord) < 3) {
            return null;
        }

        $degrees = $this->evaluerFraction($coord[0]);
        $minutes = $this->evaluerFraction($coord[1]);
        $seconds = $this->evaluerFraction($coord[2]);

        if ($degrees === null || $minutes === null || $seconds === null) {
            return null;
        }

        return $degrees + ($minutes / 60.0) + ($seconds / 3600.0);
    }

    /**
     * Évalue une fraction EXIF (ex: "22/1" → 22.0).
     */
    private function evaluerFraction(string $fraction): ?float
    {
        $parts = explode('/', $fraction);
        if (count($parts) === 2 && $parts[1] != 0) {
            return (float) $parts[0] / (float) $parts[1];
        }
        return (float) $fraction;
    }

    /**
     * Formate une taille en octets vers une chaîne lisible.
     */
    private function formaterTaille(int $octets): string
    {
        if ($octets <= 0) {
            return '0 o';
        }

        $unites = ['o', 'Ko', 'Mo', 'Go', 'To'];
        $i      = 0;
        $taille = $octets;

        while ($taille >= 1024 && $i < count($unites) - 1) {
            $taille /= 1024;
            $i++;
        }

        return round($taille, 1) . ' ' . $unites[$i];
    }
}