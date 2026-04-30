<?php

namespace Tests\Feature;

use App\Models\AnalisisFondo;
use App\Models\ConceptoIa;
use App\Models\ConocIaPaper;
use App\Models\EstadoArte;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishProfundizaRetrievalPackCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_publishes_profundiza_retrieval_pack(): void
    {
        $initialPaperCount = ConocIaPaper::count();

        $this->artisan('content:publish-profundiza-retrieval-pack')
            ->assertSuccessful();

        $this->assertDatabaseHas('conceptos_ia', [
            'slug' => 'retrieval-multimodal',
            'status' => 'published',
            'featured' => 1,
        ]);

        $this->assertDatabaseHas('analisis_fondo', [
            'slug' => 'nuevo-cuello-botella-ia-recuperar-evidencia',
            'status' => 'published',
            'featured' => 1,
        ]);

        $this->assertSame($initialPaperCount + 3, ConocIaPaper::count());

        $this->assertDatabaseHas('estado_arte', [
            'slug' => 'estado-arte-retrieval-multimodal-agentes-abril-2026',
            'subfield' => 'retrieval-multimodal',
            'status' => 'published',
        ]);
    }

    public function test_it_updates_existing_records_when_forced(): void
    {
        ConceptoIa::create([
            'title' => 'Viejo concepto',
            'slug' => 'retrieval-multimodal',
            'content' => '<p>Viejo.</p>',
            'status' => 'draft',
        ]);

        $this->artisan('content:publish-profundiza-retrieval-pack --force')
            ->assertSuccessful();

        $this->assertSame('published', ConceptoIa::where('slug', 'retrieval-multimodal')->value('status'));
        $this->assertTrue(AnalisisFondo::where('slug', 'nuevo-cuello-botella-ia-recuperar-evidencia')->exists());
        $this->assertTrue(EstadoArte::where('slug', 'estado-arte-retrieval-multimodal-agentes-abril-2026')->exists());
    }
}
