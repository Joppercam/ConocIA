@extends('layouts.app')

@section('title', 'Voces y Posturas — Quién opina qué sobre la regulación de IA en Chile | ConocIA')
@section('meta_description', 'El proyecto de ley de IA genera un debate intenso en Chile. Gobierno, academia, juristas, big tech y think tanks: estas son las principales posturas sobre cómo regular la inteligencia artificial.')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-3">
        <nav style="font-size:.82rem;color:#64748b;margin-bottom:1.25rem;">
            <a href="{{ route('regulacion.index') }}" style="color:#7dd3f0;text-decoration:none;">
                <i class="fas fa-balance-scale me-1"></i>Observatorio de Regulación IA
            </a>
            <span class="mx-2">›</span>
            <span style="color:#94a3b8;">Voces y Posturas</span>
        </nav>
        <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">DEBATE PÚBLICO</span>
        <h1 class="fw-bold text-white mb-3" style="font-size:2.2rem;line-height:1.15;">¿Qué opinan los actores clave?</h1>
        <p style="color:#94a3b8;font-size:1.05rem;line-height:1.7;max-width:620px;">
            El proyecto de ley de IA genera un debate intenso en Chile. Estas son las principales posturas — presentadas sin tomar partido, para que te formes tu propia opinión.
        </p>
    </div>
</section>

