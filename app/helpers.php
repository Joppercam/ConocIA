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

if (!function_exists('format_news_content')) {
    /**
     * Formatea contenido editorial para evitar bloques ilegibles cuando una noticia
     * llega como texto plano o con residuos incrustados desde una fuente externa.
     */
    function format_news_content(?string $content): string
    {
        if (blank($content)) {
            return '';
        }

        $content = trim($content);

        // Eliminar scripts inline y residuos de widgets/embeds.
        $content = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $content);
        $content = preg_replace('/\{\s*"videoId"\s*:\s*"[^"]+".*?\}/is', '', $content);
        $content = preg_replace('/\(function\(\)\s*\{[\s\S]*$/i', '', $content);

        // Eliminar bloques editoriales de origen que no aportan al artículo.
        $cleanupPatterns = [
            '/Índice de Contenidos\s*\(\d+\).*?(?=(El primer filtro|Vamos al turrón|[A-ZÁÉÍÓÚÑ][^\.]{0,80}\.))/isu',
            '/Algunos de los enlaces de este artículo son afiliados.*$/isu',
            '/Imágenes\s*\|.*$/isu',
            '/En Xataka\s*\|.*$/isu',
            '/- La noticia .* fue publicada originalmente .*$/isu',
        ];

        foreach ($cleanupPatterns as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }

        // Si ya viene estructurado en HTML, solo saneamos residuos y lo devolvemos.
        if (preg_match('/<(p|h2|h3|ul|ol|blockquote|figure|iframe|img)\b/i', $content)) {
            return trim($content);
        }

        $text = html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace("/\r\n|\r/", "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        // Intentar separar frases para reconstruir párrafos legibles.
        $sentences = preg_split('/(?<=[\.\!\?])\s+(?=[A-ZÁÉÍÓÚÑ0-9"])/u', $text) ?: [$text];
        $paragraphs = [];
        $buffer = [];

        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if ($sentence === '') {
                continue;
            }

            $buffer[] = $sentence;

            if (count($buffer) >= 2 || mb_strlen(implode(' ', $buffer)) > 420) {
                $paragraphs[] = implode(' ', $buffer);
                $buffer = [];
            }
        }

        if (!empty($buffer)) {
            $paragraphs[] = implode(' ', $buffer);
        }

        $paragraphs = array_values(array_filter(array_map(
            fn ($p) => trim($p),
            $paragraphs
        )));

        return collect($paragraphs)
            ->map(fn ($p) => '<p>' . e($p) . '</p>')
            ->implode("\n");
    }
}
