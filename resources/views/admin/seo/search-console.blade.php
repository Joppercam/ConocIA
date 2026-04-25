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
                <div class="fw-semibold">Última sincronización</div>
                <div class="text-muted small">{{ $summary['last_synced_at'] ? \Illuminate\Support\Carbon::parse($summary['last_synced_at'])->diffForHumans() : 'Aún sin datos sincronizados' }}</div>
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
