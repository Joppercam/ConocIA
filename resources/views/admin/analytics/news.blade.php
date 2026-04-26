@extends('admin.layouts.app')

@section('title', 'Analítica de Noticias')

@section('content')
<div class="container-fluid">
    @php
        $maxDailyViews = max(1, (int) $dailyViews->max('total_views'));
        $periodViews = max(1, (int) ($summary['period_views'] ?? 0));
        $periodDelta = $comparisonSummary['delta_percentage'] ?? null;
    @endphp
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Analítica de Noticias</h1>
            <p class="text-muted mb-0">Rendimiento por rango de fechas usando `news_views_stats`.</p>
            <p class="text-muted mb-0 small">Comparando contra {{ \Carbon\Carbon::parse($previousStartDate)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($previousEndDate)->format('d/m/Y') }}.</p>
        </div>
        <a href="{{ route('admin.analytics.news.export', ['start_date' => $startDate, 'end_date' => $endDate, 'preset' => $selectedPreset]) }}" class="btn btn-success">
            <i class="fas fa-file-export me-1"></i> Exportar CSV
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="{{ route('admin.analytics.news', ['preset' => 'today']) }}" class="btn btn-sm {{ $selectedPreset === 'today' ? 'btn-primary' : 'btn-outline-primary' }}">Hoy</a>
                <a href="{{ route('admin.analytics.news', ['preset' => 'last_7_days']) }}" class="btn btn-sm {{ $selectedPreset === 'last_7_days' || is_null($selectedPreset) ? 'btn-primary' : 'btn-outline-primary' }}">7 días</a>
                <a href="{{ route('admin.analytics.news', ['preset' => 'last_30_days']) }}" class="btn btn-sm {{ $selectedPreset === 'last_30_days' ? 'btn-primary' : 'btn-outline-primary' }}">30 días</a>
                <a href="{{ route('admin.analytics.news', ['preset' => 'current_month']) }}" class="btn btn-sm {{ $selectedPreset === 'current_month' ? 'btn-primary' : 'btn-outline-primary' }}">Mes actual</a>
                <a href="{{ route('admin.analytics.news', ['preset' => 'previous_month']) }}" class="btn btn-sm {{ $selectedPreset === 'previous_month' ? 'btn-primary' : 'btn-outline-primary' }}">Mes anterior</a>
            </div>
            <form action="{{ route('admin.analytics.news') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Desde</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Hasta</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Aplicar</button>
                    <a href="{{ route('admin.analytics.news') }}" class="btn btn-outline-secondary">Últimos 7 días</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Visitas del Período</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['period_views']) }}</div>
                    <div class="small mt-2 {{ is_null($periodDelta) ? 'text-muted' : ($periodDelta >= 0 ? 'text-success' : 'text-danger') }}">
                        Período anterior: {{ number_format($comparisonSummary['previous_period_views'] ?? 0) }}
                        @if(!is_null($periodDelta))
                            <span class="ms-2">{{ $periodDelta >= 0 ? '+' : '' }}{{ number_format($periodDelta, 1) }}%</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Noticias con Tráfico</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['active_news']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Promedio Diario</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['average_views_per_day']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold text-primary">Últimas Lecturas del Portal</h6>
                <div class="text-muted small mt-1">Incluye visitas públicas a noticias, columnas, papers, conceptos, análisis, startups, videos y páginas generales.</div>
            </div>
            <span class="badge bg-primary">{{ $latestVisits->count() }} eventos</span>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-6 col-lg-3">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small text-uppercase fw-bold">Eventos</div>
                        <div class="h5 mb-0">{{ number_format($visitSummary['total'] ?? 0) }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small text-uppercase fw-bold">Humanos</div>
                        <div class="h5 mb-0 text-success">{{ number_format($visitSummary['humans'] ?? 0) }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small text-uppercase fw-bold">Bots</div>
                        <div class="h5 mb-0 text-secondary">{{ number_format($visitSummary['bots'] ?? 0) }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small text-uppercase fw-bold">Páginas únicas</div>
                        <div class="h5 mb-0">{{ number_format($visitSummary['unique_pages'] ?? 0) }}</div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.analytics.news') }}" method="GET" class="row g-2 align-items-end mb-3">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Audiencia</label>
                    <select name="visit_audience" class="form-select form-select-sm">
                        <option value="all" {{ $visitAudience === 'all' ? 'selected' : '' }}>Todos</option>
                        <option value="humans" {{ $visitAudience === 'humans' ? 'selected' : '' }}>Solo humanos</option>
                        <option value="bots" {{ $visitAudience === 'bots' ? 'selected' : '' }}>Solo bots</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Sección</label>
                    <select name="visit_type" class="form-select form-select-sm">
                        <option value="all" {{ $visitType === 'all' ? 'selected' : '' }}>Todo el portal</option>
                        <option value="noticia" {{ $visitType === 'noticia' ? 'selected' : '' }}>Noticias</option>
                        <option value="columna" {{ $visitType === 'columna' ? 'selected' : '' }}>Columnas</option>
                        <option value="paper" {{ $visitType === 'paper' ? 'selected' : '' }}>Papers</option>
                        <option value="concepto" {{ $visitType === 'concepto' ? 'selected' : '' }}>Conceptos IA</option>
                        <option value="analisis" {{ $visitType === 'analisis' ? 'selected' : '' }}>Análisis</option>
                        <option value="estado_del_arte" {{ $visitType === 'estado_del_arte' ? 'selected' : '' }}>Estado del Arte</option>
                        <option value="startup" {{ $visitType === 'startup' ? 'selected' : '' }}>Startups</option>
                        <option value="investigacion" {{ $visitType === 'investigacion' ? 'selected' : '' }}>Investigación</option>
                        <option value="video" {{ $visitType === 'video' ? 'selected' : '' }}>Videos</option>
                        <option value="pagina" {{ $visitType === 'pagina' ? 'selected' : '' }}>Páginas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-primary">Filtrar lecturas</button>
                    <a href="{{ route('admin.analytics.news', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                </div>
                <div class="col-md-3">
                    @if(($visitSummary['top_channels'] ?? collect())->count() > 0)
                        <div class="small text-muted mb-1">Canales principales</div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach(($visitSummary['top_channels'] ?? collect())->take(3) as $channel)
                                <span class="badge bg-light text-dark border">{{ $channel->channel }}: {{ number_format($channel->count) }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </form>

            <div class="row g-3 mb-4">
                <div class="col-lg-8">
                    <div class="border rounded h-100">
                        <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold text-primary">Top páginas humanas</div>
                                <div class="text-muted small">Qué contenido sí está recibiendo lectura real en el período.</div>
                            </div>
                            <span class="badge bg-success">{{ $humanTopPages->count() }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Contenido</th>
                                        <th style="width:90px;">Sección</th>
                                        <th style="width:90px;">Lecturas</th>
                                        <th style="width:95px;">Visitantes</th>
                                        <th style="width:110px;">Última</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($humanTopPages as $page)
                                        <tr>
                                            <td>
                                                <a href="{{ $page->url }}" target="_blank" rel="noopener" class="fw-semibold">
                                                    {{ \Illuminate\Support\Str::limit($page->title, 72) }}
                                                </a>
                                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit(parse_url($page->url, PHP_URL_PATH) ?: $page->url, 78) }}</div>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $page->section_label }}</span></td>
                                            <td>{{ number_format($page->human_views) }}</td>
                                            <td>{{ number_format($page->unique_visitors) }}</td>
                                            <td class="text-muted small">{{ \Carbon\Carbon::parse($page->last_viewed_at)->diffForHumans() }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">Aún no hay lecturas humanas en este rango.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="border rounded h-100">
                        <div class="px-3 py-2 border-bottom">
                            <div class="fw-bold text-primary">Canales humanos</div>
                            <div class="text-muted small">De dónde llegan las visitas no bot.</div>
                        </div>
                        <div class="p-3">
                            @forelse($humanTopChannels as $channel)
                                <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                    <span>{{ $channel->channel }}</span>
                                    <span class="badge bg-light text-dark border">{{ number_format($channel->count) }}</span>
                                </div>
                            @empty
                                <p class="text-muted text-center mb-0">Sin canales humanos todavía.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="border rounded mb-4">
                <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-primary">Para empujar u optimizar</div>
                        <div class="text-muted small">Contenido publicado sin lecturas humanas registradas en este período.</div>
                    </div>
                    <span class="badge bg-warning text-dark">{{ $contentWithoutHumanViews->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Contenido</th>
                                <th style="width:120px;">Sección</th>
                                <th style="width:130px;">Publicado</th>
                                <th style="width:120px;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contentWithoutHumanViews as $item)
                                <tr>
                                    <td>
                                        <a href="{{ $item->url }}" target="_blank" rel="noopener" class="fw-semibold">
                                            {{ \Illuminate\Support\Str::limit($item->title, 95) }}
                                        </a>
                                        <div class="text-muted small">{{ \Illuminate\Support\Str::limit(parse_url($item->url, PHP_URL_PATH) ?: $item->url, 100) }}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $item->section_label }}</span></td>
                                    <td class="text-muted small">
                                        {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->diffForHumans() : 'Sin fecha' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">Republicar</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Todo el contenido revisado tiene al menos una lectura humana en este rango.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($latestVisits->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:120px;">Hora</th>
                                <th style="width:130px;">Sección</th>
                                <th>Lectura / Página</th>
                                <th style="width:120px;">Canal</th>
                                <th style="width:150px;">Origen</th>
                                <th style="width:110px;">Visitante</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestVisits as $visit)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($visit->viewed_at)->locale('es')->diffForHumans() }}</div>
                                        <div class="text-muted small">{{ \Carbon\Carbon::parse($visit->viewed_at)->format('d/m H:i') }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $visit->is_bot ? 'bg-secondary' : 'bg-info' }}">
                                            {{ $visit->section_label }}
                                        </span>
                                        @if($visit->is_bot)
                                            <div class="text-muted small mt-1">bot</div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ $visit->url }}" target="_blank" rel="noopener" class="fw-semibold">
                                            {{ \Illuminate\Support\Str::limit($visit->title ?? $visit->url, 95) }}
                                        </a>
                                        <div class="text-muted small">{{ \Illuminate\Support\Str::limit(parse_url($visit->url, PHP_URL_PATH) ?: $visit->url, 100) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $visit->channel_label }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $visit->referrer_label }}</span>
                                    </td>
                                    <td>
                                        <code>{{ $visit->visitor_label }}</code>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted mb-0">Aún no hay eventos de lectura registrados para este rango. El registro empieza a poblarse desde este despliegue.</p>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Frentes Estratégicos</h6>
            <span class="badge bg-primary">{{ $strategicSections->count() }} líneas</span>
        </div>
        <div class="card-body">
            @if($strategicSections->count() > 0)
                <p class="text-muted small mb-3">Lectura editorial para ver qué líneas están aportando más al posicionamiento y cuáles conviene reforzar.</p>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Línea</th>
                                <th>Noticias</th>
                                <th>Visitas</th>
                                <th>Período anterior</th>
                                <th>Variación</th>
                                <th>Participación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($strategicSections as $section)
                                <tr>
                                    <td class="fw-semibold">{{ $section->section }}</td>
                                    <td>{{ number_format($section->news_count) }}</td>
                                    <td>{{ number_format($section->period_views) }}</td>
                                    <td>{{ number_format($section->previous_period_views) }}</td>
                                    <td class="{{ is_null($section->delta_percentage) ? 'text-muted' : ($section->delta_percentage >= 0 ? 'text-success' : 'text-danger') }}">
                                        @if(!is_null($section->delta_percentage))
                                            {{ $section->delta_percentage >= 0 ? '+' : '' }}{{ number_format($section->delta_percentage, 1) }}%
                                        @else
                                            Sin base
                                        @endif
                                    </td>
                                    <td>{{ number_format($section->share * 100, 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center mb-0">No hay suficiente información para agrupar líneas estratégicas todavía.</p>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Visitas por Día</h6>
                </div>
                <div class="card-body">
                    @if($dailyViews->count() > 0)
                        <div class="mb-4">
                            @foreach($dailyViews as $day)
                                <div class="analytics-bar-row">
                                    <div class="analytics-bar-label">{{ \Carbon\Carbon::parse($day->view_date)->format('d/m') }}</div>
                                    <div class="analytics-bar-track">
                                        <div class="analytics-bar-fill" style="width: {{ max(4, round(($day->total_views / $maxDailyViews) * 100)) }}%;"></div>
                                    </div>
                                    <div class="analytics-bar-value">{{ number_format($day->total_views) }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Visitas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dailyViews as $day)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($day->view_date)->format('d/m/Y') }}</td>
                                            <td>{{ number_format($day->total_views) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center mb-0">No hay datos para el rango seleccionado.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Categorías</h6>
                </div>
                <div class="card-body">
                    @if($topCategories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Categoría</th>
                                        <th>Visitas</th>
                                        <th>Período anterior</th>
                                        <th>Variación</th>
                                        <th>Participación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topCategories as $category)
                                        <tr>
                                            <td>{{ $category->category_name }}</td>
                                            <td>{{ number_format($category->period_views) }}</td>
                                            <td>{{ number_format($category->previous_period_views) }}</td>
                                            <td class="{{ $category->previous_period_views > 0 ? (($category->period_views - $category->previous_period_views) >= 0 ? 'text-success' : 'text-danger') : 'text-muted' }}">
                                                @if($category->previous_period_views > 0)
                                                    {{ (($category->period_views - $category->previous_period_views) >= 0 ? '+' : '') . number_format((($category->period_views - $category->previous_period_views) / $category->previous_period_views) * 100, 1) }}%
                                                @else
                                                    Sin base
                                                @endif
                                            </td>
                                            <td>{{ number_format(($category->period_views / $periodViews) * 100, 1) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center mb-0">No hay categorías con datos en este rango.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Autores</h6>
                </div>
                <div class="card-body">
                    @if($topAuthors->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Autor</th>
                                        <th>Noticias</th>
                                        <th>Visitas</th>
                                        <th>Período anterior</th>
                                        <th>Variación</th>
                                        <th>Participación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topAuthors as $author)
                                        <tr>
                                            <td>{{ $author->author_name }}</td>
                                            <td>{{ number_format($author->news_count) }}</td>
                                            <td>{{ number_format($author->period_views) }}</td>
                                            <td>{{ number_format($author->previous_period_views) }}</td>
                                            <td class="{{ $author->previous_period_views > 0 ? (($author->period_views - $author->previous_period_views) >= 0 ? 'text-success' : 'text-danger') : 'text-muted' }}">
                                                @if($author->previous_period_views > 0)
                                                    {{ (($author->period_views - $author->previous_period_views) >= 0 ? '+' : '') . number_format((($author->period_views - $author->previous_period_views) / $author->previous_period_views) * 100, 1) }}%
                                                @else
                                                    Sin base
                                                @endif
                                            </td>
                                            <td>{{ number_format(($author->period_views / $periodViews) * 100, 1) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center mb-0">No hay autores con datos en este rango.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Top Noticias del Período</h6>
        </div>
        <div class="card-body">
            @if($topNews->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Visitas del período</th>
                                <th>Período anterior</th>
                                <th>Variación</th>
                                <th>Participación</th>
                                <th>Visitas totales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topNews as $news)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.news.edit', $news->id) }}">
                                            {{ $news->title }}
                                        </a>
                                    </td>
                                    <td>{{ $news->category_name }}</td>
                                    <td>{{ number_format($news->period_views) }}</td>
                                    <td>{{ number_format($news->previous_period_views) }}</td>
                                    <td class="{{ $news->previous_period_views > 0 ? (($news->period_views - $news->previous_period_views) >= 0 ? 'text-success' : 'text-danger') : 'text-muted' }}">
                                        @if($news->previous_period_views > 0)
                                            {{ (($news->period_views - $news->previous_period_views) >= 0 ? '+' : '') . number_format((($news->period_views - $news->previous_period_views) / $news->previous_period_views) * 100, 1) }}%
                                        @else
                                            Sin base
                                        @endif
                                    </td>
                                    <td>{{ number_format(($news->period_views / $periodViews) * 100, 1) }}%</td>
                                    <td>{{ number_format($news->total_views) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center mb-0">No hay noticias con datos para este rango.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .analytics-bar-row {
        display: grid;
        grid-template-columns: 56px 1fr 64px;
        gap: 12px;
        align-items: center;
        margin-bottom: 10px;
    }

    .analytics-bar-label,
    .analytics-bar-value {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .analytics-bar-track {
        height: 12px;
        border-radius: 999px;
        background: #e9ecef;
        overflow: hidden;
    }

    .analytics-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #0d6efd 0%, #20c997 100%);
    }
</style>
@endpush
