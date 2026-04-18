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
        $news = News::whereRaw("content LIKE '%\`\`\`%'")->get();

        if ($news->isEmpty()) {
            $this->info('No se encontraron noticias con code fences. Todo limpio.');
            return Command::SUCCESS;
        }

        $this->info("Encontradas {$news->count()} noticias con code fences. Limpiando...");

        $fixed = 0;
        foreach ($news as $item) {
            $cleaned = $this->stripMarkdownFences($item->content);
            if ($cleaned !== $item->content) {
                $item->update(['content' => $cleaned]);
                $this->line("  ✓ #{$item->id}: {$item->title}");
                $fixed++;
            }
        }

        $this->info("Listo. {$fixed} artículos corregidos.");
        return Command::SUCCESS;
    }

    private function stripMarkdownFences(string $text): string
    {
        $text = trim($text);
        if (preg_match('/^```(?:html)?\s*\n?([\s\S]*?)\n?```\s*$/i', $text, $m)) {
            return trim($m[1]);
        }
        return $text;
    }
}
