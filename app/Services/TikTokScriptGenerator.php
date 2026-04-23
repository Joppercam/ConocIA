<?php

namespace App\Services;

use App\Models\News;
use App\Models\TikTokScript;
use App\Services\GeminiQuotaGuard;
use App\Services\OpenAIService;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokScriptGenerator
{
    protected string $apiKey;
    protected string $model;
    protected int $maxTokens;

    public function __construct()
    {
        $this->apiKey    = config('services.gemini.api_key', '');
        $this->model     = config('services.gemini.model', 'gemini-2.0-flash');
        $this->maxTokens = 500;
    }

    /**
     * Generar guión de TikTok para una noticia
     */
    public function generateScript(News $news): ?TikTokScript
    {
        try {
            $prompt   = $this->getPromptForArticle($news);
            $response = $this->callOpenAI($prompt);

            if (empty($response)) {
                $response = $this->callGemini($prompt);
            }

            return $this->processResponse($news, $response);
        } catch (Exception $e) {
            Log::error('Error generating TikTok script: ' . $e->getMessage(), [
                'news_id' => $news->id,
                'error'   => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Obtener el prompt adecuado según el tipo de noticia
     */
    protected function getPromptForArticle(News $news): string
    {
        $basePrompt  = "Genera un guión para TikTok basado en la siguiente noticia:\n\n";
        $basePrompt .= "TÍTULO: {$news->title}\n\n";
        $basePrompt .= "CONTENIDO: " . $this->truncateContent($news->content) . "\n\n";

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
        $maxChars = 1000;

        if (strlen($content) <= $maxChars) {
            return $content;
        }

        return substr($content, 0, $maxChars) . '...';
    }

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

    protected function callOpenAI(string $prompt): string
    {
        $openai = app(OpenAIService::class);

        if (!$openai->isAvailable()) {
            return '';
        }

        return $openai->generateText(
            $prompt,
            $this->maxTokens,
            0.7,
            'Eres un experto en creación de contenido viral para TikTok a partir de noticias. Conviertes artículos periodísticos en guiones breves pero impactantes, adecuados para audiencias jóvenes.'
        );
    }

    /**
     * Llamada a la API de Google Gemini
     */
    protected function callGemini(string $prompt): string
    {
        $guard = app(GeminiQuotaGuard::class);

        if (!$guard->canCall('low')) {
            throw new Exception('Gemini quota exceeded for TikTok scripts. ' . $guard->summary());
        }

        $response = Http::timeout(30)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}",
            [
                'system_instruction' => [
                    'parts' => [['text' => 'Eres un experto en creación de contenido viral para TikTok a partir de noticias. Conviertes artículos periodísticos en guiones breves pero impactantes, adecuados para audiencias jóvenes.']],
                ],
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature'     => 0.7,
                    'maxOutputTokens' => $this->maxTokens,
                ],
            ]
        );

        if ($response->failed()) {
            throw new Exception('Gemini API Error: ' . $response->body());
        }

        $guard->record();
        return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    /**
     * Procesar la respuesta de Gemini y crear el guión
     */
    protected function processResponse(News $news, string $content): TikTokScript
    {
        preg_match('/\[SCRIPT\](.*?)\[\/SCRIPT\]/s', $content, $scriptMatches);
        preg_match('/\[VISUALES\](.*?)\[\/VISUALES\]/s', $content, $visualMatches);
        preg_match('/\[HASHTAGS\](.*?)\[\/HASHTAGS\]/s', $content, $hashtagMatches);

        $script           = trim($scriptMatches[1] ?? '');
        $visualSuggestions = trim($visualMatches[1] ?? '');
        $hashtags         = trim($hashtagMatches[1] ?? '');

        return TikTokScript::create([
            'news_id'           => $news->id,
            'script_content'    => $script,
            'visual_suggestions' => $visualSuggestions,
            'hashtags'          => $hashtags,
            'status'            => 'pending_review',
            'tiktok_score'      => $news->tiktok_score ?? 0,
            'ai_response_raw'   => $content,
        ]);
    }
}
