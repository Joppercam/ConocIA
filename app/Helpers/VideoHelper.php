<?php

namespace App\Helpers;

class VideoHelper
{
    /**
     * Formatea la duración en segundos a un formato legible
     *
     * @param int|null $seconds
     * @return string
     */
    public static function formatDuration($seconds)
    {
        if (!$seconds) {
            return '0:00';
        }
        
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        $remainingSeconds = $seconds % 60;
        
        if ($hours > 0) {
            return $hours . ':' . str_pad($remainingMinutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($remainingSeconds, 2, '0', STR_PAD_LEFT);
        }
        
        return $minutes . ':' . str_pad($remainingSeconds, 2, '0', STR_PAD_LEFT);
    }
    
    /**
     * Formatea un número grande a un formato más legible (K, M)
     *
     * @param int|null $number
     * @return string
     */
    public static function formatNumber($number)
    {
        if (!$number) {
            return '0';
        }
        
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        }
        
        if ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        
        return number_format($number);
    }
    
    /**
     * Formatea una fecha relativa (hace X tiempo)
     *
     * @param \DateTime|string $date
     * @return string
     */
    public static function formatRelativeDate($date)
    {
        if (!$date) {
            return '';
        }
        
        if (is_string($date)) {
            $date = new \DateTime($date);
        }
        
        $now = new \DateTime();
        $diff = $now->diff($date);
        
        if ($diff->days == 0) {
            if ($diff->h == 0) {
                if ($diff->i == 0) {
                    return 'Justo ahora';
                }
                return 'Hace ' . $diff->i . ' ' . ($diff->i == 1 ? 'minuto' : 'minutos');
            }
            return 'Hace ' . $diff->h . ' ' . ($diff->h == 1 ? 'hora' : 'horas');
        } 
        
        if ($diff->days == 1) {
            return 'Ayer';
        }
        
        if ($diff->days < 7) {
            return 'Hace ' . $diff->days . ' días';
        }
        
        if ($diff->days < 30) {
            $weeks = floor($diff->days / 7);
            return 'Hace ' . $weeks . ' ' . ($weeks == 1 ? 'semana' : 'semanas');
        }
        
        if ($diff->days < 365) {
            $months = floor($diff->days / 30);
            return 'Hace ' . $months . ' ' . ($months == 1 ? 'mes' : 'meses');
        }
        
        $years = floor($diff->days / 365);
        return 'Hace ' . $years . ' ' . ($years == 1 ? 'año' : 'años');
    }
    
