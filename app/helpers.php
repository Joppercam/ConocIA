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

        // Si ya viene con una estructura editorial razonable, solo saneamos residuos y lo devolvemos.
        if (news_content_has_usable_structure($content)) {
            return trim($content);
        }

        $text = html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace("/\r\n|\r/", "\n", $text);
        $text = preg_replace('/([a-záéíóúñ]{4,})([A-ZÁÉÍÓÚÑ][a-záéíóúñ]{3,})/u', '$1 $2', $text);
        $text = preg_replace('/([\.\!\?])([A-ZÁÉÍÓÚÑ¿"])/u', '$1 $2', $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        // Intentar rescatar subtítulos incrustados típicos de artículos importados
        // en texto plano, donde un encabezado aparece pegado al comienzo del párrafo.
        $headingStarters = '(?:Como|Si|Lo|Vamos|Ahora|Por|Además|Esto|Este|Esta|Estas|Estos|Tenemos|Puede|Puedes|Debería|Importante|En|Para)';
        $text = preg_replace('/(¿[^?\n]{6,90}\?)(?=\s*' . $headingStarters . '\b)/u', "\n\n$1\n\n", $text);
        $text = preg_replace('/([A-ZÁÉÍÓÚÑ][^\.!\?\n]{12,90}?)(?=\s+' . $headingStarters . '\b)/u', "\n\n$1\n\n", $text);
        $text = preg_replace('/(?<=[\.\!\?])\s+([A-ZÁÉÍÓÚÑ][A-Za-zÁÉÍÓÚÑáéíóúñ0-9"\-]{2,}(?:\s+[A-ZÁÉÍÓÚÑa-záéíóúñ0-9"\-]{2,}){1,8})(?=\s+(?:[A-ZÁÉÍÓÚÑ¿]|Si|Como|Lo|Vamos|Ahora|Por|Además|Esto|Este|Esta|Estas|Estos|Tenemos|Puede|Puedes|Debería|Importante|En|Para)\b)/u', "\n\n$1\n\n", $text);
        $text = preg_replace('/(Algunos [^\.!\?\n]{6,80})(?=\s+Cargador\b)/u', "\n\n$1\n\n", $text);
        $text = preg_replace('/(Cargador [A-Z0-9ÁÉÍÓÚÑ][^\.!\?\n]{2,60})(?=\s+(?:Este|Tenemos|Cuenta)\b)/u', "\n\n$1\n\n", $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        $chunks = preg_split("/\n{2,}/", $text) ?: [$text];
        $htmlChunks = [];

        foreach ($chunks as $chunk) {
            $chunk = trim($chunk);

            if ($chunk === '') {
                continue;
            }

            if (news_chunk_looks_like_heading($chunk)) {
                $htmlChunks[] = '<h2>' . e($chunk) . '</h2>';
                continue;
            }

            $sentences = preg_split('/(?<=[\.\!\?])\s+(?=[A-ZÁÉÍÓÚÑ0-9"])/u', $chunk) ?: [$chunk];
            $paragraphs = [];
            $buffer = [];

            foreach ($sentences as $sentence) {
                $sentence = trim($sentence);
                if ($sentence === '') {
                    continue;
                }

                if (news_chunk_looks_like_heading($sentence)) {
                    if (!empty($buffer)) {
                        $paragraphs[] = implode(' ', $buffer);
                        $buffer = [];
                    }
                    $paragraphs[] = '__HEADING__' . $sentence;
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

            foreach ($paragraphs as $paragraph) {
                if (str_starts_with($paragraph, '__HEADING__')) {
                    $htmlChunks[] = '<h2>' . e(str_replace('__HEADING__', '', $paragraph)) . '</h2>';
                } else {
                    $htmlChunks[] = '<p>' . e(trim($paragraph)) . '</p>';
                }
            }
        }

        return implode("\n", $htmlChunks);
    }
}

if (!function_exists('news_content_has_usable_structure')) {
    function news_content_has_usable_structure(string $content): bool
    {
        if (!preg_match('/<(p|h2|h3|ul|ol|blockquote|figure|iframe|img)\b/i', $content)) {
            return false;
        }

        $blockCount = preg_match_all('/<(p|h2|h3|ul|ol|blockquote|figure)\b/i', $content);
        $paragraphCount = preg_match_all('/<p\b/i', $content);
        $headingCount = preg_match_all('/<h2\b|<h3\b/i', $content);

        preg_match_all('/<p\b[^>]*>(.*?)<\/p>/is', $content, $paragraphs);
        $maxParagraphLength = 0;

        foreach ($paragraphs[1] ?? [] as $paragraph) {
            $length = mb_strlen(trim(strip_tags($paragraph)));
            $maxParagraphLength = max($maxParagraphLength, $length);
        }

        if ($headingCount >= 1 && $paragraphCount >= 2) {
            return true;
        }

        if ($paragraphCount >= 3 && $maxParagraphLength > 0 && $maxParagraphLength <= 900) {
            return true;
        }

        if ($blockCount >= 5 && $maxParagraphLength > 0 && $maxParagraphLength <= 900) {
            return true;
        }

        return false;
    }
}

if (!function_exists('news_content_looks_incomplete')) {
    /**
     * Detecta noticias importadas con cuerpo demasiado corto o truncado.
     */
    function news_content_looks_incomplete(?string $content): bool
    {
        if (blank($content)) {
            return true;
        }

        $text = trim(html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($text === '') {
            return true;
        }

        $wordCount = str_word_count($text);

        if ($wordCount < 80) {
            return true;
        }

        if (preg_match('/(?:\.\.\.|…)\s*$/u', $text)) {
            return true;
        }

        if (preg_match('/\b(?:así lo|lee también|más información|para seguir leyendo)\s*(?:\.\.\.|…)?\s*$/iu', $text)) {
            return true;
        }

        return false;
    }
}

if (!function_exists('news_text_looks_truncated')) {
    /**
     * Detecta textos editoriales que ya vienen cortados desde la fuente o desde
     * una generación previa. No se usa para castigar recortes visuales locales.
     */
    function news_text_looks_truncated(?string $text): bool
    {
        $text = trim(html_entity_decode(strip_tags((string) $text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($text === '') {
            return false;
        }

        if (preg_match('/(?:\.\.\.|…)\s*$/u', $text)) {
            return true;
        }

        return preg_match('/\b(?:así lo|lee también|más información|para seguir leyendo|continúa leyendo|ver más)\s*(?:\.\.\.|…)?\s*$/iu', $text) === 1;
    }
}

if (!function_exists('news_clean_editorial_text')) {
    function news_clean_editorial_text(?string $text): string
    {
        $clean = html_entity_decode(strip_tags((string) $text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $clean = preg_replace('/\s+/u', ' ', $clean) ?? '';
        $clean = preg_replace('/[[:cntrl:]]/u', '', $clean) ?? '';

        return trim($clean);
    }
}

if (!function_exists('news_sentence_teaser')) {
    /**
     * Recorta en borde de oración siempre que se pueda y nunca agrega "...".
     */
    function news_sentence_teaser(?string $text, int $limit = 220): string
    {
        $clean = news_clean_editorial_text($text);

        if ($clean === '') {
            return '';
        }

        if (mb_strlen($clean) <= $limit) {
            return rtrim($clean);
        }

        $slice = mb_substr($clean, 0, $limit);

        if (preg_match_all('/[\.!?](?=\s|$)/u', $slice, $matches, PREG_OFFSET_CAPTURE) && !empty($matches[0])) {
            $last = end($matches[0]);
            $position = $last[1] + strlen($last[0]);

            if ($position >= min(80, $limit)) {
                return rtrim(mb_substr($slice, 0, $position));
            }
        }

        $lastSpace = mb_strrpos($slice, ' ');

        if ($lastSpace !== false && $lastSpace >= min(80, $limit - 20)) {
            return rtrim(mb_substr($slice, 0, $lastSpace), " \t\n\r\0\x0B,;:");
        }

        return rtrim($slice, " \t\n\r\0\x0B,;:");
    }
}

if (!function_exists('news_editorial_teaser')) {
    /**
     * Elige una bajada pública sin elipsis artificiales. Si summary/excerpt ya
     * vienen truncados, usa contenido como respaldo.
     */
    function news_editorial_teaser(?string $summary, ?string $excerpt = null, ?string $content = null, int $limit = 220): string
    {
        foreach ([$summary, $excerpt] as $candidate) {
            $clean = news_clean_editorial_text($candidate);

            if ($clean !== '' && !news_text_looks_truncated($clean)) {
                return news_sentence_teaser($clean, $limit);
            }
        }

        $contentText = news_clean_editorial_text($content);

        if ($contentText !== '' && !news_text_looks_truncated($contentText)) {
            return news_sentence_teaser($contentText, $limit);
        }

        return '';
    }
}

if (!function_exists('news_chunk_looks_like_heading')) {
    function news_chunk_looks_like_heading(string $chunk): bool
    {
        $chunk = trim($chunk);

        if ($chunk === '') {
            return false;
        }

        $length = mb_strlen($chunk);
        $words  = preg_split('/\s+/u', $chunk) ?: [];
        $count  = count($words);

        if ($length < 8 || $length > 95 || $count < 2 || $count > 14) {
            return false;
        }

        if (str_contains($chunk, ':') || str_contains($chunk, ';')) {
            return false;
        }

        if (preg_match('/[\.!]\s*$/u', $chunk)) {
            return false;
        }

        if (preg_match('/^¿[^?]+\?$/u', $chunk)) {
            return true;
        }

        return preg_match('/^[A-ZÁÉÍÓÚÑ]/u', $chunk) === 1;
    }
}
