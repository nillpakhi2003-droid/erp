<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageService
{
    protected $disk;

    public function __construct()
    {
        $this->disk = env('CDN_ENABLED', false) ? 'images' : 'public';
    }

    /**
     * Upload and optimize an image
     */
    public function upload(UploadedFile $file, string $path = 'uploads', array $options = []): string
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $path . '/' . $filename;

        // Store file to configured disk
        $file->storeAs($path, $filename, $this->disk);

        return $this->getUrl($fullPath);
    }

    /**
     * Upload thumbnail version
     * Note: Install intervention/image package for advanced image manipulation
     */
    public function uploadWithThumbnail(UploadedFile $file, string $path = 'uploads'): array
    {
        // For now, just upload the original
        // To enable thumbnails: composer require intervention/image
        $original = $this->upload($file, $path);

        return [
            'original' => $original,
            'thumbnail' => $original, // Same as original until intervention/image is installed
        ];
    }

    /**
     * Delete an image
     */
    public function delete(string $url): bool
    {
        $path = $this->urlToPath($url);
        
        if ($path && Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }

        return false;
    }

    /**
     * Get full URL for a path
     */
    public function getUrl(string $path): string
    {
        if (env('CDN_ENABLED', false)) {
            return env('CDN_URL') . '/' . $path;
        }

        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Convert URL to storage path
     */
    protected function urlToPath(string $url): ?string
    {
        $cdnUrl = env('CDN_URL', '');
        $appUrl = env('APP_URL', '');

        if ($cdnUrl && str_starts_with($url, $cdnUrl)) {
            return str_replace($cdnUrl . '/', '', $url);
        }

        if (str_starts_with($url, $appUrl)) {
            return str_replace($appUrl . '/storage/', '', $url);
        }

        return null;
    }
}
