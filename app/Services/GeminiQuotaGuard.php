<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Protege la cuota diaria de Gemini API (free tier: ~50 req/día).
 *
 * Prioridades y sus umbrales de corte (porcentaje del límite diario):
 *  critical  → siempre permitido (hasta el 100% del límite)   ← briefing
 *  high      → permitido hasta el 85% del límite              ← RSS / Guardian
 *  medium    → permitido hasta el 65% del límite              ← news:fetch / videos
 *  low       → permitido hasta el 40% del límite              ← TikTok scripts/kit
 *  off       → nunca llamar a Gemini (keywords / comentarios)
 *
 * Configuración:
 *  GEMINI_DAILY_LIMIT=45  (por defecto, deja 5 llamadas de margen)
 */
class GeminiQuotaGuard
{
    protected int $dailyLimit;

    protected array $thresholds = [
        'critical' => 1.00,
        'high'     => 0.85,
        'medium'   => 0.65,
        'low'      => 0.40,
    ];

    public function __construct()
    {
        $this->dailyLimit = (int) env('GEMINI_DAILY_LIMIT', 45);
    }

    /**
     * Devuelve la clave de cache para el contador del día actual.
     */
    protected function cacheKey(): string
    {
        return 'gemini_calls_' . now()->toDateString();
    }

    /**
     * Número de llamadas registradas hoy.
     */
    public function usedToday(): int
    {
        return (int) Cache::get($this->cacheKey(), 0);
    }

    /**
     * Límite configurado.
     */
    public function limit(): int
    {
        return $this->dailyLimit;
    }

    /**
     * Porcentaje de cuota consumido hoy (0.0 – 1.0+).
     */
    public function usageRatio(): float
    {
        return $this->dailyLimit > 0
            ? $this->usedToday() / $this->dailyLimit
            : 1.0;
    }

    /**
     * ¿Se puede hacer una llamada con esta prioridad?
     *
     * @param string $priority  critical | high | medium | low
     */
    public function canCall(string $priority = 'medium'): bool
    {
        if (!isset($this->thresholds[$priority])) {
            Log::warning("GeminiQuotaGuard: prioridad desconocida '{$priority}', usando 'medium'.");
            $priority = 'medium';
        }

        $threshold = $this->thresholds[$priority];
        $ratio     = $this->usageRatio();

        if ($ratio >= $threshold) {
            Log::info("GeminiQuotaGuard: cuota agotada para prioridad '{$priority}'. " .
                "Usado: {$this->usedToday()}/{$this->dailyLimit} ({$ratio}).");
            return false;
        }

        return true;
    }

    /**
     * Registra una llamada exitosa a Gemini.
     * Debe llamarse DESPUÉS de que la petición HTTP fue exitosa.
     */
    public function record(): void
    {
        $key = $this->cacheKey();
        // Incrementar; si no existe aún, inicializar en 1 con TTL de 26 horas
        $current = Cache::get($key, 0);
        Cache::put($key, $current + 1, now()->addHours(26));

        if ($current + 1 >= $this->dailyLimit) {
            Log::warning("GeminiQuotaGuard: límite diario alcanzado ({$this->dailyLimit} llamadas).");
        }
    }

    /**
     * Resumen legible del estado actual (útil para logs/diagnóstico).
     */
    public function summary(): string
    {
        $used  = $this->usedToday();
        $limit = $this->dailyLimit;
        $pct   = $limit > 0 ? round(($used / $limit) * 100) : 100;
        return "Gemini quota: {$used}/{$limit} ({$pct}%) — " . now()->toDateString();
    }
}
