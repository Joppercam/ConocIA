<?php
// app/Http/Controllers/SitemapController.php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Category;
use App\Models\Column;
use App\Models\Research;
use App\Models\ConceptoIa;
use App\Models\AnalisisFondo;
use App\Models\ConocIaPaper;
use App\Models\EstadoArte;
use App\Models\Video;

class SitemapController extends Controller
{
    public function index()
    {
        $news       = News::where('status', 'published')->latest('updated_at')->first();
        $researches = Research::where('status', 'published')->latest('updated_at')->first();
        $columns    = Column::latest('updated_at')->first();
        $conceptos  = ConceptoIa::where('status', 'published')->latest('updated_at')->first();
        $analises   = AnalisisFondo::where('status', 'published')->latest('updated_at')->first();
        $papers     = ConocIaPaper::where('status', 'published')->latest('updated_at')->first();
        $digests    = EstadoArte::where('status', 'published')->latest('updated_at')->first();

        $videos = Video::latest('updated_at')->first();

        $sitemaps = [
            ['url' => 'sitemap-main.xml',        'last' => now()],
            ['url' => 'sitemap-news.xml',         'last' => $news?->updated_at ?? now()],
            ['url' => 'sitemap-categories.xml',   'last' => now()],
            ['url' => 'sitemap-research.xml',     'last' => $researches?->updated_at ?? now()],
            ['url' => 'sitemap-columns.xml',      'last' => $columns?->updated_at ?? now()],
            ['url' => 'sitemap-profundiza.xml',   'last' => collect([$conceptos, $analises, $papers, $digests])
                ->filter()->max(fn($m) => $m->updated_at) ?? now()],
            ['url' => 'sitemap-videos.xml',       'last' => $videos?->updated_at ?? now()],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($sitemaps as $s) {
            $xml .= '<sitemap>';
            $xml .= '<loc>' . url($s['url']) . '</loc>';
            $xml .= '<lastmod>' . (is_string($s['last']) ? $s['last'] : $s['last']->toIso8601String()) . '</lastmod>';
            $xml .= '</sitemap>';
        }
        $xml .= '</sitemapindex>';

        return response($xml)->header('Content-Type', 'text/xml');
    }

    public function news()
    {
        // Google News sitemap spec: only articles published in the last 2 days
        $news = News::where('status', 'published')
            ->where('published_at', '>=', now()->subDays(2))
            ->orderBy('published_at', 'desc')
            ->limit(1000)
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">';
        
        foreach ($news as $article) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('news.show', $article->slug ?? $article->id) . '</loc>';
            $xml .= '<lastmod>' . $article->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '<news:news>';
            $xml .= '<news:publication>';
            $xml .= '<news:name>' . config('app.name', 'ConocIA') . '</news:name>';
            $xml .= '<news:language>' . app()->getLocale() . '</news:language>';
            $xml .= '</news:publication>';
            $xml .= '<news:publication_date>' . ($article->published_at ? $article->published_at->toIso8601String() : $article->created_at->toIso8601String()) . '</news:publication_date>';
            $xml .= '<news:title>' . htmlspecialchars($article->title) . '</news:title>';
            if(is_object($article->category) && isset($article->category->name)) {
                $xml .= '<news:keywords>' . htmlspecialchars($article->category->name) . '</news:keywords>';
            }
            $xml .= '</news:news>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        return response($xml)->header('Content-Type', 'text/xml');
    }

    public function categories()
    {
        $categories = Category::all();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($categories as $category) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('news.by.category', $category->slug) . '</loc>';
            $xml .= '<lastmod>' . now()->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        return response($xml)->header('Content-Type', 'text/xml');
    }

    public function research()
    {
        $researches = Research::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($researches as $research) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('research.show', $research->slug ?? $research->id) . '</loc>';
            $xml .= '<lastmod>' . $research->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        return response($xml)->header('Content-Type', 'text/xml');
    }

    public function columns()
    {
        $columns = Column::orderBy('updated_at', 'desc')->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($columns as $column) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('columns.show', $column->slug ?? $column->id) . '</loc>';
            $xml .= '<lastmod>' . $column->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml)->header('Content-Type', 'text/xml');
    }

