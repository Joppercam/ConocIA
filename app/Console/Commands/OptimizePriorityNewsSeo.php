<?php

namespace App\Console\Commands;

use App\Models\News;
use Illuminate\Console\Command;

class OptimizePriorityNewsSeo extends Command
{
    protected $signature = 'content:optimize-priority-news-seo {--dry-run : Muestra cambios sin escribir en base de datos}';

    protected $description = 'Optimiza títulos y resúmenes de noticias prioritarias detectadas desde Search Console.';

    public function handle(): int
    {
        $updates = $this->priorityUpdates();
        $applied = 0;

        foreach ($updates as $update) {
            $news = News::query()
                ->where('slug', 'like', $update['slug_prefix'] . '%')
                ->first();

            if (!$news) {
                $this->warn("No encontré una noticia para el patrón {$update['slug_prefix']}");
                continue;
            }

            $changes = [];

            if ($news->title !== $update['title']) {
                $changes['title'] = [$news->title, $update['title']];
            }

            if (($news->summary ?? '') !== $update['summary']) {
                $changes['summary'] = [$news->summary, $update['summary']];
            }

            if ($changes === []) {
                $this->line("Sin cambios: {$news->slug}");
                continue;
            }

            $this->info("Optimización preparada para {$news->slug}");

            foreach ($changes as $field => [$from, $to]) {
                $this->line(" - {$field}:");
                $this->line("   antes: " . ($from ?: '[vacío]'));
                $this->line("   ahora: {$to}");
            }

            if (!$this->option('dry-run')) {
                $news->forceFill([
                    'title' => $update['title'],
                    'summary' => $update['summary'],
                ])->save();
            }

            $applied++;
        }

        $this->newLine();
        $this->info($this->option('dry-run')
            ? "Dry run completo. {$applied} noticias con cambios sugeridos."
            : "Optimización completa. {$applied} noticias actualizadas.");

        return self::SUCCESS;
    }

    private function priorityUpdates(): array
    {
        return [
            [
                'slug_prefix' => 'perplexity-ai-gratis-vs-version-pro-diferencias',
                'title' => 'Perplexity gratis vs Pro: diferencias, precio y qué plan conviene en 2026',
                'summary' => 'Comparamos Perplexity gratis y Pro en funciones, límites, precio y casos de uso para elegir qué versión conviene realmente en 2026.',
            ],
            [
                'slug_prefix' => 'guia-completa-para-integrar-chatgpt-con-apple',
                'title' => 'Cómo integrar ChatGPT con Apple: guía completa para iPhone, iPad y Mac',
                'summary' => 'Guía práctica para integrar ChatGPT con Apple paso a paso en iPhone, iPad y Mac, con requisitos, opciones disponibles y límites actuales.',
            ],
            [
                'slug_prefix' => 'guia-completa-para-elegir-el-ipad-2025-ideal',
                'title' => 'Qué iPad comprar en 2025: guía completa para elegir el modelo ideal',
                'summary' => 'Explicamos qué iPad comprar en 2025 según uso, precio, tamaño y rendimiento para elegir el modelo ideal sin gastar de más.',
            ],
            [
                'slug_prefix' => 'comparativa-entre-chatgpt-y-gemini-quien-lidera',
                'title' => 'ChatGPT vs Gemini: comparativa 2026, diferencias clave y cuál conviene más',
                'summary' => 'Analizamos ChatGPT vs Gemini en calidad, velocidad, funciones y uso real para entender cuál conviene más en 2026.',
            ],
            [
                'slug_prefix' => 'que-es-google-ai-studio-y-para-que-sirve',
                'title' => 'Qué es Google AI Studio y para qué sirve: guía clara para empezar',
                'summary' => 'Te explicamos qué es Google AI Studio, para qué sirve y cómo empezar a usarlo para prototipos, prompts y apps con modelos de Google.',
            ],
        ];
    }
}
