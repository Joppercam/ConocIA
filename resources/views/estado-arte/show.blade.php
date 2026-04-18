@extends('layouts.app')

@section('reading_progress', true)

@section('title', $digest->title . ' — Estado del Arte | ConocIA')

@section('content')
<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-8">

            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('estado-arte.index') }}" class="text-muted">Estado del Arte</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('estado-arte.subfield', $digest->subfield) }}" class="text-muted">{{ $digest->subfield_label }}</a></li>
                    <li class="breadcrumb-item active" style="color:#334155;">{{ $digest->week_start?->format('d/m/Y') }}</li>
                </ol>
            </nav>

            <div class="rounded-3 mb-4 p-3 px-4 d-flex align-items-center justify-content-between" style="background:#f0f9ff;border:1px solid #bae6fd;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge" style="background:var(--primary-color);color:#fff;font-size:.75rem;">{{ $digest->subfield_label }}</span>
                    <span style="color:#0369a1;font-size:.84rem;">{{ $digest->period_label }}</span>
                </div>
                <div class="d-flex gap-3" style="font-size:.8rem;color:#64748b;white-space:nowrap;">
                    <span><i class="fas fa-clock me-1"></i>{{ $digest->reading_time ?? 5 }} min</span>
                    <span><i class="fas fa-eye me-1"></i>{{ number_format($digest->views) }}</span>
                </div>
            </div>

            <h1 class="fw-bold mb-3" style="color:#0f172a;font-size:1.9rem;line-height:1.25;">{{ $digest->title }}</h1>

            @if($digest->excerpt)
            <p style="color:#475569;font-size:1.02rem;line-height:1.7;margin-bottom:2rem;">{{ $digest->excerpt }}</p>
            @endif

            @if(!empty($digest->key_developments))
            <div class="profundiza-card p-4 mb-5">
                <p class="profundiza-section-label"><i class="fas fa-bolt me-2" style="color:var(--primary-color);"></i>Desarrollos clave esta semana</p>
                <ul class="mb-0" style="list-style:none;padding:0;">
                    @foreach($digest->key_developments as $dev)
                    <li class="d-flex gap-2 mb-2">
                        <i class="fas fa-angle-right mt-1 flex-shrink-0" style="color:var(--primary-color);font-size:.8rem;"></i>
                        <span style="color:#334155;font-size:.9rem;line-height:1.5;">{{ $dev }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="article-content mb-5">
                {!! $digest->content !!}
            </div>

            @if($sourceNews->isNotEmpty())
            <div class="profundiza-card p-4 mb-5">
                <h5 class="fw-semibold mb-3" style="color:#0f172a;font-size:.95rem;">
                    <i class="fas fa-newspaper me-2" style="color:var(--primary-color);"></i>Noticias que nutrieron este digest
                </h5>
                <ul class="mb-0" style="list-style:none;padding:0;">
                    @foreach($sourceNews as $news)
                    <li class="mb-2">
                        <a href="{{ route('news.show', $news->slug) }}" class="d-flex gap-2 align-items-start text-decoration-none">
                            <i class="fas fa-angle-right mt-1 flex-shrink-0" style="color:var(--primary-color);font-size:.8rem;"></i>
                            <span style="color:#334155;font-size:.88rem;">{{ $news->title }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

        </div>

        <div class="col-lg-4">
            <div class="sticky-top" style="top:80px;">

                @if($previousEditions->isNotEmpty())
                <div class="profundiza-card p-4 mb-4">
                    <p class="profundiza-section-label">Ediciones anteriores — {{ $digest->subfield_label }}</p>
                    @foreach($previousEditions as $prev)
                    <a href="{{ route('estado-arte.show', $prev->slug) }}" class="d-block text-decoration-none mb-3 pb-3" style="border-bottom:1px solid #f1f5f9;">
                        <div style="color:#1e293b;font-size:.84rem;font-weight:500;">{{ $prev->period_label }}</div>
                        @if(!empty($prev->key_developments))
                        <div style="color:#94a3b8;font-size:.74rem;margin-top:3px;">{{ Str::limit($prev->key_developments[0] ?? '', 60) }}</div>
                        @endif
                    </a>
                    @endforeach
                </div>
                @endif

                <a href="{{ route('estado-arte.subfield', $digest->subfield) }}" class="btn w-100 btn-outline-secondary btn-sm mb-2">
                    Ver todas las ediciones de {{ $digest->subfield_label }}
                </a>
                <a href="{{ route('estado-arte.index') }}" class="btn w-100 btn-sm mb-2" style="background:var(--primary-color);color:#fff;">
                    <i class="fas fa-th-large me-2"></i>Todos los campos
                </a>
                <a href="{{ route('analisis.index') }}" class="btn w-100 btn-outline-secondary btn-sm">
                    <i class="fas fa-microscope me-2"></i>Análisis de Fondo
                </a>
            </div>
        </div>
    </div>
</div>
@php
$breadcrumbs = [
    ['name' => 'Inicio', 'url' => url('/')],
    ['name' => 'Estado del Arte', 'url' => route('estado-arte.index')],
    ['name' => $digest->title],
];
@endphp
@include('partials.schema-breadcrumb', ['crumbs' => $breadcrumbs])
@include('partials.schema-article', ['item' => $digest, 'routeName' => 'estado-arte.show', 'type' => 'TechArticle', 'section' => 'Estado del Arte'])
@endsection
