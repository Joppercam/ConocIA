<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('getImageUrl')) {
    /**
     * Obtiene la URL de la imagen o una imagen predeterminada
     * @param string|null $imageName Nombre de la imagen
     * @param string $type Tipo (news, author, etc)
     * @param string $size Tamaño (small, medium, large)
     * @return string URL de la imagen
     */
    function getImageUrl($imageName, $type = 'news', $size = 'medium')
    {
        if (!$imageName || str_contains($imageName, 'default') || str_contains($imageName, 'placeholder')) {
            return Storage::url("images/defaults/{$type}-default-{$size}.jpg");
        }

        if (\Illuminate\Support\Str::startsWith($imageName, ['http://', 'https://'])) {
            return $imageName;
        }

        $normalized = ltrim($imageName, '/');

        if (\Illuminate\Support\Str::startsWith($normalized, 'storage/')) {
            $storagePath = \Illuminate\Support\Str::after($normalized, 'storage/');

            if (Storage::disk('public')->exists($storagePath)) {
                return asset($normalized);
            }

            $normalized = basename($storagePath);
        }

        foreach ([
            "images/{$type}/{$normalized}",
            "{$type}/{$normalized}",
            $normalized,
        ] as $candidate) {
            if (Storage::disk('public')->exists($candidate)) {
                return Storage::url($candidate);
            }
        }

        if (file_exists(public_path($normalized))) {
            return asset($normalized);
        }

        return Storage::url("images/defaults/{$type}-default-{$size}.jpg");
    }
}

/**
 * Alias de getImageUrl() para mantener compatibilidad con código anterior
 */
if (!function_exists('getNewsImage')) {
    function getNewsImage($imageName, $type = 'news', $size = 'medium') {
        return getImageUrl($imageName, $type, $size);
    }
}
