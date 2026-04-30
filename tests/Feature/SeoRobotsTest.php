<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoRobotsTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_results_are_noindexed(): void
    {
        $response = $this->get(route('search', ['query' => 'chatgpt']));

        $response->assertOk();
        $response->assertSee('noindex, follow');
    }

    public function test_paginated_news_index_is_noindexed(): void
    {
        $response = $this->get(route('news.index', ['page' => 2]));

        $response->assertOk();
        $response->assertSee('noindex, follow');
    }

    public function test_legacy_public_sections_redirect_to_current_urls(): void
    {
        $this->get('/noticias')
            ->assertRedirect('/news');

        $this->get('/profundiza')
            ->assertRedirect('/conceptos-ia');
    }

    public function test_robots_txt_blocks_current_admin_prefix(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Disallow: /cp-conocia/', $robots);
        $this->assertStringContainsString('Disallow: /cp-conocia', $robots);
    }
}
