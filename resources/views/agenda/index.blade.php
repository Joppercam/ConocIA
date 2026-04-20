@extends('layouts.app')

@section('title', 'Agenda de Eventos IA 2025 — Conferencias y Webinars | ConocIA')

@section('meta')
    @include('partials.seo-meta', [
        'metaTitle'       => 'Agenda de Eventos de IA 2025 | ConocIA',
        'metaDescription' => 'Calendario de conferencias, webinars y deadlines de inteligencia artificial en 2025. ICML, NeurIPS, ICLR, CVPR y más eventos del ecosistema IA en español.',
        'metaKeywords'    => 'conferencias inteligencia artificial 2025, NeurIPS, ICML, ICLR, eventos IA, webinars machine learning',
        'metaUrl'         => route('agenda.index'),
        'metaType'        => 'website',
    ])
@endsection

@section('content')

{{-- Hero --}}
<div style="background:var(--dark-bg);border-bottom:1px solid #2a2a2a;" class="py-5">
    <div class="container">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div style="width:4px;height:40px;background:var(--primary-color);border-radius:2px;flex-shrink:0;"></div>
            <div>
                <h1 class="mb-0 text-white fw-bold" style="font-size:2rem;">Agenda IA</h1>
                <p class="mb-0 mt-1" style="color:#aaa;font-size:.9rem;">
                    {{ $upcoming->count() }} eventos próximos · Conferencias, webinars y deadlines
                </p>
            </div>
        </div>
        <p class="text-white-50 mb-0" style="max-width:600px;font-size:.92rem;">
            El calendario de referencia del ecosistema de inteligencia artificial en español.
        </p>
    </div>
</div>

