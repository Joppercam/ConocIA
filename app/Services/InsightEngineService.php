<?php

namespace App\Services;

use App\Models\Insight;
use App\Models\News;
use Illuminate\Support\Str;

class InsightEngineService
{
    public function generarInsight(News $noticia): Insight
    {
        $text = trim(strip_tags($noticia->summary ?: $noticia->excerpt ?: $noticia->content));
        $summary = Str::limit($text, 280);
        $tipo = $this->detectarOportunidad($noticia);
        $score = $this->clasificarImpacto($noticia);

        return Insight::firstOrCreate(
            ['noticia_id' => $noticia->id, 'tipo' => $tipo],
            [
                'resumen' => $summary ?: 'Esta noticia resume una señal relevante para el ecosistema de inteligencia artificial.',
                'impacto' => $this->impactText($tipo, $score),
                'relevancia' => $score,
                'insight_accionable' => $this->actionText($tipo, $noticia),
                'is_premium' => true,
            ]
        );
    }

    public function clasificarImpacto(News $noticia): int
    {
        $haystack = Str::lower($noticia->title . ' ' . $noticia->summary . ' ' . $noticia->excerpt . ' ' . $noticia->keywords);
        $score = 45;

        foreach (['empresa', 'business', 'cloud', 'modelo', 'agente', 'regulación', 'salud', 'financiamiento', 'openai', 'google', 'anthropic'] as $term) {
            if (Str::contains($haystack, $term)) {
                $score += 7;
            }
        }

        return min(95, max(30, $score + min(15, (int) floor(($noticia->views ?? 0) / 50))));
    }

    public function detectarOportunidad(News $noticia): string
    {
        $haystack = Str::lower($noticia->title . ' ' . $noticia->summary . ' ' . $noticia->excerpt);

        if (Str::contains($haystack, ['riesgo', 'regulación', 'privacidad', 'seguridad', 'demanda', 'sesgo'])) {
            return 'riesgo';
        }

        if (Str::contains($haystack, ['startup', 'inversión', 'financiamiento', 'mercado', 'empresa', 'cloud'])) {
            return 'oportunidad';
        }

        return 'tendencia';
    }

    private function impactText(string $tipo, int $score): string
    {
        return match ($tipo) {
            'riesgo' => "Señal de riesgo o cambio regulatorio con relevancia {$score}/100 para decisiones estratégicas.",
            'oportunidad' => "Señal de oportunidad comercial o tecnológica con relevancia {$score}/100.",
            default => "Señal de tendencia emergente con relevancia {$score}/100 para seguimiento ejecutivo.",
        };
    }

    private function actionText(string $tipo, News $noticia): string
    {
        return match ($tipo) {
            'riesgo' => 'Revisar exposición legal, reputacional o de seguridad asociada a esta tendencia antes de adoptar soluciones similares.',
            'oportunidad' => 'Evaluar si esta señal abre una oportunidad de producto, alianza, automatización o posicionamiento comercial.',
            default => 'Monitorear evolución durante la semana y comparar con señales similares en el sector.',
        };
    }
}
