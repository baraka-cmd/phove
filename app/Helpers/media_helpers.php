<?php

/**
 * Helpers globaux pour gérer les médias.
 *
 * Utilise Cloudinary en production, le disque local 'public' en développement.
 */

use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Storage;

if (!function_exists('media_store')) {
    /**
     * Upload un fichier (Cloudinary en prod, disque 'public' en local).
     *
     * @param  \Illuminate\Http\UploadedFile|string  $file
     * @param  string  $folder  Dossier cible (ex: "projets/images")
     * @return string|null      URL Cloudinary ou chemin local
     */
    function media_store($file, string $folder = 'uploads'): ?string
    {
        // En local : on utilise le disque 'public'
        if (config('app.env') !== 'production') {
            if (is_string($file)) {
                return null;
            }
            return $file->store($folder, 'public');
        }

        // En production : Cloudinary
        if (is_string($file)) {
            return null;
        }

        $url = app(CloudinaryService::class)->upload($file, $folder);

        if (!$url) {
            throw new \RuntimeException("L'envoi du média vers Cloudinary a échoué. Vérifiez la configuration Cloudinary et les journaux Render.");
        }

        return $url;
    }
}

if (!function_exists('media_store_as')) {
    /**
     * Upload un fichier avec un nom spécifique.
     */
    function media_store_as($file, string $folder, string $nomFichier): ?string
    {
        if (config('app.env') !== 'production') {
            return $file->storeAs($folder, $nomFichier, 'public');
        }

        // Cloudinary génère son propre nom → on ignore $nomFichier
        $url = app(CloudinaryService::class)->upload($file, $folder);

        if (!$url) {
            throw new \RuntimeException("L'envoi du média vers Cloudinary a échoué. Vérifiez la configuration Cloudinary et les journaux Render.");
        }

        return $url;
    }
}

if (!function_exists('media_delete')) {
    /**
     * Supprime un fichier (Cloudinary ou disque 'public').
     */
    function media_delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (str_starts_with($path, 'http')) {
            // URL Cloudinary
            app(CloudinaryService::class)->delete($path);
        } else {
            // Chemin local
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}

if (!function_exists('media_exists')) {
    /**
     * Vérifie si un fichier existe.
     */
    function media_exists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        if (str_starts_with($path, 'http')) {
            // URL Cloudinary → suppose que le fichier existe
            return true;
        }

        return Storage::disk('public')->exists($path);
    }
}

if (!function_exists('media_url')) {
    /**
     * Retourne l'URL publique d'un média.
     */
    function media_url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return img_url($path);
    }
}

if (!function_exists('media_miniature')) {
    /**
     * Retourne l'URL de miniature (Cloudinary avec transformation, ou route custom).
     */
    function media_miniature(?string $fichier, ?string $miniature = null, int $w = 400, int $h = 300): ?string
    {
        // Miniature Cloudinary stockée
        if ($miniature && str_starts_with($miniature, 'http')) {
            return $miniature;
        }

        // Générer à la volée (Cloudinary)
        if ($fichier && str_starts_with($fichier, 'http')) {
            return str_replace('/upload/', "/upload/w_{$w},h_{$h},c_fill/", $fichier);
        }

        // Miniature locale
        if ($miniature) {
            return img_url($miniature);
        }

        // Fallback
        if ($fichier) {
            return img_url($fichier);
        }
        return null;
    }
}