<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptimizePriorityNewsSeoCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_priority_news_seo_command_updates_targeted_articles(): void
    {
        $perplexity = News::factory()->create([
            'slug' => 'perplexity-ai-gratis-vs-version-pro-diferencias-entre-sus-planes',
            'title' => 'Perplexity AI gratis vs version pro',
            'summary' => 'Resumen viejo.',
        ]);

        $apple = News::factory()->create([
            'slug' => 'guia-completa-para-integrar-chatgpt-con-apple-in',
            'title' => 'Guia completa para integrar chatgpt con apple',
            'summary' => 'Resumen viejo.',
        ]);

        $ipad = News::factory()->create([
            'slug' => 'guia-completa-para-elegir-el-ipad-2025-ideal-nor',
            'title' => 'Guia completa para elegir el ipad 2025 ideal',
            'summary' => 'Resumen viejo.',
        ]);

        $gemini = News::factory()->create([
            'slug' => 'comparativa-entre-chatgpt-y-gemini-quien-lidera-ahora',
            'title' => 'Comparativa entre chatgpt y gemini',
            'summary' => 'Resumen viejo.',
        ]);

        $aiStudio = News::factory()->create([
            'slug' => 'que-es-google-ai-studio-y-para-que-sirve',
            'title' => 'Que es google ai studio',
            'summary' => 'Resumen viejo.',
        ]);

        $untouched = News::factory()->create([
            'slug' => 'noticia-sin-relacion',
            'title' => 'Otra noticia',
            'summary' => 'No debería cambiar.',
        ]);

        $this->artisan('content:optimize-priority-news-seo')
            ->expectsOutputToContain('Optimización completa.')
            ->assertSuccessful();

        $this->assertSame(
            'Perplexity gratis vs Pro: diferencias, precio y qué plan conviene en 2026',
            $perplexity->fresh()->title
        );

        $this->assertSame(
            'Cómo integrar ChatGPT con Apple: guía completa para iPhone, iPad y Mac',
            $apple->fresh()->title
        );

        $this->assertSame(
            'Qué iPad comprar en 2025: guía completa para elegir el modelo ideal',
            $ipad->fresh()->title
        );

        $this->assertSame(
            'ChatGPT vs Gemini: comparativa 2026, diferencias clave y cuál conviene más',
            $gemini->fresh()->title
        );

        $this->assertSame(
            'Qué es Google AI Studio y para qué sirve: guía clara para empezar',
            $aiStudio->fresh()->title
        );

        $this->assertSame('Otra noticia', $untouched->fresh()->title);
        $this->assertSame('No debería cambiar.', $untouched->fresh()->summary);
    }

    public function test_priority_news_seo_command_supports_dry_run(): void
    {
        $news = News::factory()->create([
            'slug' => 'que-es-google-ai-studio-y-para-que-sirve',
            'title' => 'Que es google ai studio',
            'summary' => 'Resumen viejo.',
        ]);

        $this->artisan('content:optimize-priority-news-seo --dry-run')
            ->expectsOutputToContain('Dry run completo.')
            ->assertSuccessful();

        $this->assertSame('Que es google ai studio', $news->fresh()->title);
        $this->assertSame('Resumen viejo.', $news->fresh()->summary);
    }
}
