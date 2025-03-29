<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\NewsHistoric;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class NewsArchivingTest extends TestCase
{
    use RefreshDatabase;

    public function test_move_old_news_to_historic()
    {
        // Crear noticias antiguas
        $oldNews = News::factory()->count(5)->create([
            'created_at' => Carbon::now()->subDays(5)
        ]);
        
        // Crear noticias recientes
        $recentNews = News::factory()->count(3)->create([
            'created_at' => Carbon::now()->subDays(2)
        ]);
        
        // Ejecutar el comando
        $this->artisan('news:archive')
             ->assertExitCode(0);
        
        // Verificar que solo las noticias antiguas se movieron
        $this->assertEquals(0, News::whereIn('id', $oldNews->pluck('id'))->count());
        $this->assertEquals(5, NewsHistoric::count());
        $this->assertEquals(3, News::count());
    }

    public function test_can_access_historic_news()
    {
        // Crear una noticia
        $news = News::factory()->create();
        $slug = $news->slug;
        
        // Convertirla a histórica manualmente
        NewsHistoric::create([
            'title' => $news->title,
            'slug' => $news->slug . '-archive-123',
            'content' => $news->content,
            'category_id' => $news->category_id,
            'author_id' => $news->author_id,
            'status' => $news->status,
            'original_id' => $news->id
        ]);
        
        // Eliminar la original
        $news->delete();
        
        // Intentar acceder por la URL original
        $response = $this->get('/news/' . $slug);
        
        // Debería redirigir o mostrar la noticia histórica
        $response->assertStatus(200);
    }
}