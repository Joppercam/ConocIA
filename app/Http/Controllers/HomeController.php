<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Research;
use App\Models\Category;
use App\Models\Column;
use App\Models\Video;
use App\Models\ConceptoIa;
use App\Models\AnalisisFondo;
use App\Models\ConocIaPaper;
use App\Models\EstadoArte;
use App\Models\Startup;
use App\Models\EcosystemActor;
use App\Models\Regulation;
use App\Mail\ContactFormMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Muestra la página de inicio
     */
    public function index()
    {
        $featuredNews = $this->fetchFeaturedNews();
        $featuredIds = $featuredNews->pluck('id')->toArray();

        $viewData = Cache::remember('home_page_data_v2', 600, function () use ($featuredIds) {
            return array_merge(
                [
                    'popularNews'  => $this->fetchPopularNews(),
                    'secondaryNews' => $this->fetchSecondaryNews(),
                    'featuredCategories' => $this->fetchFeaturedCategories(),
                ],
                $this->fetchRecentNews($featuredIds),
                $this->fetchColumnsData(),
                $this->fetchResearchData(),
                $this->fetchProfundizaData(),
            );
        });

        extract($viewData);
        $featuredNews = $this->fetchFeaturedNews();

        $featuredPaper = $featuredPaper ?? Cache::remember('home_featured_paper', 300,
            fn() => ConocIaPaper::published()
                ->featured()
                ->orderByDesc('published_at')
                ->orderByDesc('arxiv_published_date')
                ->first()
        );
        $profundizaMeta = $profundizaMeta ?? [];

        $recentNews     = $recentNews->shuffle();
        $featuredVideos  = $this->fetchFeaturedVideos();
        $startupOfWeek = Cache::remember('startup_of_week', 3600, fn() =>
            Startup::active()
                ->where('featured_week', now()->startOfWeek()->toDateString())
                ->whereNotNull('profile_content')
                ->first()
        );

        $recentStartups = $startupOfWeek ? collect([]) : Cache::remember('recent_startups_fallback', 3600, fn() =>
            Startup::active()->orderByDesc('created_at')->limit(3)->get()
        );

        $chileNews = Cache::remember('chile_news_home', 900, fn() =>
            News::with('category')
                ->published()
                ->whereHas('category', fn($q) => $q->where('slug', 'ia-en-chile'))
                ->latest('published_at')
                ->limit(8)
                ->get()
        );

        // IDs de artículos "trending": top 5 en vistas de los últimos 7 días
        $trendingIds = Cache::remember('trending_ids', 900, fn() =>
            News::where('status', 'published')
                ->where('published_at', '>=', now()->subDays(7))
                ->orderBy('views', 'desc')
                ->limit(5)
                ->pluck('id')
                ->toArray()
        );

        $homeStats = Cache::remember('home_stats_v1', 3600, function () {
            return [
                'chile_notes' => News::where('status', 'published')
                    ->whereHas('category', fn($q) => $q->where('slug', 'ia-en-chile'))
                    ->count(),
                'papers'   => ConocIaPaper::count(),
                'research' => Research::count(),
                'fields'   => 6,
            ];
        });

        $ecosistemaStats = Cache::remember('home_ecosistema_stats', 3600, function () {
            $total = EcosystemActor::count();
            $tipos = EcosystemActor::selectRaw('type, count(*) as cnt')
                ->groupBy('type')
                ->pluck('cnt', 'type');
            return [
                'total'         => $total,
                'universidades' => $tipos['universidad'] ?? 0,
                'startups'      => $tipos['startup'] ?? 0,
                'gobierno'      => $tipos['gobierno'] ?? 0,
            ];
        });

        $homeRegulations = Cache::remember('home_regulations_preview', 3600, fn() =>
            Regulation::orderByDesc('updated_at')->limit(3)->get(['id', 'title', 'slug', 'status', 'scope', 'updated_at'])
        );

        $coursesTeaser = [
            ['slug' => 'ia-para-derecho',     'badge' => 'Derecho',     'icon' => 'fa-balance-scale',       'color' => '#a78bfa'],
            ['slug' => 'ia-para-docentes',    'badge' => 'Educación',   'icon' => 'fa-chalkboard-teacher',  'color' => '#00c896'],
            ['slug' => 'ia-para-periodistas', 'badge' => 'Periodismo',  'icon' => 'fa-newspaper',           'color' => '#38b6ff'],
            ['slug' => 'ia-para-rrhh',        'badge' => 'RRHH',        'icon' => 'fa-users-cog',           'color' => '#f59e0b'],
            ['slug' => 'ia-para-salud',       'badge' => 'Salud',       'icon' => 'fa-heartbeat',           'color' => '#f472b6'],
            ['slug' => 'ia-para-pymes',       'badge' => 'PyMEs',       'icon' => 'fa-store',               'color' => '#34d399'],
        ];

        $getImageUrl      = $this->imageUrlHelper();
        $getCategoryStyle = fn($cat) => $cat && isset($cat->color)
            ? 'background-color: ' . $cat->color . ';'
            : 'background-color: var(--primary-color);';
        $getCategoryIcon  = fn($cat) => $cat && isset($cat->icon) ? $cat->icon : 'fa-tag';

        return view('home', compact(
            'featuredNews',
            'recentNews',
            'popularNews',
            'latestColumns',
            'latestColumnsSection',
            'latestColumnsSectionFeatured',
            'secondaryNews',
            'featuredCategories',
            'researchArticles',
            'featuredResearch',
            'mostCommented',
            'featuredVideos',
            'trendingIds',
            'latestConceptos',
            'latestAnalises',
            'latestPapers',
            'featuredPaper',
            'latestDigests',
            'profundizaMeta',
            'startupOfWeek',
            'recentStartups',
            'chileNews',
            'homeStats',
            'ecosistemaStats',
            'homeRegulations',
            'coursesTeaser'
        ))->with([
            'getImageUrl'      => $getImageUrl,
            'getCategoryStyle' => $getCategoryStyle,
            'getCategoryIcon'  => $getCategoryIcon,
        ]);
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
        ]);

        try {
            $adminEmail = config('mail.from.address', 'newsletter@conocia.cl');

            Mail::to($adminEmail)->send(new ContactFormMail(
                senderName:  $validated['name'],
                senderEmail: $validated['email'],
                subject:     $validated['subject'],
                messageBody: $validated['message'],
            ));
        } catch (\Exception $e) {
            Log::error('Contact form mail failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', '¡Mensaje enviado! Te responderemos a la brevedad.');
    }

    // -------------------------------------------------------------------------
    // Private data-fetching methods
    // -------------------------------------------------------------------------

    private function fetchFeaturedNews()
    {
        $news = Cache::remember('home_hero_news_pool_v3', 600, function () {
            return News::with('category')
                ->where('status', 'published')
                ->where('published_at', '>=', now()->subDays(30))
                ->whereNotNull('image')
                ->where(function ($q) {
                    $q->where('image', '!=', '')
                      ->where('image', '!=', 'null')
                      ->where('image', '!=', 'default.jpg')
                      ->whereRaw("image NOT LIKE '%default%'")
                      ->whereRaw("image NOT LIKE '%placeholder%'");
                })
                ->orderByDesc('published_at')
                ->orderByDesc('featured')
                ->take(12)
                ->get();
        });

        return $this->rotateHeroNews($news)->take(5)->values();
    }

    private function rotateHeroNews($news)
    {
        $news = collect($news)->values();
        $count = $news->count();

        if ($count <= 1) {
            return $news;
        }

        $slot = intdiv(now()->hour, 4);
        $offset = $slot % $count;

        return $news->slice($offset)->concat($news->slice(0, $offset))->values();
    }

    private function fetchRecentNews(array $featuredIds): array
    {
        $key = md5(implode(',', $featuredIds));

        $latestNews = Cache::remember("latest_news_{$key}", 1800,
            fn() => News::with('category')
                ->where('status', 'published')
                ->whereNotIn('id', $featuredIds)
                ->latest()
                ->take(28)
                ->get()
        );

        $recentNews = Cache::remember("recent_news_{$key}", 1800,
            fn() => News::with(['category', 'author'])
                ->withCount('comments')
                ->where('status', 'published')
                ->whereNotIn('id', $featuredIds)
                ->latest()
                ->take(28)
                ->get()
        );

        return compact('latestNews', 'recentNews');
    }

    private function fetchPopularNews()
    {
        return Cache::remember('popular_news', 1800,
            fn() => News::with('category')
                ->where('status', 'published')
                ->orderBy('views', 'desc')
                ->take(10)
                ->get()
        );
    }

    private function fetchSecondaryNews()
    {
        return Cache::remember('secondary_news', 1800,
            fn() => News::where('featured', false)
                ->where('status', 'published')
                ->with('category')
                ->latest()
                ->take(2)
                ->get()
        );
    }

    private function fetchFeaturedCategories()
    {
        return Cache::remember('featured_categories', 3600,
            fn() => Category::withCount(['news' => fn($q) => $q->where('status', 'published')])
                ->orderBy('news_count', 'desc')
                ->take(10)
                ->get()
        );
    }

    private function fetchColumnsData(): array
    {
        try {
            $latestColumns = Cache::remember('latest_columns', 1800,
                fn() => Column::with('author')->where('featured', true)->latest()->take(4)->get()
            );

            $latestColumnsSectionFeatured = Cache::remember('latest_columns_section_featured', 1800,
                fn() => Column::with('author')->where('featured', true)->latest()->take(8)->get()
            );

            $latestColumnsSection = Cache::remember('latest_columns_section', 1800,
                fn() => Column::with('author')->where('featured', false)->latest()->take(9)->get()
            );

        } catch (\Exception $e) {
            $latestColumns                = collect([]);
            $latestColumnsSectionFeatured = collect([]);
            $latestColumnsSection         = collect([]);
        }

        return compact('latestColumns', 'latestColumnsSectionFeatured', 'latestColumnsSection');
    }

    private function fetchResearchData(): array
    {
        $researchArticles = Cache::remember('research_articles', 1800,
            fn() => Research::with('category')
                ->where(fn($q) => $q->where('status', 'published')->orWhere('status', 'active'))
                ->latest()
                ->take(8)
                ->get()
        );

        $featuredResearch = Cache::remember('featured_research', 1800,
            fn() => Research::with('category')
                ->where('featured', true)
                ->where(fn($q) => $q->where('status', 'published')->orWhere('status', 'active'))
                ->orderBy('citations', 'desc')
                ->take(5)
                ->get()
        );

        $mostCommented = Cache::remember('most_commented_research', 1800,
            fn() => Research::with('category')
                ->where(fn($q) => $q->where('status', 'published')->orWhere('status', 'active'))
                ->orderBy('comments_count', 'desc')
                ->take(3)
                ->get()
        );

        return compact('researchArticles', 'featuredResearch', 'mostCommented');
    }

    private function fetchProfundizaData(): array
    {
        $latestConceptos = Cache::remember('home_latest_conceptos', 300,
            fn() => ConceptoIa::published()->latest()->take(3)->get()
        );

        $latestAnalises = Cache::remember('home_latest_analises', 300,
            fn() => AnalisisFondo::published()->latest()->take(3)->get()
        );

        $latestPapers = Cache::remember('home_latest_papers', 300,
            fn() => ConocIaPaper::published()
                ->orderByDesc('featured')
                ->orderByDesc('published_at')
                ->orderByDesc('arxiv_published_date')
                ->take(3)
                ->get()
        );

        $featuredPaper = Cache::remember('home_featured_paper', 300,
            fn() => ConocIaPaper::published()
                ->featured()
                ->orderByDesc('published_at')
                ->orderByDesc('arxiv_published_date')
                ->first()
        );

        $latestDigests = Cache::remember('home_latest_digests', 300,
            fn() => EstadoArte::published()->orderByDesc('week_start')->take(3)->get()
        );

        $profundizaMeta = [
            'conceptos' => $this->profundizaSectionMeta(ConceptoIa::published(), $latestConceptos, 'published_at'),
            'analisis' => $this->profundizaSectionMeta(AnalisisFondo::published(), $latestAnalises, 'published_at'),
            'papers' => $this->profundizaSectionMeta(ConocIaPaper::published(), $latestPapers, 'arxiv_published_date'),
            'digests' => $this->profundizaSectionMeta(EstadoArte::published(), $latestDigests, 'week_start'),
        ];

        return compact('latestConceptos', 'latestAnalises', 'latestPapers', 'featuredPaper', 'latestDigests', 'profundizaMeta');
    }

    private function profundizaSectionMeta($query, $items, string $dateField): array
    {
        $count = (clone $query)->count();
        $latestItem = $items->first();
        $latestDate = $latestItem?->{$dateField};

        return [
            'count' => $count,
            'latest_label' => $latestDate ? $latestDate->locale('es')->isoFormat('D MMM YYYY') : null,
        ];
    }

    private function fetchFeaturedVideos()
    {
        $featured = Video::where('is_featured', true)
            ->with('platform')
            ->orderBy('published_at', 'desc')
            ->take(5)
            ->get();

        // Fallback: si no hay videos destacados, usar los más recientes
        if ($featured->isEmpty()) {
            $featured = Video::with('platform')
                ->orderBy('published_at', 'desc')
                ->take(5)
                ->get();
        }

        return $featured;
    }

    /**
     * Filtra noticias con imágenes físicamente disponibles
     */
    private function filterNewsWithPhysicalImages($news, int $minCount = 5)
    {
        $cacheKey = 'news_with_physical_images_' . md5(implode(',', $news->pluck('id')->toArray()));

        return Cache::remember($cacheKey, 1800, function () use ($news, $minCount) {
            $valid = $news->filter(function ($item) {
                if (empty($item->image)) return false;
                // URL externa (Pexels, CDN): válida directamente, pero no URLs de storage local
                if (Str::startsWith($item->image, ['http://', 'https://']) && !Str::contains($item->image, '/storage/')) return true;
                // Imagen local: verificar existencia
                if (Str::startsWith($item->image, 'storage/')) {
                    return Storage::disk('public')->exists(str_replace('storage/', '', $item->image));
                }
                return false;
            });

            return $valid->take($minCount);
        });
    }

    /**
     * Closure helper para URLs de imagen (compatible con vistas existentes)
     */
    private function imageUrlHelper(): \Closure
    {
        $defaultImages = [
            'news'     => ['large' => 'images/defaults/news-default-large.jpg', 'medium' => 'images/defaults/news-default-medium.jpg', 'small' => 'images/defaults/news-default-small.jpg'],
            'research' => ['large' => 'images/defaults/research-default-large.jpg', 'medium' => 'images/defaults/research-default-medium.jpg', 'small' => 'images/defaults/research-default-small.jpg'],
            'profile'  => 'images/defaults/user-profile.jpg',
            'avatars'  => 'images/defaults/avatar-default.jpg',
        ];

        return function ($imagePath, $type = 'news', $size = 'large') use ($defaultImages) {
            if (!$imagePath || $imagePath === '' || $imagePath === 'null') {
                $def = $defaultImages[$type] ?? [];
                return asset(is_array($def) ? ($def[$size] ?? $def['medium']) : ($def ?: 'images/defaults/default.jpg'));
            }
            // URL completa (R2, CDN o imagen externa) — devolver directamente
            if (Str::startsWith($imagePath, ['http://', 'https://'])) {
                return $imagePath;
            }
            // Ruta local storage/...
            return asset(Str::startsWith($imagePath, 'storage/') ? $imagePath : 'storage/' . $imagePath);
        };
    }
}
