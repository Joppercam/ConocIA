@extends('admin.layouts.app')

@section('title', 'Estadísticas de TikTok')

@section('styles')
<style>
    .stats-card {
        transition: all 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Cabecera -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estadísticas de TikTok</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.tiktok.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p>
                        Este panel muestra las métricas y estadísticas de rendimiento de los guiones de TikTok publicados.
                        Los datos se actualizan cada hora y cubren los últimos 30 días de actividad.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-info">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ number_format($dailyStats->sum('total_views')) }}</h3>
                            <p class="mb-0">Vistas Totales</p>
                        </div>
                        <div>
                            <i class="fas fa-eye fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-success">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ number_format($dailyStats->sum('total_likes')) }}</h3>
                            <p class="mb-0">Likes Totales</p>
                        </div>
                        <div>
                            <i class="fas fa-heart fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-warning">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ number_format($dailyStats->sum('total_shares')) }}</h3>
                            <p class="mb-0">Compartidos</p>
                        </div>
                        <div>
                            <i class="fas fa-share-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-danger">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ number_format($dailyStats->sum('total_clicks')) }}</h3>
                            <p class="mb-0">Clics al Portal</p>
                        </div>
                        <div>
                            <i class="fas fa-external-link-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Tendencias -->
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tendencias de los Últimos 30 Días</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rendimiento por Categoría</h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Videos -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top 10 Videos por Engagement</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Vistas</th>
                                <th>Likes</th>
                                <th>Comentarios</th>
                                <th>Compartidos</th>
                                <th>Engagement</th>
                                <th>CTR</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topVideos as $video)
                            <tr>
                                <td>{{ Str::limit($video->title, 50) }}</td>
                                <td>{{ number_format($video->views) }}</td>
                                <td>{{ number_format($video->likes) }}</td>
                                <td>{{ number_format($video->comments) }}</td>
                                <td>{{ number_format($video->shares) }}</td>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ number_format(($video->engagement / $video->views) * 100, 2) }}%
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ number_format(($video->clicks_to_portal / $video->views) * 100, 2) }}%
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.tiktok.edit', $video->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Adicionales -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Engagement vs. CTR</h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="engagementVsCtrChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribución de Interacciones</h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="interactionsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas por Categoría -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estadísticas por Categoría</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th>Vistas</th>
                                <th>Likes</th>
                                <th>Comentarios</th>
                                <th>Compartidos</th>
                                <th>Clics</th>
                                <th>Engagement</th>
                                <th>CTR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoryStats as $stat)
                            <tr>
                                <td>{{ $stat->category }}</td>
                                <td>{{ number_format($stat->total_views) }}</td>
                                <td>{{ number_format($stat->total_likes) }}</td>
                                <td>{{ number_format($stat->total_comments) }}</td>
                                <td>{{ number_format($stat->total_shares) }}</td>
                                <td>{{ number_format($stat->total_clicks) }}</td>
                                <td>
                                    @if($stat->total_views > 0)
                                        <span class="badge badge-primary">
                                            {{ number_format((($stat->total_likes + $stat->total_comments + $stat->total_shares) / $stat->total_views) * 100, 2) }}%
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">0%</span>
                                    @endif
                                </td>
                                <td>
                                    @if($stat->total_views > 0)
                                        <span class="badge badge-info">
                                            {{ number_format(($stat->total_clicks / $stat->total_views) * 100, 2) }}%
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">0%</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function() {
        // Convertir datos para las gráficas
        const dailyStatsData = @json($dailyStats);
        const categoryStatsData = @json($categoryStats);
        const topVideosData = @json($topVideos);
        
        // Configurar colores
        const colors = {
            views: 'rgba(52, 152, 219, 0.7)',
            likes: 'rgba(46, 204, 113, 0.7)',
            comments: 'rgba(155, 89, 182, 0.7)',
            shares: 'rgba(241, 196, 15, 0.7)',
            clicks: 'rgba(231, 76, 60, 0.7)',
        };
        
        // Gráfico de tendencias
        if (dailyStatsData.length > 0) {
            const trendsCtx = document.getElementById('trendsChart').getContext('2d');
            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: dailyStatsData.map(item => moment(item.date).format('DD/MM')),
                    datasets: [
                        {
                            label: 'Vistas',
                            data: dailyStatsData.map(item => item.total_views),
                            borderColor: colors.views,
                            backgroundColor: 'transparent',
                            tension: 0.3
                        },
                        {
                            label: 'Likes',
                            data: dailyStatsData.map(item => item.total_likes),
                            borderColor: colors.likes,
                            backgroundColor: 'transparent',
                            tension: 0.3
                        },
                        {
                            label: 'Compartidos',
                            data: dailyStatsData.map(item => item.total_shares),
                            borderColor: colors.shares,
                            backgroundColor: 'transparent',
                            tension: 0.3
                        },
                        {
                            label: 'Clics',
                            data: dailyStatsData.map(item => item.total_clicks),
                            borderColor: colors.clicks,
                            backgroundColor: 'transparent',
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Gráfico por categoría
        if (categoryStatsData.length > 0) {
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryStatsData.map(item => item.category),
                    datasets: [{
                        data: categoryStatsData.map(item => item.total_views),
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.7)',
                            'rgba(46, 204, 113, 0.7)',
                            'rgba(155, 89, 182, 0.7)',
                            'rgba(241, 196, 15, 0.7)',
                            'rgba(231, 76, 60, 0.7)',
                            'rgba(52, 73, 94, 0.7)',
                            'rgba(26, 188, 156, 0.7)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        }
                    }
                }
            });
        }
        
        // Gráfico de Engagement vs CTR
        if (categoryStatsData.length > 0) {
            const engagementData = categoryStatsData.map(item => {
                if (item.total_views > 0) {
                    return ((item.total_likes + item.total_comments + item.total_shares) / item.total_views) * 100;
                }
                return 0;
            });
            
            const ctrData = categoryStatsData.map(item => {
                if (item.total_views > 0) {
                    return (item.total_clicks / item.total_views) * 100;
                }
                return 0;
            });
            
            const engagementCtx = document.getElementById('engagementVsCtrChart').getContext('2d');
            new Chart(engagementCtx, {
                type: 'bar',
                data: {
                    labels: categoryStatsData.map(item => item.category),
                    datasets: [
                        {
                            label: 'Engagement (%)',
                            data: engagementData,
                            backgroundColor: 'rgba(52, 152, 219, 0.7)',
                            borderColor: 'rgba(52, 152, 219, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'CTR (%)',
                            data: ctrData,
                            backgroundColor: 'rgba(231, 76, 60, 0.7)',
                            borderColor: 'rgba(231, 76, 60, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Porcentaje (%)'
                            }
                        }
                    }
                }
            });
        }
        
        // Gráfico de distribución de interacciones
        const totalLikes = dailyStatsData.reduce((sum, item) => sum + parseFloat(item.total_likes || 0), 0);
        const totalComments = dailyStatsData.reduce((sum, item) => sum + parseFloat(item.total_comments || 0), 0);
        const totalShares = dailyStatsData.reduce((sum, item) => sum + parseFloat(item.total_shares || 0), 0);
        
        const interactionsCtx = document.getElementById('interactionsChart').getContext('2d');
        new Chart(interactionsCtx, {
            type: 'pie',
            data: {
                labels: ['Likes', 'Comentarios', 'Compartidos'],
                datasets: [{
                    data: [totalLikes, totalComments, totalShares],
                    backgroundColor: [
                        colors.likes,
                        colors.comments,
                        colors.shares
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
    });
</script>
@endpush