<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Services\ClaudeService;
use App\Services\GeminiQuotaGuard;
use App\Services\OpenAIService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReprocessShortNews extends Command
{
    protected $signature = 'news:reprocess-short
                            {--min-words=400 : Reprocesar noticias con menos de N palabras}
                            {--limit=50 : Máximo de noticias a procesar por ejecución}
                            {--dry-run : Mostrar qué se procesaría sin hacer cambios}';

    protected $description = 'Re-expande con IA noticias que quedaron demasiado cortas';

    public function handle(ClaudeService $claude, GeminiQuotaGuard $guard, OpenAIService $openai): int
    {
        $minWords = (int) $this->option('min-words');
        $limit    = (int) $this->option('limit');
        $dryRun   = $this->option('dry-run');

        $news = News::with('category')
            ->where('status', 'published')
            ->whereNotNull('content')
            ->latest('published_at')
            ->get()
            ->filter(fn($n) => str_word_count(strip_tags($n->content)) < $minWords)
            ->take($limit);

        if ($news->isEmpty()) {
            $this->info("No hay noticias con menos de {$minWords} palabras.");
            return 0;
        }

        $this->info("Encontradas {$news->count()} noticias cortas (< {$minWords} palabras).");

        if ($dryRun) {
            foreach ($news as $n) {
                $words = str_word_count(strip_tags($n->content));
                $this->line("  [{$n->id}] {$words}p — {$n->title}");
            }
            return 0;
        }

        $ok   = 0;
        $fail = 0;

        foreach ($news as $item) {
            $words       = str_word_count(strip_tags($item->content));
            $categoryName = $item->category?->name ?? 'Inteligencia Artificial';

            $this->line("  Procesando [{$item->id}] {$words}p — " . Str::limit($item->title, 60));

            $prompt = $this->buildPrompt($item->title, $item->content, $categoryName);
            $enhanced = null;

            $apiKey      = config('services.gemini.api_key');
            $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');

            if ($openai->isAvailable()) {
                try {
                    $data = $openai->generateJson($prompt, 7000, 0.7);
                    if (!empty($data['content']) && str_word_count(strip_tags($data['content'])) >= $minWords) {
                        $enhanced = $data;
                    }
                } catch (\Exception $e) {
                    Log::warning('ReprocessShortNews OpenAI error: ' . $e->getMessage());
                }
            }

            if (!$enhanced && $apiKey && $guard->canCall('medium')) {
                try {
                    $r = Http::timeout(90)->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$apiKey}",
                        [
                            'contents' => [['parts' => [['text' => $prompt]]]],
                            'generationConfig' => [
                                'temperature'      => 0.7,
                                'maxOutputTokens'  => 8000,
                                'responseMimeType' => 'application/json',
                            ],
                        ]
                    );
                    if ($r->successful()) {
                        $guard->record();
                        $data = json_decode($r->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}', true);
                        if (!empty($data['content']) && str_word_count(strip_tags($data['content'])) >= $minWords) {
                            $enhanced = $data;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('ReprocessShortNews Gemini error: ' . $e->getMessage());
                }
            }

            if (!$enhanced && $claude->isAvailable()) {
                try {
                    $data = $claude->generateJson($prompt, 7000, 0.7);
                    if (!empty($data['content']) && str_word_count(strip_tags($data['content'])) >= $minWords) {
                        $enhanced = $data;
                    }
                } catch (\Exception $e) {
                    Log::warning('ReprocessShortNews Claude error: ' . $e->getMessage());
                }
            }

            if (!$enhanced) {
                $this->warn("    ✗ Sin resultado de IA para: {$item->title}");
                $fail++;
                continue;
            }

            $newWords    = str_word_count(strip_tags($enhanced['content']));
            $readingTime = max(1, (int) ceil($newWords / 200));

            $item->update([
                'title'        => $enhanced['title'] ?? $item->title,
                'content'      => $enhanced['content'],
                'excerpt'      => $enhanced['excerpt'] ?? Str::limit(strip_tags($enhanced['content']), 220),
                'reading_time' => $readingTime,
            ]);

            $this->info("    ✓ {$words}p → {$newWords}p");
            $ok++;

            sleep(1);
        }

        $this->info("\nResultado: {$ok} expandidas, {$fail} fallidas.");

        Cache::forget('all_published_news');
        Cache::forget('home_page_data');

        return 0;
    }

    private function buildPrompt(string $title, string $content, string $categoryName): string
    {
        return <<<PROMPT
Eres un periodista senior especializado en {$categoryName}, con el estilo editorial de MIT Technology Review o Wired en español.

ARTÍCULO A EXPANDIR:
Título: {$title}
Contenido actual: {$content}

El artículo quedó demasiado corto. Expándelo a mínimo 1.000 palabras manteniendo la información original y añadiendo:
- Contexto del sector y relevancia del tema
- Implicaciones para la industria o usuarios
- Sección <h2>Contexto clave</h2> con 2-3 conceptos técnicos explicados de forma accesible
- Sección <h2>Para profundizar</h2> con lista <ul> de 3 ítems: <strong>Tema</strong> — descripción breve
- Al menos un <blockquote> con la idea más importante

REQUISITOS:
- Mínimo 1.000 palabras en el campo content
- HTML válido: <p>, <h2>, <ul><li>, <blockquote>
- No menciones que fue expandido o reescrito
- Excerpt: 2 oraciones que capturen la esencia. Máximo 220 caracteres.

Responde SOLO en JSON con estas claves: title, content, excerpt.
PROMPT;
    }
}
