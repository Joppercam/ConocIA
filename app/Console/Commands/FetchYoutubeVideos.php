<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Models\VideoCategory;
use App\Models\VideoPlatform;
use App\Services\Video\YoutubeService;
use App\Services\Video\VideoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchYoutubeVideos extends Command
{
    protected $signature = 'videos:fetch-youtube
                            {--per-query=4     : Videos a importar por búsqueda}
                            {--dry-run         : Mostrar resultados sin guardar en DB}
                            {--min-duration=120 : Duración mínima en segundos (evita shorts)}
                            {--max-duration=7200 : Duración máxima en segundos (evita maratones)}';

    protected $description = 'Importa videos de YouTube sobre IA y tecnología alineados con el perfil editorial de ConocIA';

    /**
     * Queries agrupadas por perfil.
     * 'categories' = IDs de VideoCategory a asignar (Tecnología=4, Ciencia=6, Educación=9)
     */
    protected array $queries = [
        // ── IA General ───────────────────────────────────────────────────────
        [
            'q'          => 'inteligencia artificial 2025 explicado',
            'categories' => [4],
            'label'      => 'IA General',
        ],
        [
            'q'          => 'inteligencia artificial futuro impacto sociedad',
            'categories' => [4, 6],
            'label'      => 'IA & Sociedad',
        ],

        // ── Modelos de Lenguaje ──────────────────────────────────────────────
        [
            'q'          => 'ChatGPT GPT-4 cómo funciona español',
            'categories' => [4, 9],
            'label'      => 'ChatGPT / GPT',
        ],
        [
            'q'          => 'Claude Anthropic inteligencia artificial',
            'categories' => [4],
            'label'      => 'Claude / Anthropic',
        ],
        [
            'q'          => 'Gemini Google IA generativa',
            'categories' => [4],
            'label'      => 'Gemini / Google AI',
        ],
        [
            'q'          => 'modelos de lenguaje LLM cómo funcionan',
            'categories' => [4, 6, 9],
            'label'      => 'LLMs',
        ],

        // ── Machine Learning / Deep Learning ─────────────────────────────────
        [
            'q'          => 'machine learning tutorial español desde cero',
            'categories' => [4, 9],
            'label'      => 'Machine Learning Tutorial',
        ],
        [
            'q'          => 'deep learning redes neuronales explicación',
            'categories' => [6, 9],
            'label'      => 'Deep Learning',
        ],
        [
            'q'          => 'computer vision visión por computadora IA',
            'categories' => [4, 6],
            'label'      => 'Computer Vision',
        ],

        // ── IA Generativa ────────────────────────────────────────────────────
        [
            'q'          => 'IA generativa imágenes texto audio 2025',
            'categories' => [4],
            'label'      => 'IA Generativa',
        ],
        [
            'q'          => 'Stable Diffusion Midjourney DALL-E tutorial español',
            'categories' => [4, 9],
            'label'      => 'Generación de Imágenes',
        ],

        // ── Impacto y Regulación ─────────────────────────────────────────────
        [
            'q'          => 'IA trabajo empleos automatización futuro',
            'categories' => [4],
            'label'      => 'IA & Trabajo',
        ],
        [
            'q'          => 'regulación inteligencia artificial ley Europa',
            'categories' => [4],
            'label'      => 'Regulación IA',
        ],
        [
            'q'          => 'ética inteligencia artificial sesgos riesgos',
            'categories' => [4, 6],
            'label'      => 'Ética IA',
        ],

        // ── Robótica y Ciencia ───────────────────────────────────────────────
        [
            'q'          => 'robótica inteligencia artificial humanoides 2025',
            'categories' => [4, 6],
            'label'      => 'Robótica',
        ],
        [
            'q'          => 'IA en medicina diagnóstico salud',
            'categories' => [6],
            'label'      => 'IA en Medicina',
        ],
    ];

    public function handle(): int
    {
        $perQuery    = (int) $this->option('per-query');
        $dryRun      = $this->option('dry-run');
        $minDuration = (int) $this->option('min-duration');
        $maxDuration = (int) $this->option('max-duration');

        $platform = VideoPlatform::where('code', 'youtube')->first();
        if (!$platform) {
            $this->error('Plataforma YouTube no encontrada en DB.');
            return Command::FAILURE;
        }

        try {
            $youtubeService = new YoutubeService();
        } catch (\Exception $e) {
            $this->error('Error al inicializar YoutubeService: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $videoService = app(VideoService::class);

        $imported = 0;
        $skipped  = 0;
        $errors   = 0;

        foreach ($this->queries as $queryDef) {
            $q          = $queryDef['q'];
            $catIds     = $queryDef['categories'];
            $label      = $queryDef['label'];

            $this->line("\n<fg=cyan>── {$label}</> → \"{$q}\"");

            try {
                $results = $youtubeService->search([$q], $perQuery + 3); // +3 para compensar filtrados
            } catch (\Exception $e) {
                $this->warn("  Error buscando: " . $e->getMessage());
                $errors++;
                continue;
            }

            $count = 0;
            foreach ($results as $videoData) {
                if ($count >= $perQuery) break;

                $externalId = $videoData['external_id'] ?? null;
                if (!$externalId) continue;

                // Filtrar por duración
                $duration = (int) ($videoData['duration_seconds'] ?? 0);
                if ($duration < $minDuration || $duration > $maxDuration) {
                    $this->line("  <fg=yellow>SKIP</> {$externalId} (duración {$duration}s fuera de rango)");
                    $skipped++;
                    continue;
                }

                // Verificar si ya existe
                if (Video::where('external_id', $externalId)->exists()) {
                    $this->line("  <fg=yellow>DUPL</> {$externalId} — ya existe");
                    $skipped++;
                    continue;
                }

                $title = $videoData['title'] ?? $externalId;

                if ($dryRun) {
                    $this->line("  <fg=green>DRY </> [{$duration}s] {$title}");
                    $count++;
                    continue;
                }

                try {
                    // Obtener datos completos del video
                    $fullData = $youtubeService->getVideoInfo($externalId);
                    if (!$fullData) {
                        $this->warn("  Sin datos para {$externalId}");
                        continue;
                    }

                    $video = $videoService->createVideoFromData($fullData);

                    // Asignar categorías
                    $validCatIds = VideoCategory::whereIn('id', $catIds)->pluck('id')->toArray();
                    if (!empty($validCatIds)) {
                        $video->categories()->sync($validCatIds);
                    }

                    $this->line("  <fg=green>OK  </> [{$duration}s] {$title}");
                    $imported++;
                    $count++;

                    // Pausa breve para respetar rate limit de YouTube API
                    usleep(300000); // 300ms

                } catch (\Exception $e) {
                    $this->warn("  Error importando {$externalId}: " . $e->getMessage());
                    Log::warning("FetchYoutubeVideos error: {$externalId} — " . $e->getMessage());
                    $errors++;
                }
            }

            sleep(1); // pausa entre queries
        }

        $this->newLine();
        $this->info("Importados: {$imported} | Omitidos: {$skipped} | Errores: {$errors}");

        if ($dryRun) {
            $this->comment('(Modo dry-run: ningún video fue guardado)');
        }

        return Command::SUCCESS;
    }
}
