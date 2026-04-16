<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsHistoric;
use App\Models\Tag;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Helpers\ImageHelper;
use App\Services\KeywordExtractorService;

class NewsController extends Controller
{
    public function index()
    {
        $news = Cache::remember('news_index_list', 1800, fn() =>
            News::with(['category', 'author'])
                ->where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->paginate(10)
        );

        if ($news->isEmpty()) {
            Log::info('No se encontraron noticias en el método index');
        }

        return view('news.index', [
            'news'             => $news,
            'categories'       => $this->sidebarCategories(),
            'mostReadArticles' => $this->sidebarMostRead(),
            'popularTags'      => $this->sidebarPopularTags(),
            'trendingIds'      => $this->trendingIds(),
        ]);
    }

    public function category($slug)
    {
        $category = Cache::remember("category_{$slug}", 3600,
            fn() => Category::where('slug', $slug)->firstOrFail()
        );

        $news = Cache::remember("news_by_category_{$category->id}", 1800,
            fn() => News::where('category_id', $category->id)
                ->where('status', 'published')
                ->with('author')
                ->latest('published_at')
                ->paginate(10)
        );

        return view('news.index', [
            'news'             => $news,
            'category'         => $category,
            'categories'       => $this->sidebarCategories(),
            'mostReadArticles' => $this->sidebarMostRead(),
            'popularTags'      => $this->sidebarPopularTags(),
            'trendingIds'      => $this->trendingIds(),
        ]);
    }

    public function byCategory($slug)
    {
        return redirect()->route('news.category', $slug);
    }

    public function show($slug)
    {
        $article = News::where('slug', $slug)
            ->with(['category', 'tags', 'author', 'comments' => fn($q) =>
                $q->where('status', 'approved')->whereNull('parent_id')->orderBy('created_at', 'desc')
            ])
            ->first();

        if (!$article) {
            $article = NewsHistoric::where('slug', 'like', $slug . '%')
                ->with(['category', 'author', 'comments' => fn($q) =>
                    $q->where('status', 'approved')->whereNull('parent_id')->orderBy('created_at', 'desc')
                ])
                ->first();

            abort_if(!$article, 404);
        }

        if ($article instanceof News && $article->status === 'published') {
            $this->incrementArticleViews($article);
        }

        if ($article instanceof News && !$article->keywords) {
            $this->maybeExtractKeywords($article);
        }

        return view('news.show', [
            'article'         => $article,
            'relatedArticles' => $this->fetchRelatedArticles($article),
            'mostReadArticles' => $this->sidebarMostRead(),
            'popularTags'     => $this->sidebarPopularTags(),
        ])->with([
            'getImageUrl'      => $this->imageUrlHelper(),
            'getCategoryStyle' => fn($cat) => $cat && isset($cat->color) ? 'background-color: ' . $cat->color . ';' : 'background-color: var(--primary-color);',
            'getCategoryIcon'  => fn($cat) => $cat && isset($cat->icon) ? $cat->icon : 'fa-tag',
        ]);
    }

    public function tag($slug)
    {
        $tag = Cache::remember("tag_{$slug}", 3600,
            fn() => Tag::where('slug', $slug)->firstOrFail()
        );

        $news = Cache::remember("news_by_tag_{$tag->id}", 1800,
            fn() => News::whereHas('tags', fn($q) => $q->where('tags.id', $tag->id))
                ->where('status', 'published')
                ->with(['category', 'author'])
                ->latest('published_at')
                ->paginate(10)
        );

        return view('news.tag', [
            'news'             => $news,
            'tag'              => $tag,
            'categories'       => $this->sidebarCategories(),
            'mostReadArticles' => $this->sidebarMostRead(),
        ]);
    }

