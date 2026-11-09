<?php

if (!function_exists('img_url')) {
    /**
     * Retourne une URL exploitable pour une image locale ou distante.
     *
     * Les médias uploadés sont servis par la route media.show.
     * Les images historiques conservées dans public/assets/images restent servies
     * directement depuis le dossier public.
     */
    function img_url(?string $path): string
    {
        if (!$path) {
            return asset('assets/images/logo-phove.png');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $relativePath = ltrim(str_replace(chr(92), '/', $path), '/');

        // Accepter les chemins déjà relatifs à public/ (ex. assets/images/photo.jpg).
        $publicRoot = realpath(public_path());
        $publicFile = realpath(public_path($relativePath));

        if ($publicRoot && $publicFile && str_starts_with($publicFile, $publicRoot . DIRECTORY_SEPARATOR)) {
            return asset($relativePath);
        }

        // Médias déposés sur le disque Laravel public.
        $storageRoot = realpath(storage_path('app/public'));
        $storageFile = realpath(storage_path('app/public/' . $relativePath));

        if ($storageRoot && $storageFile && str_starts_with($storageFile, $storageRoot . DIRECTORY_SEPARATOR)) {
            $encodedPath = implode('/', array_map('rawurlencode', explode('/', $relativePath)));
            return url('/media/' . $encodedPath);
        }

        // Compatibilité avec les anciens noms de fichiers placés dans assets/images.
        $legacyRoot = realpath(public_path('assets/images'));
        $legacyFile = realpath(public_path('assets/images/' . $relativePath));

        if ($legacyRoot && $legacyFile && str_starts_with($legacyFile, $legacyRoot . DIRECTORY_SEPARATOR)) {
            return asset('assets/images/' . $relativePath);
        }

        return asset('assets/images/logo-phove.png');
    }
}
