<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


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
     * Taille maximale pour les images (10 Mo).
     */
    protected int $tailleMaxImage = 10240;

    /**
     * Taille maximale pour les vidéos (50 Mo).
     */
    protected int $tailleMaxVideo = 51200;
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
        $totalMedias = Media::count();
        $totalImages = Media::where('type', 'image')->count();
        $totalVideos = Media::where('type', 'video')->count();
        $totalPublies = Media::where('publier', true)->count();
        $totalPrives = Media::where('publier', false)->count();

        // Espace disque utilisé
        $espaceDisque = Media::sum('taille');
        $espaceDisqueFormate = $this->formaterTaille($espaceDisque);

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
            'titre' => 'Par titre',
            'album' => 'Par album',
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

    /**
     * Affiche le formulaire d'upload de médias.
     */
    public function create()
    {
        $projets = Projet::orderBy('titre')->get();
        $albums = Media::select('album')
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
    /**
 * Enregistre un ou plusieurs médias.
 */
public function store(Request $request)
{
    // Vérifier si on a des fichiers via l'input direct ou via JS
    $hasDirectFiles = $request->hasFile('fichiers');
    $hasSingleFile = $request->hasFile('fichier');

    if (!$hasDirectFiles && !$hasSingleFile) {
        return redirect()->back()
            ->with('error', 'Veuillez sélectionner au moins un fichier.');
    }

    // Récupérer les fichiers
    $fichiers = $hasDirectFiles ? $request->file('fichiers') : [$request->file('fichier')];

    // Validation
    $request->validate([
        'fichiers' => ['required', 'array', 'min:1', 'max:20'],
        'fichiers.*' => [
            'required',
            'file',
            function ($attribute, $value, $fail) {
                $extension = strtolower($value->getClientOriginalExtension());
                $formatsAcceptes = array_merge($this->formatsImages, $this->formatsVideos);
                
                if (!in_array($extension, $formatsAcceptes)) {
                    $fail('Le format "' . $extension . '" n\'est pas accepté. Formats acceptés : ' . implode(', ', $formatsAcceptes));
                }
                
                // Taille différenciée
                $tailleMax = in_array($extension, $this->formatsImages) ? $this->tailleMaxImage : $this->tailleMaxVideo;
                $tailleMaxMo = $tailleMax / 1024;
                
                if ($value->getSize() > $tailleMax * 1024) {
                    $fail('Le fichier "' . $value->getClientOriginalName() . '" dépasse la taille maximale de ' . $tailleMaxMo . ' Mo.');
                }
            },
        ],
        'projet_id' => ['nullable', 'exists:projets,id'],
        'album' => ['nullable', 'string', 'max:255'],
        'publier' => ['nullable', 'boolean'],
    ], [
        'fichiers.required' => 'Veuillez sélectionner au moins un fichier.',
        'fichiers.max' => 'Vous ne pouvez pas uploader plus de 20 fichiers à la fois.',
    ]);

    $mediasEnregistres = [];
    $erreurs = [];

    foreach ($fichiers as $fichier) {
        try {
            $extension = strtolower($fichier->getClientOriginalExtension());
            $type = in_array($extension, $this->formatsImages) ? 'image' : 'video';

            // Générer un nom unique
            $nomFichier = Str::uuid() . '.' . $extension;

            // Dossier de stockage
            $dossier = $type === 'image' ? 'medias/images' : 'medias/videos';
            $chemin = $fichier->storeAs($dossier, $nomFichier, 'public');

            // Miniature pour les images
            $cheminMiniature = null;
            if ($type === 'image' && in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                try {
                    $cheminMiniature = $this->genererMiniature($fichier, $nomFichier);
                } catch (\Exception $e) {
                    // Si la génération de miniature échoue, on continue sans
                }
            }

            // Créer le média
            $media = Media::create([
                'titre' => pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME),
                'type' => $type,
                'fichier' => $chemin,
                'miniature' => $cheminMiniature,
                'taille' => $fichier->getSize(),
                'projet_id' => $request->projet_id,
                'album' => $request->album,
                'description' => null,
                'date_prise' => now(),
                'lieu' => null,
                'auteur' => auth()->user()->nom ?? 'Admin',
                'publier' => $request->boolean('publier', true),
                'ordre' => Media::where('album', $request->album)->max('ordre') + 1,
                'user_id' => auth()->id(),
            ]);

            $mediasEnregistres[] = $media;
        } catch (\Exception $e) {
            $erreurs[] = $fichier->getClientOriginalName() . ' : ' . $e->getMessage();
        }
    }

    $count = count($mediasEnregistres);
    $message = $count . ' média(s) enregistré(s) avec succès.';

    if (count($erreurs) > 0) {
        return redirect()->route('admin.medias.index')
            ->with('warning', $message . ' ' . count($erreurs) . ' erreur(s).');
    }

    return redirect()->route('admin.medias.index')
        ->with('success', $message);
}

    /**
     * Affiche le détail d'un média.
     */
    public function show(Media $media)
    {
        $media->load('projet', 'uploadPar');

        // Médias précédent et suivant dans le même album ou global
        $precedent = Media::where('id', '<', $media->id)
            ->when($media->album, fn($q) => $q->where('album', $media->album))
            ->orderBy('id', 'desc')
            ->first();

        $suivant = Media::where('id', '>', $media->id)
            ->when($media->album, fn($q) => $q->where('album', $media->album))
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
        $albums = Media::select('album')
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
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'album' => ['nullable', 'string', 'max:255'],
            'projet_id' => ['nullable', 'exists:projets,id'],
            'lieu' => ['nullable', 'string', 'max:255'],
            'auteur' => ['nullable', 'string', 'max:255'],
            'date_prise' => ['nullable', 'date'],
            'publier' => ['nullable', 'boolean'],
            'ordre' => ['nullable', 'integer', 'min:0'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
        ]);

        $validated['publier'] = $request->boolean('publier');

        $media->update($validated);

        return redirect()->route('admin.medias.index')
            ->with('success', 'Le média a été mis à jour avec succès.');
    }

    /**
     * Supprime un média.
     */
    public function destroy(Media $media)
    {
        // Supprimer le fichier physique
        if ($media->fichier && Storage::disk('public')->exists($media->fichier)) {
            Storage::disk('public')->delete($media->fichier);
        }

        // Supprimer la miniature
        if ($media->miniature && Storage::disk('public')->exists($media->miniature)) {
            Storage::disk('public')->delete($media->miniature);
        }

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
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:medias,id'],
        ], [
            'ids.required' => 'Veuillez sélectionner au moins un média.',
        ]);

        $medias = Media::whereIn('id', $request->ids)->get();
        $count = 0;

        foreach ($medias as $media) {
            if ($media->fichier && Storage::disk('public')->exists($media->fichier)) {
                Storage::disk('public')->delete($media->fichier);
            }
            if ($media->miniature && Storage::disk('public')->exists($media->miniature)) {
                Storage::disk('public')->delete($media->miniature);
            }
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
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:medias,id'],
            'album' => ['required', 'string', 'max:255'],
        ], [
            'ids.required' => 'Veuillez sélectionner au moins un média.',
            'album.required' => 'Le nom de l\'album est obligatoire.',
        ]);

        $count = Media::whereIn('id', $request->ids)->update(['album' => $request->album]);

        return redirect()->route('admin.medias.index')
            ->with('success', $count . ' média(s) déplacé(s) vers l\'album "' . $request->album . '".');
    }

    /**
     * Action batch : changer le statut de publication.
     */
    public function batchTogglePublier(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:medias,id'],
            'publier' => ['required', 'boolean'],
        ]);

        $count = Media::whereIn('id', $request->ids)->update(['publier' => $request->publier]);
        $etat = $request->publier ? 'publié(s)' : 'dépublié(s)';

        return redirect()->route('admin.medias.index')
            ->with('success', $count . ' média(s) ' . $etat . ' avec succès.');
    }

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
     */
    public function replaceFile(Request $request, Media $media)
    {
        $request->validate([
            'fichiers' => ['required', 'array', 'min:1', 'max:20'],
            'fichiers.*' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    $formatsAcceptes = array_merge($this->formatsImages, $this->formatsVideos);
                    
                    if (!in_array($extension, $formatsAcceptes)) {
                        $fail('Le format "' . $extension . '" n\'est pas accepté. Formats acceptés : ' . implode(', ', $formatsAcceptes));
                    }
                    
                    // Taille différenciée
                    $tailleMax = in_array($extension, $this->formatsImages) ? $this->tailleMaxImage : $this->tailleMaxVideo;
                    $tailleMaxMo = $tailleMax / 1024;
                    
                    if ($value->getSize() > $tailleMax * 1024) {
                        $fail('Le fichier "' . $value->getClientOriginalName() . '" dépasse la taille maximale de ' . $tailleMaxMo . ' Mo.');
                    }
                },
            ],
            'projet_id' => ['nullable', 'exists:projets,id'],
            'album' => ['nullable', 'string', 'max:255'],
            'publier' => ['nullable', 'boolean'],
        ], [
            'fichiers.required' => 'Veuillez sélectionner au moins un fichier.',
            'fichiers.max' => 'Vous ne pouvez pas uploader plus de 20 fichiers à la fois.',
        ]);

        $fichier = $request->file('fichier');
        $extension = strtolower($fichier->getClientOriginalExtension());
        $type = in_array($extension, $this->formatsImages) ? 'image' : 'video';

        // Supprimer l'ancien fichier
        if ($media->fichier && Storage::disk('public')->exists($media->fichier)) {
            Storage::disk('public')->delete($media->fichier);
        }
        if ($media->miniature && Storage::disk('public')->exists($media->miniature)) {
            Storage::disk('public')->delete($media->miniature);
        }

        // Stocker le nouveau
        $nomFichier = Str::uuid() . '.' . $extension;
        $dossier = $type === 'image' ? 'medias/images' : 'medias/videos';
        $chemin = $fichier->storeAs($dossier, $nomFichier, 'public');

        $cheminMiniature = null;
        if ($type === 'image' && in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            $cheminMiniature = $this->genererMiniature($fichier, $nomFichier);
        }

        $media->update([
            'type' => $type,
            'fichier' => $chemin,
            'miniature' => $cheminMiniature,
            'taille' => $fichier->getSize(),
        ]);

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
     * Génère une miniature pour une image.
     */
    /**
 * Génère une miniature pour une image (sans package externe).
 */
