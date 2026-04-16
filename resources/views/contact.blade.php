@extends('layouts.app')

@section('title', 'Contacto — ConocIA')
@section('meta_description', 'Ponete en contacto con el equipo de ConocIA. Sugerencias, colaboraciones, correcciones y consultas.')

@section('content')

<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-2">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb" style="font-size:.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted">Inicio</a></li>
                <li class="breadcrumb-item active text-white">Contacto</li>
            </ol>
        </nav>
        <h1 class="fw-bold text-white mb-2" style="font-size:2rem;">Contacto</h1>
        <p style="color:#94a3b8;margin:0;font-size:.95rem;">Sugerencias, colaboraciones, correcciones o simplemente saludar.</p>
    </div>
</section>

<div class="container py-5">
    <div class="row g-5 justify-content-center">

        {{-- Formulario --}}
        <div class="col-lg-7">

            @if(session('success'))
            <div class="alert border-0 mb-4 p-4" style="background:#f0fdf4;border-left:4px solid #22c55e !important;border-radius:.5rem;">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-check-circle" style="color:#22c55e;font-size:1.4rem;"></i>
                    <div>
                        <div class="fw-semibold" style="color:#15803d;">Mensaje enviado</div>
                        <div style="color:#166534;font-size:.88rem;">{{ session('success') }}</div>
                    </div>
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="alert border-0 mb-4 p-4" style="background:#fef2f2;border-left:4px solid #ef4444 !important;border-radius:.5rem;">
                <div class="fw-semibold mb-1" style="color:#b91c1c;">Revisá los siguientes campos:</div>
                <ul class="mb-0 ps-3" style="color:#dc2626;font-size:.88rem;">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="profundiza-card p-4 p-lg-5">
                <form action="{{ route('contact.send') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem;color:#334155;">Nombre</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Tu nombre" value="{{ old('name') }}" required
                                   style="font-size:.9rem;">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem;color:#334155;">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   placeholder="tu@correo.com" value="{{ old('email') }}" required
                                   style="font-size:.9rem;">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#334155;">Asunto</label>
                        <select name="subject" class="form-select @error('subject') is-invalid @enderror" style="font-size:.9rem;" required>
                            <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Seleccioná un motivo</option>
                            <option value="Sugerencia de contenido" {{ old('subject') == 'Sugerencia de contenido' ? 'selected' : '' }}>Sugerencia de contenido</option>
                            <option value="Corrección de artículo" {{ old('subject') == 'Corrección de artículo' ? 'selected' : '' }}>Corrección de artículo</option>
                            <option value="Colaboración o columna" {{ old('subject') == 'Colaboración o columna' ? 'selected' : '' }}>Colaboración o columna</option>
                            <option value="Consulta sobre newsletter" {{ old('subject') == 'Consulta sobre newsletter' ? 'selected' : '' }}>Consulta sobre newsletter</option>
                            <option value="Publicidad o partnership" {{ old('subject') == 'Publicidad o partnership' ? 'selected' : '' }}>Publicidad o partnership</option>
                            <option value="Otro" {{ old('subject') == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#334155;">Mensaje</label>
                        <textarea name="message" rows="6"
                                  class="form-control @error('message') is-invalid @enderror"
                                  placeholder="Escribí tu mensaje aquí..." required
                                  style="font-size:.9rem;resize:vertical;">{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100" style="font-size:.92rem;padding:.75rem;">
                        <i class="fas fa-paper-plane me-2"></i>Enviar mensaje
                    </button>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="profundiza-card p-4 mb-4">
                <h5 class="fw-bold mb-4" style="color:#0f172a;font-size:.97rem;">Otras formas de conectar</h5>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div style="width:38px;height:38px;background:rgba(56,182,255,.12);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fab fa-x-twitter" style="color:var(--primary-color);font-size:.9rem;"></i>
                    </div>
                    <div>
                        <div class="fw-semibold" style="color:#1e293b;font-size:.88rem;">X (Twitter)</div>
                        <div style="color:#64748b;font-size:.8rem;">Seguinos para noticias al instante y threads de análisis.</div>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div style="width:38px;height:38px;background:rgba(56,182,255,.12);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-envelope" style="color:var(--primary-color);font-size:.9rem;"></i>
                    </div>
                    <div>
                        <div class="fw-semibold" style="color:#1e293b;font-size:.88rem;">Newsletter</div>
                        <div style="color:#64748b;font-size:.8rem;">Digest semanal con lo más importante de la semana en IA.</div>
                        <a href="{{ route('home') }}#newsletter" class="btn btn-outline-secondary btn-sm mt-2" style="font-size:.75rem;">Suscribirme</a>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3">
                    <div style="width:38px;height:38px;background:rgba(56,182,255,.12);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-pen-nib" style="color:var(--primary-color);font-size:.9rem;"></i>
                    </div>
                    <div>
                        <div class="fw-semibold" style="color:#1e293b;font-size:.88rem;">Escribí para ConocIA</div>
                        <div style="color:#64748b;font-size:.8rem;">¿Querés publicar un artículo de opinión? Envianos tu propuesta.</div>
                        <a href="{{ route('guest-posts.create') }}" class="btn btn-outline-secondary btn-sm mt-2" style="font-size:.75rem;">Enviar colaboración</a>
                    </div>
                </div>
            </div>

            <div class="profundiza-card p-4" style="background:#f0f9ff;border-color:#bae6fd;">
                <div class="d-flex gap-2 align-items-start">
                    <i class="fas fa-info-circle mt-1 flex-shrink-0" style="color:#0369a1;"></i>
                    <p style="color:#0369a1;font-size:.83rem;line-height:1.6;margin:0;">
                        Para correcciones urgentes de información incorrecta en un artículo, indicá el título y la sección específica. Respondemos en menos de 24 horas.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
