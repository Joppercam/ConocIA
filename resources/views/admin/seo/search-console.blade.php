@extends('admin.layouts.app')

@section('title', 'SEO / Search Console')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">SEO / Search Console</h1>
            <p class="text-muted mb-0">Clicks, impresiones y queries orgánicas sincronizadas desde Google Search Console.</p>
        </div>
    </div>

    @if(!$isConfigured)
        <div class="alert alert-warning">
            <strong>Search Console aún no está configurado.</strong>
            <div class="mt-2">Define <code>GOOGLE_SEARCH_CONSOLE_SITE_URL</code> y preferentemente las credenciales OAuth <code>GOOGLE_SEARCH_CONSOLE_CLIENT_ID</code>, <code>GOOGLE_SEARCH_CONSOLE_CLIENT_SECRET</code> y <code>GOOGLE_SEARCH_CONSOLE_REFRESH_TOKEN</code>. Luego ejecuta <code>php artisan seo:sync-search-console</code>.</div>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Propiedad</label>
                    <input type="text" name="site_url" value="{{ $siteUrl }}" class="form-control" placeholder="sc-domain:conocia.cl o https://conocia.cl/">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select name="type" class="form-select">
                        @foreach(['web', 'discover', 'googleNews', 'image', 'video', 'news'] as $option)
                            <option value="{{ $option }}" {{ $type === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Rango</label>
                    <select name="days" class="form-select">
                        @foreach([7, 14, 28, 90] as $option)
                            <option value="{{ $option }}" {{ $days === $option ? 'selected' : '' }}>{{ $option }} días</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 text-md-end">
                    <button class="btn btn-primary">Actualizar vista</button>
                    <span class="ms-2 text-muted small">Sync: <code>php artisan seo:sync-search-console --days={{ $days }} --type={{ $type }}</code></span>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Clicks</div><div class="h4 mb-0">{{ number_format($summary['clicks']) }}</div></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Impresiones</div><div class="h4 mb-0">{{ number_format($summary['impressions']) }}</div></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">CTR Promedio</div><div class="h4 mb-0">{{ number_format($summary['avg_ctr'] * 100, 2) }}%</div></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Posición Promedio</div><div class="h4 mb-0">{{ number_format($summary['avg_position'], 2) }}</div></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Páginas</div><div class="h4 mb-0">{{ number_format($summary['pages']) }}</div></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Queries</div><div class="h4 mb-0">{{ number_format($summary['queries']) }}</div></div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-semibold">Ventana analizada</div>
                <div class="text-muted small">{{ $startDate }} a {{ $endDate }}</div>
            </div>
            <div class="text-end">
                <div class="text-muted small mb-1">Comparada contra {{ $previousStartDate }} a {{ $previousEndDate }}</div>
                <div class="fw-semibold">Última sincronización</div>
                <div class="text-muted small">{{ $summary['last_synced_at'] ? \Illuminate\Support\Carbon::parse($summary['last_synced_at'])->diffForHumans() : 'Aún sin datos sincronizados' }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Prioridades SEO</strong>
                    <span class="badge bg-warning-subtle text-dark">{{ $opportunityPages->count() }} oportunidades</span>
                </div>
                <div class="card-body">
                    @if($opportunityPages->isNotEmpty())
                        <div class="small text-muted mb-3">URLs con impresiones, posición aprovechable y CTR bajo. Son las mejores candidatas para reescribir títulos, mejorar snippet y reforzar enlazado interno.</div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Página</th>
                                        <th class="text-end">Imp.</th>
                                        <th class="text-end">Pos.</th>
                                        <th class="text-end">CTR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($opportunityPages as $row)
                                        <tr>
                                            <td style="max-width:360px;">
                                                <a href="{{ $row->page }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($row->page, 72) }}</a>
                                            </td>
                                            <td class="text-end">{{ number_format($row->impressions) }}</td>
                                            <td class="text-end">{{ number_format($row->position, 1) }}</td>
                                            <td class="text-end">{{ number_format($row->ctr * 100, 2) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">Todavía no hay suficientes datos para detectar oportunidades claras.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Salud Técnica</strong>
                    <span class="badge {{ $mixedHosts ? 'bg-danger' : 'bg-success' }}">
                        {{ $mixedHosts ? 'Hosts mezclados' : 'Host consistente' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="small text-muted mb-3">
                        Host canónico esperado: <strong>{{ $canonicalHost ?: 'sin definir en APP_URL' }}</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Host</th>
                                    <th class="text-end">URLs</th>
                                    <th class="text-end">Imp.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hostBreakdown as $host)
                                    <tr>
                                        <td>{{ $host['host'] }}</td>
                                        <td class="text-end">{{ number_format($host['pages']) }}</td>
                                        <td class="text-end">{{ number_format($host['impressions']) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">Sin hosts detectados todavía.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($mixedHosts)
                        <div class="alert alert-danger mt-3 mb-0 py-2">
                            Google está viendo más de un host para el sitio. Conviene unificar `www` vs sin `www`, revisar `canonical` y forzar una sola versión en sitemap.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($actionableNews->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Noticias Para Optimizar Ahora</strong>
            <span class="badge bg-primary">{{ $actionableNews->count() }} detectadas</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Noticia</th>
                            <th class="text-end">Imp.</th>
                            <th class="text-end">Pos.</th>
                            <th class="text-end">Title</th>
                            <th class="text-end">Desc.</th>
                            <th class="text-center">Resumen</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($actionableNews as $item)
                            <tr>
                                <td style="max-width:360px;">
                                    <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($item['news']->title, 72) }}</div>
                                    <div class="small text-muted">{{ $item['news']->slug }}</div>
                                </td>
                                <td class="text-end">{{ number_format($item['impressions']) }}</td>
                                <td class="text-end">{{ number_format($item['position'], 1) }}</td>
                                <td class="text-end">
                                    <span class="badge {{ $item['seo_title_length'] <= 60 ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $item['seo_title_length'] }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="badge {{ $item['seo_description_length'] >= 110 && $item['seo_description_length'] <= 155 ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $item['seo_description_length'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $item['has_summary'] ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item['has_summary'] ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.news.edit', $item['news']) }}" class="btn btn-sm btn-outline-primary">
                                        Optimizar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white"><strong>Páginas Cayendo</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Página</th>
                                    <th class="text-end">Imp.</th>
                                    <th class="text-end">Delta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fallingPages as $row)
                                    <tr>
                                        <td style="max-width:220px;">
                                            <a href="{{ $row->page }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($row->page, 52) }}</a>
                                        </td>
                                        <td class="text-end">{{ number_format($row->impressions) }}</td>
                                        <td class="text-end text-danger">{{ number_format($row->delta_impressions) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">Sin caídas fuertes en este rango.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white"><strong>Páginas Subiendo</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Página</th>
                                    <th class="text-end">Imp.</th>
                                    <th class="text-end">Delta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($risingPages as $row)
                                    <tr>
                                        <td style="max-width:220px;">
                                            <a href="{{ $row->page }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($row->page, 52) }}</a>
                                        </td>
                                        <td class="text-end">{{ number_format($row->impressions) }}</td>
                                        <td class="text-end text-success">+{{ number_format($row->delta_impressions) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">Sin subidas fuertes todavía.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white"><strong>Nuevas Oportunidades</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Página</th>
                                    <th class="text-end">Imp.</th>
                                    <th class="text-end">Pos.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($newOpportunityPages as $row)
                                    <tr>
                                        <td style="max-width:220px;">
                                            <a href="{{ $row->page }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($row->page, 52) }}</a>
                                        </td>
                                        <td class="text-end">{{ number_format($row->impressions) }}</td>
                                        <td class="text-end">{{ number_format($row->position, 1) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">Sin nuevas oportunidades claras.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white"><strong>Top páginas</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Página</th>
                                <th class="text-end">Clicks</th>
                                <th class="text-end">Imp.</th>
                                <th class="text-end">CTR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPages as $row)
                                <tr>
                                    <td style="max-width:380px;"><a href="{{ $row->page }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($row->page, 80) }}</a></td>
                                    <td class="text-end">{{ number_format($row->clicks) }}</td>
                                    <td class="text-end">{{ number_format($row->impressions) }}</td>
                                    <td class="text-end">{{ number_format($row->ctr * 100, 2) }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Sin datos todavía.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white"><strong>Top queries</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Query</th>
                                <th class="text-end">Clicks</th>
                                <th class="text-end">Imp.</th>
                                <th class="text-end">CTR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topQueries as $row)
                                <tr>
                                    <td style="max-width:280px;">{{ \Illuminate\Support\Str::limit($row->query, 60) }}</td>
                                    <td class="text-end">{{ number_format($row->clicks) }}</td>
                                    <td class="text-end">{{ number_format($row->impressions) }}</td>
                                    <td class="text-end">{{ number_format($row->ctr * 100, 2) }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Sin datos todavía.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white"><strong>Páginas Sin Clicks</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Página</th>
                                    <th class="text-end">Imp.</th>
                                    <th class="text-end">Pos.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($zeroClickPages as $row)
                                    <tr>
                                        <td style="max-width:360px;">
                                            <a href="{{ $row->page }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($row->page, 72) }}</a>
                                        </td>
                                        <td class="text-end">{{ number_format($row->impressions) }}</td>
                                        <td class="text-end">{{ number_format($row->position, 1) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">Sin datos todavía.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white"><strong>Queries Ruidosas</strong></div>
                <div class="card-body">
                    <div class="small text-muted mb-3">Términos que suelen indicar indexación de bajo valor, ruido técnico o páginas débiles.</div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Query</th>
                                    <th class="text-end">Imp.</th>
                                    <th class="text-end">Pos.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($weirdQueries as $row)
                                    <tr>
                                        <td>{{ \Illuminate\Support\Str::limit($row->query, 70) }}</td>
                                        <td class="text-end">{{ number_format($row->impressions) }}</td>
                                        <td class="text-end">{{ number_format($row->position, 1) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">No se detectó ruido claro en este rango.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white"><strong>Rendimiento diario</strong></div>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th class="text-end">Clicks</th>
                        <th class="text-end">Impresiones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyPerformance as $row)
                        <tr>
                            <td>{{ $row->metric_date }}</td>
                            <td class="text-end">{{ number_format($row->clicks) }}</td>
                            <td class="text-end">{{ number_format($row->impressions) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">Sin datos diarios todavía.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
