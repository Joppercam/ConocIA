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
        // Verificar si la imagen existe
        if ($imageName && !str_contains($imageName, 'default') && 
            Storage::disk('public')->exists("images/{$type}/{$imageName}")) {
            return Storage::url("images/{$type}/{$imageName}");
        }
        
        // Imagen predeterminada
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