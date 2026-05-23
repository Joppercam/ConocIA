@extends('layouts.app')

@section('title', $page['title'] . ' — Próximamente en ConocIA')
@section('meta_description', $page['description'])

@section('content')
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);min-height:70vh;display:flex;align-items:center;" class="py-5">
    <div class="container py-4 text-center">

        <div style="width:80px;height:80px;background:rgba(56,182,255,.15);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;border:1px solid rgba(56,182,255,.3);">
            <i class="fas {{ $page['icon'] }}" style="color:var(--primary-color);font-size:2rem;"></i>
        </div>

        <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.8rem;letter-spacing:.06em;">
            <i class="fas fa-clock me-1"></i>PRÓXIMAMENTE
        </span>

        <h1 class="fw-bold text-white mb-2" style="font-size:2.4rem;line-height:1.15;">
            {{ $page['title'] }}
        </h1>
        <p class="mb-3" style="color:var(--primary-color);font-size:1.05rem;font-weight:500;">
            {{ $page['subtitle'] }}
        </p>
        <p style="color:#94a3b8;font-size:1rem;line-height:1.8;max-width:560px;margin:0 auto 2.5rem;">
            {{ $page['description'] }}
        </p>

        {{-- Formulario de notificación --}}
        <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:1rem;padding:2rem;max-width:480px;margin:0 auto 2rem;">
            <p style="color:#94a3b8;font-size:.9rem;margin-bottom:1rem;">
                <i class="fas fa-bell me-2" style="color:var(--primary-color);"></i>
                Notifícame cuando esté disponible
            </p>
            <form action="{{ route('newsletter.subscribe') }}" method="POST">
                @csrf
                <input type="hidden" name="source" value="coming-soon-{{ $slug }}">
                <div class="d-flex gap-2">
                    <input type="email" name="email" placeholder="tu@correo.com" required
                           class="form-control"
                           style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.2);color:#fff;font-size:.88rem;">
                    <button type="submit" class="btn btn-primary flex-shrink-0" style="font-size:.88rem;white-space:nowrap;">
                        Avisarme
                    </button>
                </div>
                <div style="color:#475569;font-size:.72rem;margin-top:.5rem;">
                    <i class="fas fa-lock me-1"></i>Sin spam · Cancelá cuando quieras
                </div>
            </form>
        </div>

        <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm px-4">
            <i class="fas fa-arrow-left me-2"></i>Volver al inicio
        </a>

    </div>
</section>

{{-- Mientras tanto, explora esto --}}
<div class="container py-5">
    <p class="profundiza-section-label text-center">Mientras tanto, explora</p>
    <div class="row g-3 justify-content-center">
        <div class="col-md-3 col-6">
            <a href="{{ route('conceptos.index') }}" class="profundiza-card d-block text-center p-3 text-decoration-none">
                <i class="fas fa-book-open mb-2 d-block" style="color:var(--primary-color);font-size:1.4rem;"></i>
                <div class="fw-bold" style="color:#0f172a;font-size:.88rem;">Conceptos IA</div>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('papers.index') }}" class="profundiza-card d-block text-center p-3 text-decoration-none">
                <i class="fas fa-file-alt mb-2 d-block" style="color:var(--primary-color);font-size:1.4rem;"></i>
                <div class="fw-bold" style="color:#0f172a;font-size:.88rem;">ConocIA Papers</div>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('estado-arte.index') }}" class="profundiza-card d-block text-center p-3 text-decoration-none">
                <i class="fas fa-chart-line mb-2 d-block" style="color:var(--primary-color);font-size:1.4rem;"></i>
                <div class="fw-bold" style="color:#0f172a;font-size:.88rem;">Estado del Arte</div>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('radio.index') }}" class="profundiza-card d-block text-center p-3 text-decoration-none">
                <i class="fas fa-podcast mb-2 d-block" style="color:var(--primary-color);font-size:1.4rem;"></i>
                <div class="fw-bold" style="color:#0f172a;font-size:.88rem;">ConocIA Radio</div>
            </a>
        </div>
    </div>
</div>
@endsection
