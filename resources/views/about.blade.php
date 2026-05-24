@extends('layouts.app')

@section('title', 'Quiénes Somos — ConocIA, Plataforma de Divulgación en IA')
@section('meta_description', 'Conoce la misión, visión y línea editorial de ConocIA: la plataforma chilena de divulgación en inteligencia artificial para Chile y Latinoamérica.')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <div class="col-lg-8">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">SOBRE NOSOTROS</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.4rem;line-height:1.15;">
                    Quiénes Somos
                </h1>
                <p style="color:#94a3b8;font-size:1.1rem;line-height:1.75;max-width:600px;">
                    Una plataforma creada para que la inteligencia artificial deje de ser un tema de expertos.
                </p>
            </div>
            <div class="col-lg-4 d-none d-lg-flex justify-content-end">
                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:1rem;padding:2rem;" class="text-center">
                    <i class="fas fa-brain" style="font-size:3rem;color:var(--primary-color);display:block;margin-bottom:.75rem;"></i>
                    <div class="text-white fw-bold">ConocIA</div>
                    <div style="color:#64748b;font-size:.85rem;margin-top:.3rem;">Plataforma de Divulgación en IA</div>
                    <div style="color:#38b6ff;font-size:.78rem;margin-top:.5rem;"><i class="fas fa-map-marker-alt me-1"></i>Santiago, Chile</div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">

    {{-- Misión / Visión / Línea editorial --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-4">
            <div class="profundiza-card h-100 p-4">
                <div style="width:48px;height:48px;background:rgba(56,182,255,.12);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:1.1rem;">
                    <i class="fas fa-bullseye" style="color:var(--primary-color);font-size:1.2rem;"></i>
                </div>
                <h3 class="fw-bold mb-3" style="color:#0f172a;font-size:1.05rem;text-transform:uppercase;letter-spacing:.05em;">Misión</h3>
                <p style="color:#475569;font-size:.95rem;line-height:1.8;margin:0;">
                    ConocIA es una plataforma chilena de divulgación científico-tecnológica dedicada a democratizar el acceso al conocimiento sobre inteligencia artificial. Eliminamos la barrera del idioma y la complejidad técnica para que cualquier persona, sin importar su formación, pueda entender cómo la IA está transformando el mundo y cómo le afecta directamente.
                </p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="profundiza-card h-100 p-4">
                <div style="width:48px;height:48px;background:rgba(56,182,255,.12);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:1.1rem;">
                    <i class="fas fa-eye" style="color:var(--primary-color);font-size:1.2rem;"></i>
                </div>
                <h3 class="fw-bold mb-3" style="color:#0f172a;font-size:1.05rem;text-transform:uppercase;letter-spacing:.05em;">Visión</h3>
                <p style="color:#475569;font-size:.95rem;line-height:1.8;margin:0;">
                    Ser la plataforma de referencia en divulgación de inteligencia artificial en español, contribuyendo a la alfabetización tecnológica de la ciudadanía chilena y latinoamericana, con rigor científico y accesibilidad como pilares fundamentales.
                </p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="profundiza-card h-100 p-4">
                <div style="width:48px;height:48px;background:rgba(56,182,255,.12);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:1.1rem;">
                    <i class="fas fa-pen-nib" style="color:var(--primary-color);font-size:1.2rem;"></i>
                </div>
                <h3 class="fw-bold mb-3" style="color:#0f172a;font-size:1.05rem;text-transform:uppercase;letter-spacing:.05em;">Línea Editorial</h3>
                <p style="color:#475569;font-size:.95rem;line-height:1.8;margin:0;">
                    Divulgación científico-tecnológica rigurosa, accesible y orientada al impacto social. Publicamos contenido original basado en fuentes primarias: papers académicos, reportes oficiales y datos verificables. No promovemos productos ni servicios comerciales. Nuestro compromiso es con la verdad, la claridad y la utilidad pública del conocimiento.
                </p>
            </div>
        </div>
    </div>

    {{-- Fundador --}}
    <div class="mb-5">
        <p class="profundiza-section-label text-center">El equipo</p>
        <h2 class="fw-bold text-center mb-4" style="color:#0f172a;font-size:1.6rem;">Quién está detrás de ConocIA</h2>
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="profundiza-card p-4 p-md-5">
                    <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-4">

                        {{-- Foto / placeholder --}}
                        <div class="flex-shrink-0 text-center">
                            <div style="width:96px;height:96px;background:linear-gradient(135deg,rgba(56,182,255,.25),rgba(0,200,150,.15));border-radius:50%;border:2px solid rgba(56,182,255,.35);display:flex;align-items:center;justify-content:center;margin:0 auto;">
                                <span class="fw-bold" style="color:var(--primary-color);font-size:1.4rem;letter-spacing:.03em;">JPB</span>
                            </div>
                            {{-- Redes sociales --}}
                            <div class="d-flex justify-content-center gap-2 mt-3">
                                <a href="#" title="LinkedIn" style="width:32px;height:32px;background:rgba(56,182,255,.1);border:1px solid rgba(56,182,255,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#7dd3f0;text-decoration:none;font-size:.8rem;" target="_blank" rel="noopener">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" title="GitHub" style="width:32px;height:32px;background:rgba(56,182,255,.1);border:1px solid rgba(56,182,255,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#7dd3f0;text-decoration:none;font-size:.8rem;" target="_blank" rel="noopener">
                                    <i class="fab fa-github"></i>
                                </a>
                                <a href="#" title="X / Twitter" style="width:32px;height:32px;background:rgba(56,182,255,.1);border:1px solid rgba(56,182,255,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#7dd3f0;text-decoration:none;font-size:.8rem;" target="_blank" rel="noopener">
                                    <i class="fab fa-x-twitter"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Bio --}}
                        <div>
                            <div class="fw-bold mb-1" style="color:#0f172a;font-size:1.1rem;">Juan Pablo Basualdo</div>
                            <div style="color:var(--primary-color);font-size:.82rem;margin-bottom:.75rem;">Fundador y Director</div>
                            <p style="color:#475569;font-size:.93rem;line-height:1.75;margin:0;">
                                Ingeniero informático y desarrollador de software. Fundó ConocIA con la convicción de que el conocimiento sobre inteligencia artificial no puede seguir siendo exclusivo de quienes leen papers en inglés o trabajan en Silicon Valley. Desde Santiago de Chile, combina su formación técnica con una vocación por explicar lo complejo de forma accesible.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Números en vivo --}}
    @php
        $newsCount      = \App\Models\News::where('status','published')->count();
        $papersCount    = \App\Models\ConocIaPaper::count();
        $conceptosCount = \App\Models\ConceptoIa::where('status','published')->count();
        $colCount       = \App\Models\Column::whereNotNull('published_at')->where('published_at','<=',now())->count();
    @endphp
    <div class="mb-5" style="background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%);border-radius:.75rem;padding:2rem 2.5rem;border:1px solid #bae6fd;">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="fw-bold" style="font-size:2.2rem;color:var(--primary-color);line-height:1;">{{ number_format($newsCount) }}</div>
                <div style="color:#475569;font-size:.78rem;margin-top:4px;">Artículos publicados</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-bold" style="font-size:2.2rem;color:var(--primary-color);line-height:1;">{{ $papersCount }}</div>
                <div style="color:#475569;font-size:.78rem;margin-top:4px;">Papers explicados</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-bold" style="font-size:2.2rem;color:var(--primary-color);line-height:1;">{{ $conceptosCount }}</div>
                <div style="color:#475569;font-size:.78rem;margin-top:4px;">Conceptos IA</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-bold" style="font-size:2.2rem;color:var(--primary-color);line-height:1;">{{ $colCount }}</div>
                <div style="color:#475569;font-size:.78rem;margin-top:4px;">Columnas de opinión</div>
            </div>
        </div>
    </div>

    {{-- Contacto --}}
    <div style="background:linear-gradient(135deg,#0a1020 0%,#0f1b2d 100%);border-radius:.75rem;padding:2.5rem;" class="text-center">
        <i class="fas fa-envelope mb-3 d-block" style="color:var(--primary-color);font-size:2rem;"></i>
        <h3 class="fw-bold text-white mb-2" style="font-size:1.3rem;">¿Tienes una investigación o propuesta?</h3>
        <p style="color:#94a3b8;font-size:.93rem;margin-bottom:.5rem;max-width:540px;margin-left:auto;margin-right:auto;">
            Estamos abiertos a colaboraciones con investigadores, periodistas y profesionales interesados en divulgar conocimiento sobre inteligencia artificial.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
            <a href="mailto:contacto@conocia.cl" class="btn btn-primary px-4" style="font-size:.9rem;">
                <i class="fas fa-envelope me-2"></i>contacto@conocia.cl
            </a>
            <a href="{{ route('submit-research') }}" class="btn btn-outline-light px-4" style="font-size:.9rem;">
                <i class="fas fa-paper-plane me-2"></i>Enviar investigación
            </a>
        </div>
        <div class="mt-3" style="color:#475569;font-size:.82rem;">
            <i class="fas fa-map-marker-alt me-2"></i>Santiago, Chile
        </div>
    </div>

</div>
@endsection
