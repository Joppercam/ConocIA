@extends('layouts.app')

@section('title', 'Nuestro Impacto — ConocIA, Plataforma de Divulgación en IA')
@section('meta_description', 'Métricas verificables del alcance y contribución de ConocIA a la divulgación de inteligencia artificial en Chile y Latinoamérica.')

@push('styles')
<style>
.impact-metric-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: .75rem;
    padding: 2rem 1.5rem;
    text-align: center;
    transition: border-color .22s, transform .22s, box-shadow .22s;
}
.impact-metric-card:hover {
    border-color: rgba(56,182,255,.5);
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(56,182,255,.1);
}
.metric-number {
    font-size: 3rem;
    font-weight: 800;
    line-height: 1;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-color-light));
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}
.metric-label {
    color: #475569;
    font-size: .88rem;
    margin-top: .5rem;
    font-weight: 500;
}
.metric-desc {
    color: #94a3b8;
    font-size: .78rem;
    margin-top: .3rem;
    line-height: 1.5;
}
.impact-pillar-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: .75rem;
    padding: 1.5rem;
    transition: border-color .2s;
}
.impact-pillar-card:hover {
    border-color: rgba(56,182,255,.4);
}
</style>
@endpush

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-3 text-center">
        <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">MÉTRICAS VERIFICABLES</span>
        <h1 class="fw-bold text-white mb-3" style="font-size:2.4rem;line-height:1.15;">
            Nuestro Impacto
        </h1>
        <p style="color:#94a3b8;font-size:1.05rem;line-height:1.75;max-width:580px;margin:0 auto;">
            Cada número representa conocimiento democratizado, barreras eliminadas y ciudadanos que entienden mejor cómo la IA transforma su mundo.
        </p>
    </div>
</section>

<div class="container py-5">

    {{-- Grid de métricas principales --}}
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-4 col-lg-3">
            <div class="impact-metric-card">
                <div class="metric-number">{{ number_format($metrics['total_articles']) }}</div>
                <div class="metric-label">Artículos publicados</div>
                <div class="metric-desc">Noticias y análisis sobre IA en español</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="impact-metric-card">
                <div class="metric-number">{{ $metrics['papers_explained'] }}</div>
                <div class="metric-label">Papers explicados</div>
                <div class="metric-desc">Papers de arXiv accesibles en español</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="impact-metric-card">
                <div class="metric-number">{{ $metrics['research_articles'] }}</div>
                <div class="metric-label">Investigaciones</div>
                <div class="metric-desc">Análisis originales sobre IA y sociedad</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="impact-metric-card">
                <div class="metric-number">{{ $metrics['radio_episodes'] }}</div>
                <div class="metric-label">Episodios de Radio</div>
                <div class="metric-desc">Briefings diarios generados con IA</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="impact-metric-card">
                <div class="metric-number">{{ $metrics['estado_arte'] }}</div>
                <div class="metric-label">Digests Estado del Arte</div>
                <div class="metric-desc">Resúmenes semanales por campo de IA</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="impact-metric-card">
                <div class="metric-number">{{ $metrics['fields_covered'] }}</div>
                <div class="metric-label">Campos de IA cubiertos</div>
                <div class="metric-desc">NLP, visión, robótica, ética y más</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="impact-metric-card">
                <div class="metric-number">{{ $metrics['concepts'] }}</div>
                <div class="metric-label">Conceptos IA</div>
                <div class="metric-desc">Enciclopedia de IA en crecimiento</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="impact-metric-card">
                <div class="metric-number">{{ number_format($metrics['registered_users']) }}</div>
                <div class="metric-label">Usuarios registrados</div>
                <div class="metric-desc">Comunidad activa de lectores</div>
            </div>
        </div>
    </div>

    {{-- Pilares de impacto --}}
    <div class="mb-5">
        <p class="profundiza-section-label text-center">Dimensiones de impacto</p>
        <h2 class="fw-bold text-center mb-4" style="color:#0f172a;font-size:1.5rem;">Cómo contribuimos a la ciencia pública</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="impact-pillar-card h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:44px;height:44px;background:rgba(56,182,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-universal-access" style="color:var(--primary-color);font-size:1.1rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-0" style="color:#0f172a;font-size:1rem;">Acceso sin barreras</h3>
                    </div>
                    <p style="color:#475569;font-size:.9rem;line-height:1.75;margin:0;">
                        Todo nuestro contenido es gratuito y en español. Eliminamos la barrera idiomática que separa a millones de hispanohablantes de la ciencia de frontera en inteligencia artificial.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="impact-pillar-card h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:44px;height:44px;background:rgba(56,182,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-graduation-cap" style="color:var(--primary-color);font-size:1.1rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-0" style="color:#0f172a;font-size:1rem;">Alfabetización tecnológica</h3>
                    </div>
                    <p style="color:#475569;font-size:.9rem;line-height:1.75;margin:0;">
                        Nuestros Conceptos IA y análisis de fondo ayudan a ciudadanos sin formación técnica a entender los fundamentos de la IA y sus implicancias para la sociedad chilena.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="impact-pillar-card h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:44px;height:44px;background:rgba(56,182,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-flag-checkered" style="color:var(--primary-color);font-size:1.1rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-0" style="color:#0f172a;font-size:1rem;">Ecosistema IA en Chile</h3>
                    </div>
                    <p style="color:#475569;font-size:.9rem;line-height:1.75;margin:0;">
                        Somos el registro más completo del ecosistema de inteligencia artificial en Chile: startups, universidades, regulación y políticas públicas — todo en un solo lugar.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="impact-pillar-card h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:44px;height:44px;background:rgba(56,182,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-microscope" style="color:var(--primary-color);font-size:1.1rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-0" style="color:#0f172a;font-size:1rem;">Rigor científico</h3>
                    </div>
                    <p style="color:#475569;font-size:.9rem;line-height:1.75;margin:0;">
                        Todo nuestro contenido se basa en fuentes primarias: papers académicos, reportes oficiales y datos verificables. No promovemos productos ni servicios comerciales.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- CTA --}}
    <div style="background:linear-gradient(135deg,#0a1020 0%,#0f1b2d 100%);border-radius:.75rem;padding:2.5rem;" class="text-center">
        <i class="fas fa-hands-helping mb-3 d-block" style="color:var(--primary-color);font-size:2rem;"></i>
        <h3 class="fw-bold text-white mb-2" style="font-size:1.3rem;">¿Quieres contribuir?</h3>
        <p style="color:#94a3b8;font-size:.9rem;margin-bottom:1.5rem;">Puedes enviar investigaciones, colaborar como autor o simplemente compartir nuestro contenido.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('submit-research') }}" class="btn btn-primary px-4" style="font-size:.9rem;">
                <i class="fas fa-paper-plane me-2"></i>Enviar investigación
            </a>
            <a href="{{ route('quienes-somos') }}" class="btn btn-outline-light px-4" style="font-size:.9rem;">
                <i class="fas fa-info-circle me-2"></i>Conoce el equipo
            </a>
        </div>
    </div>

</div>
@endsection
