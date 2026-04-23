<?php

namespace App\Console\Commands;

use App\Models\ConceptoIa;
use App\Services\GeminiQuotaGuard;
use App\Services\ClaudeService;
use App\Services\OpenAIService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateConceptosIa extends Command
{
    protected $signature = 'conceptos:generate
                            {--topic= : Concepto específico a generar}
                            {--count=1 : Cuántos conceptos generar}
                            {--force : Regenerar aunque ya exista}';

    protected $description = 'Genera páginas de enciclopedia de IA con contenido editorial profundo';

    /**
     * Catálogo de conceptos a cubrir (se generan de a uno por semana).
     */
    protected array $catalog = [
        // Fundamentos
        'transformer-architecture'      => 'Arquitectura Transformer',
        'attention-mechanism'           => 'Mecanismo de Atención',
        'backpropagation'               => 'Backpropagation',
        'embeddings'                    => 'Embeddings',
        'tokenizacion'                  => 'Tokenización',
        // Modelos de lenguaje
        'large-language-models'         => 'Modelos de Lenguaje Grande (LLMs)',
        'rlhf'                          => 'RLHF — Reinforcement Learning from Human Feedback',
        'chain-of-thought'              => 'Chain of Thought Prompting',
        'rag'                           => 'RAG — Retrieval-Augmented Generation',
        'fine-tuning'                   => 'Fine-Tuning',
        'prompt-engineering'            => 'Prompt Engineering',
        'context-window'                => 'Ventana de Contexto',
        // Arquitecturas
        'redes-neuronales-convolucionales' => 'Redes Neuronales Convolucionales (CNN)',
        'redes-recurrentes-lstm'        => 'Redes Recurrentes y LSTM',
        'diffusion-models'              => 'Modelos de Difusión',
        'mixture-of-experts'            => 'Mixture of Experts (MoE)',
        'multimodal-ai'                 => 'IA Multimodal',
        // Entrenamiento
        'pretraining'                   => 'Preentrenamiento',
        'transfer-learning'             => 'Transfer Learning',
        'few-shot-learning'             => 'Few-Shot Learning',
        'overfitting-underfitting'      => 'Overfitting y Underfitting',
        // Aplicaciones
        'computer-vision'               => 'Computer Vision',
        'procesamiento-lenguaje-natural' => 'Procesamiento del Lenguaje Natural (NLP)',
        'speech-recognition'            => 'Reconocimiento de Voz',
        'generative-ai'                 => 'IA Generativa',
        // Impacto y ética
        'alucinaciones-ia'              => 'Alucinaciones en IA',
        'sesgo-algoritmico'             => 'Sesgo Algorítmico',
        'ia-explicable'                 => 'IA Explicable (XAI)',
        'alineamiento-ia'               => 'Alineamiento de IA',
        'agentes-ia'                    => 'Agentes de IA',
    ];

    public function handle(): int
    {
        $guard = app(GeminiQuotaGuard::class);

        $topics = $this->topicsToGenerate();

        foreach ($topics as $slug => $name) {
            if (!$guard->canCall('low')) {
                $this->warn('Gemini quota insuficiente para conceptos. ' . $guard->summary());
                break;
            }

            $this->info("Generando concepto: {$name}...");
            $this->generateConcept($slug, $name, $guard);
            sleep(2);
        }

        return Command::SUCCESS;
    }

    protected function topicsToGenerate(): array
    {
        $count  = (int) $this->option('count');
        $topic  = $this->option('topic');
        $force  = $this->option('force');

        if ($topic) {
            $slug = Str::slug($topic);
            return [$slug => $topic];
        }

        $existing = ConceptoIa::pluck('slug')->toArray();

        $pending = array_filter(
            $this->catalog,
            fn($name, $slug) => $force || !in_array($slug, $existing),
            ARRAY_FILTER_USE_BOTH
        );

        return array_slice($pending, 0, $count, true);
    }

    protected function generateConcept(string $slug, string $name, GeminiQuotaGuard $guard): void
    {
        $prompt = <<<PROMPT
Eres un redactor enciclopédico especializado en inteligencia artificial, con el estilo de Wikipedia en inglés pero adaptado para un portal editorial hispanohablante de referencia técnica.

Genera una entrada de enciclopedia completa sobre: **{$name}**

ESTRUCTURA OBLIGATORIA (HTML, sigue este orden exactamente):

1. **definition** (campo JSON separado, texto plano, 1-2 oraciones): Definición concisa y precisa, sin tecnicismos innecesarios. Ej: "Los embeddings son representaciones numéricas densas de palabras o conceptos en un espacio vectorial de alta dimensión."

2. **content** (HTML completo):
   - <h2>¿Qué es?</h2> — Explicación accesible del concepto, con analogías si ayudan. 2-3 párrafos.
   - <h2>¿Cómo funciona internamente?</h2> — Mecanismo técnico. Usa diagramas descritos en texto, fórmulas conceptuales si aplica. 2-3 párrafos.
   - <h2>¿Por qué importa?</h2> — Relevancia en el campo actual. Aplicaciones reales. 2 párrafos.
   - <h2>Historia y evolución</h2> — Origen, hitos clave, quiénes lo desarrollaron y cuándo. 1-2 párrafos.
   - <h2>Conceptos relacionados</h2> — Lista <ul> de 3-5 conceptos que el lector debería conocer después de entender este.
   - <h2>Para profundizar</h2> — Lista <ul> con 3 recursos: papers fundacionales, libros o cursos de referencia. Formato: <strong>Título</strong> — descripción breve de por qué vale la pena.

3. **excerpt** (texto plano, máx 220 caracteres): Resumen que genere curiosidad, para cards.

4. **category**: Una de estas categorías exactas: "Fundamentos", "Modelos de Lenguaje", "Arquitecturas", "Entrenamiento", "Aplicaciones", "Ética e Impacto"

5. **key_players**: Array JSON de hasta 4 objetos {name, role}. Ej: {"name":"Vaswani et al.","role":"Autores del paper original Attention Is All You Need"}

6. **related_concepts**: Array JSON de slugs de conceptos relacionados (usa slugs de esta lista: transformer-architecture, attention-mechanism, embeddings, rlhf, rag, fine-tuning, diffusion-models, mixture-of-experts, chain-of-thought, prompt-engineering)

7. **reading_time**: Número entero de minutos estimados de lectura.

Extensión del content: mínimo 900 palabras.

Responde SOLO en JSON con claves: definition, content, excerpt, category, key_players, related_concepts, reading_time.
PROMPT;

        $geminiKey   = config('services.gemini.api_key', '');
        $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');

        $data = $this->callAI($prompt, $geminiKey, $geminiModel, $guard);

        if (empty($data['content'])) {
            $this->warn("No se pudo generar el concepto: {$name}");
            return;
        }

        $existingSlug = ConceptoIa::where('slug', $slug)->first();

        $attributes = [
            'title'            => $name,
            'slug'             => $slug,
            'definition'       => $data['definition'] ?? null,
            'content'          => $data['content'],
            'excerpt'          => $data['excerpt'] ?? Str::limit(strip_tags($data['content']), 200),
            'category'         => $data['category'] ?? null,
            'key_players'      => $data['key_players'] ?? null,
            'related_concepts' => $data['related_concepts'] ?? null,
            'reading_time'     => $data['reading_time'] ?? max(1, (int) ceil(str_word_count(strip_tags($data['content'])) / 200)),
            'status'           => 'published',
            'published_at'     => now(),
        ];

        if ($existingSlug && $this->option('force')) {
            $existingSlug->update($attributes);
            $this->info("  Concepto actualizado: {$name}");
        } else {
            ConceptoIa::create($attributes);
            $this->info("  Concepto creado: {$name}");
        }
    }

    protected function callAI(string $prompt, string $geminiKey, string $geminiModel, GeminiQuotaGuard $guard): array
    {
        $openai = app(OpenAIService::class);

        if ($openai->isAvailable()) {
            $data = $openai->generateJson($prompt, 4000, 0.65);
            if (!empty($data['content'])) {
                Log::info('GenerateConceptosIa: generado con OpenAI.');
                return $data;
            }
        }

        try {
            if (!empty($geminiKey) && $guard->canCall('low')) {
                $r = Http::timeout(60)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                    [
                        'contents'         => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.65, 'maxOutputTokens' => 4000, 'responseMimeType' => 'application/json'],
                    ]
                );
                if ($r->successful()) {
                    $data = json_decode($r->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}', true);
                    if (!empty($data['content'])) {
                        $guard->record();
                        return $data;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('GenerateConceptosIa Gemini error: ' . $e->getMessage());
        }

        $claude = app(ClaudeService::class);
        if ($claude->isAvailable()) {
            $data = $claude->generateJson($prompt, 4000, 0.65);
            if (!empty($data['content'])) {
                Log::info('GenerateConceptosIa: generado con Claude (fallback).');
                return $data;
            }
        }

        return [];
    }
}
