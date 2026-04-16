@extends('layouts.app')

@section('title', 'Acerca de ConocIA — Portal de IA en español')
@section('meta_description', 'ConocIA es el portal de referencia sobre inteligencia artificial en español. Noticias, análisis, papers y conocimiento de frontera sobre IA.')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">SOBRE NOSOTROS</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.4rem;line-height:1.15;">
                    Conocimiento sobre IA,<br><span style="color:var(--primary-color);">en tu idioma</span>
                </h1>
                <p style="color:#94a3b8;font-size:1.05rem;line-height:1.75;max-width:520px;">
                    ConocIA es el portal de referencia sobre inteligencia artificial en español.
                    Cubrimos las noticias que importan, explicamos la ciencia que está cambiando el mundo
                    y hacemos accesible el conocimiento de frontera — sin la barrera del idioma.
                </p>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end">
                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:1rem;padding:2rem;" class="text-center">
                    <i class="fas fa-brain" style="font-size:3.5rem;color:var(--primary-color);display:block;margin-bottom:1rem;"></i>
                    <div class="text-white fw-bold" style="font-size:1.3rem;">ConocIA</div>
                    <div style="color:#64748b;font-size:.82rem;">Inteligencia artificial para todos</div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">

    {{-- Misión --}}
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8 text-center">
            <p class="profundiza-section-label">Nuestra misión</p>
            <h2 class="fw-bold mb-3" style="color:#0f172a;font-size:1.7rem;">
                La IA avanza más rápido de lo que se puede explicar.<br>Nosotros cerramos esa brecha.
            </h2>
            <p style="color:#475569;font-size:1rem;line-height:1.8;">
                Millones de personas en el mundo hispanohablante quieren entender qué está pasando con la inteligencia artificial —
                qué significa para su trabajo, para la sociedad, para el futuro. Pero la mayoría del contenido serio está en inglés,
                es técnico en exceso o es superficial. ConocIA existe para cubrir ese vacío.
            </p>
        </div>
    </div>

    {{-- Pilares --}}
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="profundiza-card h-100 p-4 text-center">
                <div style="width:52px;height:52px;background:rgba(56,182,255,.12);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="fas fa-newspaper" style="color:var(--primary-color);font-size:1.3rem;"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color:#0f172a;font-size:.97rem;">Noticias</h5>
                <p style="color:#64748b;font-size:.84rem;line-height:1.6;margin:0;">
                    Las noticias más relevantes sobre IA, curadas y contextualizadas. Sin ruido, solo lo que importa.
                </p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="profundiza-card h-100 p-4 text-center">
                <div style="width:52px;height:52px;background:rgba(56,182,255,.12);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="fas fa-microscope" style="color:var(--primary-color);font-size:1.3rem;"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color:#0f172a;font-size:.97rem;">Análisis profundo</h5>
                <p style="color:#64748b;font-size:.84rem;line-height:1.6;margin:0;">
                    Piezas editoriales de largo aliento que van más allá del ciclo noticioso.
                </p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="profundiza-card h-100 p-4 text-center">
                <div style="width:52px;height:52px;background:rgba(56,182,255,.12);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="fas fa-file-alt" style="color:var(--primary-color);font-size:1.3rem;"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color:#0f172a;font-size:.97rem;">Papers explicados</h5>
                <p style="color:#64748b;font-size:.84rem;line-height:1.6;margin:0;">
                    Papers de arXiv traducidos y explicados en español. Ciencia de frontera accesible.
                </p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="profundiza-card h-100 p-4 text-center">
                <div style="width:52px;height:52px;background:rgba(56,182,255,.12);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="fas fa-chart-line" style="color:var(--primary-color);font-size:1.3rem;"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color:#0f172a;font-size:.97rem;">Estado del Arte</h5>
                <p style="color:#64748b;font-size:.84rem;line-height:1.6;margin:0;">
                    Un digest semanal por subcampo de la IA. Lo que avanzó, lo que importa, lo que viene.
                </p>
            </div>
        </div>
    </div>

    {{-- Cómo funciona / Tecnología --}}
    <div class="row g-4 mb-5 align-items-center">
        <div class="col-lg-6">
            <p class="profundiza-section-label">Cómo funciona</p>
            <h3 class="fw-bold mb-3" style="color:#0f172a;font-size:1.4rem;">Automatización inteligente + criterio editorial</h3>
            <p style="color:#475569;font-size:.97rem;line-height:1.8;">
                ConocIA combina automatización con IA (Gemini, arXiv, múltiples fuentes RSS) con criterio editorial humano.
                Procesamos docenas de fuentes diariamente para seleccionar lo relevante, generamos contexto en español
                y publicamos contenido que vale la pena leer.
            </p>
            <ul style="color:#475569;font-size:.95rem;line-height:2;" class="ps-3">
                <li>Noticias desde NewsAPI, The Guardian, RSS curados</li>
                <li>Papers desde arXiv con resúmenes generados por IA</li>
                <li>Digest semanal por subcampo, generado automáticamente</li>
                <li>Moderación de comentarios asistida por IA</li>
            </ul>
        </div>
        <div class="col-lg-6">
            <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:.75rem;padding:2rem;">
                <p class="profundiza-section-label mb-3">En números</p>
                <div class="row g-3 text-center">
                    @php
                        $newsCount     = \App\Models\News::where('status','published')->count();
                        $subCount      = \App\Models\Newsletter::where('is_active',true)->count();
                        $categoryCount = \App\Models\Category::count();
                    @endphp
                    <div class="col-4">
                        <div class="fw-bold" style="font-size:2rem;color:var(--primary-color);line-height:1;">{{ number_format($newsCount) }}</div>
                        <div style="color:#64748b;font-size:.78rem;margin-top:4px;">Artículos</div>
                    </div>
                    <div class="col-4" style="border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                        <div class="fw-bold" style="font-size:2rem;color:var(--primary-color);line-height:1;">{{ $categoryCount }}</div>
                        <div style="color:#64748b;font-size:.78rem;margin-top:4px;">Categorías</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold" style="font-size:2rem;color:var(--primary-color);line-height:1;">{{ number_format($subCount) }}</div>
                        <div style="color:#64748b;font-size:.78rem;margin-top:4px;">Suscriptores</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CTA Newsletter --}}
    <div style="background:linear-gradient(135deg,#0a1020 0%,#0f1b2d 100%);border-radius:.75rem;padding:2.5rem;" class="text-center">
        <i class="fas fa-robot mb-3 d-block" style="color:var(--primary-color);font-size:2rem;"></i>
        <h3 class="fw-bold text-white mb-2" style="font-size:1.3rem;">Mantente al día con el newsletter semanal</h3>
        <p style="color:#64748b;font-size:.9rem;margin-bottom:1.5rem;">Lo más importante de la semana en IA, directo a tu correo.</p>
        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="d-flex justify-content-center gap-2 flex-wrap">
            @csrf
            <input type="email" name="email" class="form-control" placeholder="tu@correo.com" required
                   style="max-width:280px;font-size:.88rem;">
            <button type="submit" class="btn btn-primary px-4" style="font-size:.88rem;">
                <i class="fas fa-paper-plane me-2"></i>Suscribirme
            </button>
        </form>
        <div style="color:#475569;font-size:.72rem;margin-top:.5rem;"><i class="fas fa-lock me-1"></i>Sin spam · Cancelá cuando quieras</div>
    </div>

    {{-- Links --}}
    <div class="d-flex justify-content-center gap-3 mt-5 flex-wrap">
        <a href="{{ route('news.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-newspaper me-2"></i>Ver noticias</a>
        <a href="{{ route('conceptos.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-book-open me-2"></i>Conceptos IA</a>
        <a href="{{ route('contact') }}" class="btn btn-sm text-white" style="background:var(--primary-color);"><i class="fas fa-envelope me-2"></i>Contacto</a>
    </div>

</div>
@endsection
