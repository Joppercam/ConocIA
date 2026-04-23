<?php

namespace App\Console\Commands;

use App\Models\AiModel;
use App\Services\ClaudeService;
use App\Services\GeminiQuotaGuard;
use App\Services\OpenAIService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncAiModels extends Command
{
    protected $signature = 'models:sync
                            {--dry-run : Mostrar cambios sin guardar}
                            {--new-only : Solo agregar nuevos, no actualizar existentes}';

    protected $description = 'Sincroniza modelos IA con información actualizada usando Gemini/Claude';

    public function handle(): int
    {
        $dryRun  = $this->option('dry-run');
        $newOnly = $this->option('new-only');
        $guard   = app(GeminiQuotaGuard::class);

        $existing = AiModel::pluck('name')->toArray();
        $this->info('Modelos actuales: ' . implode(', ', $existing));

        $data = $this->fetchUpdatesFromAI($existing, $guard);

        if (empty($data)) {
            $this->warn('La IA no devolvió datos. Abortando.');
            return Command::SUCCESS;
        }

        $added   = 0;
        $updated = 0;

        foreach ($data as $item) {
            if (empty($item['name'])) continue;

            $existing_model = AiModel::where('name', $item['name'])->first();

            if ($existing_model) {
                if ($newOnly) {
                    $this->line("  SKIP (--new-only): {$item['name']}");
                    continue;
                }

                $changes = $this->diffModel($existing_model, $item);
                if (empty($changes)) {
                    $this->line("  SIN CAMBIOS: {$item['name']}");
                    continue;
                }

                if ($dryRun) {
                    $this->line("  [dry-run UPDATE] {$item['name']}: " . implode(', ', array_keys($changes)));
                    continue;
                }

                $existing_model->update($changes);
                $this->info("  ACTUALIZADO: {$item['name']} (" . implode(', ', array_keys($changes)) . ')');
                $updated++;
            } else {
                if ($dryRun) {
                    $this->line("  [dry-run NUEVO] {$item['name']}");
                    continue;
                }

                $slug = Str::slug($item['name']);
                if (AiModel::where('slug', $slug)->exists()) {
                    $slug .= '-' . Str::random(4);
                }

                AiModel::create([
                    'name'                 => $item['name'],
                    'slug'                 => $slug,
                    'company'              => $item['company'] ?? 'Desconocido',
                    'company_slug'         => Str::slug($item['company'] ?? 'desconocido'),
                    'type'                 => $item['type'] ?? 'llm',
                    'access'               => $item['access'] ?? 'closed',
                    'release_date'         => $item['release_date'] ?? null,
                    'context_window'       => $item['context_window'] ?? null,
                    'context_window_label' => $item['context_window_label'] ?? null,
                    'price_input'          => $item['price_input'] ?? null,
                    'price_output'         => $item['price_output'] ?? null,
                    'has_free_tier'        => $item['has_free_tier'] ?? false,
                    'score_mmlu'           => $item['score_mmlu'] ?? null,
                    'score_humaneval'      => $item['score_humaneval'] ?? null,
                    'score_math'           => $item['score_math'] ?? null,
                    'cap_text'             => $item['cap_text'] ?? true,
                    'cap_image_input'      => $item['cap_image_input'] ?? false,
                    'cap_image_output'     => $item['cap_image_output'] ?? false,
                    'cap_code'             => $item['cap_code'] ?? false,
                    'cap_voice'            => $item['cap_voice'] ?? false,
                    'cap_web_search'       => $item['cap_web_search'] ?? false,
                    'cap_files'            => $item['cap_files'] ?? false,
                    'cap_reasoning'        => $item['cap_reasoning'] ?? false,
                    'description'          => $item['description'] ?? null,
                    'official_url'         => $item['official_url'] ?? null,
                    'featured'             => false,
                    'active'               => true,
                    'sort_order'           => 99,
                ]);

                $this->info("  NUEVO: {$item['name']}");
                $added++;
            }
        }

        $this->info("Resultado: {$added} nuevos, {$updated} actualizados.");
        return Command::SUCCESS;
    }

    protected function fetchUpdatesFromAI(array $existingNames, GeminiQuotaGuard $guard): array
    {
        $today       = now()->format('Y-m-d');
        $existingStr = implode(', ', $existingNames);

        $prompt = <<<PROMPT
Hoy es {$today}. Eres un experto en modelos de inteligencia artificial.

Modelos que ya tenemos en nuestra base de datos: {$existingStr}

Tarea:
1. Para los modelos que ya tenemos, verifica si hay información desactualizada (precios, contexto, benchmarks nuevos, capacidades añadidas). Devuelve solo los que tengan cambios reales.
2. Lista modelos importantes lanzados recientemente que NO estén en nuestra lista.

Devuelve un array JSON. Cada objeto debe tener estos campos:
- name: nombre exacto del modelo (ej: "GPT-4o", "Claude 3.7 Sonnet")
- company: empresa (OpenAI, Anthropic, Google, Meta, Mistral, xAI, DeepSeek, etc.)
- type: llm|multimodal|image|audio
- access: closed|open|api-only
- release_date: "YYYY-MM" o null
- context_window: número entero de tokens o null
- context_window_label: "128K", "1M", etc. o null
- price_input: USD por millón de tokens input, decimal, o null
- price_output: USD por millón de tokens output, decimal, o null
- has_free_tier: true|false
- score_mmlu: decimal porcentaje o null
- score_humaneval: decimal porcentaje o null
- score_math: decimal porcentaje o null
- cap_text: true|false
- cap_image_input: true|false
- cap_image_output: true|false
- cap_code: true|false
- cap_voice: true|false
- cap_web_search: true|false
- cap_files: true|false
- cap_reasoning: true|false
- description: 2-3 oraciones en español describiendo el modelo
- official_url: URL oficial

Devuelve SOLO el array JSON. Máximo 10 modelos.
PROMPT;

        $geminiKey   = config('services.gemini.api_key', '');
        $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');
        $openai      = app(OpenAIService::class);

        try {
            if ($openai->isAvailable()) {
                $data = $openai->generateJson($prompt, 4000, 0.1);
                if (is_array($data) && !empty($data)) {
                    return isset($data[0]) ? $data : ($data['models'] ?? []);
                }
            }
        } catch (\Exception) {}

        try {
            if (!empty($geminiKey) && $guard->canCall('high')) {
                $r = Http::timeout(60)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                    [
                        'contents'         => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 4000, 'responseMimeType' => 'application/json'],
                    ]
                );

                if ($r->successful()) {
                    $raw  = $r->json()['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                    $data = json_decode($raw, true);
                    if (is_array($data)) {
                        $guard->record();
                        return isset($data[0]) ? $data : ($data['models'] ?? []);
                    }
                }
            }
        } catch (\Exception) {}

        try {
            $claude = app(ClaudeService::class);
            if ($claude->isAvailable()) {
                $data = $claude->generateJson($prompt, 4000, 0.1);
                if (is_array($data) && !empty($data)) {
                    return isset($data[0]) ? $data : ($data['models'] ?? []);
                }
            }
        } catch (\Exception) {}

        return [];
    }

    protected function diffModel(AiModel $model, array $newData): array
    {
        $updatable = [
            'price_input', 'price_output', 'has_free_tier',
            'context_window', 'context_window_label',
            'score_mmlu', 'score_humaneval', 'score_math',
            'cap_text', 'cap_image_input', 'cap_image_output', 'cap_code',
            'cap_voice', 'cap_web_search', 'cap_files', 'cap_reasoning',
            'description',
        ];

        $changes = [];
        foreach ($updatable as $field) {
            if (!array_key_exists($field, $newData)) continue;
            $newVal = $newData[$field];
            $oldVal = $model->$field;
            if ((string) $newVal !== (string) $oldVal && $newVal !== null) {
                $changes[$field] = $newVal;
            }
        }
        return $changes;
    }
}
