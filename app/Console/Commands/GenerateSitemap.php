<?php
// app/Console/Commands/GenerateSitemap.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Category;
use App\Models\Column;
use App\Models\Research;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Genera archivos de sitemap XML estáticos';

    public function handle()
    {
        $this->info('Generando sitemap...');

        // Generar el sitemap principal
        $this->generateSitemapIndex();
        $this->generateMainSitemap();
        $this->generateNewsSitemap();
        $this->generateCategoriesSitemap();
        $this->generateResearchSitemap();
        $this->generateColumnsSitemap();

        $this->info('Sitemaps generados correctamente!');
        return Command::SUCCESS;
    }

    protected function generateSitemapIndex()
    {
        $news = News::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->first();
        
        $researches = Research::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->first();
        
        $columns = Column::orderBy('updated_at', 'desc')
            ->first();

        $content = view('sitemap.index', [
            'news' => $news ? collect([$news]) : collect([]),
            'researches' => $researches ? collect([$researches]) : collect([]),
            'columns' => $columns ? collect([$columns]) : collect([]),
            'categories' => Category::all(),
        ])->render();

        Storage::disk('public')->put('sitemaps/sitemap.xml', $content);
        $this->info('Sitemap index generado');
    }

    protected function generateMainSitemap()
    {
        $content = view('sitemap.main')->render();
        Storage::disk('public')->put('sitemaps/sitemap-main.xml', $content);
        $this->info('Sitemap principal generado');
    }

    protected function generateNewsSitemap()
    {
        $news = News::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->get();

        $content = view('sitemap.news', [
            'news' => $news,
        ])->render();

        Storage::disk('public')->put('sitemaps/sitemap-news.xml', $content);
        $this->info('Sitemap de noticias generado');
    }

    protected function generateCategoriesSitemap()
    {
        $categories = Category::all();

        $content = view('sitemap.categories', [
            'categories' => $categories,
        ])->render();

        Storage::disk('public')->put('sitemaps/sitemap-categories.xml', $content);
        $this->info('Sitemap de categorías generado');
    }

    protected function generateResearchSitemap()
    {
        $researches = Research::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->get();

        $content = view('sitemap.research', [
            'researches' => $researches,
        ])->render();

        Storage::disk('public')->put('sitemaps/sitemap-research.xml', $content);
        $this->info('Sitemap de investigación generado');
    }

    protected function generateColumnsSitemap()
    {
        $columns = Column::orderBy('updated_at', 'desc')
            ->get();

        $content = view('sitemap.columns', [
            'columns' => $columns,
        ])->render();

        Storage::disk('public')->put('sitemaps/sitemap-columns.xml', $content);
        $this->info('Sitemap de columnas generado');
    }
}