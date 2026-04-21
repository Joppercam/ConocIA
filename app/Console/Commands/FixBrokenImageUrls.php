<?php

namespace App\Console\Commands;

use App\Models\News;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixBrokenImageUrls extends Command
{
    protected $signature = 'news:fix-image-urls {--dry-run : Mostrar cambios sin aplicarlos}';
    protected $description = 'Corrige URLs de imágenes que contienen el path absoluto del servidor';

    public function handle()
    {
        $dryRun  = $this->option('dry-run');
        $baseUrl = rtrim(config('filesystems.disks.custom_public.url', env('APP_URL') . '/storage'), '/');
        $badPattern = '/var/www/html/storage/app/public/';

        $news = News::whereNotNull('image')
            ->where('image', 'like', '%' . $badPattern . '%')
            ->get();

        if ($news->isEmpty()) {
            $this->info('No se encontraron imágenes con URLs rotas.');
            return 0;
        }

        $this->info("Encontradas {$news->count()} noticias con URLs rotas.");

        $fixed = 0;
        foreach ($news as $item) {
            $pos = strpos($item->image, $badPattern);
            if ($pos === false) continue;

            $relativePath = substr($item->image, $pos + strlen($badPattern));
            $newUrl = $baseUrl . '/' . ltrim($relativePath, '/');

            $this->line("  <fg=yellow>ANTES:</> {$item->image}");
            $this->line("  <fg=green>DESPUÉS:</> {$newUrl}");
            $this->line('');

            if (!$dryRun) {
                $item->update(['image' => $newUrl]);
            }

            $fixed++;
        }

        if ($dryRun) {
            $this->warn("SIMULACIÓN: {$fixed} URLs serían corregidas.");
        } else {
            $this->info("{$fixed} URLs corregidas.");
            // Limpiar caches relacionadas
            \Illuminate\Support\Facades\Cache::forget('all_published_news');
            \Illuminate\Support\Facades\Cache::forget('home_page_data');
            $this->info('Cache limpiado.');
        }

        return 0;
    }
}
