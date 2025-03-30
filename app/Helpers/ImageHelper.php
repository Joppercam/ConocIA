<?php
// app/Helpers/ImageHelper.php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

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
        // Verificar si la imagen existe
        if ($imageName && !str_contains($imageName, 'default') && 
            Storage::disk('public')->exists("images/{$type}/{$imageName}")) {
            return Storage::url("images/{$type}/{$imageName}");
        }
        
        // Imagen predeterminada
        return Storage::url("images/defaults/{$type}-default-{$size}.jpg");
    }
}