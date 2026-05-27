@extends('admin.layouts.app')

@section('title', 'Briefing Diario')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Briefing Diario</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- Estado actual --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Estado del último briefing</h5>
                </div>
                <div class="card-body">
                    @if($briefing)
                    <table class="table table-sm">
                        <tr>
                            <th class="text-muted fw-normal" style="width:160px;">Fecha</th>
                            <td>
                                {{ $briefing->date->format('d/m/Y') }}
                                @if($briefing->date->isToday())
                                    <span class="badge bg-success ms-1">Hoy</span>
                                @else
                                    <span class="badge bg-warning text-dark ms-1">No es de hoy</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Generado</th>
                            <td>{{ $briefing->generated_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Contenidos</th>
                            <td>{{ $briefing->news_count ?? 0 }} ítems · ~{{ $briefing->estimated_minutes }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Script</th>
                            <td>{{ $briefing->script ? str_word_count($briefing->script) . ' palabras' : '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Audio MP3</th>
                            <td>
                                @if($briefing->audio_url)
                                    <span class="badge bg-success">Generado</span>
                                    <a href="{{ $briefing->audio_url }}" target="_blank" class="ms-2 small">Escuchar</a>
                                @else
                                    <span class="badge bg-danger">Sin audio</span>
                                    <span class="text-muted ms-2 small">GOOGLE_TTS_KEY puede no estar configurada</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    @if($briefing->script)
                    <div class="mt-3">
                        <p class="text-muted small fw-semibold mb-1">Script (preview):</p>
                        <div class="p-3 rounded" style="background:#f8fafc;font-size:.82rem;line-height:1.7;max-height:200px;overflow-y:auto;color:#475569;">
                            {{ Str::limit($briefing->script, 600) }}
                        </div>
                    </div>
                    @endif

                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-microphone-slash fa-2x mb-2 d-block"></i>
                        No hay ningún briefing generado todavía.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="col-lg-4">

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Generar briefing</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Genera el script de hoy usando las noticias, papers y conceptos más recientes. Si ya existe uno para hoy, usa "Forzar regeneración".</p>
                    <form method="POST" action="{{ route('admin.briefing.generate') }}">
                        @csrf
                        <input type="hidden" name="force" value="0">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-robot me-1"></i>Generar script de hoy
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.briefing.generate') }}">
                        @csrf
                        <input type="hidden" name="force" value="1">
                        <button type="submit" class="btn btn-outline-warning w-100 btn-sm">
                            <i class="fas fa-redo me-1"></i>Forzar regeneración
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Generar audio MP3</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Convierte el script del último briefing a audio MP3 usando Google Cloud TTS. Requiere <code>GOOGLE_TTS_KEY</code> configurada en el servidor.</p>
                    <form method="POST" action="{{ route('admin.briefing.audio') }}">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" {{ !$briefing || !$briefing->script ? 'disabled' : '' }}>
                            <i class="fas fa-volume-up me-1"></i>Generar audio MP3
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Configuración</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            @if(config('services.google_tts.key'))
                                <span class="badge bg-success me-1">OK</span> GOOGLE_TTS_KEY configurada
                            @else
                                <span class="badge bg-danger me-1">FALTA</span> GOOGLE_TTS_KEY no configurada
                            @endif
                        </li>
                        <li class="mb-2">
                            @if(config('services.gemini.api_key'))
                                <span class="badge bg-success me-1">OK</span> GEMINI_API_KEY configurada
                            @else
                                <span class="badge bg-danger me-1">FALTA</span> GEMINI_API_KEY no configurada
                            @endif
                        </li>
                        <li>
                            @if(config('anthropic.api_key') || env('ANTHROPIC_API_KEY'))
                                <span class="badge bg-success me-1">OK</span> ANTHROPIC_API_KEY configurada
                            @else
                                <span class="badge bg-danger me-1">FALTA</span> ANTHROPIC_API_KEY no configurada
                            @endif
                        </li>
                    </ul>
                    <hr class="my-3">
                    <p class="text-muted mb-1" style="font-size:.75rem;"><strong>Scheduler:</strong> El briefing se genera automáticamente cada día a las 08:30.</p>
                    <p class="text-muted mb-0" style="font-size:.75rem;"><strong>Voz:</strong> es-US-Neural2-B (Google Cloud TTS)</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
