<?php

// database/seeders/TrustedSourceSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrustedSource;

class TrustedSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            [
                'name' => 'Reuters',
                'url' => 'https://www.reuters.com',
                'description' => 'Agencia internacional de noticias',
                'content_selector' => 'article',
                'reliability_score' => 95,
            ],
            [
                'name' => 'Associated Press',
                'url' => 'https://apnews.com',
                'description' => 'Agencia de noticias de Estados Unidos',
                'content_selector' => '.Article',
                'reliability_score' => 95,
            ],
            [
                'name' => 'BBC',
                'url' => 'https://www.bbc.com',
                'description' => 'Servicio de radiodifusión pública del Reino Unido',
                'content_selector' => 'main',
                'reliability_score' => 90,
            ],
            [
                'name' => 'El País',
                'url' => 'https://elpais.com',
                'description' => 'Periódico español de información general',
                'content_selector' => '.article_body',
                'reliability_score' => 85,
            ],
            [
                'name' => 'La Nación',
                'url' => 'https://www.lanacion.com.ar',
                'description' => 'Diario argentino de información general',
                'content_selector' => '.com-text',
                'reliability_score' => 80,
            ],
            [
                'name' => 'The New York Times',
                'url' => 'https://www.nytimes.com',
                'description' => 'Periódico estadounidense',
                'content_selector' => '.StoryBodyCompanionColumn',
                'reliability_score' => 90,
            ],
            [
                'name' => 'The Guardian',
                'url' => 'https://www.theguardian.com',
                'description' => 'Periódico británico',
                'content_selector' => '.article-body-commercial-selector',
                'reliability_score' => 90,
            ],
            [
                'name' => 'CNN',
                'url' => 'https://www.cnn.com',
                'description' => 'Canal de televisión estadounidense',
                'content_selector' => '.article__content',
                'reliability_score' => 80,
            ],
            [
                'name' => 'France 24',
                'url' => 'https://www.france24.com',
                'description' => 'Canal de televisión francés',
                'content_selector' => '.article-content',
                'reliability_score' => 85,
            ],
            [
                'name' => 'DW',
                'url' => 'https://www.dw.com',
                'description' => 'Servicio de radiodifusión internacional de Alemania',
                'content_selector' => '.longText',
                'reliability_score' => 85,
            ],
        ];
        
        foreach ($sources as $source) {
            TrustedSource::create([
                'name' => $source['name'],
                'url' => $source['url'],
                'description' => $source['description'],
                'content_selector' => $source['content_selector'],
                'active_for_monitoring' => true,
                'reliability_score' => $source['reliability_score'],
            ]);
        }
    }
}