<?php

declare(strict_types=1);

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);

        $this->cloudinary = new Cloudinary();
    }

    /**
     * Upload un fichier vers Cloudinary.
     */
    public function upload(UploadedFile $file, string $folder = 'uploads'): ?string
    {
        try {
            $result = $this->cloudinary
                ->uploadApi()
                ->upload($file->getRealPath(), [
                    'folder'          => $folder,
                    'resource_type'   => 'auto',
                    'use_filename'    => true,
                    'unique_filename' => true,
                ]);

            return $result['secure_url'] ?? null;
        } catch (\Throwable $e) {
            Log::error('Cloudinary upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Supprime un fichier Cloudinary à partir de son URL.
     */
    public function delete(string $url): bool
    {
        try {
            $publicId = $this->extractPublicId($url);

            if (!$publicId) {
                return false;
            }

            $this->cloudinary->uploadApi()->destroy($publicId);
            return true;
        } catch (\Throwable $e) {
            Log::error('Cloudinary delete failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extrait le public_id depuis une URL Cloudinary.
     */
    protected function extractPublicId(string $url): ?string
    {
        if (!str_contains($url, 'cloudinary.com')) {
            return null;
        }

        $parts = explode('/upload/', $url);

        if (count($parts) < 2) {
            return null;
        }

        $path = $parts[1];

        $path = preg_replace('#^v\d+/#', '', $path);
        $publicId = preg_replace('/\.[^.]+$/', '', $path);

        return $publicId;
    }
}