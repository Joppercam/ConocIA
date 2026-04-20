<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('events')->truncate();

        $events = [
            [
                'title' => 'ICML 2025 — International Conference on Machine Learning',
                'slug' => 'icml-2025',
                'description' => 'La conferencia de machine learning más importante del mundo. Presentación de los avances más recientes en aprendizaje automático, deep learning y IA.',
                'type' => 'conference',
                'start_date' => '2025-07-13',
                'end_date' => '2025-07-19',
                'location' => 'Vancouver, Canadá',
                'is_online' => false,
                'url' => 'https://icml.cc/',
                'organizer' => 'IMLS',
                'is_free' => false,
                'featured' => true,
                'active' => true,
            ],
            [
                'title' => 'NeurIPS 2025 — Neural Information Processing Systems',
                'slug' => 'neurips-2025',
                'description' => 'Una de las conferencias de IA y machine learning más prestigiosas del mundo. Investigadores de todo el planeta presentan los avances más innovadores.',
                'type' => 'conference',
                'start_date' => '2025-12-01',
                'end_date' => '2025-12-07',
                'location' => 'San Diego, EE.UU.',
                'is_online' => false,
                'url' => 'https://neurips.cc/',
                'organizer' => 'NeurIPS Foundation',
                'is_free' => false,
                'featured' => true,
                'active' => true,
            ],
            [
                'title' => 'ICLR 2025 — International Conference on Learning Representations',
                'slug' => 'iclr-2025',
                'description' => 'Conferencia dedicada al aprendizaje de representaciones con deep learning. Foco en modelos de lenguaje, visión y sistemas multimodales.',
                'type' => 'conference',
                'start_date' => '2025-04-24',
                'end_date' => '2025-04-28',
                'location' => 'Singapur',
                'is_online' => false,
                'url' => 'https://iclr.cc/',
                'organizer' => 'ICLR',
                'is_free' => false,
                'featured' => true,
                'active' => true,
            ],
            [
                'title' => 'AI for Good Global Summit 2025',
                'slug' => 'ai-for-good-2025',
                'description' => 'Cumbre organizada por la ONU sobre el uso de la inteligencia artificial para abordar los grandes desafíos globales: salud, clima, educación y más.',
                'type' => 'summit',
                'start_date' => '2025-07-08',
                'end_date' => '2025-07-11',
                'location' => 'Ginebra, Suiza',
                'is_online' => true,
                'url' => 'https://aiforgood.itu.int/',
                'organizer' => 'ITU / ONU',
                'is_free' => true,
                'featured' => true,
                'active' => true,
            ],
            [
                'title' => 'Webinar: Introducción a los Agentes de IA con LangChain',
                'slug' => 'webinar-agentes-langchain-2025',
                'description' => 'Sesión práctica en español sobre cómo construir agentes de IA con LangChain y modelos de lenguaje. Incluye ejemplos en Python y demo en vivo.',
                'type' => 'webinar',
                'start_date' => '2025-05-15',
                'end_date' => null,
                'location' => null,
                'is_online' => true,
                'url' => '#',
                'organizer' => 'ConocIA',
                'is_free' => true,
                'featured' => true,
                'active' => true,
            ],
            [
                'title' => 'ACL 2025 — Annual Meeting of the Association for Computational Linguistics',
                'slug' => 'acl-2025',
                'description' => 'La conferencia principal de procesamiento del lenguaje natural (NLP). Investigaciones sobre modelos de lenguaje, traducción automática y comprensión del texto.',
                'type' => 'conference',
                'start_date' => '2025-07-27',
                'end_date' => '2025-08-01',
                'location' => 'Viena, Austria',
                'is_online' => false,
                'url' => 'https://2025.aclweb.org/',
                'organizer' => 'ACL',
                'is_free' => false,
                'featured' => false,
                'active' => true,
            ],
            [
                'title' => 'Deadline: Envío de papers — NeurIPS 2025',
                'slug' => 'neurips-2025-deadline',
                'description' => 'Fecha límite de envío de papers para NeurIPS 2025. Los trabajos deben estar completos y seguir el formato oficial de la conferencia.',
                'type' => 'deadline',
                'start_date' => '2025-05-30',
                'end_date' => null,
                'location' => null,
                'is_online' => true,
                'url' => 'https://neurips.cc/Conferences/2025',
                'organizer' => 'NeurIPS Foundation',
                'is_free' => true,
                'featured' => false,
                'active' => true,
            ],
            [
                'title' => 'CVPR 2025 — Computer Vision and Pattern Recognition',
                'slug' => 'cvpr-2025',
                'description' => 'La conferencia más importante de visión por computadora. Presenta los últimos avances en reconocimiento de imágenes, video, generación visual y modelos multimodales.',
                'type' => 'conference',
                'start_date' => '2025-06-10',
                'end_date' => '2025-06-17',
                'location' => 'Nashville, EE.UU.',
                'is_online' => false,
                'url' => 'https://cvpr.thecvf.com/',
                'organizer' => 'IEEE / CVF',
                'is_free' => false,
                'featured' => false,
                'active' => true,
            ],
        ];

        foreach ($events as $event) {
            DB::table('events')->insert(array_merge($event, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
