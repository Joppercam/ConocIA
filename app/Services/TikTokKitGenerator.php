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
    protected string $geminiKey;
    protected string $geminiModel;
    protected string $googleTtsKey;

    public function __construct()
    {
        $this->geminiKey    = config('services.gemini.api_key', '');
        $this->geminiModel  = config('services.gemini.model', 'gemini-2.0-flash');
        $this->googleTtsKey = config('services.google_tts.key', '');
    }

    public function generate(TikTokScript $script): true|string
    {
        $title = $script->news?->title ?? 'ConocIA';

        $audioPath = $this->generateAudio($script);
        if (!$audioPath) {
            return 'No se pudo generar el audio.';
        }

        [$caption, $onscreen] = $this->generateCaptionAndOnscreen($script->script_content, $title, $script->hashtags);

        $script->update([
            'audio_path'       => $audioPath,
            'caption'          => $caption,
            'onscreen_text'    => $onscreen,
            'kit_generated_at' => now(),
        ]);

        return true;
    }

    protected function generateAudio(TikTokScript $script): ?string
    {
        $dir  = 'tiktok-kits/' . $script->id;
        $path = $dir . '/audio.mp3';

        // 1. Reusar audio del podcast si ya existe
        $episode = $script->news?->podcastEpisode;
        if ($episode?->isReady() && $episode->audio_path) {
            try {
                $audioData = Storage::disk('r2')->get($episode->audio_path);
                if ($audioData) {
                    Storage::disk('local')->makeDirectory($dir);
                    Storage::disk('local')->put($path, $audioData);
                    Log::info('TikTokKit: reutilizando audio del podcast', ['script_id' => $script->id]);
                    return $path;
                }
            } catch (\Exception $e) {
                Log::warning('TikTokKit: no se pudo descargar audio del podcast', ['error' => $e->getMessage()]);
            }
        }

        // 2. Generar con Google TTS (gratis)
        if (empty($this->googleTtsKey)) {
            Log::warning('TikTokKitGenerator: GOOGLE_TTS_KEY no configurada.');
            return null;
        }

        $clean = strip_tags($script->script_content);
        $clean = preg_replace('/[\*#]+/', '', $clean);
        $clean = trim(preg_replace('/\s+/', ' ', $clean));
        $clean = mb_strcut($clean, 0, 4800);

        try {
            $response = Http::timeout(60)
                ->post("https://texttospeech.googleapis.com/v1/text:synthesize?key={$this->googleTtsKey}", [
                    'input' => ['text' => $clean],
                    'voice' => [
                        'languageCode' => 'es-US',
                        'name'         => 'es-US-Neural2-A',
                        'ssmlGender'   => 'FEMALE',
                    ],
                    'audioConfig' => ['audioEncoding' => 'MP3'],
                ]);

            if ($response->failed()) {
                Log::error('TikTokKit Google TTS error: ' . $response->body());
                return null;
            }

            $audioData = base64_decode($response->json('audioContent'));
            if (!$audioData) return null;

            Storage::disk('local')->makeDirectory($dir);
            Storage::disk('local')->put($path, $audioData);

            return $path;
        } catch (\Exception $e) {
            Log::error('TikTokKit TTS exception: ' . $e->getMessage());
            return null;
        }
    }

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
        $guard  = app(GeminiQuotaGuard::class);
        $openai = app(OpenAIService::class);

        try {
            if ($openai->isAvailable()) {
                $data = $openai->generateJson($prompt, 600, 0.7);
                if (!empty($data)) return $data;
            }
        } catch (\Exception) {}

        try {
            if (!empty($this->geminiKey) && $guard->canCall('low')) {
                $r = Http::timeout(30)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent?key={$this->geminiKey}",
                    [
                        'contents'          => [['parts' => [['text' => $prompt]]]],
                        'generationConfig'  => [
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

        $audioAbsolute = storage_path('app/' . $script->audio_path);
        if (file_exists($audioAbsolute)) {
            $zip->addFile($audioAbsolute, 'audio.mp3');
        }

        // Imagen branded si existe
        $imageAbsolute = storage_path('app/tiktok-kits/' . $script->id . '/imagen-tiktok.jpg');
        if (!file_exists($imageAbsolute)) {
            $imageAbsolute = $this->generateBrandedImage($script);
        }
        if ($imageAbsolute && file_exists($imageAbsolute)) {
            $zip->addFile($imageAbsolute, 'imagen-tiktok.jpg');
        }

        if ($script->caption) {
            $zip->addFromString('caption.txt', $script->caption);
        }

        if ($script->onscreen_text) {
            $header  = "FRASES PARA MOSTRAR EN PANTALLA (texto overlay)\n";
            $header .= "Usá cada línea en un momento diferente del video.\n";
            $header .= str_repeat('-', 40) . "\n\n";
            $zip->addFromString('onscreen-text.txt', $header . $script->onscreen_text);
        }

        $scriptHeader  = "GUIÓN COMPLETO — ConocIA TikTok\n";
        $scriptHeader .= "Noticia: " . ($script->news?->title ?? '') . "\n";
        $scriptHeader .= "Generado: " . $script->kit_generated_at?->format('d/m/Y H:i') . "\n";
        $scriptHeader .= str_repeat('-', 40) . "\n\n";
        $zip->addFromString('guion.txt', $scriptHeader . strip_tags($script->script_content));

        $zip->close();

        return $zipPath;
    }

    protected function generateBrandedImage(TikTokScript $script): ?string
    {
        if (!function_exists('imagecreatetruecolor')) return null;

        $news = $script->news;
        $dir  = storage_path('app/tiktok-kits/' . $script->id);
        $path = $dir . '/imagen-tiktok.jpg';

        $w = 1080;
        $h = 1920;

        $img = imagecreatetruecolor($w, $h);

        // Fondo degradado oscuro (estilo ConocIA)
        for ($y = 0; $y < $h; $y++) {
            $t = $y / $h;
            $color = imagecolorallocate($img, (int)(10 + $t * 15), (int)(16 + $t * 30), (int)(45 + $t * 30));
            imageline($img, 0, $y, $w, $y, $color);
        }

        // Línea decorativa amarilla
        $yellow = imagecolorallocate($img, 245, 158, 11);
        imagefilledrectangle($img, 80, 260, 200, 270, $yellow);

        // Textos con fuente TTF si está disponible
        $font = $this->findFont();
        $white = imagecolorallocate($img, 255, 255, 255);
        $gray  = imagecolorallocate($img, 180, 180, 200);

        if ($font) {
            imagettftext($img, 72, 0, 80, 220, $yellow, $font, 'Conocía');

            $title = $news?->title ?? 'Inteligencia Artificial';
            $lines = $this->wrapText($font, 56, $title, $w - 160);
            $y = 320;
            foreach ($lines as $line) {
                imagettftext($img, 56, 0, 80, $y, $white, $font, $line);
                $y += 75;
            }

            imagettftext($img, 38, 0, 80, $h - 160, $gray, $font, 'conocia.cl');
            imagettftext($img, 32, 0, 80, $h - 110, $yellow, $font, '#IA #InteligenciaArtificial');
        } else {
            imagestring($img, 5, 80, 200, 'ConocIA', $yellow);
            imagestring($img, 4, 80, 240, $news?->title ?? '', $white);
        }

        imagejpeg($img, $path, 88);
        imagedestroy($img);

        return $path;
    }

    private function findFont(): ?string
    {
        $candidates = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            '/usr/share/fonts/truetype/freefont/FreeSansBold.ttf',
            '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
        ];
        foreach ($candidates as $f) {
            if (file_exists($f)) return $f;
        }
        return null;
    }

    private function wrapText(string $font, int $size, string $text, int $maxWidth): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $line  = '';

        foreach ($words as $word) {
            $test = $line ? "$line $word" : $word;
            $box  = imagettfbbox($size, 0, $font, $test);
            $tw   = abs($box[4] - $box[0]);
            if ($tw > $maxWidth && $line !== '') {
                $lines[] = $line;
                $line    = $word;
            } else {
                $line = $test;
            }
        }
        if ($line) $lines[] = $line;

        return $lines;
    }

    protected function buildFallbackCaption(string $title, ?string $existingHashtags): string
    {
        $base = \Illuminate\Support\Str::limit(strip_tags($title), 100);
        $tags = $existingHashtags ?: '#ConocIA #InteligenciaArtificial #IA #Tech #AINews';
        return $base . "\n\n" . $tags;
    }

    protected function buildFallbackOnscreen(string $script): string
    {
        $sentences = preg_split('/(?<=[.!?])\s+/', strip_tags($script));
        $short = array_filter($sentences, fn($s) => str_word_count($s) <= 10 && strlen($s) > 10);
        return implode("\n", array_slice(array_values($short), 0, 6));
    }
}
