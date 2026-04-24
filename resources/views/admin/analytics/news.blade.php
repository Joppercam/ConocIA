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
