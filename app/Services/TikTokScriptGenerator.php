<?php

namespace App\Services;

use App\Models\News; // Cambiado de Article a News
use App\Models\TikTokScript;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokScriptGenerator
{
    protected $apiKey;
    protected $model;
    protected $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->model = config('services.openai.model', 'gpt-4');
        $this->maxTokens = config('services.openai.max_tokens', 500);
    }

    /**
     * Generar guión de TikTok para una noticia
     *
     * @param News $news
     * @return TikTokScript|null
     */
    public function generateScript(News $news): ?TikTokScript
    {
        try {
            // Obtener el prompt adecuado según la categoría de la noticia
            $prompt = $this->getPromptForArticle($news);
            
            // Llamar a la API de OpenAI
            $response = $this->callOpenAI($prompt);
            
            // Procesar la respuesta y crear el guión
            return $this->processResponse($news, $response);
        } catch (Exception $e) {
            Log::error('Error generating TikTok script: ' . $e->getMessage(), [
                'news_id' => $news->id,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Obtener el prompt adecuado según el tipo de noticia
     */
    protected function getPromptForArticle(News $news): string
    {
        // Base del prompt
        $basePrompt = "Genera un guión para TikTok basado en la siguiente noticia:\n\n";
        $basePrompt .= "TÍTULO: {$news->title}\n\n";
        $basePrompt .= "CONTENIDO: " . $this->truncateContent($news->content) . "\n\n";
        
        // Instrucciones según el tipo de contenido
        $category = $news->category->name ?? '';
        
        switch (strtolower($category)) {
            case 'noticias':
                return $basePrompt . $this->getNewsPrompt();
            case 'investigaciones':
                return $basePrompt . $this->getInvestigationPrompt();
            case 'columnas':
                return $basePrompt . $this->getColumnPrompt();
            default:
                return $basePrompt . $this->getDefaultPrompt();
        }
    }
    
    /**
     * Truncar contenido para no exceder límites de tokens
     */
    protected function truncateContent(string $content): string
    {
        // Estimación aproximada: 1 token ≈ 4 caracteres para español
        $maxChars = 1000; // ~250 tokens para el contenido
        
        if (strlen($content) <= $maxChars) {
            return $content;
        }
        
        // Truncar y añadir indicador
        return substr($content, 0, $maxChars) . '...';
    }
    
    /**
     * Prompt para noticias
     */
    protected function getNewsPrompt(): string
    {
        return "Requisitos para el guión de noticia en TikTok:
- Duración: 30-60 segundos (aproximadamente 100-150 palabras)
- Tono: informativo pero conversacional, adecuado para audiencia joven
- Estructura: introducción impactante, 2-3 puntos clave, conclusión
- Incluye 2-3 sugerencias de elementos visuales que deberían acompañar el guión
- Añade 1-2 hashtags relevantes para maximizar alcance
- Termina con una referencia a nuestro portal para dirigir tráfico

FORMATO DE RESPUESTA:
[SCRIPT]
El guión completo aquí...
[/SCRIPT]

[VISUALES]
- Sugerencia visual 1
- Sugerencia visual 2
- Sugerencia visual 3
[/VISUALES]

[HASHTAGS]
#hashtag1 #hashtag2
[/HASHTAGS]";
    }
    
    /**
     * Prompt para investigaciones
     */
    protected function getInvestigationPrompt(): string
    {
        return "Requisitos para el guión de investigación en TikTok:
- Duración: 45-60 segundos (aproximadamente 120-150 palabras)
- Tono: revelador, intrigante, que genere curiosidad
- Estructura: pregunta provocativa, datos sorprendentes, conclusión impactante
- Incluye 2-3 sugerencias de elementos visuales que deberían acompañar el guión
- Añade 1-2 hashtags relevantes para maximizar alcance
- Termina invitando a leer la investigación completa en nuestro portal

FORMATO DE RESPUESTA:
[SCRIPT]
El guión completo aquí...
[/SCRIPT]

[VISUALES]
- Sugerencia visual 1
- Sugerencia visual 2
- Sugerencia visual 3
[/VISUALES]

[HASHTAGS]
#hashtag1 #hashtag2
[/HASHTAGS]";
    }
    
    /**
     * Prompt para columnas de opinión
     */
    protected function getColumnPrompt(): string
    {
        return "Requisitos para el guión de columna de opinión en TikTok:
- Duración: 30-45 segundos (aproximadamente 100-120 palabras)
- Tono: persuasivo, con personalidad, que incite a la reflexión
- Estructura: afirmación provocativa, argumento principal, llamado a la acción
- Incluye 2-3 sugerencias de elementos visuales que deberían acompañar el guión
- Añade 1-2 hashtags relevantes para maximizar alcance
- Menciona al columnista y termina invitando a leer la columna completa

FORMATO DE RESPUESTA:
[SCRIPT]
El guión completo aquí...
[/SCRIPT]

[VISUALES]
- Sugerencia visual 1
- Sugerencia visual 2
- Sugerencia visual 3
[/VISUALES]

[HASHTAGS]
#hashtag1 #hashtag2
[/HASHTAGS]";
    }
    
    /**
     * Prompt predeterminado
     */
    protected function getDefaultPrompt(): string
    {
        return "Requisitos para el guión de TikTok:
- Duración: 30-60 segundos (aproximadamente 100-150 palabras)
- Tono: informativo pero entretenido, adecuado para audiencia joven
- Estructura: gancho inicial, desarrollo conciso, conclusión memorable
- Incluye 2-3 sugerencias de elementos visuales que deberían acompañar el guión
- Añade 1-2 hashtags relevantes para maximizar alcance
- Termina con una referencia a nuestro portal para dirigir tráfico

FORMATO DE RESPUESTA:
[SCRIPT]
El guión completo aquí...
[/SCRIPT]

[VISUALES]
- Sugerencia visual 1
- Sugerencia visual 2
- Sugerencia visual 3
[/VISUALES]

[HASHTAGS]
#hashtag1 #hashtag2
[/HASHTAGS]";
    }
    
    /**
     * Realizar la llamada a la API de OpenAI
     */
    protected function callOpenAI(string $prompt): array
    {
        $response = Http::withoutVerifying()  // Añadir esta línea
        ->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un experto en creación de contenido viral para TikTok a partir de noticias. Conviertes artículos periodísticos en guiones breves pero impactantes, adecuados para audiencias jóvenes.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => $this->maxTokens,
            'temperature' => 0.7,
        ]);
        
        if ($response->failed()) {
            throw new Exception('OpenAI API Error: ' . $response->body());
        }
        
        return $response->json();
    }
    
    /**
     * Procesar la respuesta de OpenAI y crear el guión
     */
    protected function processResponse(News $news, array $openAIResponse): TikTokScript
    {
        // Extraer el contenido de la respuesta
        $content = $openAIResponse['choices'][0]['message']['content'] ?? '';
        
        // Extraer las partes del guión usando expresiones regulares
        preg_match('/\[SCRIPT\](.*?)\[\/SCRIPT\]/s', $content, $scriptMatches);
        preg_match('/\[VISUALES\](.*?)\[\/VISUALES\]/s', $content, $visualMatches);
        preg_match('/\[HASHTAGS\](.*?)\[\/HASHTAGS\]/s', $content, $hashtagMatches);
        
        $script = trim($scriptMatches[1] ?? '');
        $visualSuggestions = trim($visualMatches[1] ?? '');
        $hashtags = trim($hashtagMatches[1] ?? '');
        
        // Crear el guión en la base de datos
        return TikTokScript::create([
            'news_id' => $news->id, // Cambiado de article_id a news_id
            'script_content' => $script,
            'visual_suggestions' => $visualSuggestions,
            'hashtags' => $hashtags,
            'status' => 'pending_review',
            'tiktok_score' => $news->tiktok_score ?? 0,
            'ai_response_raw' => json_encode($openAIResponse)
        ]);
    }
}