<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTikTokControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_tiktok_script_from_legacy_article_id_payload(): void
    {
        $admin = $this->createAdminUser();
        $category = Category::create([
            'name' => 'TikTok',
            'slug' => 'tiktok',
            'description' => 'Categoria TikTok',
            'is_active' => true,
        ]);

        $news = News::factory()->create([
            'category_id' => $category->id,
            'user_id' => $admin->id,
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.tiktok.store'), [
            'article_id' => $news->id,
            'script_content' => 'Guion breve para TikTok sobre la noticia.',
            'visual_suggestions' => 'Texto en pantalla y captura del titular.',
            'hashtags' => '#ia #news',
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('admin.tiktok.edit', 1));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tiktok_scripts', [
            'news_id' => $news->id,
            'status' => 'draft',
            'script_content' => 'Guion breve para TikTok sobre la noticia.',
        ]);
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
