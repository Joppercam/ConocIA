<?php

namespace App\Services;

use App\Models\TikTokScript;
use App\Services\GeminiQuotaGuard;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class TikTokKitGenerator
{
    protected string $openaiKey;
    protected string $geminiKey;
    protected string $geminiModel;

    public function __construct()
    {
        $this->openaiKey   = env('OPENAI_API_KEY', '');
        $this->geminiKey   = config('services.gemini.api_key', '');
        $this->geminiModel = config('services.gemini.model', 'gemini-2.0-flash');
    }

    /**
     * Genera el kit completo para un guión aprobado.
     * Devuelve true si todo fue exitoso, string con error si falló.
     */
    public function generate(TikTokScript $script): true|string
    {
        $title = $script->news?->title ?? 'ConocIA';

        // 1. Audio MP3
        $audioPath = $this->generateAudio($script->script_content, $script->id);
        if (!$audioPath) {
            return 'No se pudo generar el audio. Verificá que OPENAI_API_KEY tenga créditos.';
        }

        // 2. Caption + onscreen text
        [$caption, $onscreen] = $this->generateCaptionAndOnscreen($script->script_content, $title, $script->hashtags);

        $script->update([
            'audio_path'       => $audioPath,
            'caption'          => $caption,
            'onscreen_text'    => $onscreen,
            'kit_generated_at' => now(),
        ]);

        return true;
    }

    /**
     * Genera el MP3 con OpenAI TTS y lo guarda en storage.
     * Devuelve la ruta relativa o null si falla.
     */
    protected function generateAudio(string $script, int $scriptId): ?string
    {
        if (empty($this->openaiKey)) {
            Log::warning('TikTokKitGenerator: OPENAI_API_KEY no configurada.');
            return null;
        }

        // Limpiar el texto de markdown/HTML antes de enviar al TTS
        $clean = strip_tags($script);
        $clean = preg_replace('/\*+/', '', $clean);
        $clean = preg_replace('/#+\s/', '', $clean);
        $clean = trim($clean);

        try {
            $response = Http::timeout(60)
                ->withToken($this->openaiKey)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post('https://api.openai.com/v1/audio/speech', [
                    'model' => 'tts-1',
                    'input' => $clean,
                    'voice' => 'nova',   // nova: femenina natural en español; onyx: masculina
                    'speed' => 1.0,
                    'response_format' => 'mp3',
                ]);

            if ($response->failed()) {
                Log::error('TikTokKitGenerator TTS error: ' . $response->body());
                return null;
            }

            $dir  = 'tiktok-kits/' . $scriptId;
            $path = $dir . '/audio.mp3';

            Storage::disk('local')->makeDirectory($dir);
            Storage::disk('local')->put($path, $response->body());

            return $path;
        } catch (\Exception $e) {
            Log::error('TikTokKitGenerator TTS exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Genera caption para TikTok y frases onscreen usando Gemini (con fallback a OpenAI).
     * Devuelve [caption, onscreen_text].
     */
    protected function generateCaptionAndOnscreen(string $script, string $title, ?string $existingHashtags): array
    {
        $prompt = <<<PROMPT
Eres un experto en contenido para TikTok especializado en inteligencia artificial.

Basándote en este guión de TikTok:
---
{$script}
---

Genera DOS cosas en JSON:

1. "caption": La descripción del video para TikTok.
   - Máximo 150 caracteres de texto
   - Luego una línea vacía
   - 10-12 hashtags relevantes en español e inglés mezclados
   - Incluir siempre: #ConocIA #InteligenciaArtificial #IA
   - Formato final: texto\n\n#hashtag1 #hashtag2 ...

2. "onscreen": Array de 5 a 7 frases cortas para mostrar como texto en pantalla.
   - Cada frase: máximo 8 palabras
   - Son los momentos clave del guión
   - Impactantes, que enganchen al espectador
   - En español

Responde SOLO en JSON con las claves "caption" y "onscreen" (onscreen es un array de strings).
PROMPT;

        $data = $this->callAI($prompt);

        $caption = $data['caption'] ?? $this->buildFallbackCaption($title, $existingHashtags);
        $onscreen = isset($data['onscreen']) && is_array($data['onscreen'])
            ? implode("\n", $data['onscreen'])
            : $this->buildFallbackOnscreen($script);

        return [$caption, $onscreen];
    }

    protected function callAI(string $prompt): array
    {
        $guard = app(GeminiQuotaGuard::class);
        $openai = app(OpenAIService::class);

        try {
            if ($openai->isAvailable()) {
                $data = $openai->generateJson($prompt, 600, 0.7);
                if (!empty($data)) {
                    return $data;
                }
            }
        } catch (\Exception) {}

        try {
            if (!empty($this->geminiKey) && $guard->canCall('low')) {
                $r = Http::timeout(30)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent?key={$this->geminiKey}",
                    [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => [
                            'temperature'      => 0.7,
                            'maxOutputTokens'  => 600,
                            'responseMimeType' => 'application/json',
                        ],
                    ]
                );
                if ($r->successful()) {
                    $data = json_decode($r->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}', true);
                    if (!empty($data)) {
                        $guard->record();
                        return $data;
                    }
                }
            }
        } catch (\Exception) {}

        return [];
    }

    protected function buildFallbackCaption(string $title, ?string $existingHashtags): string
    {
        $base = \Illuminate\Support\Str::limit(strip_tags($title), 100);
        $tags = $existingHashtags ?: '#ConocIA #InteligenciaArtificial #IA #Tech #AINews';
        return $base . "\n\n" . $tags;
    }

    protected function buildFallbackOnscreen(string $script): string
    {
        // Extraer las primeras 6 frases cortas del script como fallback
        $sentences = preg_split('/(?<=[.!?])\s+/', strip_tags($script));
        $short = array_filter($sentences, fn($s) => str_word_count($s) <= 10 && strlen($s) > 10);
        return implode("\n", array_slice(array_values($short), 0, 6));
    }

    /**
     * Genera un ZIP con todos los archivos del kit.
     * Devuelve la ruta absoluta al ZIP o null si falla.
     */
    public function buildZip(TikTokScript $script): ?string
    {
        if (!$script->kit_generated_at) return null;

        $dir     = storage_path('app/tiktok-kits/' . $script->id);
        $zipPath = $dir . '/conocia-tiktok-kit-' . $script->id . '.zip';

        if (!is_dir($dir)) return null;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return null;
        }

        // Audio
        $audioAbsolute = storage_path('app/' . $script->audio_path);
        if (file_exists($audioAbsolute)) {
            $zip->addFile($audioAbsolute, 'audio.mp3');
        }

        // Caption
        if ($script->caption) {
            $zip->addFromString('caption.txt', $script->caption);
        }

        // Onscreen text
        if ($script->onscreen_text) {
            $header  = "FRASES PARA MOSTRAR EN PANTALLA (texto overlay)\n";
            $header .= "Usá cada línea en un momento diferente del video.\n";
            $header .= str_repeat('-', 40) . "\n\n";
            $zip->addFromString('onscreen-text.txt', $header . $script->onscreen_text);
        }

        // Guión completo
        $scriptHeader  = "GUIÓN COMPLETO — ConocIA TikTok\n";
        $scriptHeader .= "Noticia: " . ($script->news?->title ?? '') . "\n";
        $scriptHeader .= "Generado: " . $script->kit_generated_at?->format('d/m/Y H:i') . "\n";
        $scriptHeader .= str_repeat('-', 40) . "\n\n";
        $zip->addFromString('guion.txt', $scriptHeader . strip_tags($script->script_content));

        $zip->close();

        return $zipPath;
    }
}
