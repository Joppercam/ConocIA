<?php

namespace App\Console\Commands;

use App\Models\News;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CleanNewsContent extends Command
{
    protected $signature = 'news:clean-content
                            {--apply : Guarda los cambios en base de datos}
                            {--published : Revisa solo noticias publicadas}
                            {--limit=0 : Limita la cantidad de noticias a revisar}
                            {--id=* : Revisa IDs específicos}';

    protected $description = 'Audita y repara contenido de noticias existentes en base de datos';

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

        $news = $query->get(['id', 'title', 'slug', 'status', 'content']);

        if ($news->isEmpty()) {
            $this->warn('No se encontraron noticias para revisar.');
            return Command::SUCCESS;
        }

        $apply = (bool) $this->option('apply');
        $this->info(sprintf(
            'Revisando %d noticias%s%s...',
            $news->count(),
            $this->option('published') ? ' publicadas' : '',
            $apply ? ' y aplicando cambios' : ' en modo auditoría'
        ));

        $reviewed = 0;
        $fixed = 0;

        $rows = [];

        foreach ($news as $item) {
            $reviewed++;

            $original = (string) ($item->content ?? '');
            $normalized = $this->normalizeContent($original);

            if ($normalized === $original) {
                continue;
            }

            $rows[] = [
                $item->id,
                $item->status,
                Str::limit($item->slug, 40),
                strlen($original) . ' -> ' . strlen($normalized),
                $this->summarizeChange($original, $normalized),
            ];

            if ($apply) {
                $item->forceFill(['content' => $normalized])->save();
                $fixed++;
                $this->line("  ✓ #{$item->id}: {$item->title}");
            }
        }

        if ($rows === []) {
            $this->info('No se detectaron noticias que requieran reparación.');
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->table(
            ['ID', 'Estado', 'Slug', 'Largo', 'Señal detectada'],
            $rows
        );

        if (!$apply) {
            $this->newLine();
            $this->warn('Modo auditoría: no se guardaron cambios.');
            $this->line('Para aplicar la reparación, vuelve a correr el comando con `--apply`.');
        } else {
            News::clearHomeCache();
            $this->newLine();
            $this->info("Listo. {$fixed} artículos corregidos de {$reviewed} revisados.");
        }

        return Command::SUCCESS;
    }

    private function normalizeContent(string $text): string
    {
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        // Eliminar markdown code fences.
        if (preg_match('/^```(?:html)?\s*\n?([\s\S]*?)\n?```\s*$/i', $text, $m)) {
            $text = trim($m[1]);
        }

        // Eliminar estilos y hojas externas incrustadas.
        $text = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $text);
        $text = preg_replace('/<link\b[^>]*>/i', '', $text);

        // Convertir <h1> a <h2> para no duplicar el título del artículo.
        $text = preg_replace('/<h1(\s[^>]*)?>/i', '<h2$1>', $text);
        $text = preg_replace('/<\/h1>/i', '</h2>', $text);

        // Normalización editorial común para rescatar artículos planos.
        $text = format_news_content($text);

        return trim($text);
    }

    private function summarizeChange(string $original, string $normalized): string
    {
        $signals = [];

        if (preg_match('/^```/m', $original)) {
            $signals[] = 'code fences';
        }

        if (preg_match('/<style\b|<link\b/i', $original)) {
            $signals[] = 'estilos incrustados';
        }

        if (preg_match('/<h1\b/i', $original)) {
            $signals[] = 'h1 interno';
        }

        if (!preg_match('/<(p|h2|h3|ul|ol|blockquote|figure|iframe|img)\b/i', $original)
            && preg_match('/<(p|h2|h3)\b/i', $normalized)) {
            $signals[] = 'texto plano';
        }

        if ($signals === []) {
            $signals[] = 'normalización general';
        }

        return implode(', ', $signals);
    }
}
