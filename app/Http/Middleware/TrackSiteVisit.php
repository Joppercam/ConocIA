<?php

namespace App\Http\Middleware;

use App\Models\AnalisisFondo;
use App\Models\Column;
use App\Models\ConceptoIa;
use App\Models\ConocIaPaper;
use App\Models\EstadoArte;
use App\Models\News;
use App\Models\Research;
use App\Models\Startup;
use App\Models\Video;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackSiteVisit
{
    private static ?bool $hasTable = null;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrack($request, $response)) {
            $this->recordVisit($request);
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (!$request->isMethod('GET') || !$response->isSuccessful()) {
            return false;
        }

        if ($request->is('cp-conocia*') || $request->is('api/*') || $request->is('storage/*')) {
            return false;
        }

        if (in_array($request->path(), ['up', 'favicon.ico', 'robots.txt'], true)) {
            return false;
        }

        if (preg_match('/\.(css|js|map|png|jpe?g|gif|svg|webp|ico|woff2?|ttf|eot|xml)$/i', $request->path())) {
            return false;
        }

        return $this->tableExists();
    }

    private function recordVisit(Request $request): void
    {
        try {
            $content = $this->resolveContent($request);
            $userAgent = (string) $request->userAgent();

            DB::table('site_visit_events')->insert([
                'content_type' => $content['type'],
                'content_id' => $content['id'],
                'title' => Str::limit($content['title'] ?? $this->fallbackTitle($request), 500, ''),
                'url' => Str::limit($request->fullUrl(), 1000, ''),
                'route_name' => $request->route()?->getName(),
                'referrer' => Str::limit((string) $request->headers->get('referer'), 1000, ''),
                'ip_hash' => $request->ip() ? hash('sha256', $request->ip() . '|' . config('app.key')) : null,
                'user_agent' => Str::limit($userAgent, 500, ''),
                'is_bot' => $this->isBot($userAgent),
                'viewed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('TrackSiteVisit failed: ' . $e->getMessage());
        }
    }

    private function resolveContent(Request $request): array
    {
        $routeName = (string) $request->route()?->getName();

        return match ($routeName) {
            'news.show' => $this->resolveRouteModel($request, 'news', News::class, 'noticia'),
            'columns.show' => $this->resolveBySlug($request, Column::class, 'columna'),
            'papers.show' => $this->resolveBySlug($request, ConocIaPaper::class, 'paper'),
            'conceptos.show' => $this->resolveBySlug($request, ConceptoIa::class, 'concepto'),
            'analisis.show' => $this->resolveBySlug($request, AnalisisFondo::class, 'analisis'),
            'estado-arte.show' => $this->resolveBySlug($request, EstadoArte::class, 'estado_del_arte'),
            'startups.show' => $this->resolveRouteModel($request, 'startup', Startup::class, 'startup'),
            'research.show' => $this->resolveById($request, Research::class, 'investigacion'),
            'videos.show' => $this->resolveRouteModel($request, 'video', Video::class, 'video', 'title'),
            default => [
                'type' => $this->routeType($routeName),
                'id' => null,
                'title' => $this->fallbackTitle($request),
            ],
        };
    }

    private function resolveRouteModel(Request $request, string $param, string $modelClass, string $type, string $titleField = 'title'): array
    {
        $value = $request->route($param);
        $model = $value instanceof $modelClass ? $value : null;

        if (!$model && is_scalar($value)) {
            $query = $modelClass::query()->where('id', $value);

            if (Schema::hasColumn((new $modelClass())->getTable(), 'slug')) {
                $query->orWhere('slug', (string) $value);
            }

            $model = $query->first();
        }

        return [
            'type' => $type,
            'id' => $model?->id,
            'title' => $model?->{$titleField} ?? $this->fallbackTitle($request),
        ];
    }

    private function resolveBySlug(Request $request, string $modelClass, string $type): array
    {
        $slug = (string) $request->route('slug');
        $model = $slug !== '' ? $modelClass::query()->where('slug', $slug)->first() : null;

        return [
            'type' => $type,
            'id' => $model?->id,
            'title' => $model?->title ?? $this->fallbackTitle($request),
        ];
    }

    private function resolveById(Request $request, string $modelClass, string $type): array
    {
        $id = $request->route('id');
        $model = $id ? $modelClass::query()->find($id) : null;

        return [
            'type' => $type,
            'id' => $model?->id,
            'title' => $model?->title ?? $this->fallbackTitle($request),
        ];
    }

    private function routeType(string $routeName): string
    {
        if ($routeName === 'home' || $routeName === '') {
            return 'pagina';
        }

        return Str::of($routeName)
            ->before('.')
            ->replace('-', '_')
            ->toString();
    }

    private function fallbackTitle(Request $request): string
    {
        $routeName = $request->route()?->getName();

        return match ($routeName) {
            'home' => 'Home',
            'news.index' => 'Noticias',
            'columns.index' => 'Columnas',
            'papers.index' => 'ConocIA Papers',
            'conceptos.index' => 'Conceptos IA',
            'analisis.index' => 'Analisis de Fondo',
            'estado-arte.index' => 'Estado del Arte',
            'startups.index' => 'Startups IA',
            'research.index' => 'Investigacion',
            'videos.index' => 'Videos',
            default => '/' . ltrim($request->path(), '/'),
        };
    }

    private function isBot(string $userAgent): bool
    {
        return Str::contains(Str::lower($userAgent), [
            'bot', 'crawl', 'spider', 'slurp', 'facebookexternalhit', 'preview', 'monitor', 'uptime',
        ]);
    }

    private function tableExists(): bool
    {
        if (self::$hasTable !== null) {
            return self::$hasTable;
        }

        try {
            return self::$hasTable = Schema::hasTable('site_visit_events');
        } catch (\Throwable) {
            return self::$hasTable = false;
        }
    }
}
