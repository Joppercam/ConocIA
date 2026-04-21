@extends('layouts.app')

@section('title', 'Agentes de IA — ConocIA')
@section('meta_description', 'Qué son los agentes de IA, cómo evolucionaron y las herramientas más relevantes del ecosistema en español.')

@section('content')

{{-- ── Hero educativo ── --}}
<div style="background:linear-gradient(135deg,#0a1020 0%,#0d1b2e 100%);border-bottom:1px solid #1e2430;">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge mb-3 px-3 py-2" style="background:rgba(56,182,255,.15);color:var(--primary-color);font-size:.75rem;letter-spacing:.05em;">
                    <i class="fas fa-robot me-2"></i>DIRECTORIO DE AGENTES IA
                </span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.2rem;line-height:1.2;">
                    ¿Qué es un<br><span style="color:var(--primary-color);">agente de IA</span>?
                </h1>
                <p class="mb-4" style="color:#94a3b8;font-size:1rem;line-height:1.7;">
                    Un agente de IA es un sistema que no solo responde preguntas — <strong style="color:#cbd5e1;">planifica, decide y actúa</strong> de forma autónoma para alcanzar un objetivo. A diferencia de un chatbot, puede usar herramientas, navegar la web, escribir código, ejecutar tareas encadenadas y corregir sus propios errores en tiempo real.
                </p>
                <p style="color:#64748b;font-size:.9rem;line-height:1.6;">
                    La transición de los LLMs a los agentes es el cambio más profundo que vive la IA en este momento: pasamos de modelos que <em>responden</em> a sistemas que <em>trabajan</em>.
                </p>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                {{-- Diagrama visual simple --}}
                <div class="p-4 rounded-3" style="background:rgba(56,182,255,.05);border:1px solid rgba(56,182,255,.12);">
                    <div class="text-center mb-3" style="color:#64748b;font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;">Cómo funciona un agente</div>
                    @foreach([
                        ['fas fa-eye','#38b6ff','Percibe','Lee el entorno, archivos, APIs'],
                        ['fas fa-brain','#a78bfa','Razona','Planifica pasos con un LLM'],
                        ['fas fa-tools','#00c896','Actúa','Ejecuta herramientas y código'],
                        ['fas fa-sync-alt','#fbbf24','Itera','Evalúa resultados y corrige'],
                    ] as [$icon,$color,$title,$desc])
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                             style="width:36px;height:36px;background:{{ $color }}22;border:1px solid {{ $color }}44;">
                            <i class="{{ $icon }}" style="color:{{ $color }};font-size:.75rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-white" style="font-size:.82rem;">{{ $title }}</div>
                            <div style="color:#64748b;font-size:.72rem;">{{ $desc }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Línea de tiempo: evolución ── --}}
<div style="background:#080e19;border-bottom:1px solid #1e2430;">
    <div class="container py-4">
        <h2 class="fw-bold text-white mb-4" style="font-size:1.1rem;">
            <i class="fas fa-history me-2" style="color:var(--primary-color);"></i>Cómo llegamos hasta aquí
        </h2>
        <div class="row g-3">
            @foreach([
                ['2022','ChatGPT','#38b6ff','Los LLMs llegan al gran público. El modelo responde, pero no actúa.'],
                ['2023','AutoGPT & LangChain','#a78bfa','Primeros experimentos de agentes autónomos. Prometedores pero inestables.'],
                ['2024','RAG + Tool use','#00c896','Los modelos aprenden a usar herramientas reales: búsqueda web, código, APIs.'],
                ['2025','Agentes multimodales','#fbbf24','Los agentes ven, escuchan y actúan sobre interfaces gráficas. Computer use.'],
                ['2026','Ecosistema maduro','#f87171','Frameworks estables (CrewAI, LangGraph), agentes especializados en producción.'],
            ] as [$year,$name,$color,$desc])
            <div class="col-md-6 col-lg" style="min-width:0;">
                <div class="h-100 rounded-2 p-3" style="background:#0d1524;border:1px solid #1e2d47;border-top:3px solid {{ $color }};">
                    <div class="fw-bold mb-1" style="color:{{ $color }};font-size:.8rem;">{{ $year }}</div>
                    <div class="fw-semibold text-white mb-1" style="font-size:.88rem;">{{ $name }}</div>
                    <div style="color:#64748b;font-size:.78rem;line-height:1.5;">{{ $desc }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Noticias recientes sobre agentes ── --}}
