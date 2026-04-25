<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChileSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_chile_section_is_accessible(): void
    {
        $category = Category::create([
            'name' => 'IA en Chile',
            'slug' => 'ia-en-chile',
            'description' => 'Cobertura del ecosistema local.',
            'is_active' => true,
        ]);

        News::factory()->create([
            'category_id' => $category->id,
            'title' => 'Proyecto chileno usa IA en astronomía',
            'slug' => 'proyecto-chileno-usa-ia-astronomia',
        ]);

        $response = $this->get(route('chile.index'));

        $response->assertOk();
        $response->assertSee('IA en Chile');
        $response->assertSee('Proyecto chileno usa IA en astronomía');
    }
}
