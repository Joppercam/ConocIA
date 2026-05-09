<?php

namespace App\Services;

use App\Models\News;
use App\Models\PodcastEpisode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PodcastService
{
    public function generate(News $news): PodcastEpisode
    {
        $episode = PodcastEpisode::firstOrCreate(
            ['news_id' => $news->id],
            ['status' => 'pending', 'voice' => config('services.podcast.voice', 'nova')]
        );

        $episode->update(['status' => 'processing', 'error_message' => null]);

        try {
            $text = $this->buildText($news);

            $response = Http::withToken(config('openai.api_key'))
                ->timeout(120)
                ->post('https://api.openai.com/v1/audio/speech', [
                    'model' => 'tts-1',
                    'input' => $text,
                    'voice' => $episode->voice,
                    'response_format' => 'mp3',
                ]);

            if (!$response->successful()) {
                throw new \RuntimeException('OpenAI TTS error: ' . $response->body());
            }

            $audioData = $response->body();
            $path = 'podcasts/' . $news->slug . '.mp3';

            Storage::disk('r2')->put($path, $audioData, 'public');

            $publicUrl = config('filesystems.disks.r2.url') . '/' . $path;
            $wordCount = str_word_count(strip_tags($news->content ?? ''));
            $durationSeconds = (int) max(10, round($wordCount / 150 * 60));

            $episode->update([
                'audio_path'       => $path,
                'audio_url'        => $publicUrl,
                'duration_seconds' => $durationSeconds,
                'file_size'        => strlen($audioData),
                'status'           => 'ready',
                'generated_at'     => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('PodcastService error', ['news_id' => $news->id, 'error' => $e->getMessage()]);
            $episode->update(['status' => 'error', 'error_message' => $e->getMessage()]);
            throw $e;
        }

        return $episode->fresh();
    }

    private function buildText(News $news): string
    {
        $content = strip_tags($news->content ?? '');
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $content = preg_replace('/\s+/', ' ', $content);
        $content = mb_substr(trim($content), 0, 4000);

        return "ConocIA. {$news->title}. {$news->summary} {$content}... Para leer el artículo completo, visitá ConocIA punto cl.";
    }
}
