<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<html lang="es">', false);
        $this->assertSame(1, substr_count($response->getContent(), '<title>'));
        $this->assertSame(1, substr_count($response->getContent(), '<meta name="description"'));
    }
}
