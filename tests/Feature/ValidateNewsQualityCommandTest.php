<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ValidateNewsQualityCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_quality_command_deactivates_incomplete_news_and_clears_broken_images(): void
    {
        Cache::flush();

        $brokenImageUrl = 'https://cdn.example.com/broken-news.jpg';

        Http::fake([
            $brokenImageUrl => Http::response('', 404),
        ]);

        $news = News::factory()->create([
            'content' => '<p>Cada vez más empresas usan IA.</p><p>Así lo …</p>',
            'image' => $brokenImageUrl,
            'status' => 'published',
            'featured' => true,
        ]);

        $news->forceFill(['is_published' => true])->save();

        $this->artisan('news:validate-quality --published --apply')
            ->assertExitCode(0);

        $news->refresh();

        $this->assertSame('draft', $news->status);
        $this->assertFalse((bool) $news->is_published);
        $this->assertFalse((bool) $news->featured);
        $this->assertNull($news->image);
    }

    public function test_incomplete_published_news_is_not_publicly_accessible(): void
    {
        $news = News::factory()->create([
            'slug' => 'nota-incompleta',
            'content' => '<p>Cada vez más empresas usan IA.</p><p>Así lo …</p>',
            'status' => 'published',
        ]);

        $news->forceFill(['is_published' => true])->save();

        $this->get('/news/nota-incompleta')->assertNotFound();
    }

    public function test_quality_command_repairs_truncated_summary_from_complete_content(): void
    {
        $news = News::factory()->create([
            'summary' => 'La nueva inversión en inteligencia artificial dejó al mercado...',
            'excerpt' => 'La nueva inversión en inteligencia artificial dejó al mercado...',
            'content' => '<p>La nueva inversión en inteligencia artificial dejó al mercado con una señal clara sobre infraestructura, energía y talento. La decisión refuerza una tendencia de largo plazo para la industria tecnológica y confirma que la competencia ya no se juega solamente en modelos, sino también en capacidad de cómputo, acuerdos energéticos y disponibilidad de equipos especializados.</p><p>El movimiento también abre preguntas sobre concentración y acceso para empresas medianas. Si el costo de entrenar y operar sistemas avanzados sigue creciendo, muchas organizaciones dependerán de proveedores externos para construir productos con IA. Esa dependencia puede acelerar adopción, pero también limitar soberanía técnica y capacidad de negociación.</p>',
            'status' => 'published',
        ]);

        $this->artisan('news:validate-quality --published --apply')
            ->assertExitCode(0);

        $news->refresh();

        $this->assertSame('published', $news->status);
        $this->assertStringNotContainsString('...', $news->summary);
        $this->assertStringStartsWith('La nueva inversión en inteligencia artificial dejó al mercado con una señal clara', $news->summary);
        $this->assertSame($news->summary, $news->excerpt);
    }

    public function test_news_editorial_teaser_falls_back_when_summary_is_truncated(): void
    {
        $teaser = news_editorial_teaser(
            'La compañía confirmó nuevos planes...',
            null,
            '<p>La compañía confirmó nuevos planes para integrar inteligencia artificial en sus operaciones regionales. La medida busca reducir tiempos de respuesta y mejorar la trazabilidad de procesos internos.</p>',
            180
        );

        $this->assertStringNotContainsString('...', $teaser);
        $this->assertStringStartsWith('La compañía confirmó nuevos planes para integrar inteligencia artificial', $teaser);
    }

    public function test_news_editorial_teaser_rejects_unfinished_summary_fragments(): void
    {
        $teaser = news_editorial_teaser(
            'Anthropic, la empresa detrás de Claude, está sondeando una nueva ronda de financiación que la valoraría en más de 900.000 millones de dólares. Si se cierra, superaría a OpenAI como la startup de IA más valiosa del mundo. La empr',
            null,
            '<p>Anthropic está sondeando una nueva ronda de financiación que la valoraría en más de 900.000 millones de dólares. Si se cierra, superaría a OpenAI como la startup de IA más valiosa del mundo.</p><p>La empresa busca reforzar su posición en infraestructura y modelos de frontera.</p>',
            260
        );

        $this->assertStringNotContainsString('La empr', $teaser);
        $this->assertSame(
            'Anthropic está sondeando una nueva ronda de financiación que la valoraría en más de 900.000 millones de dólares. Si se cierra, superaría a OpenAI como la startup de IA más valiosa del mundo.',
            $teaser
        );
    }
}
