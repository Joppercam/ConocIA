<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Services\DifficultyClassifierService;
use Illuminate\Console\Command;

class ClassifyNewsDifficulty extends Command
{
    protected $signature = 'news:classify-difficulty {--force : Reclasificar aunque ya tenga nivel asignado}';
    protected $description = 'Asigna difficulty_level a noticias existentes usando el clasificador heurístico';

    public function handle(): int
    {
        $query = News::where('status', 'published');

        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('difficulty_level')
                  ->orWhere('difficulty_level', '');
            });
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('No hay noticias pendientes de clasificar. Usa --force para reclasificar todas.');
            return 0;
        }

        $this->info("Clasificando {$total} noticias...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $counts = ['basico' => 0, 'intermedio' => 0, 'avanzado' => 0];

        $query->select('id', 'title', 'content')->chunk(100, function ($noticias) use ($bar, &$counts) {
            foreach ($noticias as $noticia) {
                $nivel = DifficultyClassifierService::classify($noticia->title, $noticia->content ?? '');
                $noticia->timestamps = false;
                $noticia->difficulty_level = $nivel;
                $noticia->save();
                $counts[$nivel]++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("Clasificación completada:");
        $this->table(['Nivel', 'Cantidad'], [
            ['Básico',      $counts['basico']],
            ['Intermedio',  $counts['intermedio']],
            ['Avanzado',    $counts['avanzado']],
        ]);

        return 0;
    }
}