@if($agentNews->isNotEmpty())
<div style="background:#0a0f1a;border-bottom:1px solid #1e2430;">
    <div class="container py-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="fw-bold text-white mb-0" style="font-size:1.05rem;">
                <i class="fas fa-newspaper me-2" style="color:var(--primary-color);"></i>Últimas noticias sobre agentes
            </h2>
            <a href="{{ route('news.index') }}" style="color:var(--primary-color);font-size:.78rem;" class="text-decoration-none">
                Ver más <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="row g-3">
            @foreach($agentNews as $item)
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('news.show', $item->slug) }}" class="text-decoration-none d-block h-100">
                    <div class="h-100 rounded-2 p-3" style="background:#0d1524;border:1px solid #1e2d47;transition:border-color .2s;"
                         onmouseover="this.style.borderColor='rgba(56,182,255,.3)'" onmouseout="this.style.borderColor='#1e2d47'">
                        @if($item->image)
                        <img src="{{ $item->image }}" alt="{{ $item->title }}"
                             style="width:100%;height:100px;object-fit:cover;border-radius:6px;margin-bottom:.75rem;">
                        @endif
                        <div class="fw-semibold text-white mb-1" style="font-size:.82rem;line-height:1.4;">
                            {{ Str::limit($item->title, 80) }}
                        </div>
                        <div style="color:#475569;font-size:.72rem;">{{ $item->published_at?->diffForHumans() }}</div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── Directorio de agentes ── --}}
<div class="container py-5">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fw-bold mb-0" style="font-size:1.2rem;">
            Directorio de agentes y frameworks
        </h2>
        <span style="color:#64748b;font-size:.82rem;">{{ $agents->total() }} herramientas</span>
    </div>

    {{-- Filtros --}}
    @if($categories->isNotEmpty())
    <div class="d-flex flex-wrap gap-2 mb-4">
        <button class="btn btn-sm btn-primary filter-btn active" data-filter="all">Todos</button>
        @foreach($categories as $cat)
        <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="{{ $cat }}">
            {{ \App\Models\AiAgent::categoryLabels()[$cat] ?? ucfirst($cat) }}
        </button>
        @endforeach
    </div>
    @endif

    @if($agents->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="fas fa-robot fa-3x mb-3 d-block opacity-25"></i>
        <p>Próximamente agentes de IA destacados.</p>
        <p class="small">Ejecutá <code>php artisan agents:fetch</code> para importar.</p>
    </div>
    @else
    <div class="row g-3" id="agents-grid">
        @foreach($agents as $agent)
        <div class="col-md-6 col-lg-4 agent-card" data-category="{{ $agent->category }}">
            <a href="{{ route('agents.show', $agent) }}" class="text-decoration-none d-block h-100">
                <div class="card h-100 border-0 shadow-sm" style="border-radius:12px;transition:transform .15s,box-shadow .15s;"
                     onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.12)'"
                     onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3 mb-2">
                            @if($agent->logo)
                                <img src="{{ $agent->logo }}" alt="{{ $agent->name }}"
                                     style="width:40px;height:40px;object-fit:contain;border-radius:8px;flex-shrink:0;">
                            @else
                                <div style="width:40px;height:40px;background:rgba(56,182,255,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid rgba(56,182,255,.15);">
                                    <i class="fas fa-robot" style="color:var(--primary-color);font-size:.85rem;"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1 min-width-0">
                                <div class="fw-semibold mb-0" style="font-size:.92rem;line-height:1.3;">{{ $agent->name }}</div>
                                @if($agent->stars_github)
                                <span style="color:#94a3b8;font-size:.72rem;">
                                    <i class="fas fa-star me-1" style="color:#fbbf24;font-size:.6rem;"></i>{{ $agent->formatted_stars }}
                                </span>
                                @endif
                            </div>
                        </div>

                        @if($agent->tagline)
                        <p class="text-muted mb-2" style="font-size:.8rem;line-height:1.5;">{{ Str::limit($agent->tagline, 90) }}</p>
                        @endif

                        <div class="d-flex flex-wrap gap-1">
                            @if($agent->category)
                            <span class="badge bg-secondary" style="font-size:.65rem;">{{ $agent->category_label }}</span>
                            @endif
                            <span class="badge" style="background:{{ $agent->pricing_color }};font-size:.65rem;">{{ $agent->pricing_label }}</span>
                            @if($agent->type === 'open-source')
                            <span class="badge bg-success" style="font-size:.65rem;">Open Source</span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $agents->links() }}</div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('active','btn-primary');
            b.classList.add('btn-outline-secondary');
        });
        this.classList.add('active','btn-primary');
        this.classList.remove('btn-outline-secondary');

        const filter = this.dataset.filter;
        document.querySelectorAll('.agent-card').forEach(card => {
            card.style.display = (filter === 'all' || card.dataset.category === filter) ? '' : 'none';
        });
    });
});
</script>
@endpush
