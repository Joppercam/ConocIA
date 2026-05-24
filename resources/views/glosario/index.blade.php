@extends('layouts.app')

@section('title', 'Glosario de IA — Términos clave explicados | ConocIA')
@section('meta_description', 'Glosario de inteligencia artificial en español: ' . $total . ' términos clave explicados de forma simple, desde algoritmo hasta transformer.')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">APRENDE</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.4rem;line-height:1.15;">Glosario de IA</h1>
                <p style="color:#94a3b8;font-size:1.05rem;line-height:1.7;max-width:580px;">
                    Términos clave de inteligencia artificial explicados de forma simple. {{ $total }} definiciones accesibles para cualquier persona.
                </p>
            </div>
            <div class="col-lg-4 d-none d-lg-flex justify-content-end">
                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:1rem;padding:2rem;" class="text-center">
                    <div class="fw-bold text-white" style="font-size:2.5rem;line-height:1;">{{ $total }}</div>
                    <div style="color:#64748b;font-size:.88rem;margin-top:.3rem;">términos definidos</div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">

    {{-- Buscador --}}
    <div class="mb-4">
        <input type="text" id="glossary-search" class="form-control form-control-lg"
               placeholder="Buscar término..."
               style="border:2px solid #e2e8f0;border-radius:.75rem;font-size:1rem;padding:.75rem 1.25rem;">
    </div>

    {{-- Índice A–Z --}}
    <div class="d-flex flex-wrap gap-2 mb-5" id="letter-index">
        @foreach($letters as $letter)
            <a href="#letter-{{ $letter }}" class="btn btn-sm btn-outline-secondary"
               style="min-width:36px;font-weight:600;">{{ $letter }}</a>
        @endforeach
    </div>

    {{-- Términos por letra --}}
    <div id="glossary-list">
        @foreach($byLetter as $letter => $terms)
            <div class="letter-group mb-5" data-letter="{{ $letter }}">
                <h2 id="letter-{{ $letter }}" class="fw-bold mb-4"
                    style="color:#0f172a;font-size:1.8rem;border-bottom:2px solid rgba(56,182,255,.3);padding-bottom:.5rem;">
                    {{ $letter }}
                </h2>
                <div class="row g-3">
                    @foreach($terms as $term)
                        <div class="col-12 term-item" data-term="{{ strtolower($term->term) }}">
                            <div class="profundiza-card p-4">
                                <div class="d-flex flex-wrap align-items-start gap-2 mb-2">
                                    <h3 class="fw-bold mb-0" style="color:#0f172a;font-size:1.05rem;">{{ $term->term }}</h3>
                                    <span class="badge" style="background:{{ $term->difficulty_color }}22;color:{{ $term->difficulty_color }};border:1px solid {{ $term->difficulty_color }}44;font-size:.72rem;">
                                        {{ $term->difficulty_label }}
                                    </span>
                                </div>
                                <p style="color:#475569;font-size:.93rem;line-height:1.75;margin:0;">{{ $term->definition }}</p>
                                @if($term->explanation)
                                    <p style="color:#64748b;font-size:.88rem;line-height:1.7;margin-top:.5rem;">{{ $term->explanation }}</p>
                                @endif
                                @if($term->related_concept_url)
                                    <div class="mt-2">
                                        <a href="{{ $term->related_concept_url }}" class="btn btn-sm btn-outline-primary" style="font-size:.8rem;">
                                            <i class="fas fa-book-open me-1"></i>Ver concepto completo
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Sin resultados --}}
    <div id="no-results" class="text-center py-5 d-none">
        <i class="fas fa-search" style="font-size:2.5rem;color:#cbd5e1;margin-bottom:1rem;display:block;"></i>
        <p style="color:#64748b;">No se encontraron términos que coincidan con tu búsqueda.</p>
    </div>

    {{-- CTA --}}
    <div style="background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%);border-radius:.75rem;padding:2rem;border:1px solid #bae6fd;margin-top:2rem;" class="text-center">
        <p style="color:#0369a1;font-size:.95rem;margin:0;">
            <i class="fas fa-lightbulb me-2"></i>
            ¿Falta algún término? Escríbenos a <a href="mailto:contacto@conocia.cl" style="color:#0369a1;font-weight:600;">contacto@conocia.cl</a>
        </p>
    </div>

</div>

@push('scripts')
<script>
document.getElementById('glossary-search').addEventListener('input', function () {
    const q      = this.value.toLowerCase().trim();
    const items  = document.querySelectorAll('.term-item');
    const groups = document.querySelectorAll('.letter-group');
    let   found  = 0;

    items.forEach(item => {
        const match = item.dataset.term.includes(q);
        item.style.display = match ? '' : 'none';
        if (match) found++;
    });

    groups.forEach(group => {
        const visible = group.querySelectorAll('.term-item:not([style*="none"])').length;
        group.style.display = visible > 0 ? '' : 'none';
    });

    document.getElementById('no-results').classList.toggle('d-none', found > 0);
    document.getElementById('letter-index').style.display = q ? 'none' : '';
});
</script>
@endpush

@endsection