    public function archive($year, $month = null)
    {
        $query = News::query()
            ->where('status', 'published')
            ->whereRaw('YEAR(COALESCE(published_at, created_at)) = ?', [$year]);

        if ($month) {
            $query->whereRaw('MONTH(COALESCE(published_at, created_at)) = ?', [(int)$month]);
        }

        $news         = $query->with(['category', 'author'])->latest('published_at')->paginate(10);
        $archiveTitle = $month ? date('F Y', strtotime("{$year}-{$month}-01")) : "Año {$year}";

        return view('news.archive', [
            'news'             => $news,
            'year'             => $year,
            'month'            => $month,
            'archiveTitle'     => $archiveTitle,
            'mostReadArticles' => $this->sidebarMostRead(),
            'categories'       => $this->sidebarCategories(),
            'mostReadArticles' => $this->sidebarMostRead(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Private sidebar helpers (elimina duplicación entre métodos públicos)
    // -------------------------------------------------------------------------

    private function trendingIds(): array
    {
        return Cache::remember('trending_ids', 900, fn() =>
            News::where('status', 'published')
                ->where('published_at', '>=', now()->subDays(7))
                ->orderBy('views', 'desc')
                ->limit(5)
                ->pluck('id')
                ->toArray()
        );
    }

    private function sidebarCategories()
    {
        return Cache::remember('all_categories', 3600,
            fn() => Category::withCount(['news' => fn($q) => $q->where('status', 'published')])
                ->orderBy('news_count', 'desc')
                ->get()
        );
    }

    private function sidebarMostRead()
    {
        return Cache::remember('most_read_articles', 1800,
            fn() => News::with('category')
                ->where('status', 'published')
                ->orderBy('views', 'desc')
                ->take(5)
                ->get()
        );
    }

    private function sidebarPopularTags()
    {
        return Cache::remember('popular_tags', 3600,
            fn() => Tag::select('tags.*')
                ->join('news_tag', 'tags.id', '=', 'news_tag.tag_id')
                ->join('news', 'news.id', '=', 'news_tag.news_id')
                ->where('news.status', 'published')
                ->groupBy('tags.id')
                ->selectRaw('COUNT(news_tag.news_id) as news_count')
                ->orderByDesc('news_count')
                ->limit(10)
                ->get()
        );
    }

    // -------------------------------------------------------------------------
    // Private show() helpers
    // -------------------------------------------------------------------------

    private function fetchRelatedArticles($article)
    {
        return Cache::remember('related_articles_' . $article->id, 1800, function () use ($article) {
            // Tag IDs del artículo actual
            $tagIds = ($article->relationLoaded('tags') && !is_null($article->tags) && !is_string($article->tags) && $article->tags->isNotEmpty())
                ? $article->tags->pluck('id')
                : collect([]);

            // Keywords del artículo actual (almacenadas como CSV)
            $articleKeywords = ($article->keywords)
                ? collect(array_map('trim', explode(',', $article->keywords)))->filter()->values()
                : collect([]);

            // Buscar candidatos: misma categoría O tags en común
            $hasCriteria = $article->category_id || $tagIds->isNotEmpty();

            $candidates = $hasCriteria
                ? News::where('id', '!=', $article->id)
                    ->where('status', 'published')
                    ->with(['category', 'tags'])
                    ->where(function ($q) use ($article, $tagIds) {
                        if ($article->category_id) {
                            $q->where('category_id', $article->category_id);
                        }
                        if ($tagIds->isNotEmpty()) {
                            $q->orWhereHas('tags', fn($tq) => $tq->whereIn('tags.id', $tagIds));
                        }
                    })
                    ->latest('published_at')
                    ->take(30)
                    ->get()
                : collect([]);

            // Puntuar y ordenar: categoría (+2), tag compartido (+1 c/u), keyword compartida (+1 c/u)
            $scored = $candidates
                ->map(function ($candidate) use ($article, $tagIds, $articleKeywords) {
                    $score = 0;

                    if ($article->category_id && $candidate->category_id === $article->category_id) {
                        $score += 2;
                    }
                    if ($tagIds->isNotEmpty()) {
                        $score += $tagIds->intersect($candidate->tags->pluck('id'))->count();
                    }
                    if ($articleKeywords->isNotEmpty() && $candidate->keywords) {
                        $candidateKeywords = collect(array_map('trim', explode(',', $candidate->keywords)));
                        $score += $articleKeywords->intersect($candidateKeywords)->count();
                    }

                    $candidate->relevance_score = $score;
                    return $candidate;
                })
                ->sortByDesc('relevance_score')
                ->take(6)
                ->values();

            // Completar con recientes si hay menos de 6
            if ($scored->count() < 6) {
                $excludeIds = $scored->pluck('id')->push($article->id)->all();
                $recent = News::where('status', 'published')
                    ->whereNotIn('id', $excludeIds)
                    ->with('category')
                    ->latest('published_at')
                    ->take(6 - $scored->count())
                    ->get();
                return $scored->concat($recent);
            }

            return $scored;
        });
    }

    private function maybeExtractKeywords(News $article): void
    {
        try {
            $keywords = app(KeywordExtractorService::class)->extractKeywords($article->content);
            $article->keywords = implode(',', $keywords);
            $article->save();
        } catch (\Exception $e) {
            Log::error('Error al extraer palabras clave: ' . $e->getMessage());
        }
    }

    private function imageUrlHelper(): \Closure
    {
        return function ($imagePath, $type = 'news', $size = 'large') {
            if ($imagePath && (Str::startsWith($imagePath, 'storage/') || Str::startsWith($imagePath, '/storage/'))) {
                $imagePath = basename($imagePath);
            }
            return ImageHelper::getImageUrl($imagePath, $type, $size);
        };
    }

    // -------------------------------------------------------------------------
    // View counter
    // -------------------------------------------------------------------------

    private function incrementArticleViews(News $article): void
    {
        $viewKey = 'news_viewed_' . $article->id;

        if (session()->has($viewKey)) {
            return;
        }

        DB::transaction(function () use ($article) {
            DB::table('news')->where('id', $article->id)->increment('views');

            try {
                $exists = DB::table('news_views_stats')
                    ->where('news_id', $article->id)
                    ->where('view_date', now()->format('Y-m-d'))
                    ->exists();

                if ($exists) {
                    DB::table('news_views_stats')
                        ->where('news_id', $article->id)
                        ->where('view_date', now()->format('Y-m-d'))
                        ->update(['views' => DB::raw('views + 1'), 'updated_at' => now()]);
                } else {
                    DB::table('news_views_stats')->insert([
                        'news_id'    => $article->id,
                        'view_date'  => now()->format('Y-m-d'),
                        'views'      => 1,
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error al actualizar estadísticas de vistas: ' . $e->getMessage());
            }
        });

        session()->put($viewKey, true);

        if (!Cookie::has('news_viewed_' . $article->id)) {
            Cookie::queue('news_viewed_' . $article->id, true, 1440);
        }
    }
}
