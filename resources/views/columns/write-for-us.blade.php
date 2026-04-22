@extends('layouts.app')

@section('title', 'Escribí para ConocIA — Convocatoria a expertos en IA')
@section('meta_description', 'ConocIA convoca a investigadores, académicos y profesionales de inteligencia artificial a publicar columnas de análisis en español.')

@section('content')

{{-- Hero --}}
<div style="background:linear-gradient(135deg,#0a1020 0%,#0d1b2e 100%);border-bottom:1px solid #1e2430;">
    <div class="container py-5">
        <div class="row g-5 align-items-center">
            <div class="col-lg-7">
                <span class="badge mb-3 px-3 py-2" style="background:rgba(56,182,255,.15);color:var(--primary-color);font-size:.75rem;letter-spacing:.05em;">
                    <i class="fas fa-pencil-alt me-2"></i>CONVOCATORIA ABIERTA
                </span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2rem;line-height:1.25;">
                    Tu conocimiento merece<br>
                    <span style="color:var(--primary-color);">llegar más lejos</span>
                </h1>
                <p style="color:#94a3b8;font-size:1rem;line-height:1.75;" class="mb-0">
                    ConocIA es el principal portal de inteligencia artificial en español de Chile. Buscamos investigadores, académicos y profesionales que quieran explicar, cuestionar y proyectar el impacto de la IA desde una perspectiva latinoamericana.
                </p>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <div class="rounded-3 p-4" style="background:rgba(56,182,255,.05);border:1px solid rgba(56,182,255,.12);">
                    <div style="color:#64748b;font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;" class="mb-3">Lo que ofrecemos</div>
                    @foreach([
                        ['fas fa-users','Audiencia cualificada interesada en IA'],
                        ['fas fa-search','Posicionamiento SEO en Google Chile'],
                        ['fas fa-id-card','Perfil de autor con tu institución y área de expertise'],
                        ['fas fa-share-alt','Difusión en redes sociales y newsletter'],
                        ['fas fa-file-alt','Formato largo que hace justicia a tu análisis'],
                    ] as [$icon, $text])
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div style="width:28px;height:28px;background:rgba(56,182,255,.1);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="{{ $icon }}" style="color:var(--primary-color);font-size:.7rem;"></i>
                        </div>
                        <span style="color:#cbd5e1;font-size:.82rem;line-height:1.5;">{{ $text }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Temas que buscamos --}}
<div style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
    <div class="container py-5">
        <h2 class="fw-bold mb-4 text-center" style="font-size:1.2rem;">Temas que nos interesan</h2>
        <div class="row g-3">
            @foreach([
                ['fas fa-gavel','#38b6ff','Regulación y política pública de IA en Chile y Latam'],
                ['fas fa-flask','#a78bfa','Investigación aplicada: NLP, visión, agentes, multimodal'],
                ['fas fa-industry','#00c896','IA en sectores: salud, educación, minería, finanzas'],
                ['fas fa-balance-scale','#f59e0b','Ética, sesgos y responsabilidad algorítmica'],
                ['fas fa-rocket','#f87171','Ecosistema de startups de IA en Chile'],
                ['fas fa-graduation-cap','#6366f1','Educación en IA: brechas, oportunidades, formación'],
                ['fas fa-landmark','#0891b2','Políticas de Estado y digitalización pública'],
                ['fas fa-globe-americas','#059669','Impacto social y laboral en el contexto chileno'],
            ] as [$icon, $color, $text])
            <div class="col-md-6 col-lg-3">
                <div class="d-flex align-items-start gap-3 p-3 rounded-2 h-100" style="background:#fff;border:1px solid #e2e8f0;">
                    <div style="width:32px;height:32px;background:{{ $color }}18;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="{{ $icon }}" style="color:{{ $color }};font-size:.75rem;"></i>
                    </div>
                    <span style="font-size:.82rem;color:#334155;line-height:1.5;">{{ $text }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Requisitos + formulario --}}
<div class="container py-5">
    <div class="row g-5">

        <div class="col-lg-5">
            <h2 class="fw-bold mb-4" style="font-size:1.1rem;">¿Quiénes pueden escribir?</h2>
            <ul class="list-unstyled">
                @foreach([
                    'Investigadores y académicos de universidades chilenas o latinoamericanas',
                    'Profesionales con experiencia demostrable en IA o transformación digital',
                    'Estudiantes de doctorado con investigación activa en el área',
                    'Consultores o técnicos con perspectiva crítica y fundada',
                ] as $item)
                <li class="d-flex align-items-start gap-2 mb-3">
                    <i class="fas fa-check-circle mt-1" style="color:#00c896;font-size:.85rem;flex-shrink:0;"></i>
                    <span style="font-size:.9rem;color:#475569;">{{ $item }}</span>
                </li>
                @endforeach
            </ul>

            <h2 class="fw-bold mb-3 mt-4" style="font-size:1.1rem;">Formato de las columnas</h2>
            <div class="rounded-2 p-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                @foreach([
                    ['Extensión','800 a 1.500 palabras'],
                    ['Idioma','Español (con rigor pero accesible)'],
                    ['Exclusividad','Inédito, no publicado en otro medio'],
                    ['Plazo','Respondemos en 5 días hábiles'],
                ] as [$label, $value])
                <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #e9ecef;font-size:.85rem;">
                    <span style="color:#64748b;">{{ $label }}</span>
                    <span class="fw-semibold" style="color:#334155;">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="col-lg-7">
            <h2 class="fw-bold mb-4" style="font-size:1.1rem;">Envía tu propuesta</h2>

            @if(session('success'))
            <div class="alert alert-success rounded-2 mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
            @endif

            <form action="{{ route('columns.write-for-us.submit') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Nombre completo *</label>
                        <input type="text" name="name" required value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror" style="font-size:.88rem;">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Email *</label>
                        <input type="email" name="email" required value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" style="font-size:.88rem;">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Institución / Empresa</label>
                        <input type="text" name="institution" value="{{ old('institution') }}"
                               placeholder="Universidad de Chile, CMM, empresa..."
                               class="form-control" style="font-size:.88rem;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Área de expertise</label>
                        <input type="text" name="expertise" value="{{ old('expertise') }}"
                               placeholder="NLP, ética IA, políticas públicas..."
                               class="form-control" style="font-size:.88rem;">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Título o idea de la columna *</label>
                        <input type="text" name="title" required value="{{ old('title') }}"
                               placeholder="¿Sobre qué querés escribir?"
                               class="form-control @error('title') is-invalid @enderror" style="font-size:.88rem;">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Resumen de la propuesta * <span class="text-muted fw-normal">(200 palabras máx.)</span></label>
                        <textarea name="summary" required rows="5"
                                  placeholder="¿Cuál es el argumento central? ¿Por qué es relevante ahora?"
                                  class="form-control @error('summary') is-invalid @enderror" style="font-size:.88rem;">{{ old('summary') }}</textarea>
                        @error('summary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">LinkedIn o web personal</label>
                        <input type="url" name="linkedin" value="{{ old('linkedin') }}"
                               placeholder="https://linkedin.com/in/..."
                               class="form-control" style="font-size:.88rem;">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn rounded-pill px-5 py-2 fw-semibold"
                                style="background:var(--primary-color);color:#fff;font-size:.9rem;">
                            <i class="fas fa-paper-plane me-2"></i>Enviar propuesta
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

@include('partials.schema-breadcrumb', ['crumbs' => [
    ['name' => 'Inicio', 'url' => url('/')],
    ['name' => 'Columnas', 'url' => route('columns.index')],
    ['name' => 'Escribí para ConocIA'],
]])
@endsection
