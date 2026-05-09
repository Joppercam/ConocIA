<?php

namespace App\Services;

use App\Models\News;
use App\Models\PodcastEpisode;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PodcastService
{
    private string $apiKey;
    private string $voice;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->voice  = config('services.podcast.voice', 'nova');
        $this->model  = 'tts-1';
    }

    public function generate(PodcastEpisode $episode): void
    {
        $news = $episode->news;

        $episode->update(['status' => 'processing']);

        try {
            $text      = $this->prepareText($news);
            $audioData = $this->callTtsApi($text);
            $path      = $this->storeAudio($news->slug, $audioData);

            $episode->update([
                'status'           => 'ready',
                'audio_path'       => $path,
                'audio_url'        => Storage::disk('r2')->url($path),
                'file_size'        => strlen($audioData),
                'duration_seconds' => $this->estimateDuration($text),
                'generated_at'     => now(),
                'error_message'    => null,
            ]);
        } catch (Exception $e) {
            Log::error('PodcastService: error generating episode', [
                'episode_id' => $episode->id,
                'news_id'    => $news->id,
                'error'      => $e->getMessage(),
            ]);

            $episode->update([
                'status'        => 'error',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function prepareText(News $news): string
    {
        $intro   = 'ConocIA. ' . $news->title . '. ';
        $summary = $news->summary ? $news->summary . ' ' : '';
        $outro   = ' Para leer el artículo completo, visitá ConocIA punto com.';

        $bodyHtml   = $news->content ?? '';
        $bodyClean  = trim(strip_tags($bodyHtml));
        $bodyClean  = preg_replace('/\s+/', ' ', $bodyClean);

        $available = 4000 - strlen($intro) - strlen($summary) - strlen($outro);
        $body      = strlen($bodyClean) > $available
            ? substr($bodyClean, 0, $available) . '...'
            : $bodyClean;

        return $intro . $summary . $body . $outro;
    }

    private function callTtsApi(string $text): string
    {
        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->timeout(120)
            ->post('https://api.openai.com/v1/audio/speech', [
                'model' => $this->model,
                'input' => $text,
                'voice' => $this->voice,
            ]);

        if ($response->failed()) {
            throw new Exception('OpenAI TTS error: ' . $response->body());
        }

        return $response->body();
    }

    private function storeAudio(string $slug, string $audioData): string
    {
        $path = 'podcasts/' . $slug . '.mp3';

        Storage::disk('r2')->put($path, $audioData, [
            'ContentType'        => 'audio/mpeg',
            'CacheControl'       => 'public, max-age=31536000',
        ]);

        return $path;
    }

    private function estimateDuration(string $text): int
    {
        // Spanish TTS: ~150 words per minute average
        $wordCount = str_word_count($text);
        return (int) ceil(($wordCount / 150) * 60);
    }
}
