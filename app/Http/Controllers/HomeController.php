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
        $viewData = Cache::remember('home_page_data', 1800, function () {
            $featuredNews  = $this->fetchFeaturedNews();
            $featuredIds   = $featuredNews->pluck('id')->toArray();

            return array_merge(
                [
                    'featuredNews' => $featuredNews,
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

        $recentNews     = $recentNews->shuffle();
        $featuredVideos = $this->fetchFeaturedVideos();

        // IDs de artículos "trending": top 5 en vistas de los últimos 7 días
        $trendingIds = Cache::remember('trending_ids', 900, fn() =>
            News::where('status', 'published')
                ->where('published_at', '>=', now()->subDays(7))
                ->orderBy('views', 'desc')
                ->limit(5)
                ->pluck('id')
                ->toArray()
        );

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
            'latestDigests'
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
        $published = Cache::remember('all_published_news', 1800, function () {
            // Intentar primero con imágenes reales
            $withImages = News::with('category')
                ->where('status', 'published')
                ->whereNotNull('image')
                ->where(function ($q) {
                    $q->where('image', '!=', '')
                      ->where('image', '!=', 'null')
                      ->where('image', '!=', 'default.jpg')
                      ->whereRaw("image NOT LIKE '%default%'")
                      ->whereRaw("image NOT LIKE '%placeholder%'");
                })
                ->latest('published_at')
                ->take(5)
                ->get();

            // Si no hay ninguna con imagen válida, usar cualquier noticia publicada
            if ($withImages->isEmpty()) {
                return News::with('category')
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->take(5)
                    ->get();
            }

            return $withImages;
        });

        $featured = $this->filterNewsWithPhysicalImages($published, 5);

        if ($featured->count() < 5) {
            $existingIds = $featured->pluck('id')->toArray();
            $additional  = Cache::remember('additional_news_' . implode(',', $existingIds), 1800,
                fn() => News::with('category')
                    ->where('status', 'published')
                    ->whereNotIn('id', $existingIds)
                    ->latest()
                    ->take(5 - $featured->count())
                    ->get()
            );
            $featured = $featured->concat($additional);
        }

        return $featured;
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
                fn() => Column::with('author')->where('featured', true)->latest()->take(2)->get()
            );

            $latestColumnsSectionFeatured = Cache::remember('latest_columns_section_featured', 1800,
                fn() => Column::with('author')->where('featured', true)->latest()->take(8)->get()
            );

            $latestColumnsSection = Cache::remember('latest_columns_section', 1800,
                fn() => Column::with('author')->where('featured', false)->latest()->take(9)->get()
            );

            Log::info("Columnas destacadas: {$latestColumnsSectionFeatured->count()}, No destacadas: {$latestColumnsSection->count()}");
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
        $latestConceptos = Cache::remember('home_latest_conceptos', 1800,
            fn() => ConceptoIa::where('status', 'published')->latest()->take(3)->get()
        );

        $latestAnalises = Cache::remember('home_latest_analises', 1800,
            fn() => AnalisisFondo::where('status', 'published')->latest()->take(3)->get()
        );

        $latestPapers = Cache::remember('home_latest_papers', 1800,
            fn() => ConocIaPaper::where('status', 'published')->latest()->take(3)->get()
        );

        $latestDigests = Cache::remember('home_latest_digests', 1800,
            fn() => EstadoArte::where('status', 'published')->latest()->take(3)->get()
        );

        return compact('latestConceptos', 'latestAnalises', 'latestPapers', 'latestDigests');
    }

    private function fetchFeaturedVideos()
    {
        return Video::where('is_featured', true)
            ->with('platform')
            ->orderBy('published_at', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * Filtra noticias con imágenes físicamente disponibles
     */
    private function filterNewsWithPhysicalImages($news, int $minCount = 5)
    {
        $cacheKey = 'news_with_physical_images_' . md5(implode(',', $news->pluck('id')->toArray()));

        return Cache::remember($cacheKey, 1800, function () use ($news, $minCount) {
            $valid = $news->filter(function ($item) {
                // Sin imagen: se muestra con imagen default, se acepta
                if (empty($item->image)) {
                    return true;
                }
                // Imagen externa (URL): se acepta directamente
                if (Str::startsWith($item->image, ['http://', 'https://'])) {
                    return true;
                }
                // Imagen local: verificar que el archivo exista
                if (!Str::startsWith($item->image, 'storage/')) {
                    return false;
                }
                return Storage::disk('public')->exists(str_replace('storage/', '', $item->image));
            });

            // Si hay suficientes con imágenes reales, usarlas
            if ($valid->count() >= $minCount) {
                return $valid->take($minCount);
            }

            // Completar con el resto — se mostrarán con imagen default en la vista
            $remaining = $news->diff($valid)->take($minCount - $valid->count());
            return $valid->concat($remaining);
        });
    }

    /**
     * Closure helper para URLs de imagen (compatible con vistas existentes)
     */
    private function imageUrlHelper(): \Closure
    {
        $defaultImages = [
            'news'     => ['large' => 'storage/images/defaults/news-default-large.jpg', 'medium' => 'storage/images/defaults/news-default-medium.jpg', 'small' => 'storage/images/defaults/news-default-small.jpg'],
            'research' => ['large' => 'storage/images/defaults/research-default-large.jpg', 'medium' => 'storage/images/defaults/research-default-medium.jpg', 'small' => 'storage/images/defaults/research-default-small.jpg'],
            'profile'  => 'storage/images/defaults/user-profile.jpg',
            'avatars'  => 'storage/images/defaults/avatar-default.jpg',
        ];

        return function ($imagePath, $type = 'news', $size = 'large') use ($defaultImages) {
            if (!$imagePath || $imagePath === '' || $imagePath === 'null') {
                $def = $defaultImages[$type] ?? [];
                return asset(is_array($def) ? ($def[$size] ?? $def['medium']) : ($def ?: 'storage/images/defaults/default.jpg'));
            }
            return asset(Str::startsWith($imagePath, 'storage/') ? $imagePath : 'storage/' . $imagePath);
        };
    }
}
