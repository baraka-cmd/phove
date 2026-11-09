<?php

if (!function_exists('img_url')) {
    /**
     * Retourne une URL exploitable pour une image locale ou distante.
     *
     * - Les URL Cloudinary (HTTPS/HTTP) sont conservées telles quelles.
     * - Les chemins présents dans public/ sont servis directement.
     * - Les médias présents dans storage/app/public sont servis via /media/.
     * - Un chemin introuvable utilise le logo local de secours.
     *
     * @param  string|null  $path
     * @return string
     */
    function img_url(?string $path): string
    {
        if (!$path) {
            return asset('assets/images/logo-phove.png');
        }

        $path = trim($path);

        if ($path === '') {
            return asset('assets/images/logo-phove.png');
        }

        // Cloudinary et autres URL distantes : ne jamais les transformer.
        if (preg_match('~^https?://~i', $path)) {
            return $path;
        }

        // Normaliser les chemins locaux sans permettre de sortir des répertoires autorisés.
        $relativePath = ltrim(str_replace(chr(92), '/', $path), '/');

        if ($relativePath === '' || str_contains($relativePath, '../') || $relativePath === '..') {
            return asset('assets/images/logo-phove.png');
        }

        $publicRoot = realpath(public_path());
        $publicFile = realpath(public_path($relativePath));

        if (
            $publicRoot !== false &&
            $publicFile !== false &&
            str_starts_with($publicFile, $publicRoot . DIRECTORY_SEPARATOR) &&
            is_file($publicFile)
        ) {
            return asset($relativePath);
        }

        $storageRoot = realpath(storage_path('app/public'));
        $storageFile = realpath(storage_path('app/public/' . $relativePath));

        if (
            $storageRoot !== false &&
            $storageFile !== false &&
            str_starts_with($storageFile, $storageRoot . DIRECTORY_SEPARATOR) &&
            is_file($storageFile)
        ) {
            $encodedPath = implode('/', array_map('rawurlencode', explode('/', $relativePath)));

            return url('/media/' . $encodedPath);
        }

        // Compatibilité avec les anciens fichiers stockés dans public/assets/images.
        $legacyRoot = realpath(public_path('assets/images'));
        $legacyFile = realpath(public_path('assets/images/' . $relativePath));

        if (
            $legacyRoot !== false &&
            $legacyFile !== false &&
            str_starts_with($legacyFile, $legacyRoot . DIRECTORY_SEPARATOR) &&
            is_file($legacyFile)
        ) {
            return asset('assets/images/' . $relativePath);
        }

        return asset('assets/images/logo-phove.png');
    }
}
