<?php

namespace App\Services\Video;

use App\Models\VideoPlatform;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

abstract class AbstractVideoService implements VideoServiceInterface
{
    protected ?VideoPlatform $platform = null;
    protected string $apiKey = '';

    /**
     * Resuelve la plataforma desde la BD con fallback a config.
     */
    protected function resolvePlatform(string $code, string $configKey): void
    {
        try {
            if (Schema::hasTable('video_platforms')) {
                $this->platform = VideoPlatform::where('code', $code)->first();
                $this->apiKey   = $this->platform?->api_key ?? config($configKey) ?? '';
                return;
            }
        } catch (Throwable $e) {
            // Durante bootstrap, tests o entornos sin BD disponible,
            // degradamos a config para no bloquear toda la aplicación.
            Log::warning(static::class . ' - platform resolution fallback: ' . $e->getMessage());
        }

        $this->apiKey = config($configKey) ?? '';
    }

    /**
     * Ejecuta una llamada a API con caché y manejo de errores centralizado.
     * Elimina el try/catch repetido en cada servicio.
     *
     * @param string   $cacheKey
     * @param mixed    $ttl          Carbon instance o segundos
     * @param callable $apiCall      Debe retornar el resultado o [] en caso de respuesta vacía
     * @param mixed    $default      Valor por defecto si falla
     */
    protected function cachedApiCall(string $cacheKey, mixed $ttl, callable $apiCall, mixed $default = []): mixed
    {
        return Cache::remember($cacheKey, $ttl, function () use ($apiCall, $default) {
            try {
                return $apiCall();
            } catch (\Exception $e) {
                Log::error(static::class . ' - API error: ' . $e->getMessage());
                return $default;
            }
        });
    }
}
