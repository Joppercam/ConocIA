<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = News::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(8);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(6)),
            'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(4, true),
            'summary' => fake()->sentence(),
            'image' => null,
            'category_id' => Category::query()->firstOrCreate(
                ['slug' => 'general'],
                ['name' => 'General', 'description' => 'Categoría general', 'is_active' => true]
            )->id,
            'user_id' => User::factory(),
            'views' => fake()->numberBetween(0, 500),
            'status' => 'published',
            'tags' => null,
            'featured' => false,
            'source' => null,
            'source_url' => null,
            'published_at' => now()->subDay(),
            'reading_time' => fake()->numberBetween(1, 12),
        ];
    }
}
