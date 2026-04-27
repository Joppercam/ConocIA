@extends('layouts.app')

@section('title', 'Planes ConocIA')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Planes ConocIA</h1>
        <p class="text-muted">Convierte noticias de IA en inteligencia accionable.</p>
    </div>

    <div class="row g-4">
        @foreach([
            'free' => ['name' => 'FREE', 'price' => '$0', 'features' => ['Noticias abiertas', 'Resumen IA limitado', 'Sin alertas personalizadas']],
            'pro' => ['name' => 'PRO', 'price' => 'Próximamente', 'features' => ['Resúmenes IA ilimitados', 'Insights premium', 'Contenido premium', 'Alertas básicas']],
            'business' => ['name' => 'BUSINESS', 'price' => 'Próximamente', 'features' => ['Insights estratégicos avanzados', 'Reportes descargables', 'Inteligencia de tendencias', 'Prioridad IA']],
        ] as $slug => $plan)
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h4 mb-0">{{ $plan['name'] }}</h2>
                            @auth
                                @if(auth()->user()->plan() === $slug)
                                    <span class="badge bg-success">Actual</span>
                                @endif
                            @endauth
                        </div>
                        <div class="display-6 fw-bold mb-3">{{ $plan['price'] }}</div>
                        <ul class="list-unstyled mb-4">
                            @foreach($plan['features'] as $feature)
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>{{ $feature }}</li>
                            @endforeach
                        </ul>
                        <form action="{{ route('billing.select', $slug) }}" method="POST">
                            @csrf
                            <button class="btn {{ $slug === 'free' ? 'btn-outline-primary' : 'btn-primary' }} w-100">
                                {{ $slug === 'free' ? 'Usar FREE' : 'Actualizar plan' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="alert alert-info mt-4">
        Integración Stripe en preparación: esta etapa deja lista la lógica SaaS, gating y métricas. Los cambios de plan son manuales para el MVP comercial.
    </div>
</div>
@endsection
