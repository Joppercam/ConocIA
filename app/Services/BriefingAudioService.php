<?php

namespace App\Services;

use App\Models\DailyBriefing;
use App\Services\TtsTextCleaner;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BriefingAudioService
{
    // Google TTS limita a 5000 bytes por request; usamos 4800 como margen seguro.
    private const CHUNK_BYTES = 4800;

    public function generate(DailyBriefing $briefing): true|string
    {
        $apiKey = config('services.google_tts.key');

        if (!$apiKey) {
            return 'GOOGLE_TTS_KEY no está configurada.';
        }

        try {
            $text   = $this->prepareText($briefing->script);
            $chunks = $this->splitIntoChunks($text);
            $audio  = '';

            foreach ($chunks as $chunk) {
                $part = $this->synthesize($chunk, $apiKey);
                if ($part === null) {
                    return 'Error al sintetizar un fragmento del briefing.';
                }
                $audio .= $part;
            }

            if (empty($audio)) {
                return 'Google TTS devolvió audio vacío.';
            }

            $path      = 'briefing-audio/' . $briefing->date->format('Y-m-d') . '.mp3';
            Storage::disk('r2')->put($path, $audio);
            $publicUrl = rtrim(config('filesystems.disks.r2.url'), '/') . '/' . $path;

            $briefing->update([
                'audio_url'          => $publicUrl,
                'audio_generated_at' => now(),
            ]);

            Log::info("BriefingAudioService: audio generado ({$briefing->date->format('Y-m-d')}) → {$publicUrl}");
            return true;

        } catch (\Throwable $e) {
            Log::error('BriefingAudioService exception', ['error' => $e->getMessage()]);
            return 'Error inesperado: ' . $e->getMessage();
        }
    }

    private function synthesize(string $text, string $apiKey): ?string
    {
        $response = Http::timeout(120)->post(
            "https://texttospeech.googleapis.com/v1/text:synthesize?key={$apiKey}",
            [
                'input'       => ['text' => $text],
                'voice'       => [
                    'languageCode' => 'es-US',
                    'name'         => 'es-US-Neural2-B', // Voz masculina para "Alex"
                    'ssmlGender'   => 'MALE',
                ],
                'audioConfig' => [
                    'audioEncoding'   => 'MP3',
                    'speakingRate'    => 1.0,
                    'pitch'           => 0.0,
                ],
            ]
        );

        if (!$response->successful()) {
            Log::error('BriefingAudioService Google TTS error: ' . $response->body());
            return null;
        }

        $decoded = base64_decode($response->json('audioContent') ?? '');
        return $decoded ?: null;
    }

    private function prepareText(string $script): string
    {
        return TtsTextCleaner::clean($script);
    }

    /**
     * Divide el texto en chunks de máx CHUNK_BYTES bytes,
     * cortando siempre en límites de oraciones para evitar cortes abruptos.
     */
    private function splitIntoChunks(string $text): array
    {
        if (strlen($text) <= self::CHUNK_BYTES) {
            return [$text];
        }

        // Dividir en oraciones
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $chunks    = [];
        $current   = '';

        foreach ($sentences as $sentence) {
            $candidate = $current === '' ? $sentence : $current . ' ' . $sentence;
            if (strlen($candidate) > self::CHUNK_BYTES) {
                if ($current !== '') {
                    $chunks[] = trim($current);
                }
                // Si una sola oración supera el límite, cortarla por bytes
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
