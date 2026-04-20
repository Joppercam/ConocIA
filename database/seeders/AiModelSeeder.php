<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AiModelSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ai_models')->truncate();

        $models = [
            [
                'name' => 'GPT-4o', 'slug' => 'gpt-4o', 'company' => 'OpenAI', 'company_slug' => 'openai',
                'type' => 'multimodal', 'access' => 'api-only', 'release_date' => 'Mayo 2024',
                'cap_text' => true, 'cap_image_input' => true, 'cap_image_output' => false,
                'cap_code' => true, 'cap_voice' => true, 'cap_web_search' => true,
                'cap_files' => true, 'cap_reasoning' => false,
                'context_window' => 128000, 'context_window_label' => '128K',
                'price_input' => 2.50, 'price_output' => 10.00, 'has_free_tier' => true,
                'score_mmlu' => 88.7, 'score_humaneval' => 90.2, 'score_math' => 76.6,
                'description' => 'El modelo flagship de OpenAI. Multimodal nativo con capacidades de voz, visión y texto. Base de ChatGPT.',
                'official_url' => 'https://openai.com/gpt-4o', 'featured' => true, 'active' => true, 'sort_order' => 1,
            ],
            [
                'name' => 'o3', 'slug' => 'o3', 'company' => 'OpenAI', 'company_slug' => 'openai',
                'type' => 'llm', 'access' => 'api-only', 'release_date' => 'Abril 2025',
                'cap_text' => true, 'cap_image_input' => true, 'cap_image_output' => false,
                'cap_code' => true, 'cap_voice' => false, 'cap_web_search' => false,
                'cap_files' => true, 'cap_reasoning' => true,
                'context_window' => 200000, 'context_window_label' => '200K',
                'price_input' => 10.00, 'price_output' => 40.00, 'has_free_tier' => false,
                'score_mmlu' => 96.7, 'score_humaneval' => 99.2, 'score_math' => 97.8,
                'description' => 'Modelo de razonamiento avanzado de OpenAI. Diseñado para tareas complejas de matemáticas, ciencia y programación.',
                'official_url' => 'https://openai.com/o3', 'featured' => true, 'active' => true, 'sort_order' => 2,
            ],
            [
                'name' => 'Claude Sonnet 4', 'slug' => 'claude-sonnet-4', 'company' => 'Anthropic', 'company_slug' => 'anthropic',
                'type' => 'multimodal', 'access' => 'api-only', 'release_date' => 'Febrero 2025',
                'cap_text' => true, 'cap_image_input' => true, 'cap_image_output' => false,
                'cap_code' => true, 'cap_voice' => false, 'cap_web_search' => false,
                'cap_files' => true, 'cap_reasoning' => true,
                'context_window' => 200000, 'context_window_label' => '200K',
                'price_input' => 3.00, 'price_output' => 15.00, 'has_free_tier' => true,
                'score_mmlu' => 92.4, 'score_humaneval' => 93.7, 'score_math' => 88.0,
                'description' => 'El modelo más equilibrado de Anthropic. Excelente en análisis, redacción y programación. Ventana de contexto extendida.',
                'official_url' => 'https://anthropic.com/claude', 'featured' => true, 'active' => true, 'sort_order' => 3,
            ],
            [
                'name' => 'Claude Opus 4', 'slug' => 'claude-opus-4', 'company' => 'Anthropic', 'company_slug' => 'anthropic',
                'type' => 'llm', 'access' => 'api-only', 'release_date' => 'Marzo 2025',
                'cap_text' => true, 'cap_image_input' => true, 'cap_image_output' => false,
                'cap_code' => true, 'cap_voice' => false, 'cap_web_search' => false,
                'cap_files' => true, 'cap_reasoning' => true,
                'context_window' => 200000, 'context_window_label' => '200K',
                'price_input' => 15.00, 'price_output' => 75.00, 'has_free_tier' => false,
                'score_mmlu' => 95.1, 'score_humaneval' => 96.4, 'score_math' => 93.2,
                'description' => 'El modelo más capaz de Anthropic. Razonamiento profundo, análisis de documentos complejos y tareas de alta exigencia.',
                'official_url' => 'https://anthropic.com/claude', 'featured' => false, 'active' => true, 'sort_order' => 4,
            ],
            [
                'name' => 'Gemini 2.0 Flash', 'slug' => 'gemini-2-flash', 'company' => 'Google', 'company_slug' => 'google',
                'type' => 'multimodal', 'access' => 'api-only', 'release_date' => 'Febrero 2025',
                'cap_text' => true, 'cap_image_input' => true, 'cap_image_output' => true,
                'cap_code' => true, 'cap_voice' => true, 'cap_web_search' => true,
                'cap_files' => true, 'cap_reasoning' => false,
                'context_window' => 1000000, 'context_window_label' => '1M',
                'price_input' => 0.075, 'price_output' => 0.30, 'has_free_tier' => true,
                'score_mmlu' => 89.2, 'score_humaneval' => 88.4, 'score_math' => 82.0,
                'description' => 'Modelo rápido y eficiente de Google con la mayor ventana de contexto del mercado (1 millón de tokens).',
                'official_url' => 'https://deepmind.google/technologies/gemini/', 'featured' => true, 'active' => true, 'sort_order' => 5,
            ],
            [
                'name' => 'Gemini 2.5 Pro', 'slug' => 'gemini-2-5-pro', 'company' => 'Google', 'company_slug' => 'google',
                'type' => 'multimodal', 'access' => 'api-only', 'release_date' => 'Marzo 2025',
                'cap_text' => true, 'cap_image_input' => true, 'cap_image_output' => true,
                'cap_code' => true, 'cap_voice' => true, 'cap_web_search' => true,
                'cap_files' => true, 'cap_reasoning' => true,
                'context_window' => 1000000, 'context_window_label' => '1M',
                'price_input' => 1.25, 'price_output' => 10.00, 'has_free_tier' => true,
                'score_mmlu' => 94.8, 'score_humaneval' => 95.2, 'score_math' => 91.5,
                'description' => 'El modelo más avanzado de Google. Combina razonamiento profundo con capacidad multimodal y contexto de 1M tokens.',
                'official_url' => 'https://deepmind.google/technologies/gemini/', 'featured' => true, 'active' => true, 'sort_order' => 6,
            ],
            [
                'name' => 'Llama 3.3 70B', 'slug' => 'llama-3-3-70b', 'company' => 'Meta', 'company_slug' => 'meta',
                'type' => 'llm', 'access' => 'open', 'release_date' => 'Diciembre 2024',
                'cap_text' => true, 'cap_image_input' => false, 'cap_image_output' => false,
                'cap_code' => true, 'cap_voice' => false, 'cap_web_search' => false,
                'cap_files' => false, 'cap_reasoning' => false,
                'context_window' => 128000, 'context_window_label' => '128K',
                'price_input' => null, 'price_output' => null, 'has_free_tier' => true,
                'score_mmlu' => 86.0, 'score_humaneval' => 88.4, 'score_math' => 77.0,
                'description' => 'El modelo open source de referencia de Meta. Rendimiento comparable a modelos propietarios, ejecutable localmente.',
                'official_url' => 'https://llama.meta.com/', 'featured' => true, 'active' => true, 'sort_order' => 7,
            ],
            [
                'name' => 'Mistral Large', 'slug' => 'mistral-large', 'company' => 'Mistral AI', 'company_slug' => 'mistral',
                'type' => 'llm', 'access' => 'open', 'release_date' => 'Noviembre 2024',
                'cap_text' => true, 'cap_image_input' => false, 'cap_image_output' => false,
                'cap_code' => true, 'cap_voice' => false, 'cap_web_search' => false,
                'cap_files' => false, 'cap_reasoning' => false,
                'context_window' => 128000, 'context_window_label' => '128K',
                'price_input' => 2.00, 'price_output' => 6.00, 'has_free_tier' => false,
                'score_mmlu' => 84.0, 'score_humaneval' => 85.5, 'score_math' => 74.2,
                'description' => 'El modelo más capaz de Mistral AI. Referente europeo, excelente relación rendimiento/precio para aplicaciones enterprise.',
                'official_url' => 'https://mistral.ai/', 'featured' => false, 'active' => true, 'sort_order' => 8,
            ],
            [
                'name' => 'DeepSeek V3', 'slug' => 'deepseek-v3', 'company' => 'DeepSeek', 'company_slug' => 'deepseek',
                'type' => 'llm', 'access' => 'open', 'release_date' => 'Diciembre 2024',
                'cap_text' => true, 'cap_image_input' => false, 'cap_image_output' => false,
                'cap_code' => true, 'cap_voice' => false, 'cap_web_search' => false,
                'cap_files' => false, 'cap_reasoning' => false,
                'context_window' => 128000, 'context_window_label' => '128K',
                'price_input' => 0.27, 'price_output' => 1.10, 'has_free_tier' => false,
                'score_mmlu' => 88.5, 'score_humaneval' => 89.1, 'score_math' => 90.2,
                'description' => 'El modelo chino que sacudió la industria. Rendimiento top-tier a una fracción del precio de los modelos occidentales.',
                'official_url' => 'https://www.deepseek.com/', 'featured' => true, 'active' => true, 'sort_order' => 9,
            ],
            [
                'name' => 'Grok 3', 'slug' => 'grok-3', 'company' => 'xAI', 'company_slug' => 'xai',
                'type' => 'llm', 'access' => 'api-only', 'release_date' => 'Febrero 2025',
                'cap_text' => true, 'cap_image_input' => true, 'cap_image_output' => false,
                'cap_code' => true, 'cap_voice' => false, 'cap_web_search' => true,
                'cap_files' => false, 'cap_reasoning' => true,
                'context_window' => 131072, 'context_window_label' => '131K',
                'price_input' => 3.00, 'price_output' => 15.00, 'has_free_tier' => false,
                'score_mmlu' => 93.3, 'score_humaneval' => 93.0, 'score_math' => 93.3,
                'description' => 'El modelo de Elon Musk. Acceso en tiempo real a X (Twitter), razonamiento avanzado y benchmarks competitivos.',
                'official_url' => 'https://x.ai/', 'featured' => false, 'active' => true, 'sort_order' => 10,
            ],
        ];

        foreach ($models as $model) {
            DB::table('ai_models')->insert(array_merge($model, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
