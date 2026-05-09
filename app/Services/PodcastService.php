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
            ['status' => 'pending', 'voice' => 'es-US-Neural2-A']
        );

        $episode->update(['status' => 'processing', 'error_message' => null]);

        try {
            $text      = $this->buildText($news);
            $token     = $this->getAccessToken();

            $response = Http::timeout(120)
                ->withToken($token)
                ->post('https://texttospeech.googleapis.com/v1/text:synthesize', [
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
                throw new \RuntimeException('Google TTS error: ' . $response->body());
            }

            $audioData = base64_decode($response->json('audioContent'));

            if (!$audioData) {
                throw new \RuntimeException('Google TTS: respuesta vacía o inválida.');
            }

            $path = 'podcasts/' . $news->slug . '.mp3';
            Storage::disk('r2')->put($path, $audioData);

            $publicUrl       = config('filesystems.disks.r2.url') . '/' . $path;
            $wordCount       = str_word_count(strip_tags($news->content ?? ''));
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

    private function getAccessToken(): string
    {
        $now = time();

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $claims = [
            'iss'   => config('services.search_console.client_email'),
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];

        $segments = [
            $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES)),
            $this->base64UrlEncode(json_encode($claims, JSON_UNESCAPED_SLASHES)),
        ];

        $signingInput = implode('.', $segments);
        $rawKey       = (string) config('services.search_console.private_key');
        $privateKey   = str_replace(['\r\n', '\n'], "\n", $rawKey);

        $keyResource = openssl_pkey_get_private($privateKey);

        if ($keyResource === false) {
            throw new \RuntimeException('Google TTS: clave privada inválida — ' . openssl_error_string());
        }

        openssl_sign($signingInput, $signature, $keyResource, OPENSSL_ALGO_SHA256);

        $segments[] = $this->base64UrlEncode($signature);
        $jwt        = implode('.', $segments);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Google OAuth error: ' . $response->body());
        }

        return $response->json('access_token');
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function buildText(News $news): string
    {
        $content = strip_tags($news->content ?? '');
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $content = preg_replace('/\s+/', ' ', $content);
        $content = mb_substr(trim($content), 0, 4500);

        return "ConocIA. {$news->title}. {$news->summary} {$content}. Para leer el artículo completo, visitá ConocIA punto cl.";
    }
}
