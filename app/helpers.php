<?php

use Illuminate\Support\Str;
use App\Helpers\ImageHelper;

if (!function_exists('seo_image')) {
    /**
     * Genera HTML de imagen optimizada para SEO
     */
    function seo_image($image, $alt, $type = 'news', $size = 'medium', $attributes = [])
    {
        return ImageHelper::getSeoImage($image, $alt, $type, $size, $attributes);
    }
}


if (!function_exists('seo_image')) {
    /**
     * Genera HTML de imagen optimizada para SEO
     */
    function seo_image($image, $alt, $type = 'news', $size = 'medium', $attributes = []) {
        // Utiliza la función getImageUrl existente
        $url = getImageUrl($image, $type, $size);
        
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
}