    public function profundiza()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Índices de sección
        $sections = [
            ['url' => url('/conceptos-ia'),      'freq' => 'weekly',  'pri' => '0.7'],
            ['url' => url('/analisis'),           'freq' => 'weekly',  'pri' => '0.7'],
            ['url' => url('/papers'),             'freq' => 'weekly',  'pri' => '0.7'],
            ['url' => url('/estado-del-arte'),    'freq' => 'weekly',  'pri' => '0.7'],
        ];
        foreach ($sections as $s) {
            $xml .= '<url><loc>' . $s['url'] . '</loc><changefreq>' . $s['freq'] . '</changefreq><priority>' . $s['pri'] . '</priority></url>';
        }

        // Conceptos IA
        foreach (ConceptoIa::where('status','published')->latest('updated_at')->get() as $item) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('conceptos.show', $item->slug ?? $item->id) . '</loc>';
            $xml .= '<lastmod>' . $item->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq><priority>0.7</priority>';
            $xml .= '</url>';
        }

        // Análisis de Fondo
        foreach (AnalisisFondo::where('status','published')->latest('updated_at')->get() as $item) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('analisis.show', $item->slug ?? $item->id) . '</loc>';
            $xml .= '<lastmod>' . $item->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq><priority>0.75</priority>';
            $xml .= '</url>';
        }

        // ConocIA Papers
        foreach (ConocIaPaper::where('status','published')->latest('updated_at')->get() as $item) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('papers.show', $item->slug ?? $item->id) . '</loc>';
            $xml .= '<lastmod>' . $item->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq><priority>0.75</priority>';
            $xml .= '</url>';
        }

        // Estado del Arte
        foreach (EstadoArte::where('status','published')->latest('updated_at')->get() as $item) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('estado-arte.show', $item->slug ?? $item->id) . '</loc>';
            $xml .= '<lastmod>' . $item->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq><priority>0.7</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml)->header('Content-Type', 'text/xml');
    }

    public function videos()
    {
        $videos = Video::with('categories')
            ->whereNotNull('original_url')
            ->orderBy('published_at', 'desc')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
              . ' xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';

        $xml .= '<url>'
              . '<loc>' . url('/videos') . '</loc>'
              . '<changefreq>weekly</changefreq>'
              . '<priority>0.8</priority>'
              . '</url>';

        foreach ($videos as $video) {
            if (!$video->shouldIndexForSeo()) {
                continue;
            }

            $xml .= '<url>';
            $xml .= '<loc>' . route('videos.show', $video->routeParameters()) . '</loc>';
            $xml .= '<lastmod>' . $video->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq>';
            $xml .= '<priority>0.6</priority>';
            $xml .= '<video:video>';
            $xml .= '<video:thumbnail_loc>' . htmlspecialchars($video->thumbnail_url ?? '') . '</video:thumbnail_loc>';
            $xml .= '<video:title>' . htmlspecialchars($video->title) . '</video:title>';
            if ($video->description) {
                $xml .= '<video:description>' . htmlspecialchars(mb_substr(strip_tags($video->description), 0, 2048)) . '</video:description>';
            }
            $xml .= '<video:content_loc>' . htmlspecialchars($video->original_url) . '</video:content_loc>';
            if ($video->published_at) {
                $xml .= '<video:publication_date>' . $video->published_at->toIso8601String() . '</video:publication_date>';
            }
            if ($video->duration_seconds) {
                $xml .= '<video:duration>' . $video->duration_seconds . '</video:duration>';
            }
            $xml .= '</video:video>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml)->header('Content-Type', 'text/xml');
    }
}
