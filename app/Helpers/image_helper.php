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

        if (preg_match('/^https?:\\/\\//i', $path)) {
            return $path;
        }

        $relativePath = ltrim(str_replace(chr(92), '/', $path), '/');
        $storageFile = storage_path('app/public/' . $relativePath);

        if (is_file($storageFile)) {
            $encodedPath = implode('/', array_map('rawurlencode', explode('/', $relativePath)));
            return url('/media/' . $encodedPath);
        }

        $legacyAsset = public_path('assets/images/' . $relativePath);
        if (is_file($legacyAsset)) {
            return asset('assets/images/' . $relativePath);
        }

        return asset('assets/images/logo-phove.png');
    }
}
