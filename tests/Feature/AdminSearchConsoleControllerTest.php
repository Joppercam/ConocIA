<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SearchConsoleMetric;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminSearchConsoleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_search_console_dashboard(): void
    {
        Carbon::setTestNow('2026-04-24 12:00:00');
        config()->set('services.search_console.site_url', 'https://conocia.cl/');

        $role = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $role->id,
        ]);

        SearchConsoleMetric::create([
            'site_url' => 'https://conocia.cl/',
            'metric_date' => '2026-04-24',
            'search_type' => 'web',
            'dimension_type' => 'page',
            'page' => 'https://conocia.cl/investigacion/test',
            'dimension_key_hash' => hash('sha256', 'page'),
            'clicks' => 25,
            'impressions' => 300,
            'ctr' => 0.0833,
            'position' => 6.2,
            'synced_at' => now(),
        ]);

        SearchConsoleMetric::create([
            'site_url' => 'https://conocia.cl/',
            'metric_date' => '2026-04-24',
            'search_type' => 'web',
            'dimension_type' => 'page',
            'page' => 'https://www.conocia.cl/news/test-www',
            'dimension_key_hash' => hash('sha256', 'page-www'),
            'clicks' => 0,
            'impressions' => 45,
            'ctr' => 0,
            'position' => 9.5,
            'synced_at' => now(),
        ]);

        SearchConsoleMetric::create([
            'site_url' => 'https://conocia.cl/',
            'metric_date' => '2026-03-30',
            'search_type' => 'web',
            'dimension_type' => 'page',
            'page' => 'https://conocia.cl/investigacion/test',
            'dimension_key_hash' => hash('sha256', 'page-previous'),
            'clicks' => 40,
            'impressions' => 500,
            'ctr' => 0.08,
            'position' => 5.1,
            'synced_at' => now(),
        ]);

        SearchConsoleMetric::create([
            'site_url' => 'https://conocia.cl/',
            'metric_date' => '2026-03-30',
            'search_type' => 'web',
            'dimension_type' => 'page',
            'page' => 'https://conocia.cl/news/subida-fuerte',
            'dimension_key_hash' => hash('sha256', 'page-rising-previous'),
            'clicks' => 0,
            'impressions' => 10,
            'ctr' => 0,
            'position' => 14,
            'synced_at' => now(),
        ]);

        SearchConsoleMetric::create([
            'site_url' => 'https://conocia.cl/',
            'metric_date' => '2026-04-24',
            'search_type' => 'web',
            'dimension_type' => 'page',
            'page' => 'https://conocia.cl/news/subida-fuerte',
            'dimension_key_hash' => hash('sha256', 'page-rising-current'),
            'clicks' => 1,
            'impressions' => 60,
            'ctr' => 0.016,
            'position' => 8.7,
            'synced_at' => now(),
        ]);

        SearchConsoleMetric::create([
            'site_url' => 'https://conocia.cl/',
            'metric_date' => '2026-04-24',
            'search_type' => 'web',
            'dimension_type' => 'query',
            'query' => 'ia chile',
            'dimension_key_hash' => hash('sha256', 'query'),
            'clicks' => 10,
            'impressions' => 120,
            'ctr' => 0.0833,
            'position' => 4.1,
            'synced_at' => now(),
        ]);

        SearchConsoleMetric::create([
            'site_url' => 'https://conocia.cl/',
            'metric_date' => '2026-04-24',
            'search_type' => 'web',
            'dimension_type' => 'query',
            'query' => 'youtube54156',
            'dimension_key_hash' => hash('sha256', 'query-youtube'),
            'clicks' => 0,
            'impressions' => 150,
            'ctr' => 0,
            'position' => 11.3,
            'synced_at' => now(),
        ]);

        $this->assertSame(7, SearchConsoleMetric::count());

        $this->actingAs($admin)
            ->get(route('admin.seo.search-console', [
                'site_url' => 'https://conocia.cl/',
                'days' => 28,
                'type' => 'web',
            ]))
            ->assertOk()
            ->assertSee('Search Console')
            ->assertSee('ia chile')
            ->assertSee('https://conocia.cl/investigacion/test')
            ->assertSee('Prioridades SEO')
            ->assertSee('Rendimiento por Sección')
            ->assertSee('Oportunidades por Sección')
            ->assertSee('Salud Técnica')
            ->assertSee('Investigación')
            ->assertSee('Noticias')
            ->assertSee('Queries Ruidosas')
            ->assertSee('www.conocia.cl')
            ->assertSee('Páginas Cayendo')
            ->assertSee('Páginas Subiendo')
            ->assertSee('Nuevas Oportunidades')
            ->assertSee('https://conocia.cl/news/subida-fuerte');
    }
}
