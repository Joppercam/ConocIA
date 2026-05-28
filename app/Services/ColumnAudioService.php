<?php

namespace App\Services;

use App\Models\Column;
use App\Services\TtsTextCleaner;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ColumnAudioService
{
    /**
     * Genera el audio MP3 para una columna usando Google TTS y lo sube a R2.
     * Devuelve true si fue exitoso, string con error si falló.
     */
    public function generate(Column $column): true|string
    {
        $apiKey = config('services.google_tts.key');

        if (!$apiKey) {
            return 'GOOGLE_TTS_KEY no está configurada.';
        }

        try {
            $text = $this->buildText($column);

            $response = Http::timeout(120)
                ->post("https://texttospeech.googleapis.com/v1/text:synthesize?key={$apiKey}", [
                    'input' => ['text' => $text],
                    'voice' => [
                        'languageCode' => 'es-US',
                        'name'         => 'es-US-Neural2-A',
                        'ssmlGender'   => 'FEMALE',
                    ],
                    'audioConfig' => [
                        'audioEncoding' => 'MP3',
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('ColumnAudioService Google TTS error: ' . $response->body());
                return 'Error al generar el audio: ' . $response->json('error.message', $response->status());
            }

            $audioData = base64_decode($response->json('audioContent'));

            if (!$audioData) {
                return 'Google TTS devolvió una respuesta vacía.';
            }

            $path      = 'column-audio/' . $column->slug . '.mp3';
            Storage::disk('r2')->put($path, $audioData);
            $publicUrl = config('filesystems.disks.r2.url') . '/' . $path;

            $column->update([
                'audio_path'         => $publicUrl,
                'audio_generated_at' => now(),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('ColumnAudioService exception', ['column_id' => $column->id, 'error' => $e->getMessage()]);
            return 'Error inesperado: ' . $e->getMessage();
        }
    }

    /**
     * Elimina el audio de R2 y limpia los campos del modelo.
     */
    public function delete(Column $column): void
    {
        if ($column->audio_path) {
            $path = 'column-audio/' . $column->slug . '.mp3';
            Storage::disk('r2')->delete($path);
        }

        $column->update([
            'audio_path'         => null,
            'audio_generated_at' => null,
        ]);
    }

    private function buildText(Column $column): string
    {
        // Quitar la sección de fuentes/footnotes (tras <hr>) antes de limpiar
        $raw = preg_replace('/<hr\s*\/?>.*/si', '', $column->content ?? '');

        $content = TtsTextCleaner::clean($raw);

        $prefix   = "Conocia. {$column->title}. ";
        $suffix   = '. Para leer la columna completa, visitá Conocia punto cl.';
        $maxBytes = 4800;
        $available = $maxBytes - strlen($prefix) - strlen($suffix);

        if ($available > 0) {
            $content = mb_strcut($content, 0, max(0, $available));
        } else {
            $content = '';
        }

        return $prefix . $content . $suffix;
    }
}
