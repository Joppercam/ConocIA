<?php

namespace App\Console\Commands;

use App\Models\News;
use Illuminate\Console\Command;

class CleanNewsContent extends Command
{
    protected $signature = 'news:clean-content';

    protected $description = 'Elimina markdown code fences (```html) del contenido de noticias existentes en DB';

    public function handle(): int
    {
        $news = News::all();

        $this->info("Revisando {$news->count()} noticias...");

        $fixed = 0;
        foreach ($news as $item) {
            $cleaned = $this->cleanContent($item->content ?? '');
            if ($cleaned !== $item->content) {
                $item->update(['content' => $cleaned]);
                $this->line("  ✓ #{$item->id}: {$item->title}");
                $fixed++;
            }
        }

        if ($fixed === 0) {
            $this->info('No se encontraron problemas. Todo limpio.');
        } else {
            $this->info("Listo. {$fixed} artículos corregidos.");
        }

        return Command::SUCCESS;
    }

    private function cleanContent(string $text): string
    {
        $text = trim($text);
        // Eliminar markdown code fences
        if (preg_match('/^```(?:html)?\s*\n?([\s\S]*?)\n?```\s*$/i', $text, $m)) {
            $text = trim($m[1]);
        }
        // Convertir <h1> a <h2> para no duplicar el título del artículo
        $text = preg_replace('/<h1(\s[^>]*)?>/i', '<h2$1>', $text);
        $text = preg_replace('/<\/h1>/i', '</h2>', $text);
        return $text;
    }
}
