<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Vacía la tabla antes de insertar los nuevos datos
         DB::table('categories')->truncate();

         
        $categories = [
            // Categorías técnicas
            [
                'name' => 'Inteligencia Artificial',
                'description' => 'Noticias sobre avances generales en el campo de la inteligencia artificial.',
                'color' => '#4285F4', // Azul
                'icon' => 'fa-brain'
            ],
            [
                'name' => 'Machine Learning',
                'description' => 'Actualidad sobre algoritmos y técnicas de aprendizaje automático.',
                'color' => '#0F9D58', // Verde
                'icon' => 'fa-cogs'
            ],
            [
                'name' => 'Deep Learning',
                'description' => 'Avances en redes neuronales y arquitecturas de aprendizaje profundo.',
                'color' => '#DB4437', // Rojo
                'icon' => 'fa-network-wired'
            ],
            [
                'name' => 'NLP',
                'description' => 'Desarrollos en procesamiento del lenguaje natural.',
                'color' => '#673AB7', // Púrpura
                'icon' => 'fa-comment-alt'
            ],
            [
                'name' => 'Computer Vision',
                'description' => 'Noticias sobre visión artificial y análisis de imágenes.',
                'color' => '#FF9800', // Naranja
                'icon' => 'fa-eye'
            ],
            [
                'name' => 'Robótica',
                'description' => 'Avances en robótica potenciada por inteligencia artificial.',
                'color' => '#795548', // Marrón
                'icon' => 'fa-robot'
            ],
            [
                'name' => 'Computación Cuántica',
                'description' => 'Desarrollos en computación cuántica aplicada a IA.',
                'color' => '#9C27B0', // Violeta
                'icon' => 'fa-atom'
            ],
            
            // Categorías empresariales
            [
                'name' => 'OpenAI',
                'description' => 'Noticias sobre OpenAI, sus investigaciones y productos como GPT.',
                'color' => '#412991', // Púrpura oscuro
                'icon' => 'fa-cube'
            ],
            [
                'name' => 'Google AI',
                'description' => 'Desarrollos de Google y DeepMind en el campo de la IA.',
                'color' => '#4285F4', // Azul de Google
                'icon' => 'fa-google'
            ],
            [
                'name' => 'Microsoft AI',
                'description' => 'Iniciativas de Microsoft en IA y su integración con OpenAI.',
                'color' => '#00A4EF', // Azul de Microsoft
                'icon' => 'fa-microsoft'
            ],
            [
                'name' => 'Meta AI',
                'description' => 'Proyectos de IA de Meta (Facebook).',
                'color' => '#1877F2', // Azul de Facebook
                'icon' => 'fa-facebook'
            ],
            [
                'name' => 'Amazon AI',
                'description' => 'Desarrollos de Amazon en IA y servicios cloud.',
                'color' => '#FF9900', // Naranja de Amazon
                'icon' => 'fa-amazon'
            ],
            [
                'name' => 'Anthropic',
                'description' => 'Noticias sobre Anthropic y su asistente Claude.',
                'color' => '#5A008E', // Morado
                'icon' => 'fa-comment'
            ],
            [
                'name' => 'Startups de IA',
                'description' => 'Emprendimientos emergentes en el espacio de IA.',
                'color' => '#00BCD4', // Cyan
                'icon' => 'fa-rocket'
            ],
            
            // Categorías de aplicación
            [
                'name' => 'IA Generativa',
                'description' => 'Noticias sobre sistemas de IA para generar contenido original.',
                'color' => '#E91E63', // Rosa
                'icon' => 'fa-paint-brush'
            ],
            [
                'name' => 'Automatización',
                'description' => 'Uso de IA para automatizar procesos en distintos sectores.',
                'color' => '#607D8B', // Gris azulado
                'icon' => 'fa-industry'
            ],
            [
                'name' => 'IA en Salud',
                'description' => 'Aplicaciones de IA en medicina, biotecnología y salud.',
                'color' => '#4CAF50', // Verde
                'icon' => 'fa-heartbeat'
            ],
            [
                'name' => 'IA en Finanzas',
                'description' => 'Uso de IA en mercados financieros, trading y fintech.',
                'color' => '#009688', // Verde azulado
                'icon' => 'fa-chart-line'
            ],
            [
                'name' => 'IA en Educación',
                'description' => 'Aplicaciones educativas y formativas de la IA.',
                'color' => '#3F51B5', // Índigo
                'icon' => 'fa-graduation-cap'
            ],
            
            // Categorías sociales/impacto
            [
                'name' => 'Ética de la IA',
                'description' => 'Cuestiones éticas, sesgos y justicia algorítmica.',
                'color' => '#FF5722', // Naranja oscuro
                'icon' => 'fa-balance-scale'
            ],
            [
                'name' => 'Regulación de IA',
                'description' => 'Leyes y normativas sobre IA en diferentes regiones.',
                'color' => '#2196F3', // Azul
                'icon' => 'fa-gavel'
            ],
            [
                'name' => 'Impacto Laboral',
                'description' => 'Cómo la IA está transformando el trabajo y el empleo.',
                'color' => '#FFEB3B', // Amarillo
                'icon' => 'fa-briefcase'
            ],
            [
                'name' => 'Privacidad y Seguridad',
                'description' => 'Temas de protección de datos y ciberseguridad en IA.',
                'color' => '#F44336', // Rojo
                'icon' => 'fa-shield-alt'
            ],
        ];
        
        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'color' => $category['color'],
                'icon' => $category['icon'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}