<?php
// app/Helpers/ImageHelper.php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ImageHelper
{
    /**
     * Genera HTML de imagen con atributos optimizados para SEO
     *
     * @param string|null $image Ruta de la imagen
     * @param string $alt Texto alternativo para SEO
     * @param string $type Tipo de imagen (news, author, etc)
     * @param string $size Tamaño de la imagen (small, medium, large)
     * @param array $attributes Atributos HTML adicionales
     * @return string HTML de la imagen
     */
    public static function getSeoImage($image, $alt, $type = 'news', $size = 'medium', $attributes = [])
    {
        // Obtener URL de la imagen o default
        $url = self::getImageUrl($image, $type, $size);
        
        // Preparar atributos
        $attributesStr = '';
        foreach ($attributes as $key => $value) {
            $attributesStr .= " {$key}=\"{$value}\"";
        }
        
        // Sanear el texto alt para evitar problemas con comillas
        $safeAlt = htmlspecialchars($alt, ENT_QUOTES, 'UTF-8');
        
        // Generar HTML con loading="lazy" para mejor rendimiento
        return "<img src=\"{$url}\" alt=\"{$safeAlt}\" loading=\"lazy\"{$attributesStr}>";
    }
    
    /**
     * Obtiene la URL de la imagen o una imagen predeterminada
     *
     * @param string|null $imageName Nombre de la imagen
     * @param string $type Tipo (news, author, etc)
     * @param string $size Tamaño (small, medium, large)
     * @return string URL de la imagen
     */
    public static function getImageUrl($imageName, $type = 'news', $size = 'medium')
    {
        if (!$imageName || str_contains($imageName, 'default') || str_contains($imageName, 'placeholder')) {
            return Storage::url("images/defaults/{$type}-default-{$size}.jpg");
        }

        if (Str::startsWith($imageName, ['http://', 'https://'])) {
            return $imageName;
        }

        $normalized = ltrim($imageName, '/');

        if (Str::startsWith($normalized, 'storage/')) {
            $storagePath = Str::after($normalized, 'storage/');

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

    /**
     * Obtiene la URL de la imagen solo si existe de verdad; si no, devuelve null.
     *
     * @param string|null $imageName
     * @param string $type
     * @return string|null
     */
    public static function getImageUrlOrNull($imageName, $type = 'news')
    {
        if (!$imageName || str_contains($imageName, 'default') || str_contains($imageName, 'placeholder')) {
            return null;
        }

        if (Str::startsWith($imageName, ['http://', 'https://'])) {
            return $imageName;
        }

        $normalized = ltrim($imageName, '/');

        if (Str::startsWith($normalized, 'storage/')) {
            $storagePath = Str::after($normalized, 'storage/');

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

        return null;
    }

    /**
     * Determina si una imagen es usable.
     * Solo devuelve false cuando se puede confirmar que la imagen no existe.
     */
    public static function isValidImage($imageName, $type = 'news'): bool
    {
        if (!$imageName || str_contains($imageName, 'default') || str_contains($imageName, 'placeholder')) {
            return false;
        }

        if (Str::startsWith($imageName, ['http://', 'https://'])) {
            return self::remoteImageExists($imageName);
        }

        return self::getImageUrlOrNull($imageName, $type) !== null;
    }

    private static function remoteImageExists(string $url): bool
    {
        $cacheKey = 'remote_image_exists_' . md5($url);

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($url) {
            try {
                $response = Http::timeout(8)
                    ->withHeaders([
                        'User-Agent' => 'ConocIA Image Validator/1.0',
                        'Accept' => 'image/*,*/*;q=0.8',
                    ])
                    ->head($url);

                if ($response->successful() || ($response->status() >= 300 && $response->status() < 400)) {
                    return true;
                }

                if (in_array($response->status(), [404, 410], true)) {
                    return false;
                }

                if (in_array($response->status(), [403, 405], true)) {
                    $fallback = Http::timeout(8)
                        ->withHeaders([
                            'User-Agent' => 'ConocIA Image Validator/1.0',
                            'Accept' => 'image/*,*/*;q=0.8',
                            'Range' => 'bytes=0-0',
                        ])
                        ->get($url);

                    return $fallback->successful()
                        || $fallback->status() === 206
                        || ($fallback->status() >= 300 && $fallback->status() < 400);
                }
            } catch (\Throwable) {
                return true;
            }

            return true;
        });
    }
    
    /**
     * Método optimizado para plantillas Blade que verifican directamente
     * Esta función evita llamadas a Storage y usa lógica directa de strings
     * Puede ser utilizada en las plantillas para mejorar el rendimiento sin
     * modificar el comportamiento existente
     *
     * @param string|null $imageName Nombre de la imagen
     * @param string $type Tipo (news, author, etc)
     * @return array [boolean $hasImage, string|null $imageSrc]
     */
    public static function getOptimizedImageInfo($imageName, $type = 'research')
    {
        // Valores por defecto
        $hasImage = false;
        $imageSrc = null;
        
        // Verificación rápida sin consultar almacenamiento
        if (!empty($imageName) && 
            $imageName != 'default.jpg' && 
            !str_contains($imageName, 'default') && 
            !str_contains($imageName, 'placeholder')) {
                
            // Construir la URL directamente sin verificar existencia física
            if (Str::startsWith($imageName, 'storage/')) {
                $imageSrc = asset($imageName);
            } else {
                $imageSrc = asset("storage/{$type}/" . $imageName);
            }
                
            $hasImage = true;
        }
        
        return [$hasImage, $imageSrc];
    }
}
