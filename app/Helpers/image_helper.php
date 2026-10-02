<?php

if (!function_exists('img_url')) {
    /**
     * Retourne l'URL d'une image.
     *
     * - Si c'est une URL Cloudinary (http...) → renvoie telle quelle
     * - Sinon → route media.show (fichier local)
     *
     * @param  string|null  $path
     * @return string
     */
    function img_url(?string $path): string
    {
        if (!$path) {
            return asset('assets/images/logo-phove.png');
        }

        // URL Cloudinary ou externe → retourner directement
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        // Chemin local → route media.show
        return img_url($path);
    }
}