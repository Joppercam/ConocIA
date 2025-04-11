<!-- resources/views/verificador/stats.blade.php -->
@extends('layouts.app')

@section('title', 'Estadísticas del Verificador Autónomo')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Panel de Estadísticas</h1>
        <p class="text-gray-600">Análisis del rendimiento del Verificador Autónomo</p>
    </div>

    <!-- Tarjetas de estadísticas generales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total de verificaciones</h3>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($totalVerifications) }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Afirmaciones pendientes</h3>
            <p class="text-3xl font-bold text-amber-600">{{ number_format($pendingClaims) }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Tiempo promedio de verificación</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ round($avgProcessingTime) }} horas</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Afirmaciones totales</h3>
            <p class="text-3xl font-bold text-emerald-600">{{ number_format($totalClaims) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
        <!-- Distribución de veredictos -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4">Distribución de veredictos</h3>
            <div class="h-64">
                <canvas id="verdictChart"></canvas>
            </div>
        </div>
        
        <!-- Tendencia de verificaciones -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4">Verificaciones en los últimos 30 días</h3>
            <div class="h-64">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Estadísticas por categoría -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-10">
        <h3 class="text-xl font-semibold mb-4">Estadísticas por categoría</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Categoría
                        </th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Total de verificaciones
                        </th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Verdaderas
                        </th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Parcialmente verdaderas
                        </th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Falsas
                        </th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            No verificables
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoryStats as $category)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                            <div class="text-sm font-medium text-gray-900">{{ $category['name'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200 text-sm text-gray-900">
                            {{ $category['claims_count'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                            <div class="text-sm text-gray-900">{{ $category['verdicts']['true'] ?? 0 }}</div>
                            <div class="text-xs text-gray-500">
                                @if($category['claims_count'] > 0)
                                    {{ round((($category['verdicts']['true'] ?? 0) / $category['claims_count']) * 100) }}%
                                @else
                                    0%
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                            <div class="text-sm text-gray-900">{{ $category['verdicts']['partially_true'] ?? 0 }}</div>
                            <div class="text-xs text-gray-500">
                                @if($category['claims_count'] > 0)
                                    {{ round((($category['verdicts']['partially_true'] ?? 0) / $category['claims_count']) * 100) }}%
                                @else
                                    0%
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                            <div class="text-sm text-gray-900">{{ $category['verdicts']['false'] ?? 0 }}</div>
                            <div class="text-xs text-gray-500">
                                @if($category['claims_count'] > 0)
                                    {{ round((($category['verdicts']['false'] ?? 0) / $category['claims_count']) * 100) }}%
                                @else
                                    0%
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                            <div class="text-sm text-gray-900">{{ $category['verdicts']['unverifiable'] ?? 0 }}</div>
                            <div class="text-xs text-gray-500">
                                @if($category['claims_count'] > 0)
                                    {{ round((($category['verdicts']['unverifiable'] ?? 0) / $category['claims_count']) * 100) }}%
                                @else
                                    0%
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Fuentes más verificadas -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold mb-4">Fuentes más verificadas</h3>
        <div class="h-80">
            <canvas id="sourcesChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para los gráficos
        const verdictLabels = {
            'true': 'Verdadero',
            'partially_true': 'Parcialmente verdadero',
            'false': 'Falso',
            'unverifiable': 'No verificable'
        };
        
        const verdictColors = {
            'true': '#10B981', // Verde
            'partially_true': '#F59E0B', // Ámbar
            'false': '#EF4444', // Rojo
            'unverifiable': '#6B7280' // Gris
        };
        
        // Gráfico de distribución de veredictos
        const verdictData = @json($verdictDistribution);
        const verdictCtx = document.getElementById('verdictChart').getContext('2d');
        new Chart(verdictCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(verdictData).map(key => verdictLabels[key] || key),
                datasets: [{
                    data: Object.values(verdictData),
                    backgroundColor: Object.keys(verdictData).map(key => verdictColors[key] || '#CBD5E1'),
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
        
        // Gráfico de tendencia de verificaciones
        const trendData = @json($verificationTrend);
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendData.map(item => new Date(item.date).toLocaleDateString()),
                datasets: [{
                    label: 'Verificaciones',
                    data: trendData.map(item => item.count),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Gráfico de fuentes más verificadas
        const sourcesData = @json($topSources);
        const sourcesCtx = document.getElementById('sourcesChart').getContext('2d');
        new Chart(sourcesCtx, {
            type: 'bar',
            data: {
                labels: sourcesData.map(item => item.source),
                datasets: [{
                    label: 'Verificaciones',
                    data: sourcesData.map(item => item.count),
                    backgroundColor: '#8B5CF6',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection