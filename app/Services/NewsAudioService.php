<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NewsAudioService
{
    private const CHUNK_BYTES = 4800;

    public function generate(News $news): true|string
    {
        $apiKey = config('services.google_tts.key');

        if (!$apiKey) {
            return 'GOOGLE_TTS_KEY no está configurada.';
        }

        try {
            $text   = $this->buildText($news);
            $chunks = $this->splitIntoChunks($text);
            $audio  = '';

            foreach ($chunks as $chunk) {
                $part = $this->synthesize($chunk, $apiKey);
                if ($part === null) {
                    return 'Error al sintetizar un fragmento de la noticia.';
                }
                $audio .= $part;
            }

            if (empty($audio)) {
                return 'Google TTS devolvió audio vacío.';
            }

            $path      = 'news-audio/' . $news->slug . '.mp3';
            Storage::disk('r2')->put($path, $audio);
            $publicUrl = rtrim(config('filesystems.disks.r2.url'), '/') . '/' . $path;

            $news->update([
                'audio_path'         => $publicUrl,
                'audio_generated_at' => now(),
            ]);

            Log::info("NewsAudioService: audio generado ({$news->slug}) → {$publicUrl}");
            return true;

        } catch (\Throwable $e) {
            Log::error('NewsAudioService exception', ['news_id' => $news->id, 'error' => $e->getMessage()]);
            return 'Error inesperado: ' . $e->getMessage();
        }
    }

    public function delete(News $news): void
    {
        if ($news->audio_path) {
            Storage::disk('r2')->delete('news-audio/' . $news->slug . '.mp3');
        }

        $news->update([
            'audio_path'         => null,
            'audio_generated_at' => null,
        ]);
    }

    private function synthesize(string $text, string $apiKey): ?string
    {
        $response = Http::timeout(120)->post(
            "https://texttospeech.googleapis.com/v1/text:synthesize?key={$apiKey}",
            [
                'input'       => ['text' => $text],
                'voice'       => [
                    'languageCode' => 'es-US',
                    'name'         => 'es-US-Neural2-B',
                    'ssmlGender'   => 'MALE',
                ],
                'audioConfig' => [
                    'audioEncoding' => 'MP3',
                    'speakingRate'  => 1.0,
                    'pitch'         => 0.0,
                ],
            ]
        );

        if (!$response->successful()) {
            Log::error('NewsAudioService Google TTS error: ' . $response->body());
            return null;
        }

        $decoded = base64_decode($response->json('audioContent') ?? '');
        return $decoded ?: null;
    }

    private function buildText(News $news): string
    {
        $content = TtsTextCleaner::clean($news->content ?? $news->excerpt ?? '');

        $prefix   = "Conocia. {$news->title}. ";
        $suffix   = '. Para leer la noticia completa, visitá Conocia punto cl.';
        $available = self::CHUNK_BYTES - strlen($prefix) - strlen($suffix);

        if ($available > 0) {
            $content = mb_strcut($content, 0, max(0, $available));
        } else {
            $content = '';
        }

        return $prefix . $content . $suffix;
    }

    private function splitIntoChunks(string $text): array
    {
        if (strlen($text) <= self::CHUNK_BYTES) {
            return [$text];
        }

        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $chunks    = [];
        $current   = '';

        foreach ($sentences as $sentence) {
            $candidate = $current === '' ? $sentence : $current . ' ' . $sentence;
            if (strlen($candidate) > self::CHUNK_BYTES) {
                if ($current !== '') {
                    $chunks[] = trim($current);
                }
                if (strlen($sentence) > self::CHUNK_BYTES) {
                    $chunks[] = mb_strcut($sentence, 0, self::CHUNK_BYTES);
                } else {
                    $current = $sentence;
                }
            } else {
                $current = $candidate;
            }
        }

        if ($current !== '') {
            $chunks[] = trim($current);
        }

        return array_filter($chunks);
    }
}
