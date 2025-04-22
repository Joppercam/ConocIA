<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VideoCategory;
use Illuminate\Support\Str;

class VideoCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Política',
                'description' => 'Videos relacionados con política nacional e internacional.',
            ],
            [
                'name' => 'Economía',
                'description' => 'Videos sobre economía, mercados financieros y negocios.',
            ],
            [
                'name' => 'Deportes',
                'description' => 'Videos de eventos deportivos, análisis y noticias.',
            ],
            [
                'name' => 'Tecnología',
                'description' => 'Videos sobre avances tecnológicos, gadgets y tendencias digitales.',
            ],
            [
                'name' => 'Entretenimiento',
                'description' => 'Videos de cultura, espectáculos y entretenimiento.',
            ],
            [
                'name' => 'Ciencia',
                'description' => 'Videos sobre avances científicos y descubrimientos.',
            ],
            [
                'name' => 'Salud',
                'description' => 'Videos relacionados con medicina, salud y bienestar.',
            ],
            [
                'name' => 'Medio Ambiente',
                'description' => 'Videos sobre ecología, cambio climático y sostenibilidad.',
            ],
            [
                'name' => 'Educación',
                'description' => 'Videos educativos y de formación.',
            ],
        ];

        foreach ($categories as $category) {
            VideoCategory::updateOrCreate(
                ['name' => $category['name']],
                [
                    'name' => $category['name'],
                    'slug' => Str::slug($category['name']),
                    'description' => $category['description'],
                ]
            );
        }
    }
}