<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use App\Models\VideoPlatform;
use App\Models\VideoCategory;
use App\Models\VideoTag;
use Illuminate\Support\Str;

class SeedVideosCommand extends Command
{
    protected $signature = 'videos:seed {count=5 : Número de videos a crear}';
    protected $description = 'Semilla la base de datos con videos de muestra';

    public function handle()
    {
        $count = $this->argument('count');
        $this->info("Creando $count videos de muestra...");

        // Asegurarse de que existen las plataformas
        $youtube = VideoPlatform::firstOrCreate(
            ['code' => 'youtube'],
            [
                'name' => 'YouTube',
                'embed_pattern' => 'https://www.youtube.com/embed/{id}',
                'is_active' => true,
            ]
        );
        
        $vimeo = VideoPlatform::firstOrCreate(
            ['code' => 'vimeo'],
            [
                'name' => 'Vimeo',
                'embed_pattern' => 'https://player.vimeo.com/video/{id}',
                'is_active' => true,
            ]
        );
        
        // Asegurarse de que existen categorías
        $categories = [
            'Tecnología' => 'Videos sobre tecnología y avances técnicos',
            'Noticias' => 'Reportajes y noticias actuales',
            'Ciencia' => 'Descubrimientos científicos y educación',
            'Entrevistas' => 'Entrevistas a personalidades destacadas',
        ];
        
        $categoryIds = [];
        foreach ($categories as $name => $description) {
            $category = VideoCategory::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'description' => $description,
                ]
            );
            $categoryIds[] = $category->id;
        }
        
        // Videos de muestra
        $sampleVideos = [
            [
                'platform_id' => $youtube->id,
                'external_id' => 'dQw4w9WgXcQ',
                'title' => 'Inteligencia Artificial: El futuro ya está aquí',
                'description' => 'Un análisis sobre cómo la IA está transformando nuestra sociedad y qué podemos esperar en los próximos años.',
                'thumbnail_url' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/mqdefault.jpg',
                'embed_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'original_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'duration_seconds' => 212,
            ],
            [
                'platform_id' => $youtube->id,
                'external_id' => 'jNQXAC9IVRw',
                'title' => 'Entrevista exclusiva: El impacto de la tecnología en la educación',
                'description' => 'Conversamos con expertos sobre cómo las nuevas tecnologías están cambiando la forma en que aprendemos.',
                'thumbnail_url' => 'https://img.youtube.com/vi/jNQXAC9IVRw/mqdefault.jpg',
                'embed_url' => 'https://www.youtube.com/embed/jNQXAC9IVRw',
                'original_url' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw',
                'duration_seconds' => 319,
            ],
            [
                'platform_id' => $vimeo->id,
                'external_id' => '76979871',
                'title' => 'Especial: Avances tecnológicos en medicina',
                'description' => 'Reportaje sobre los últimos avances tecnológicos en el campo de la medicina y la salud.',
                'thumbnail_url' => 'https://i.vimeocdn.com/video/452001751-7067e9cbff5bac4dfba33a2538b11a7bc6539c3aa02f3b4760f3f227febf5521-d_640',
                'embed_url' => 'https://player.vimeo.com/video/76979871',
                'original_url' => 'https://vimeo.com/76979871',
                'duration_seconds' => 184,
            ],
            [
                'platform_id' => $youtube->id,
                'external_id' => 'yGWvhK2eTsk',
                'title' => 'Especial: Cambio climático y sus consecuencias',
                'description' => 'Análisis en profundidad sobre el cambio climático y cómo está afectando a nuestro planeta.',
                'thumbnail_url' => 'https://img.youtube.com/vi/yGWvhK2eTsk/mqdefault.jpg',
                'embed_url' => 'https://www.youtube.com/embed/yGWvhK2eTsk',
                'original_url' => 'https://www.youtube.com/watch?v=yGWvhK2eTsk',
                'duration_seconds' => 264,
            ],
            [
                'platform_id' => $youtube->id,
                'external_id' => 'zyJq-oMm_x4',
                'title' => 'Economía digital: Criptomonedas y blockchain',
                'description' => 'Todo lo que necesitas saber sobre criptomonedas y la tecnología blockchain que está revolucionando la economía.',
                'thumbnail_url' => 'https://img.youtube.com/vi/zyJq-oMm_x4/mqdefault.jpg',
                'embed_url' => 'https://www.youtube.com/embed/zyJq-oMm_x4',
                'original_url' => 'https://www.youtube.com/watch?v=zyJq-oMm_x4',
                'duration_seconds' => 328,
            ],
            [
                'platform_id' => $vimeo->id,
                'external_id' => '371433846',
                'title' => 'Innovación y startups: Historias de éxito',
                'description' => 'Conoce las historias detrás de las startups más innovadoras y sus claves para el éxito.',
                'thumbnail_url' => 'https://i.vimeocdn.com/video/829865316-cb3c7225292a557bf179f4502a0fb4dafae171ce0fe2f39a6a001e3f05335933-d_640',
                'embed_url' => 'https://player.vimeo.com/video/371433846',
                'original_url' => 'https://vimeo.com/371433846',
                'duration_seconds' => 245,
            ],
        ];
        
        $createdCount = 0;
        
        // Crear los videos
        foreach ($sampleVideos as $index => $videoData) {
            if ($createdCount >= $count) break;
            
            // Añadir datos adicionales
            $videoData['published_at'] = now()->subDays(rand(1, 30));
            $videoData['view_count'] = rand(100, 5000);
            $videoData['is_featured'] = true;
            
            // Crear o actualizar el video
            $video = Video::firstOrCreate(
                [
                    'platform_id' => $videoData['platform_id'],
                    'external_id' => $videoData['external_id']
                ],
                $videoData
            );
            
            // Asignar categorías aleatorias
            $randomCategoryIds = array_rand(array_flip($categoryIds), rand(1, 2));
            if (!is_array($randomCategoryIds)) {
                $randomCategoryIds = [$randomCategoryIds];
            }
            $video->categories()->sync($randomCategoryIds);
            
            // Crear palabras clave
            $keywords = explode(' ', substr($videoData['title'], 0, 30));
            foreach ($keywords as $keyword) {
                if (strlen($keyword) > 3) {
                    $video->keywords()->firstOrCreate(['keyword' => $keyword]);
                }
            }
            
            $createdCount++;
            $this->info("Video creado: {$video->title}");
        }
        
        $this->info("Se han creado $createdCount videos de muestra.");
        
        return 0;
    }
}