@extends('layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('title', $video->title . ' - ConocIA')

@php
    $vidMetaDesc  = $video->description ? Str::limit($video->description, 160) : 'Video sobre inteligencia artificial en ConocIA';
    $vidMetaImage = $video->thumbnail_url ?? asset('images/defaults/social-share.jpg');
    $vidMetaUrl   = route('videos.show', $video->routeParameters());
    $vidMetaPublished = $video->published_at?->toIso8601String() ?? now()->toIso8601String();
    $vidMetaKeywords  = 'video, inteligencia artificial, tecnología'
        . ($video->categories->isNotEmpty() ? ', ' . $video->categories->pluck('name')->implode(', ') : '')
        . ($video->tags->isNotEmpty() ? ', ' . $video->tags->pluck('name')->implode(', ') : '');

    // Convertir segundos a duración ISO 8601 (PT1H2M3S)
    $vidDuration = '';
    if ($video->duration_seconds) {
        $h = intdiv($video->duration_seconds, 3600);
        $m = intdiv($video->duration_seconds % 3600, 60);
        $s = $video->duration_seconds % 60;
        $vidDuration = 'PT' . ($h ? "{$h}H" : '') . ($m ? "{$m}M" : '') . ($s ? "{$s}S" : '');
    }
@endphp

@section('meta')
    @include('partials.seo-meta', [
        'metaTitle'       => $video->title . ' - ConocIA',
        'metaDescription' => $vidMetaDesc,
        'metaKeywords'    => $vidMetaKeywords,
        'metaImage'       => $vidMetaImage,
        'metaType'        => 'video.other',
        'metaUrl'         => $vidMetaUrl,
        'metaAuthor'      => 'ConocIA',
        'metaPublished'   => $vidMetaPublished,
        'metaModified'    => null,
        'metaRobots'      => $shouldIndex ? 'index, follow' : 'noindex, follow',
    ])
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "VideoObject",
        "name": "{{ addslashes($video->title) }}",
        "description": "{{ addslashes($vidMetaDesc) }}",
        "thumbnailUrl": "{{ $vidMetaImage }}",
        "uploadDate": "{{ $vidMetaPublished }}",
        @if($vidDuration)
        "duration": "{{ $vidDuration }}",
        @endif
        "embedUrl": "{{ $video->embed_url }}",
        "url": "{{ $vidMetaUrl }}",
        "interactionStatistic": {
            "@type": "InteractionCounter",
            "interactionType": "https://schema.org/WatchAction",
            "userInteractionCount": {{ $video->view_count ?? 0 }}
        },
        "publisher": {
            "@type": "Organization",
            "name": "ConocIA",
            "@id": "{{ url('/') }}/#organization",
            "logo": { "@type": "ImageObject", "url": "{{ asset('storage/images/logo.png') }}" }
        },
        "inLanguage": "es-CL"
    }
    </script>
    @include('partials.schema-breadcrumb', ['crumbs' => [
        ['name' => 'Inicio',  'url' => url('/')],
        ['name' => 'Videos',  'url' => route('videos.index')],
        ['name' => $video->title],
    ]])
@endsection

