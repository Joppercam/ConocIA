<?php

namespace App\Console\Commands;

use App\Helpers\ImageHelper;
use App\Models\News;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ValidateNewsQuality extends Command
{
    protected $signature = 'news:validate-quality
                            {--apply : Guarda cambios en base de datos}
                            {--published : Revisa solo noticias publicadas}
                            {--limit=0 : Limita la cantidad de noticias a revisar}
                            {--id=* : Revisa IDs específicos}';

    protected $description = 'Valida calidad editorial e imágenes de noticias, desactivando las incompletas.';

    public function handle(): int
    {
        $query = News::query()->orderBy('id');

        if ($this->option('published')) {
            $query->where('status', 'published');
        }

        $ids = array_filter(array_map('intval', (array) $this->option('id')));
        if ($ids !== []) {
            $query->whereIn('id', $ids);
        }

        $limit = max((int) $this->option('limit'), 0);
        if ($limit > 0) {
            $query->limit($limit);
        }

        $news = $query->get([
            'id',
            'title',
            'slug',
            'status',
            'is_published',
            'featured',
            'content',
            'image',
        ]);

        if ($news->isEmpty()) {
            $this->warn('No se encontraron noticias para validar.');
            return Command::SUCCESS;
        }

        $apply = (bool) $this->option('apply');

        $this->info(sprintf(
            'Validando %d noticias%s%s...',
            $news->count(),
            $this->option('published') ? ' publicadas' : '',
            $apply ? ' y aplicando cambios' : ' en modo auditoría'
        ));

        $rows = [];
        $deactivated = 0;
        $imagesCleared = 0;

        foreach ($news as $item) {
            $signals = $this->detectSignals($item);

            if ($signals->isEmpty()) {
                continue;
            }

            $rows[] = [
                $item->id,
                $item->status,
                $signals->implode(', '),
                $item->slug,
            ];

            if (!$apply) {
                continue;
            }

            $dirty = false;

            if ($signals->contains('contenido incompleto')) {
                $item->status = 'draft';
                $item->is_published = false;
                $item->featured = false;
                $dirty = true;
                $deactivated++;
            }

            if ($signals->contains('imagen rota')) {
                $item->image = null;
                $dirty = true;
                $imagesCleared++;
            }

            if ($dirty) {
                $item->save();
                $this->line("  ✓ #{$item->id}: {$item->title}");
            }
        }

        if ($rows === []) {
            $this->info('No se detectaron noticias con problemas.');
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->table(['ID', 'Estado', 'Señales', 'Slug'], $rows);

        if (!$apply) {
            $this->newLine();
            $this->warn('Modo auditoría: no se guardaron cambios.');
            $this->line('Para aplicar los cambios, vuelve a correr el comando con `--apply`.');
            return Command::SUCCESS;
        }

        News::clearHomeCache();

        $this->newLine();
        $this->info("Listo. {$deactivated} noticias desactivadas y {$imagesCleared} imágenes limpiadas.");

        return Command::SUCCESS;
    }

    private function detectSignals(News $item): Collection
    {
        $signals = collect();

        if (news_content_looks_incomplete($item->content)) {
            $signals->push('contenido incompleto');
        }

        if (!empty($item->image) && !ImageHelper::isValidImage($item->image, 'news')) {
            $signals->push('imagen rota');
        }

        return $signals;
    }
}