<div class="container py-5">

    {{-- Espectro de posiciones --}}
    <div class="mb-5">
        <p class="profundiza-section-label">Espectro de posiciones</p>
        <div class="profundiza-card p-4">
            <div class="d-flex justify-content-between mb-2" style="font-size:.78rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">
                <span><i class="fas fa-arrow-left me-1"></i>Más regulación</span>
                <span>Menos regulación<i class="fas fa-arrow-right ms-1"></i></span>
            </div>
            {{-- Barra --}}
            <div style="position:relative;height:8px;background:linear-gradient(to right,#3b82f6,#10b981,#f59e0b,#ef4444);border-radius:4px;margin-bottom:2.5rem;">
                @foreach($voices as $voice)
                <div style="position:absolute;top:-4px;left:{{ $voice['spectrum_pos'] }}%;transform:translateX(-50%);">
                    <div style="width:16px;height:16px;background:{{ $voice['postura_color'] }};border:2px solid #fff;border-radius:50%;box-shadow:0 1px 4px rgba(0,0,0,.25);"></div>
                </div>
                @endforeach
            </div>
            {{-- Etiquetas --}}
            <div style="position:relative;height:3.5rem;">
                @foreach($voices as $voice)
                <div style="position:absolute;left:{{ $voice['spectrum_pos'] }}%;transform:translateX(-50%);text-align:center;width:90px;">
                    <div style="font-size:.7rem;font-weight:600;color:#334155;line-height:1.3;">{{ $voice['name'] }}</div>
                </div>
                @endforeach
            </div>
            <p style="color:#94a3b8;font-size:.78rem;margin-top:.5rem;margin-bottom:0;text-align:center;">
                Posicionamiento aproximado en el debate regulatorio. No representa una escala exacta.
            </p>
        </div>
    </div>

    {{-- Cards de voces --}}
    <p class="profundiza-section-label">Las posturas en detalle</p>
    <div class="row g-4 mb-5">
        @foreach($voices as $voice)
        <div class="col-lg-6">
            <div class="profundiza-card h-100 p-4 d-flex flex-column">

                {{-- Cabecera --}}
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div style="width:44px;height:44px;min-width:44px;background:{{ $voice['postura_color'] }}18;border:1px solid {{ $voice['postura_color'] }}33;border-radius:.5rem;display:flex;align-items:center;justify-content:center;">
                        <i class="{{ $voice['icon'] }}" style="color:{{ $voice['postura_color'] }};font-size:.95rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h3 class="fw-bold mb-0" style="color:#0f172a;font-size:.97rem;">{{ $voice['name'] }}</h3>
                            <span class="badge" style="background:{{ $voice['postura_color'] }}22;color:{{ $voice['postura_color'] }};border:1px solid {{ $voice['postura_color'] }}44;font-size:.7rem;">
                                {{ $voice['postura_label'] }}
                            </span>
                        </div>
                        <p style="color:#64748b;font-size:.8rem;margin:0;line-height:1.4;">{{ $voice['role'] }}</p>
                        <p style="color:#94a3b8;font-size:.75rem;margin:0;">{{ $voice['institution'] }}</p>
                    </div>
                </div>

                {{-- Resumen --}}
                <p style="color:#334155;font-size:.93rem;line-height:1.75;font-style:italic;margin-bottom:1rem;padding-left:.75rem;border-left:3px solid {{ $voice['postura_color'] }};">
                    "{{ $voice['summary'] }}"
                </p>

                {{-- Contexto expandible --}}
                <div class="voice-context" id="ctx-{{ $voice['id'] }}" style="display:none;">
                    <p style="color:#475569;font-size:.88rem;line-height:1.85;margin-bottom:1rem;">
                        {{ $voice['context'] }}
                    </p>
                </div>

                {{-- Punto clave --}}
                <div class="voice-punto" id="punto-{{ $voice['id'] }}" style="display:none;">
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-left:3px solid {{ $voice['postura_color'] }};border-radius:.375rem;padding:.75rem 1rem;margin-bottom:1rem;">
                        <p style="color:#1e293b;font-size:.85rem;font-weight:600;margin-bottom:.2rem;">Punto clave</p>
                        <p style="color:#475569;font-size:.85rem;line-height:1.7;margin:0;">{{ $voice['punto_clave'] }}</p>
                    </div>
                </div>

                {{-- Toggle --}}
                <div class="mt-auto pt-2">
                    <button class="btn btn-sm btn-outline-secondary voice-toggle w-100"
                            data-target="{{ $voice['id'] }}"
                            style="font-size:.8rem;">
                        <i class="fas fa-chevron-down me-1 toggle-icon"></i>
                        <span class="toggle-label">Leer contexto completo</span>
                    </button>
                </div>

            </div>
        </div>
        @endforeach
    </div>

    {{-- Pregunta al lector --}}
    <div class="mb-5 p-4 p-md-5 text-center" style="background:linear-gradient(135deg,#eff6ff 0%,#dbeafe 100%);border:1px solid #bfdbfe;border-radius:.75rem;">
        <i class="fas fa-comments" style="font-size:2rem;color:#3b82f6;margin-bottom:1rem;display:block;"></i>
        <h2 class="fw-bold mb-3" style="color:#1e3a8a;font-size:1.3rem;">¿Y tú qué opinas?</h2>
        <p style="color:#1e40af;font-size:1rem;line-height:1.75;max-width:580px;margin:0 auto 1.5rem;">
            La regulación de la IA nos afecta a todos: cómo se aprueban créditos, cómo se contrata personal, qué se muestra en pantallas. Conocer las posturas es el primer paso para formar la tuya.
        </p>
        <a href="{{ route('regulacion.index') }}" class="btn btn-primary" style="font-size:.9rem;">
            <i class="fas fa-balance-scale me-2"></i>Explorar las regulaciones concretas
        </a>
    </div>

    {{-- Nota editorial --}}
    <div class="p-4" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:.75rem;">
        <p class="mb-0" style="color:#64748b;font-size:.85rem;line-height:1.7;">
            <i class="fas fa-info-circle me-2" style="color:var(--primary-color);"></i>
            <strong style="color:#475569;">Nota editorial:</strong>
            ConocIA presenta estas posturas con fines informativos y de divulgación. Las opiniones están basadas en declaraciones y análisis públicos verificables de cada actor. No tomamos partido por ninguna posición. Nuestro compromiso es con la transparencia del debate público y el derecho de los ciudadanos a estar informados.
        </p>
    </div>

</div>

@push('scripts')
<script>
document.querySelectorAll('.voice-toggle').forEach(btn => {
    btn.addEventListener('click', function () {
        const id      = this.dataset.target;
        const ctx     = document.getElementById('ctx-' + id);
        const punto   = document.getElementById('punto-' + id);
        const icon    = this.querySelector('.toggle-icon');
        const label   = this.querySelector('.toggle-label');
        const open    = ctx.style.display === 'block';

        ctx.style.display   = open ? 'none' : 'block';
        punto.style.display = open ? 'none' : 'block';
        icon.className      = open ? 'fas fa-chevron-down me-1 toggle-icon' : 'fas fa-chevron-up me-1 toggle-icon';
        label.textContent   = open ? 'Leer contexto completo' : 'Ocultar';
    });
});
</script>
@endpush

@endsection
