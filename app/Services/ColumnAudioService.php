<?php

namespace App\Services;

use App\Models\Column;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ColumnAudioService
{
    protected string $openaiKey;

    public function __construct()
    {
        $this->openaiKey = env('OPENAI_API_KEY', '');
    }

    /**
     * Genera el audio MP3 para una columna y guarda la ruta en el modelo.
     * Devuelve true si fue exitoso, string con error si falló.
     */
    public function generate(Column $column): true|string
    {
        if (empty($this->openaiKey)) {
            return 'OPENAI_API_KEY no configurada o sin créditos.';
        }

        $audioPath = $this->generateAudio($column->content, $column->id);

        if (!$audioPath) {
            return 'No se pudo generar el audio. Verificá que OPENAI_API_KEY tenga créditos.';
        }

        $column->update([
            'audio_path'         => $audioPath,
            'audio_generated_at' => now(),
        ]);

        return true;
    }

    /**
     * Llama a OpenAI TTS y guarda el MP3 en storage.
     * Devuelve la ruta relativa o null si falla.
     */
    protected function generateAudio(string $content, int $columnId): ?string
    {
        $clean = strip_tags($content);
        $clean = preg_replace('/\*+/', '', $clean);
        $clean = preg_replace('/#+\s/', '', $clean);
        $clean = trim($clean);

        // OpenAI TTS tiene límite de 4096 caracteres por request
        if (strlen($clean) > 4096) {
            $clean = substr($clean, 0, 4096);
        }

        try {
            $response = Http::timeout(120)
                ->withToken($this->openaiKey)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post('https://api.openai.com/v1/audio/speech', [
                    'model'           => 'tts-1',
                    'input'           => $clean,
                    'voice'           => 'nova',
                    'speed'           => 0.95,
                    'response_format' => 'mp3',
                ]);

            if ($response->failed()) {
                Log::error('ColumnAudioService TTS error: ' . $response->body());
                return null;
            }

            $dir  = 'column-audio/' . $columnId;
            $path = $dir . '/audio.mp3';

            Storage::disk('local')->makeDirectory($dir);
            Storage::disk('local')->put($path, $response->body());

            return $path;
        } catch (\Exception $e) {
            Log::error('ColumnAudioService TTS exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Elimina el audio de storage y limpia los campos del modelo.
     */
    public function delete(Column $column): void
    {
        if ($column->audio_path && Storage::disk('local')->exists($column->audio_path)) {
            Storage::disk('local')->delete($column->audio_path);
        }

        $column->update([
            'audio_path'         => null,
            'audio_generated_at' => null,
        ]);
    }
}