<div class="container py-5">

    {{-- Evento destacado --}}
    @if($featured)
    <div class="mb-5">
        <div class="p-4 rounded-3 position-relative overflow-hidden" style="background:linear-gradient(135deg,#0a1020,#16213e);border:1px solid rgba(56,182,255,.2);">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background:rgba(56,182,255,.2);color:var(--primary-color);font-size:.7rem;font-weight:700;letter-spacing:.06em;">DESTACADO</span>
                        <span class="badge" style="background:{{ $featured->getTypeColor() }}22;color:{{ $featured->getTypeColor() }};font-size:.7rem;">{{ $featured->getTypeLabel() }}</span>
                        @if($featured->is_free || $featured->is_online)
                        <span class="badge" style="background:rgba(0,200,150,.15);color:#00c896;font-size:.7rem;">
                            {{ $featured->is_free ? 'Gratuito' : '' }}{{ $featured->is_free && $featured->is_online ? ' · ' : '' }}{{ $featured->is_online ? 'Online' : '' }}
                        </span>
                        @endif
                    </div>
                    <h2 class="text-white fw-bold mb-2" style="font-size:1.4rem;">{{ $featured->title }}</h2>
                    @if($featured->description)
                    <p class="mb-3" style="color:#94a3b8;font-size:.88rem;line-height:1.7;">{{ Str::limit($featured->description, 200) }}</p>
                    @endif
                    <div class="d-flex flex-wrap gap-3" style="font-size:.83rem;color:#94a3b8;">
                        <span><i class="fas fa-calendar me-1" style="color:var(--primary-color);"></i>
                            {{ $featured->start_date->day }} de {{ $featured->start_date->locale('es')->monthName }}
                            @if($featured->end_date) — {{ $featured->end_date->day }} de {{ $featured->end_date->locale('es')->monthName }}, {{ $featured->end_date->year }}
                            @else, {{ $featured->start_date->year }} @endif
                        </span>
                        @if($featured->location)
                        <span><i class="fas fa-map-marker-alt me-1" style="color:var(--primary-color);"></i>{{ $featured->location }}</span>
                        @endif
                        @if($featured->organizer)
                        <span><i class="fas fa-building me-1" style="color:var(--primary-color);"></i>{{ $featured->organizer }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    @php $days = $featured->days_until; @endphp
                    @if($days >= 0)
                    <div class="mb-3">
                        <div class="fw-bold text-white" style="font-size:2.5rem;line-height:1;">{{ $days }}</div>
                        <div style="color:#94a3b8;font-size:.82rem;">días restantes</div>
                    </div>
                    @endif
                    @if($featured->url && $featured->url !== '#')
                    <a href="{{ $featured->url }}" target="_blank" rel="noopener"
                       class="btn btn-primary px-4" style="font-size:.85rem;">
                        <i class="fas fa-external-link-alt me-1"></i>Más información
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Próximos eventos --}}
    <div class="mb-2 d-flex align-items-center gap-3">
        <div style="width:4px;height:24px;background:var(--primary-color);border-radius:2px;flex-shrink:0;"></div>
        <h2 class="mb-0 fw-bold" style="font-size:1.2rem;color:#0f172a;">Próximos eventos</h2>
    </div>

    <div class="row g-3 mt-1 mb-5">
        @forelse($upcoming as $event)
        @if(!$featured || $event->id !== $featured->id)
        <div class="col-lg-6">
            <div class="card border-0 h-100" style="border-radius:.875rem;box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge px-2 py-1" style="font-size:.68rem;font-weight:600;background:{{ $event->getTypeColor() }}18;color:{{ $event->getTypeColor() }};">
                                {{ $event->getTypeLabel() }}
                            </span>
                            @if($event->is_online)
                            <span class="badge px-2 py-1" style="font-size:.68rem;background:#f0f9ff;color:#0369a1;">Online</span>
                            @endif
                            @if($event->is_free)
                            <span class="badge px-2 py-1" style="font-size:.68rem;background:rgba(0,200,150,.1);color:#00875a;">Gratuito</span>
                            @endif
                        </div>
                        @php $days = $event->days_until; @endphp
                        @if($days >= 0 && $days <= 30)
                        <span style="font-size:.72rem;color:#ff4757;font-weight:600;">
                            {{ $days === 0 ? '¡Hoy!' : "En {$days} días" }}
                        </span>
                        @endif
                    </div>

                    <h3 class="fw-bold mb-2" style="font-size:.98rem;color:#0f172a;line-height:1.4;">{{ $event->title }}</h3>

                    @if($event->description)
                    <p class="mb-3" style="font-size:.82rem;color:#64748b;line-height:1.6;">{{ Str::limit($event->description, 120) }}</p>
                    @endif

                    <div class="d-flex flex-wrap gap-3 mb-3" style="font-size:.78rem;color:#94a3b8;">
                        <span>
                            <i class="fas fa-calendar me-1" style="color:var(--primary-color);font-size:.7rem;"></i>
                            {{ $event->start_date->locale('es')->isoFormat('D MMM YYYY') }}
                            @if($event->end_date) — {{ $event->end_date->locale('es')->isoFormat('D MMM YYYY') }}@endif
                        </span>
                        @if($event->location)
                        <span><i class="fas fa-map-marker-alt me-1" style="color:var(--primary-color);font-size:.7rem;"></i>{{ $event->location }}</span>
                        @endif
                    </div>

                    @if($event->url && $event->url !== '#')
                    <a href="{{ $event->url }}" target="_blank" rel="noopener"
                       class="btn btn-sm btn-outline-primary w-100" style="font-size:.75rem;border-radius:.5rem;">
                        <i class="fas fa-external-link-alt me-1"></i>Ver detalles
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endif
        @empty
        <div class="col-12">
            <p class="text-muted text-center py-4">No hay eventos próximos registrados.</p>
        </div>
        @endforelse
    </div>

    {{-- Eventos pasados --}}
    @if($past->count())
    <div class="mb-3 d-flex align-items-center gap-3">
        <div style="width:4px;height:24px;background:#64748b;border-radius:2px;flex-shrink:0;"></div>
        <h2 class="mb-0 fw-bold" style="font-size:1.1rem;color:#64748b;">Eventos recientes</h2>
    </div>
    <div class="row g-2">
        @foreach($past as $event)
        <div class="col-lg-4 col-md-6">
            <div class="p-3 rounded-2 d-flex gap-3 align-items-start" style="background:#f8fafc;opacity:.75;">
                <div class="text-center flex-shrink-0" style="min-width:44px;">
                    <div class="fw-bold" style="font-size:1.1rem;color:#94a3b8;line-height:1;">{{ $event->start_date->format('d') }}</div>
                    <div style="font-size:.65rem;color:#94a3b8;text-transform:uppercase;">{{ $event->start_date->locale('es')->isoFormat('MMM') }}</div>
                </div>
                <div>
                    <div class="fw-semibold" style="font-size:.82rem;color:#475569;line-height:1.4;">{{ $event->title }}</div>
                    @if($event->location)
                    <div style="font-size:.72rem;color:#94a3b8;">{{ $event->location }}</div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- CTA sugerir evento --}}
    <div class="mt-5 p-4 rounded-3 text-center" style="background:#f0f9ff;border:1px solid rgba(56,182,255,.2);">
        <i class="fas fa-calendar-plus mb-2" style="font-size:1.5rem;color:var(--primary-color);"></i>
        <h3 class="fw-bold mb-1" style="font-size:1rem;color:#0f172a;">¿Conocés un evento que debería estar aquí?</h3>
        <p class="text-muted mb-3" style="font-size:.85rem;">Escribinos y lo agregamos a la agenda.</p>
        <a href="{{ route('contact') }}" class="btn btn-primary btn-sm px-4" style="font-size:.82rem;">
            <i class="fas fa-envelope me-1"></i>Sugerir evento
        </a>
    </div>

</div>

@endsection
