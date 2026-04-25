<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SearchConsoleService
{
    public function isConfigured(): bool
    {
        return filled(config('services.search_console.site_url'))
            && ($this->hasOAuthCredentials() || $this->hasServiceAccountCredentials());
    }

    public function defaultSiteUrl(): ?string
    {
        return config('services.search_console.site_url');
    }

    public function listSites(): array
    {
        return $this->request('get', 'sites')->json('siteEntry', []);
    }

    public function querySearchAnalytics(string $siteUrl, array $payload): array
    {
        $endpoint = 'sites/' . rawurlencode($siteUrl) . '/searchAnalytics/query';

        return $this->request('post', $endpoint, $payload)->json('rows', []);
    }

    private function request(string $method, string $endpoint, array $payload = [])
    {
        $token = $this->accessToken();

        $request = Http::baseUrl('https://www.googleapis.com/webmasters/v3/')
            ->timeout(30)
            ->withToken($token)
            ->acceptJson();

        $response = $method === 'post'
            ? $request->post($endpoint, $payload)
            : $request->get($endpoint);

        if ($response->failed()) {
            throw new RuntimeException(
                'Search Console API error: ' . $response->status() . ' ' . $response->body()
            );
        }

        return $response;
    }

    private function accessToken(): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Search Console no está configurado.');
        }

        $mode = $this->hasOAuthCredentials() ? 'oauth' : 'service-account';

        return Cache::remember('search_console_access_token_' . $mode, now()->addMinutes(55), function () use ($mode) {
            $response = Http::asForm()
                ->timeout(20)
                ->post(config('services.search_console.token_uri'), $this->tokenPayload($mode));

            $token = $response->json('access_token');

            if ($response->failed() || !$token) {
                throw new RuntimeException(
                    'No se pudo obtener token de Search Console vía ' . $mode . ': '
                    . $response->status() . ' ' . $response->body()
                );
            }

            return $token;
        });
    }

    private function tokenPayload(string $mode): array
    {
        if ($mode === 'oauth') {
            return [
                'grant_type' => 'refresh_token',
                'client_id' => config('services.search_console.oauth_client_id'),
                'client_secret' => config('services.search_console.oauth_client_secret'),
                'refresh_token' => config('services.search_console.oauth_refresh_token'),
            ];
        }

        return [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->jwtAssertion(),
        ];
    }

    private function hasOAuthCredentials(): bool
    {
        return filled(config('services.search_console.oauth_client_id'))
            && filled(config('services.search_console.oauth_client_secret'))
            && filled(config('services.search_console.oauth_refresh_token'));
    }

    private function hasServiceAccountCredentials(): bool
    {
        return filled(config('services.search_console.client_email'))
            && filled(config('services.search_console.private_key'));
    }

    private function jwtAssertion(): string
    {
        $now = time();
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $claims = [
            'iss' => config('services.search_console.client_email'),
            'scope' => config('services.search_console.scope'),
            'aud' => config('services.search_console.token_uri'),
            'exp' => $now + 3600,
            'iat' => $now,
        ];

        $segments = [
            $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES)),
            $this->base64UrlEncode(json_encode($claims, JSON_UNESCAPED_SLASHES)),
        ];

        $signingInput = implode('.', $segments);
        $privateKey = str_replace('\n', "\n", (string) config('services.search_console.private_key'));
        $signature = '';

        $result = openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        if (!$result) {
            throw new RuntimeException('No se pudo firmar el JWT para Search Console.');
        }

        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