@section('content')
{{-- Page header --}}
<div style="background:var(--dark-bg);border-bottom:1px solid #2a2a2a;" class="py-3 mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('videos.index') }}" class="text-secondary text-decoration-none">Videos</a></li>
                <li class="breadcrumb-item active text-light" aria-current="page">{{ Str::limit($video->title, 50) }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <div class="col-lg-8">
            {{-- Reproductor de video --}}
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="ratio ratio-16x9">
                    <iframe src="{{ $video->embed_url }}" allowfullscreen class="rounded-top"></iframe>
                </div>
                <div class="card-body">
                    @unless($shouldIndex)
                    <div class="alert alert-secondary border-0 mb-3" style="background:#f4f6f8;color:#495057;">
                        Esta página de video sigue visible para usuarios, pero no se está enviando a indexación mientras no tenga suficiente contexto editorial.
                    </div>
                    @endunless

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            @foreach($video->categories as $category)
                            <a href="{{ route('videos.category', $category->id) }}" class="badge bg-light text-dark text-decoration-none me-2">
                                <i class="fas fa-folder me-1"></i> {{ $category->name }}
                            </a>
                            @endforeach
                        </div>
                        <div>
                            <span class="badge bg-{{ $video->platform->code === 'youtube' ? 'danger' : ($video->platform->code === 'vimeo' ? 'info' : 'primary') }}">
                                <i class="fab fa-{{ $video->platform->code }} me-1"></i> {{ $video->platform->name }}
                            </span>
                        </div>
                    </div>
                    
                    <h1 class="video-title h3 fw-bold mb-3">{{ $video->title }}</h1>
                    
                    <div class="video-meta d-flex flex-wrap mb-3">
                        <div class="me-4 text-muted">
                            <i class="far fa-calendar-alt me-1"></i> {{ $video->published_at->format('d M Y') }}
                        </div>
                        <div class="me-4 text-muted">
                            <i class="far fa-clock me-1"></i> {{ formatDuration($video->duration_seconds) }}
                        </div>
                        <div class="text-muted">
                            <i class="far fa-eye me-1"></i> {{ number_format($video->view_count) }} reproducciones
                        </div>
                    </div>
                    
                    <div class="video-description">
                        <p class="mb-0">{{ $video->description }}</p>
                    </div>

                    {{-- AI Summary panel --}}
                    @if($video->hasAiSummary())
                    <div class="mt-3 rounded-3 p-3" style="background:linear-gradient(135deg,#0d1117,#161b2e);border:1px solid rgba(56,182,255,.2);">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:28px;height:28px;background:rgba(56,182,255,.15);border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-robot" style="color:var(--primary-color);font-size:.75rem;"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-white" style="font-size:.82rem;">Resumen generado por IA</div>
                                <div style="color:#666;font-size:.7rem;">Puntos clave del video</div>
                            </div>
                            <span class="ms-auto badge" style="background:rgba(56,182,255,.1);color:var(--primary-color);border:1px solid rgba(56,182,255,.2);font-size:.65rem;">
                                Gemini AI
                            </span>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            @foreach(explode('|||', $video->ai_summary) as $i => $point)
                            <div class="d-flex align-items-start gap-2">
                                <div style="width:20px;height:20px;background:rgba(56,182,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                                    <span style="color:var(--primary-color);font-size:.65rem;font-weight:700;">{{ $i+1 }}</span>
                                </div>
                                <span style="color:#ccc;font-size:.84rem;line-height:1.5;">{{ trim($point) }}</span>
                            </div>
                            @endforeach
                        </div>
                        @if($video->ai_keywords && count($video->ai_keywords))
                        <div class="d-flex flex-wrap gap-2 mt-3 pt-3" style="border-top:1px solid rgba(56,182,255,.1);">
                            @foreach($video->ai_keywords as $kw)
                            <span style="background:rgba(56,182,255,.08);color:var(--primary-color);border:1px solid rgba(56,182,255,.15);border-radius:20px;padding:2px 10px;font-size:.72rem;">
                                {{ $kw }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="video-tags">
                            @foreach($video->tags as $tag)
                            <a href="{{ route('videos.tag', $tag->id) }}" class="btn btn-sm btn-outline-secondary mb-1 me-1 rounded-pill">
                                <i class="fas fa-tag me-1"></i> {{ $tag->name }}
                            </a>
                            @endforeach
                        </div>
                        <div class="video-share">
                            <button type="button" class="btn btn-sm btn-primary rounded-circle me-1" onclick="copyVideoUrl()" title="Copiar enlace">
                                <i class="fas fa-link"></i>
                            </button>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-sm btn-primary rounded-circle me-1" title="Compartir en Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($video->title) }}" target="_blank" class="btn btn-sm btn-info rounded-circle me-1" title="Compartir en Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($video->title . ' ' . url()->current()) }}" target="_blank" class="btn btn-sm btn-success rounded-circle" title="Compartir en WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Comentarios --}}
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Comentarios</h5>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Sistema de comentarios aquí -->
                    @if(Auth::check())
                    <form action="{{ route('videos.comment', $video->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control" name="content" rows="3" placeholder="Escribe tu comentario..."></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="far fa-paper-plane me-1"></i> Comentar
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Debes <a href="{{ route('login') }}">iniciar sesión</a> para comentar.
                    </div>
                    @endif
                    
                    <div class="comments-list">
                        <!-- Si tienes un sistema de comentarios, aquí irían los comentarios del video -->
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No hay comentarios aún. ¡Sé el primero en comentar!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Videos relacionados --}}
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Videos relacionados</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($relatedVideos as $relatedVideo)
                        <a href="{{ route('videos.show', $relatedVideo->routeParameters()) }}" class="list-group-item list-group-item-action py-3">
                            <div class="row g-0">
                                <div class="col-4 position-relative">
                                    <img src="{{ $relatedVideo->thumbnail_url }}" alt="{{ $relatedVideo->title }}" class="img-fluid rounded">
                                    <div class="position-absolute bottom-0 end-0 bg-dark text-white px-1 py-0 m-1 rounded fs-9">
                                        <i class="fas fa-clock me-1"></i> {{ formatDuration($relatedVideo->duration_seconds) }}
                                    </div>
                                </div>
                                <div class="col-8 ps-3">
                                    <h4 class="h6 mb-1 line-clamp-2">{{ $relatedVideo->title }}</h4>
                                    <div class="d-flex flex-column text-muted small">
                                        <span><i class="fab fa-{{ $relatedVideo->platform->code }} me-1"></i> {{ $relatedVideo->platform->name }}</span>
                                        <span><i class="far fa-eye me-1"></i> {{ number_format($relatedVideo->view_count) }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No hay videos relacionados disponibles.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <a href="{{ route('videos.index') }}" class="btn btn-sm btn-outline-primary">
                        Ver más videos <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
            
            {{-- Categorías de videos --}}
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Categorías</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($video->categories as $category)
                        <a href="{{ route('videos.category', $category->id) }}" class="btn btn-sm btn-outline-secondary mb-2 rounded-pill">
                            <i class="fas fa-folder me-1"></i> {{ $category->name }}
                        </a>
                        @endforeach
                        
                        @php
                            // Obtener algunas categorías populares adicionales
                            $additionalCategories = \App\Models\VideoCategory::withCount('videos')
                                ->whereNotIn('id', $video->categories->pluck('id')->toArray())
                                ->orderBy('videos_count', 'desc')
                                ->take(5)
                                ->get();
                        @endphp
                        
                        @foreach($additionalCategories as $category)
                        <a href="{{ route('videos.category', $category->id) }}" class="btn btn-sm btn-outline-secondary mb-2 rounded-pill">
                            <i class="fas fa-folder me-1"></i> {{ $category->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            
            {{-- Videos populares --}}
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Más populares</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @php
                            // Obtener algunos videos populares
                            $popularVideos = \App\Models\Video::with('platform')
                                ->where('id', '!=', $video->id)
                                ->orderBy('view_count', 'desc')
                                ->take(5)
                                ->get();
                        @endphp
                        
                        @forelse($popularVideos as $popularVideo)
                        <a href="{{ route('videos.show', $popularVideo->routeParameters()) }}" class="list-group-item list-group-item-action py-3">
                            <div class="row g-0">
                                <div class="col-4 position-relative">
                                    <img src="{{ $popularVideo->thumbnail_url }}" alt="{{ $popularVideo->title }}" class="img-fluid rounded">
                                    <div class="position-absolute bottom-0 end-0 bg-dark text-white px-1 py-0 m-1 rounded fs-9">
                                        <i class="fas fa-clock me-1"></i> {{ formatDuration($popularVideo->duration_seconds) }}
                                    </div>
                                </div>
                                <div class="col-8 ps-3">
                                    <h4 class="h6 mb-1 line-clamp-2">{{ $popularVideo->title }}</h4>
                                    <div class="d-flex flex-column text-muted small">
                                        <span><i class="fab fa-{{ $popularVideo->platform->code }} me-1"></i> {{ $popularVideo->platform->name }}</span>
                                        <span><i class="far fa-eye me-1"></i> {{ number_format($popularVideo->view_count) }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No hay videos populares disponibles.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <a href="{{ route('videos.popular') }}" class="btn btn-sm btn-outline-primary">
                        Ver más populares <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>{{-- /container --}}
@endsection

@push('styles')
<style>
/* Estilos específicos para la página de detalles del video */
.video-title {
    line-height: 1.3;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.video-description {
    white-space: pre-line;
}

.fs-9 {
    font-size: 0.75rem !important;
}
</style>
@endpush

@push('scripts')
<script>
function formatDuration(seconds) {
    if (!seconds) return '0:00';
    
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;
    const remainingSeconds = seconds % 60;
    
    if (hours > 0) {
        return `${hours}:${remainingMinutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
    }
    
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
}

function copyVideoUrl() {
    // Crear un elemento de texto temporal
    const el = document.createElement('textarea');
    el.value = window.location.href;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    
    // Mostrar un mensaje de confirmación
    alert('Enlace copiado al portapapeles');
}
</script>
@endpush
