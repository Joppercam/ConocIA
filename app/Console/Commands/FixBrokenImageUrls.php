<?php

namespace App\Console\Commands;

use App\Models\News;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FixBrokenImageUrls extends Command
{
    protected $signature = 'news:fix-image-urls {--dry-run : Mostrar cambios sin aplicarlos}';
    protected $description = 'Corrige y nullea URLs de imágenes rotas (path absoluto o R2 inaccesible)';

    public function handle()
    {
        $dryRun     = $this->option('dry-run');
        $r2Domain   = parse_url(config('filesystems.disks.custom_public.url', ''), PHP_URL_HOST) ?? '';
        $absPattern = '/var/www/html/storage/app/public/';

        // 1. URLs con path absoluto del servidor → corregir el path
        $withAbsPath = News::whereNotNull('image')
            ->where('image', 'like', '%' . $absPattern . '%')
            ->get();

        // 2. URLs que apuntan a R2 directamente (no Pexels) → nullear para que fetch-missing-images las reemplace
        $withR2 = collect();
        if ($r2Domain) {
            $withR2 = News::whereNotNull('image')
                ->where('image', 'like', '%' . $r2Domain . '%')
                ->where('image', 'not like', '%pexels%')
                ->get();
        }

        $total = $withAbsPath->count() + $withR2->count();

        if ($total === 0) {
            $this->info('No se encontraron imágenes con URLs rotas.');
            return 0;
        }

        $this->info("Encontradas: {$withAbsPath->count()} con path absoluto, {$withR2->count()} con URL de R2.");

        $fixed = 0;

        // Corregir path absoluto
        $baseUrl = rtrim(config('filesystems.disks.custom_public.url', env('APP_URL') . '/storage'), '/');
        foreach ($withAbsPath as $item) {
            $pos = strpos($item->image, $absPattern);
            if ($pos === false) continue;
            $relativePath = substr($item->image, $pos + strlen($absPattern));
            $newUrl = $baseUrl . '/' . ltrim($relativePath, '/');
            $this->line("  <fg=yellow>ABS → NULL:</> {$item->image}");
            if (!$dryRun) $item->update(['image' => null]);
            $fixed++;
        }

        // Nullear URLs de R2 para que se reemplacen con Pexels
        foreach ($withR2 as $item) {
            $this->line("  <fg=yellow>R2 → NULL:</> {$item->image}");
            if (!$dryRun) $item->update(['image' => null]);
            $fixed++;
        }

        if ($dryRun) {
            $this->warn("SIMULACIÓN: {$fixed} imágenes serían nuleadas.");
        } else {
            $this->info("{$fixed} imágenes nuleadas. Ejecutá 'news:fetch-missing-images' para reemplazarlas con Pexels.");
            Cache::forget('all_published_news');
            Cache::forget('home_page_data');
        }

        return 0;
    }
}
