<?php

use Illuminate\Support\Str;
use App\Helpers\ImageHelper;

if (!function_exists('seo_image')) {
    /**
     * Genera HTML de imagen optimizada para SEO
     */
    function seo_image($image, $alt, $type = 'news', $size = 'medium', $attributes = [])
    {
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

if (!function_exists('formatDuration')) {
    /**
     * Formatea segundos en formato de tiempo legible (minutos:segundos o horas:minutos:segundos)
     */
    function formatDuration($seconds) {
        if (!$seconds) return '0:00';
        
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        $remainingSeconds = $seconds % 60;
        
        if ($hours > 0) {
            return $hours . ':' . str_pad($remainingMinutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($remainingSeconds, 2, '0', STR_PAD_LEFT);
        }
        
        return $minutes . ':' . str_pad($remainingSeconds, 2, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('formatNumber')) {
    /**
     * Formatea números grandes en formato K (miles) o M (millones)
     */
    function formatNumber($num) {
        if (!$num) return '0';
        
        if ($num >= 1000000) {
            return number_format($num / 1000000, 1) . 'M';
        }
        
        if ($num >= 1000) {
            return number_format($num / 1000, 1) . 'K';
        }
        
        return number_format($num);
    }

}

if (!function_exists('getYoutubeApiKey')) {
    /**
     * Obtiene la API key de YouTube desde las diferentes fuentes disponibles
     */
    function getYoutubeApiKey()
    {
        // Obtener la plataforma
        $platform = \App\Models\VideoPlatform::where('code', 'youtube')->first();
        
        // Intentar obtener la API key de varias fuentes
        $dbKey = $platform ? $platform->api_key : null;
        $configKey = config('services.youtube.key');
        $envKey = env('YOUTUBE_API_KEY');
        
        // Retornar la primera que no sea nula
        return $dbKey ?: $configKey ?: $envKey;
    }
}