private function genererMiniature($fichier, string $nomOriginal): ?string
{
    try {
        $sourcePath = $fichier->getRealPath();
        $extension = strtolower($fichier->getClientOriginalExtension());
        
        // Créer l'image source selon le format
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                $sourceImage = @imagecreatefromjpeg($sourcePath);
                break;
            case 'png':
                $sourceImage = @imagecreatefrompng($sourcePath);
                break;
            case 'webp':
                $sourceImage = @imagecreatefromwebp($sourcePath);
                break;
            case 'gif':
                $sourceImage = @imagecreatefromgif($sourcePath);
                break;
            default:
                return null;
        }

        if (!$sourceImage) {
            return null;
        }

        // Dimensions originales
        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);

        // Dimensions de la miniature
        $maxWidth = 400;
        $maxHeight = 300;

        // Calcul du ratio
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = (int) round($originalWidth * $ratio);
        $newHeight = (int) round($originalHeight * $ratio);

        // Créer la miniature
        $thumbImage = imagecreatetruecolor($newWidth, $newHeight);

        // Préserver la transparence pour PNG et GIF
        if ($extension === 'png' || $extension === 'gif') {
            imagealphablending($thumbImage, false);
            imagesavealpha($thumbImage, true);
            $transparent = imagecolorallocatealpha($thumbImage, 0, 0, 0, 127);
            imagefilledrectangle($thumbImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Redimensionner
        imagecopyresampled(
            $thumbImage, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight, $originalWidth, $originalHeight
        );

        // Sauvegarder la miniature
        $nomMiniature = 'thumb_' . $nomOriginal;
        $cheminMiniature = 'medias/thumbnails/' . $nomMiniature;
        $fullPath = Storage::disk('public')->path($cheminMiniature);

        // Créer le dossier si nécessaire
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Sauvegarder selon le format
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($thumbImage, $fullPath, 80);
                break;
            case 'png':
                imagepng($thumbImage, $fullPath, 7);
                break;
            case 'webp':
                imagewebp($thumbImage, $fullPath, 80);
                break;
            case 'gif':
                imagegif($thumbImage, $fullPath);
                break;
        }

        // Libérer la mémoire
        imagedestroy($sourceImage);
        imagedestroy($thumbImage);

        return $cheminMiniature;
    } catch (\Exception $e) {
        return null;
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
        $i = 0;
        $taille = $octets;

        while ($taille >= 1024 && $i < count($unites) - 1) {
            $taille /= 1024;
            $i++;
        }

        return round($taille, 1) . ' ' . $unites[$i];
    }
}