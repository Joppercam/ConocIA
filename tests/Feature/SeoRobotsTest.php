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
}
