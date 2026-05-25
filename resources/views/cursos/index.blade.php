@extends('layouts.app')

@section('title', 'Cursos especializados de IA — Solo para registrados | ConocIA')
@section('meta_description', 'Cursos gratuitos de IA para profesionales: derecho, educación, periodismo, RRHH, salud y pymes. Contenido especializado, sin jerga técnica. Regístrate gratis.')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-3">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">CURSOS ESPECIALIZADOS</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.2rem;line-height:1.15;">IA aplicada a tu profesión</h1>
                <p style="color:#94a3b8;font-size:1rem;line-height:1.7;max-width:560px;">
                    Más allá de la alfabetización general: cursos diseñados para profesionales que necesitan entender cómo la IA transforma su campo específico, qué riesgos implica y cómo usarla con criterio.
                </p>
                <div class="d-flex align-items-center gap-3 mt-3 flex-wrap">
                    <span style="color:#64748b;font-size:.85rem;"><i class="fas fa-lock-open me-1" style="color:#00c896;"></i>Registro gratuito</span>
                    <span style="color:#64748b;font-size:.85rem;"><i class="fas fa-graduation-cap me-1" style="color:#38b6ff;"></i>{{ count($courses) }} cursos disponibles</span>
                    <span style="color:#64748b;font-size:.85rem;"><i class="fas fa-book-open me-1" style="color:#a78bfa;"></i>5 módulos · 20 lecciones por curso</span>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <div class="row g-2">
                    @foreach($courses as $c)
                    <div class="col-6">
                        <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:.75rem;padding:.875rem;display:flex;align-items:center;gap:.6rem;">
                            <div style="width:30px;height:30px;background:{{ $c['badge_color'] }}22;border-radius:.4rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas {{ $c['icon'] }}" style="color:{{ $c['badge_color'] }};font-size:.8rem;"></i>
                            </div>
                            <span style="color:#cbd5e1;font-size:.78rem;line-height:1.3;">{{ $c['badge'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">

    {{-- CTA registro (si no está logueado) --}}
    @guest
    <div class="mb-5 p-4 d-flex align-items-center justify-content-between gap-3 flex-wrap"
         style="background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%);border:1px solid #bae6fd;border-radius:.75rem;">
        <div>
            <p class="fw-bold mb-1" style="color:#0369a1;font-size:.97rem;">Accede a todos los cursos — es gratis</p>
            <p class="mb-0" style="color:#0369a1;font-size:.85rem;">Crea tu cuenta y desbloquea el contenido especializado de inmediato.</p>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            <a href="{{ route('register') }}" class="btn btn-primary" style="font-size:.88rem;">Registrarme gratis</a>
            <a href="{{ route('login') }}" class="btn btn-outline-secondary" style="font-size:.88rem;">Ya tengo cuenta</a>
        </div>
    </div>
    @endguest

    {{-- Listado de cursos --}}
    <div class="d-flex flex-column gap-4">
        @foreach($courses as $course)
        <div class="curso-row" style="border:1px solid #e2e8f0;border-radius:.875rem;overflow:hidden;">

            {{-- Franja de color lateral --}}
            <div style="display:flex;">
                <div style="width:5px;background:{{ $course['badge_color'] }};flex-shrink:0;"></div>
                <div class="flex-grow-1 p-4 p-md-5">
                    <div class="row g-4 align-items-start">

                        {{-- Info principal --}}
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="badge" style="background:{{ $course['badge_color'] }}18;color:{{ $course['badge_color'] }};border:1px solid {{ $course['badge_color'] }}33;font-size:.72rem;">
                                    {{ $course['badge'] }}
                                </span>
                                @if($course['status'] === 'disponible')
                                <span class="badge" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;font-size:.72rem;">Disponible</span>
                                @else
                                <span class="badge" style="background:#fef3c7;color:#b45309;border:1px solid #fde68a;font-size:.72rem;">Próximamente</span>
                                @endif
                            </div>

                            <h2 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin-bottom:.4rem;line-height:1.25;">
                                {{ $course['title'] }}
                            </h2>
                            <p style="color:#64748b;font-size:.85rem;margin-bottom:.875rem;">{{ $course['subtitle'] }}</p>
                            <p style="color:#475569;font-size:.9rem;line-height:1.75;margin-bottom:1rem;">
                                {{ Str::limit($course['description'], 220) }}
                            </p>

                            <div class="d-flex align-items-center gap-3 flex-wrap" style="font-size:.8rem;color:#94a3b8;">
                                <span><i class="fas fa-users me-1"></i>{{ $course['audience'] }}</span>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="col-md-4">
                            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:.625rem;padding:1.25rem;">
                                <div class="d-flex gap-3 mb-3">
                                    <div class="text-center flex-fill">
                                        <div class="fw-bold" style="color:#0f172a;font-size:1.2rem;">{{ $course['modules_count'] }}</div>
                                        <div style="color:#94a3b8;font-size:.72rem;">módulos</div>
                                    </div>
                                    <div style="width:1px;background:#e2e8f0;"></div>
                                    <div class="text-center flex-fill">
                                        <div class="fw-bold" style="color:#0f172a;font-size:1.2rem;">{{ $course['lessons_count'] }}</div>
                                        <div style="color:#94a3b8;font-size:.72rem;">lecciones</div>
                                    </div>
                                    <div style="width:1px;background:#e2e8f0;"></div>
                                    <div class="text-center flex-fill">
                                        <div class="fw-bold" style="color:#00c896;font-size:1rem;"><i class="fas fa-lock-open"></i></div>
                                        <div style="color:#94a3b8;font-size:.72rem;">gratis</div>
                                    </div>
                                </div>
                                <a href="{{ route('cursos.show', $course['slug']) }}"
                                   class="btn btn-primary w-100 mb-2" style="font-size:.85rem;">
                                    Ver curso completo
                                </a>
                                @guest
                                <a href="{{ route('register') }}" class="btn btn-outline-secondary w-100 btn-sm" style="font-size:.8rem;">
                                    <i class="fas fa-lock-open me-1"></i>Registrarme gratis
                                </a>
                                @endguest
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Nota editorial --}}
    <div class="mt-5 p-3" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:.5rem;text-align:center;">
        <p class="mb-0" style="color:#94a3b8;font-size:.82rem;">
            <i class="fas fa-info-circle me-1"></i>
            Los cursos son gratuitos con registro. El contenido es desarrollado por el equipo editorial de ConocIA y revisado por especialistas en cada área.
        </p>
    </div>

</div>

@push('styles')
<style>
.curso-row {
    transition: box-shadow .2s, border-color .2s;
}
.curso-row:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
    border-color: #cbd5e1;
}
</style>
@endpush

@endsection
