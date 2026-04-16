<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class KeywordExtractorService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
        $this->model  = config('services.gemini.model', 'gemini-2.0-flash');
    }

    /**
     * Extraer palabras clave de un texto
     */
    public function extractKeywords(string $text, int $count = 5): array
    {
        $cacheKey = 'keywords_' . md5($text . '_' . $count);

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($text, $count) {
            // Siempre usamos extracción local para no consumir cuota de Gemini.
            // Las keywords son de bajo valor vs el coste de API (~50 req/día free tier).
            return $this->fallbackKeywordExtraction($text, $count);
        });
    }

    /**
     * Extracción local sin API (fallback)
     */
    protected function fallbackKeywordExtraction(string $text, int $count = 5): array
    {
        $text = strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', '', $text));

        $stopwords = ['a', 'al', 'algo', 'algunas', 'algunos', 'ante', 'antes', 'como', 'con', 'contra', 'cual', 'cuando', 'de', 'del', 'desde', 'donde', 'durante', 'e', 'el', 'ella', 'ellas', 'ellos', 'en', 'entre', 'era', 'erais', 'eran', 'eras', 'eres', 'es', 'esa', 'esas', 'ese', 'eso', 'esos', 'esta', 'estaba', 'estaban', 'estado', 'estais', 'estamos', 'estan', 'estar', 'estas', 'este', 'esto', 'estos', 'estoy', 'fue', 'fuera', 'fueron', 'fui', 'fuimos', 'ha', 'habeis', 'habia', 'habian', 'habias', 'han', 'has', 'hasta', 'hay', 'haya', 'he', 'hemos', 'hicieron', 'hizo', 'hubo', 'la', 'las', 'le', 'les', 'lo', 'los', 'me', 'mi', 'mientras', 'mis', 'mucho', 'muchos', 'muy', 'nada', 'ni', 'no', 'nos', 'nosotras', 'nosotros', 'nuestra', 'nuestras', 'nuestro', 'nuestros', 'o', 'os', 'otra', 'otras', 'otro', 'otros', 'para', 'pero', 'poco', 'por', 'porque', 'que', 'quien', 'quienes', 'se', 'sea', 'sean', 'seas', 'ser', 'si', 'siendo', 'sin', 'sobre', 'sois', 'somos', 'son', 'soy', 'su', 'sus', 'suya', 'suyas', 'suyo', 'suyos', 'tambien', 'tanto', 'te', 'teneis', 'tenemos', 'tener', 'tengo', 'ti', 'tiempo', 'tiene', 'tienen', 'tienes', 'todo', 'todos', 'tu', 'tus', 'un', 'una', 'unas', 'uno', 'unos', 'y', 'ya', 'yo'];

        $words = explode(' ', $text);
        $filteredWords = array_filter($words, fn($w) => !in_array($w, $stopwords) && strlen($w) > 3);

        $wordCount = array_count_values($filteredWords);
        arsort($wordCount);

        return array_slice(array_keys($wordCount), 0, $count);
    }
}
