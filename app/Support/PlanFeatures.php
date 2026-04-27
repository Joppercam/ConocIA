<?php

namespace App\Support;

class PlanFeatures
{
    public static function plans(): array
    {
        return [
            'free' => [
                'label' => 'FREE',
                'features' => ['Noticias abiertas', 'Resumen IA limitado'],
            ],
            'pro' => [
                'label' => 'PRO',
                'features' => ['Resúmenes IA ilimitados', 'Insights premium', 'Contenido premium', 'Alertas básicas'],
            ],
            'business' => [
                'label' => 'BUSINESS',
                'features' => ['Insights estratégicos', 'Reportes descargables', 'Inteligencia de tendencias', 'Prioridad IA'],
            ],
        ];
    }

    public static function can(?string $plan, string $feature): bool
    {
        $plan = $plan ?: 'free';

        if ($plan === 'business') {
            return true;
        }

        return match ($feature) {
            'insights', 'premium-content', 'alerts' => $plan === 'pro',
            'business-insights', 'reports', 'trend-intelligence', 'priority-ai' => false,
            default => false,
        };
    }
}