    /**
     * Extrae el ID de un video a partir de la URL
     *
     * @param string $url
     * @return array|null ['platform' => string, 'id' => string]
     */
    public static function extractVideoIdFromUrl($url)
    {
        // YouTube
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches) ||
            preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches) ||
            preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return [
                'platform' => 'youtube',
                'id' => $matches[1]
            ];
        }
        
        // Vimeo
        if (preg_match('/vimeo\.com\/([0-9]+)/', $url, $matches) ||
            preg_match('/player\.vimeo\.com\/video\/([0-9]+)/', $url, $matches)) {
            return [
                'platform' => 'vimeo',
                'id' => $matches[1]
            ];
        }
        
        // Dailymotion
        if (preg_match('/dailymotion\.com\/video\/([a-zA-Z0-9]+)/', $url, $matches) ||
            preg_match('/dai\.ly\/([a-zA-Z0-9]+)/', $url, $matches)) {
            return [
                'platform' => 'dailymotion',
                'id' => $matches[1]
            ];
        }
        
        return null;
    }
    
    /**
     * Extrae palabras clave de un texto
     *
     * @param string $text
     * @param int $limit
     * @return array
     */
    public static function extractKeywords($text, $limit = 5)
    {
        // Eliminar HTML
        $text = strip_tags($text);
        
        // Convertir a minúsculas
        $text = strtolower($text);
        
        // Eliminar caracteres especiales
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        
        // Dividir en palabras
        $words = preg_split('/\s+/', $text);
        
        // Filtrar palabras comunes y cortas
        $commonWords = [
            'el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas', 'y', 'o', 'pero', 
            'porque', 'como', 'que', 'para', 'por', 'del', 'al', 'con', 'en', 'a', 
            'de', 'es', 'son', 'fue', 'han', 'este', 'esta', 'estos', 'estas', 'ser',
            'hacer', 'tener', 'decir', 'estar', 'ir', 'ver', 'dar', 'cada', 'entre',
            'sobre', 'también', 'hasta', 'desde', 'sin', 'contra', 'durante', 'según',
            'cuando', 'donde', 'quien', 'quienes', 'algo', 'alguien', 'todo', 'nada'
        ];
        
        $filteredWords = array_filter($words, function($word) use ($commonWords) {
            return !in_array($word, $commonWords) && strlen($word) > 3;
        });
        
        // Contar frecuencia de palabras
        $wordCounts = array_count_values($filteredWords);
        
        // Ordenar por frecuencia
        arsort($wordCounts);
        
        // Tomar las palabras más frecuentes
        return array_slice(array_keys($wordCounts), 0, $limit);
    }
    
    /**
     * Recorta un texto a una longitud específica sin cortar palabras
     *
     * @param string $text
     * @param int $length
     * @param string $append
     * @return string
     */
    public static function truncateText($text, $length = 150, $append = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        $text = substr($text, 0, $length);
        $lastSpace = strrpos($text, ' ');
        
        if ($lastSpace !== false) {
            $text = substr($text, 0, $lastSpace);
        }
        
        return $text . $append;
    }
    
    /**
     * Genera una miniatura de fallback para cuando no hay thumbnail disponible
     *
     * @param string $title
     * @param string $size
     * @return string URL de la imagen
     */
    public static function generatePlaceholderThumbnail($title, $size = '16:9')
    {
        // Obtener iniciales del título
        $words = explode(' ', $title);
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
                if (strlen($initials) >= 2) {
                    break;
                }
            }
        }
        
        // Si no hay suficientes iniciales, usar 'VD' (Video Default)
        if (strlen($initials) < 1) {
            $initials = 'VD';
        }
        
        // Generar color basado en el título (para tener consistencia)
        $hash = md5($title);
        $hue = hexdec(substr($hash, 0, 2)) % 360;
        $saturation = 75; // 75%
        $lightness = 45; // 45%
        
        // Dimensiones basadas en la relación de aspecto
        $dimensions = '1280x720'; // Por defecto 16:9
        if ($size === '1:1') {
            $dimensions = '800x800';
        } elseif ($size === '4:3') {
            $dimensions = '1024x768';
        }
        
        // Construir URL para un servicio como placeholder.com o imgix
        // Aquí usamos placeholder.com como ejemplo
        return "https://via.placeholder.com/{$dimensions}/{$hash}/FFFFFF?text={$initials}";
    }
    
    /**
     * Convierte segundos en un formato legible de duración (1h 30m 20s)
     *
     * @param int $seconds
     * @return string
     */
    public static function formatDurationText($seconds)
    {
        if (!$seconds) {
            return '0 segundos';
        }
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        $result = '';
        
        if ($hours > 0) {
            $result .= $hours . 'h ';
        }
        
        if ($minutes > 0 || $hours > 0) {
            $result .= $minutes . 'm ';
        }
        
        if ($secs > 0 || empty($result)) {
            $result .= $secs . 's';
        }
        
        return trim($result);
    }
    
    /**
     * Convierte el nombre de la plataforma a su clase de ícono correspondiente
     *
     * @param string $platform
     * @return string
     */
    public static function getPlatformIcon($platform)
    {
        $icons = [
            'youtube' => 'fab fa-youtube',
            'vimeo' => 'fab fa-vimeo-v',
            'dailymotion' => 'fas fa-play',
            'default' => 'fas fa-video'
        ];
        
        return $icons[strtolower($platform)] ?? $icons['default'];
    }
    
    /**
     * Obtiene el color de la plataforma para usar en badges, etc.
     *
     * @param string $platform
     * @return string
     */
    public static function getPlatformColor($platform)
    {
        $colors = [
            'youtube' => 'danger',
            'vimeo' => 'info',
            'dailymotion' => 'primary',
            'default' => 'secondary'
        ];
        
        return $colors[strtolower($platform)] ?? $colors['default'];
    }
}