@extends('layouts.app')

@section('title', 'Quiénes Somos — ConocIA, Plataforma de Divulgación en IA')
@section('meta_description', 'Conoce la misión, visión y línea editorial de ConocIA: la plataforma chilena de divulgación, educación y alfabetización en inteligencia artificial para Chile y Latinoamérica.')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">SOBRE NOSOTROS</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.4rem;line-height:1.15;">
                    Quiénes Somos
                </h1>
                <p style="color:#94a3b8;font-size:1.05rem;line-height:1.75;max-width:560px;">
                    ConocIA es una plataforma chilena de divulgación científico-tecnológica dedicada a democratizar el acceso al conocimiento sobre inteligencia artificial. Eliminamos la barrera del idioma y la complejidad técnica para que cualquier persona pueda entender cómo la IA está transformando el mundo.
                </p>
                <div class="d-flex gap-3 mt-4 flex-wrap">
                    <a href="{{ route('papers.index') }}" class="btn btn-primary px-4" style="font-size:.9rem;">
                        <i class="fas fa-file-alt me-2"></i>Ver Papers
                    </a>
                    <a href="{{ route('impacto') }}" class="btn btn-outline-light px-4" style="font-size:.9rem;">
                        <i class="fas fa-chart-bar me-2"></i>Nuestro Impacto
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end">
                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:1rem;padding:2rem;" class="text-center">
                    <i class="fas fa-brain" style="font-size:3.5rem;color:var(--primary-color);display:block;margin-bottom:1rem;"></i>
                    <div class="text-white fw-bold" style="font-size:1.3rem;">ConocIA</div>
                    <div style="color:#64748b;font-size:.88rem;margin-top:.3rem;">Plataforma de Divulgación en IA</div>
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
                    Democratizar el acceso al conocimiento sobre inteligencia artificial. Eliminamos la barrera del idioma y la complejidad técnica para que cualquier persona, sin importar su formación, pueda entender cómo la IA está transformando el mundo y cómo le afecta directamente.
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
                    Divulgación científico-tecnológica rigurosa, accesible y orientada al impacto social. Publicamos contenido original basado en fuentes primarias. No promovemos productos ni servicios comerciales. Nuestro compromiso es con la verdad, la claridad y la utilidad pública del conocimiento.
                </p>
            </div>
        </div>
    </div>

    {{-- Lo que hacemos --}}
    <div class="mb-5">
        <p class="profundiza-section-label text-center">Lo que hacemos</p>
        <h2 class="fw-bold text-center mb-4" style="color:#0f172a;font-size:1.6rem;">Seis pilares de divulgación</h2>
        <div class="row g-3">
            <div class="col-md-6 col-lg-4">
                <div class="d-flex align-items-start gap-3 p-3" style="background:#f8fafc;border-radius:.75rem;border:1px solid #e2e8f0;">
                    <div style="width:40px;height:40px;background:rgba(56,182,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-file-alt" style="color:var(--primary-color);font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">ConocIA Papers</div>
                        <div style="color:#64748b;font-size:.82rem;line-height:1.6;">Explicamos papers de arXiv en español — ciencia de frontera sin barreras.</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="d-flex align-items-start gap-3 p-3" style="background:#f8fafc;border-radius:.75rem;border:1px solid #e2e8f0;">
                    <div style="width:40px;height:40px;background:rgba(56,182,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-flag" style="color:var(--primary-color);font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">IA en Chile</div>
                        <div style="color:#64748b;font-size:.82rem;line-height:1.6;">Cubrimos el ecosistema local: startups, universidades, regulación y políticas públicas.</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="d-flex align-items-start gap-3 p-3" style="background:#f8fafc;border-radius:.75rem;border:1px solid #e2e8f0;">
                    <div style="width:40px;height:40px;background:rgba(56,182,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-podcast" style="color:var(--primary-color);font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">ConocIA Radio</div>
                        <div style="color:#64748b;font-size:.82rem;line-height:1.6;">Briefings diarios de IA generados con tecnología propia — siempre al día.</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="d-flex align-items-start gap-3 p-3" style="background:#f8fafc;border-radius:.75rem;border:1px solid #e2e8f0;">
                    <div style="width:40px;height:40px;background:rgba(56,182,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-flask" style="color:var(--primary-color);font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">Investigación Original</div>
                        <div style="color:#64748b;font-size:.82rem;line-height:1.6;">Publicamos investigaciones originales sobre IA y sociedad en Chile y Latinoamérica.</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="d-flex align-items-start gap-3 p-3" style="background:#f8fafc;border-radius:.75rem;border:1px solid #e2e8f0;">
                    <div style="width:40px;height:40px;background:rgba(56,182,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-chart-line" style="color:var(--primary-color);font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">Estado del Arte</div>
                        <div style="color:#64748b;font-size:.82rem;line-height:1.6;">Digest semanal del estado del arte por campo de IA — 6 áreas cubiertas.</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="d-flex align-items-start gap-3 p-3" style="background:#f8fafc;border-radius:.75rem;border:1px solid #e2e8f0;">
                    <div style="width:40px;height:40px;background:rgba(56,182,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-book-open" style="color:var(--primary-color);font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold mb-1" style="color:#0f172a;font-size:.92rem;">Conceptos IA</div>
                        <div style="color:#64748b;font-size:.82rem;line-height:1.6;">Enciclopedia de inteligencia artificial accesible — explicamos desde los fundamentos.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Números en vivo --}}
    <div class="mb-5" style="background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%);border-radius:.75rem;padding:2.5rem;border:1px solid #bae6fd;">
        <p class="profundiza-section-label text-center">En números</p>
        <h2 class="fw-bold text-center mb-4" style="color:#0f172a;font-size:1.5rem;">El alcance de nuestra divulgación</h2>
        @php
            $newsCount      = \App\Models\News::where('status','published')->count();
            $papersCount    = \App\Models\ConocIaPaper::count();
            $researchCount  = \App\Models\Research::count();
            $conceptosCount = \App\Models\ConceptoIa::where('status','published')->count();
            $userCount      = \App\Models\User::count();
            $colCount       = \App\Models\Column::where('status','published')->count();
        @endphp
        <div class="row g-4 text-center">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="fw-bold" style="font-size:2.2rem;color:var(--primary-color);line-height:1;">{{ number_format($newsCount) }}</div>
                <div style="color:#475569;font-size:.78rem;margin-top:4px;">Artículos publicados</div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="fw-bold" style="font-size:2.2rem;color:var(--primary-color);line-height:1;">{{ $papersCount }}</div>
                <div style="color:#475569;font-size:.78rem;margin-top:4px;">Papers explicados</div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="fw-bold" style="font-size:2.2rem;color:var(--primary-color);line-height:1;">{{ $researchCount }}</div>
                <div style="color:#475569;font-size:.78rem;margin-top:4px;">Investigaciones</div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="fw-bold" style="font-size:2.2rem;color:var(--primary-color);line-height:1;">{{ $conceptosCount }}</div>
                <div style="color:#475569;font-size:.78rem;margin-top:4px;">Conceptos IA</div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="fw-bold" style="font-size:2.2rem;color:var(--primary-color);line-height:1;">{{ number_format($userCount) }}</div>
                <div style="color:#475569;font-size:.78rem;margin-top:4px;">Usuarios registrados</div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="fw-bold" style="font-size:2.2rem;color:var(--primary-color);line-height:1;">6</div>
                <div style="color:#475569;font-size:.78rem;margin-top:4px;">Campos de IA cubiertos</div>
            </div>
        </div>
    </div>

    {{-- Equipo --}}
    <div class="mb-5">
        <p class="profundiza-section-label text-center">El equipo</p>
        <h2 class="fw-bold text-center mb-2" style="color:#0f172a;font-size:1.6rem;">Quién está detrás de ConocIA</h2>
        <p class="text-center mb-4" style="color:#64748b;font-size:.95rem;max-width:520px;margin:0 auto 2rem;">
            Un equipo comprometido con hacer la inteligencia artificial accesible para todos.
        </p>
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="profundiza-card text-center p-4">
                    <div style="width:80px;height:80px;background:linear-gradient(135deg,rgba(56,182,255,.2),rgba(0,225,255,.1));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;border:2px solid rgba(56,182,255,.3);">
                        <i class="fas fa-user" style="color:var(--primary-color);font-size:1.8rem;"></i>
                    </div>
                    <div class="fw-bold mb-1" style="color:#0f172a;">Equipo ConocIA</div>
                    <div style="color:var(--primary-color);font-size:.82rem;">Periodismo · Tecnología · Divulgación</div>
                    <p style="color:#64748b;font-size:.82rem;margin-top:.75rem;line-height:1.6;">
                        Comprometidos con democratizar el conocimiento sobre IA en Chile y Latinoamérica.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Contacto --}}
    <div style="background:linear-gradient(135deg,#0a1020 0%,#0f1b2d 100%);border-radius:.75rem;padding:2.5rem;" class="text-center mb-5">
        <i class="fas fa-envelope mb-3 d-block" style="color:var(--primary-color);font-size:2rem;"></i>
        <h3 class="fw-bold text-white mb-2" style="font-size:1.3rem;">¿Tienes una investigación o propuesta?</h3>
        <p style="color:#94a3b8;font-size:.9rem;margin-bottom:1.5rem;">Nos interesa colaborar con investigadores, periodistas y divulgadores.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="mailto:info@conocia.cl" class="btn btn-primary px-4" style="font-size:.9rem;">
                <i class="fas fa-envelope me-2"></i>info@conocia.cl
            </a>
            <a href="{{ route('submit-research') }}" class="btn btn-outline-light px-4" style="font-size:.9rem;">
                <i class="fas fa-paper-plane me-2"></i>Enviar investigación
            </a>
        </div>
        <div class="mt-3" style="color:#475569;font-size:.82rem;">
            <i class="fas fa-map-marker-alt me-2"></i>Santiago, Chile
        </div>
    </div>

    {{-- Links --}}
    <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="{{ route('news.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-newspaper me-2"></i>Noticias</a>
        <a href="{{ route('papers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-alt me-2"></i>Papers</a>
        <a href="{{ route('conceptos.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-book-open me-2"></i>Conceptos IA</a>
        <a href="{{ route('impacto') }}" class="btn btn-sm text-white" style="background:var(--primary-color);"><i class="fas fa-chart-bar me-2"></i>Ver impacto</a>
    </div>

</div>
@endsection
