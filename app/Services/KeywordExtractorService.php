<?php

namespace App\Services;

use OpenAI\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class KeywordExtractorService
{
    protected $openai;

    public function __construct(Client $openai)
    {
        $this->openai = $openai;
    }

    /**
     * Extraer palabras clave de un texto
     *
     * @param string $text
     * @param int $count
     * @return array
     */
    public function extractKeywords(string $text, int $count = 5): array
    {
        // Crear un hash único para el texto
        $cacheKey = 'keywords_' . md5($text . '_' . $count);
        
        return Cache::remember($cacheKey, now()->addDays(7), function () use ($text, $count) {
            try {
                // Versión actualizada de la llamada a la API
                $response = $this->openai->chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Extrae las ' . $count . ' palabras clave más relevantes del siguiente texto. Devuelve únicamente las palabras separadas por comas, sin numeración ni texto adicional:'],
                        ['role' => 'user', 'content' => $text]
                    ],
                    'max_tokens' => 100,
                    'temperature' => 0.3,
                ]);

                $result = $response->choices[0]->message->content;
                
                // Limpiar y convertir a array
                $keywords = explode(',', $result);
                $keywords = array_map('trim', $keywords);
                
                // Filtrar palabras vacías
                $keywords = array_filter($keywords, function($keyword) {
                    return !empty($keyword);
                });
                
                // Limitar al número solicitado
                return array_slice($keywords, 0, $count);
                
            } catch (\Exception $e) {
                Log::error('Error al extraer palabras clave: ' . $e->getMessage());
                
                // En caso de error, intentar extraer palabras clave usando un método simple
                return $this->fallbackKeywordExtraction($text, $count);
            }
        });
    }
    
    /**
     * Método alternativo para extraer palabras clave sin usar OpenAI
     */
    protected function fallbackKeywordExtraction(string $text, int $count = 5): array
    {
        // Eliminar signos de puntuación y convertir a minúsculas
        $text = strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', '', $text));
        
        // Palabras de parada comunes (stopwords) en español
        $stopwords = ['a', 'al', 'algo', 'algunas', 'algunos', 'ante', 'antes', 'como', 'con', 'contra', 'cual', 'cuando', 'de', 'del', 'desde', 'donde', 'durante', 'e', 'el', 'ella', 'ellas', 'ellos', 'en', 'entre', 'era', 'erais', 'eran', 'eras', 'eres', 'es', 'esa', 'esas', 'ese', 'eso', 'esos', 'esta', 'estaba', 'estaban', 'estado', 'estais', 'estamos', 'estan', 'estar', 'estas', 'este', 'esto', 'estos', 'estoy', 'etcétera', 'fue', 'fuera', 'fueron', 'fui', 'fuimos', 'ha', 'habéis', 'había', 'habían', 'habías', 'han', 'has', 'hasta', 'hay', 'haya', 'he', 'hemos', 'hicieron', 'hizo', 'hubo', 'la', 'las', 'le', 'les', 'lo', 'los', 'me', 'mi', 'mía', 'mías', 'mientras', 'mío', 'míos', 'mis', 'mucho', 'muchos', 'muy', 'nada', 'ni', 'no', 'nos', 'nosotras', 'nosotros', 'nuestra', 'nuestras', 'nuestro', 'nuestros', 'o', 'os', 'otra', 'otras', 'otro', 'otros', 'para', 'pero', 'poco', 'por', 'porque', 'que', 'quien', 'quienes', 'qué', 'se', 'sea', 'seáis', 'sean', 'seas', 'ser', 'si', 'siendo', 'sin', 'sobre', 'sois', 'somos', 'son', 'soy', 'su', 'sus', 'suya', 'suyas', 'suyo', 'suyos', 'sí', 'también', 'tanto', 'te', 'tenéis', 'tenemos', 'tener', 'tengo', 'ti', 'tiempo', 'tiene', 'tienen', 'tienes', 'todo', 'todos', 'tu', 'tus', 'tú', 'un', 'una', 'unas', 'uno', 'unos', 'vosotras', 'vosotros', 'vuestra', 'vuestras', 'vuestro', 'vuestros', 'y', 'ya', 'yo'];
        
        // Dividir en palabras
        $words = explode(' ', $text);
        
        // Filtrar stopwords y palabras cortas
        $filteredWords = array_filter($words, function($word) use ($stopwords) {
            return !in_array($word, $stopwords) && strlen($word) > 3;
        });
        
        // Contar frecuencia de palabras
        $wordCount = array_count_values($filteredWords);
        
        // Ordenar por frecuencia
        arsort($wordCount);
        
        // Tomar las N palabras más frecuentes
        return array_slice(array_keys($wordCount), 0, $count);
    }
}