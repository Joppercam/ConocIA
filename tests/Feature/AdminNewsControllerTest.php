<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminNewsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_news_from_legacy_form_payload(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::create([
            'name' => 'IA',
            'slug' => 'ia',
            'description' => 'Categoria IA',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.news.store'), [
            'title' => 'Nueva noticia legacy',
            'slug' => 'nueva-noticia-legacy',
            'excerpt' => 'Resumen legado para el formulario viejo.',
            'content' => '<p>Contenido completo de la noticia.</p>',
            'category_id' => $category->id,
            'is_featured' => '1',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.news.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('news', [
            'title' => 'Nueva noticia legacy',
            'slug' => 'nueva-noticia-legacy',
            'summary' => 'Resumen legado para el formulario viejo.',
            'excerpt' => 'Resumen legado para el formulario viejo.',
            'status' => 'published',
            'featured' => 1,
            'category_id' => $category->id,
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_update_news_from_current_form_payload(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::create([
            'name' => 'Robots',
            'slug' => 'robots',
            'description' => 'Categoria robots',
            'is_active' => true,
        ]);

        $news = News::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Titulo inicial',
            'slug' => 'titulo-inicial',
            'summary' => 'Resumen inicial',
            'excerpt' => 'Resumen inicial',
            'status' => 'draft',
            'featured' => false,
            'published_at' => null,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.news.update', $news), [
            'title' => 'Titulo actualizado',
            'slug' => 'titulo-actualizado',
            'summary' => 'Resumen actualizado desde el formulario nuevo.',
            'content' => '<p>Contenido actualizado.</p>',
            'category_id' => $category->id,
            'status' => 'published',
            'featured' => '1',
        ]);

        $response->assertRedirect(route('admin.news.index'));
        $response->assertSessionHas('success');

        $news->refresh();

        $this->assertSame('Titulo actualizado', $news->title);
        $this->assertSame('titulo-actualizado', $news->slug);
        $this->assertSame('Resumen actualizado desde el formulario nuevo.', $news->summary);
        $this->assertSame('Resumen actualizado desde el formulario nuevo.', $news->excerpt);
        $this->assertSame('published', $news->status);
        $this->assertTrue($news->featured);
        $this->assertNotNull($news->published_at);
    }

    public function test_admin_news_views_are_visible_in_dashboard_and_index(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::create([
            'name' => 'Datos',
            'slug' => 'datos',
            'description' => 'Categoria datos',
            'is_active' => true,
        ]);

        News::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Noticia con visitas',
            'slug' => 'noticia-con-visitas',
            'views' => 1234,
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $dashboardResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Visitas Totales');
        $dashboardResponse->assertSee('1,234', false);

        $indexResponse = $this->actingAs($admin)->get(route('admin.news.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Vistas');
        $indexResponse->assertSee('1,234', false);
    }

    public function test_admin_dashboard_shows_daily_views_and_trending_news(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::create([
            'name' => 'Metricas',
            'slug' => 'metricas',
            'description' => 'Categoria metricas',
            'is_active' => true,
        ]);

        $otherCategory = Category::create([
            'name' => 'Otra',
            'slug' => 'otra',
            'description' => 'Categoria otra',
            'is_active' => true,
        ]);

        $news = News::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Noticia tendencia',
            'slug' => 'noticia-tendencia',
            'views' => 2000,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $secondNews = News::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $otherCategory->id,
            'title' => 'Noticia secundaria',
            'slug' => 'noticia-secundaria',
            'views' => 900,
            'status' => 'published',
            'published_at' => now()->subDays(2),
        ]);

        DB::table('news_views_stats')->insert([
            [
                'news_id' => $news->id,
                'view_date' => now()->subDay()->toDateString(),
                'views' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'news_id' => $news->id,
                'view_date' => now()->toDateString(),
                'views' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'news_id' => $news->id,
                'view_date' => now()->subDays(7)->toDateString(),
                'views' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'news_id' => $secondNews->id,
                'view_date' => now()->subDays(3)->toDateString(),
                'views' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Tendencia de Visitas (7 días)');
        $response->assertSee('Noticias en Tendencia (7 días)');
        $response->assertSee('Hoy Vs Ayer');
        $response->assertSee('7 Días Vs 7 Días Previos');
        $response->assertSee('Top Categorías (7 días)');
        $response->assertSee('Noticia tendencia');
        $response->assertSee('Metricas');
        $response->assertSee('Otra');
        $response->assertSee('200', false);
        $response->assertSee('120', false);
        $response->assertSee('80', false);
        $response->assertSee('40', false);
        $response->assertSee('-33.3%', false);
        $response->assertSee('+300.0%', false);
    }

    public function test_admin_can_sort_news_index_by_total_views(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::create([
            'name' => 'Ranking',
            'slug' => 'ranking',
            'description' => 'Categoria ranking',
            'is_active' => true,
        ]);

        News::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Menos vista',
            'slug' => 'menos-vista',
            'views' => 50,
        ]);

        News::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Mas vista',
            'slug' => 'mas-vista',
            'views' => 500,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.news.index', [
            'order_by' => 'views',
        ]));

        $response->assertOk();
        $response->assertSeeInOrder(['Mas vista', 'Menos vista']);
    }

    public function test_admin_can_sort_news_index_by_recent_views(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::create([
            'name' => 'Tendencias',
            'slug' => 'tendencias',
            'description' => 'Categoria tendencias',
            'is_active' => true,
        ]);

        $slowNews = News::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Tendencia baja',
            'slug' => 'tendencia-baja',
        ]);

        $fastNews = News::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Tendencia alta',
            'slug' => 'tendencia-alta',
        ]);

        DB::table('news_views_stats')->insert([
            [
                'news_id' => $slowNews->id,
                'view_date' => now()->toDateString(),
                'views' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'news_id' => $fastNews->id,
                'view_date' => now()->toDateString(),
                'views' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.news.index', [
            'order_by' => 'recent_views',
            'analytics_window' => 7,
        ]));

        $response->assertOk();
        $response->assertSeeInOrder(['Tendencia alta', 'Tendencia baja']);
        $response->assertSee('150', false);
        $response->assertSee('15', false);
    }

    public function test_admin_can_view_news_analytics_page(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::create([
            'name' => 'Analitica',
            'slug' => 'analitica',
            'description' => 'Categoria analitica',
            'is_active' => true,
        ]);

        $news = News::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Noticia analitica',
            'slug' => 'noticia-analitica',
            'views' => 700,
        ]);

        DB::table('news_views_stats')->insert([
            [
                'news_id' => $news->id,
                'view_date' => now()->toDateString(),
                'views' => 70,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'news_id' => $news->id,
                'view_date' => now()->subDays(2)->toDateString(),
                'views' => 35,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.analytics.news', [
            'start_date' => now()->subDays(1)->toDateString(),
            'end_date' => now()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertSee('Analítica de Noticias');
        $response->assertSee('Frentes Estratégicos');
        $response->assertSee('Noticia analitica');
        $response->assertSee('Noticias Generales');
        $response->assertSee('Top Categorías');
        $response->assertSee('Top Autores');
        $response->assertSee('Participación');
        $response->assertSee('Período anterior');
        $response->assertSee('Variación');
        $response->assertSee($admin->name);
        $response->assertSee('70', false);
        $response->assertSee('35', false);
        $response->assertSee('+100.0%', false);
        $response->assertSee('100.0%', false);
    }

    public function test_admin_can_export_news_analytics_csv(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::create([
            'name' => 'Exportacion',
            'slug' => 'exportacion',
            'description' => 'Categoria exportacion',
            'is_active' => true,
        ]);

        $news = News::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Noticia exportable',
            'slug' => 'noticia-exportable',
            'views' => 900,
        ]);

        DB::table('news_views_stats')->insert([
            'news_id' => $news->id,
            'view_date' => now()->toDateString(),
            'views' => 90,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.analytics.news.export', [
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();

        $this->assertStringContainsString('Noticia exportable', $content);
        $this->assertStringContainsString('Exportacion', $content);
        $this->assertStringContainsString('90', $content);
        $this->assertStringContainsString('900', $content);
    }

    public function test_admin_news_analytics_preset_today_filters_range(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::create([
            'name' => 'Preset',
            'slug' => 'preset',
            'description' => 'Categoria preset',
            'is_active' => true,
        ]);

        $news = News::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Noticia preset',
            'slug' => 'noticia-preset',
            'views' => 1000,
        ]);

        DB::table('news_views_stats')->insert([
            [
                'news_id' => $news->id,
                'view_date' => now()->toDateString(),
                'views' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'news_id' => $news->id,
                'view_date' => now()->subDay()->toDateString(),
                'views' => 75,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.analytics.news', [
            'preset' => 'today',
        ]));

        $response->assertOk();
        $response->assertSee('Hoy');
        $response->assertSee('25', false);
        $response->assertSee('value="' . now()->toDateString() . '"', false);
        $response->assertSee('Período anterior: 75');
        $response->assertSee('-66.7%', false);
    }

    private function createAdminUser(): User
    {
        $role = Role::create([
            'name' => 'Administrador',
            'slug' => 'admin',
            'description' => 'Rol de administrador',
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }
}
