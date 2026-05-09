<!-- resources/views/news/show.blade.php -->
@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

@section('title', method_exists($article, 'seoTitle') ? $article->seoTitle() : ($article->title . ' - ConocIA'))
@php
// Preparar metadatos SEO para esta noticia
$metaTitle = method_exists($article, 'seoTitle') ? $article->seoTitle() : $article->title . ' - ConocIA';
$metaDescription = method_exists($article, 'seoDescription')
    ? $article->seoDescription()
    : ($article->summary ?? $article->excerpt ?? substr(strip_tags($article->content), 0, 160));
$metaKeywords = is_object($article->category) ? $article->category->name : 'noticias, tecnología, IA';

if (!empty($article->keywords)) {
    $metaKeywords .= ', ' . $article->keywords;
}

// Agregar tags si existen
if(isset($article->tags) && is_countable($article->tags) && count($article->tags) > 0) {
    $tagNames = is_string($article->tags)
        ? $article->tags
        : collect($article->tags)->pluck('name')->implode(', ');
    $metaKeywords .= ', ' . $tagNames;
}

// Imagen para metadatos OG
$metaImage = asset('images/defaults/social-share.jpg');
if (!empty($article->image) && !str_contains($article->image, 'default') && !str_contains($article->image, 'placeholder')) {
    if (Str::startsWith($article->image, ['http://', 'https://'])) {
        $metaImage = $article->image; // URL completa (R2, CDN, externa)
    } elseif (Str::startsWith($article->image, 'storage/')) {
        $metaImage = asset($article->image);
    }
}

$metaType = 'article';
$metaUrl = route('news.show', $article->slug ?? $article->id);
$articleAuthorName = is_object($article->author)
    ? ($article->author->name ?? 'ConocIA')
    : ($article->author ?: 'ConocIA');
$articleAuthorAvatar = is_object($article->author)
    ? ($article->author->avatar ?? null)
    : null;
$metaAuthor = $articleAuthorName;
$metaPublished = $article->published_at ? $article->published_at->toIso8601String() : $article->created_at->toIso8601String();
$metaModified = $article->updated_at ? $article->updated_at->toIso8601String() : null;
$contentLooksIncomplete = news_content_looks_incomplete($article->content ?? null);
$articleSummary = news_editorial_teaser($article->summary ?? null, $article->excerpt ?? null, $article->content ?? null, 260);
@endphp

@section('meta')
    @include('partials.seo-meta', [
        'metaTitle' => $metaTitle,
        'metaDescription' => $metaDescription,
        'metaKeywords' => $metaKeywords,
        'metaImage' => $metaImage,
        'metaType' => $metaType,
        'metaUrl' => $metaUrl,
        'metaAuthor' => $metaAuthor,
        'metaPublished' => $metaPublished,
        'metaModified' => $metaModified
    ])
    
    @include('partials.schema-news', ['article' => $article])
    @include('partials.schema-breadcrumb', ['crumbs' => [
        ['name' => 'Inicio',    'url' => url('/')],
        ['name' => 'Noticias',  'url' => route('news.index')],
        ['name' => $article->category?->name ?? 'IA', 'url' => $article->category ? route('news.by.category', $article->category->slug) : null],
        ['name' => $article->title],
    ]])
@endsection
@section('reading_progress', true)
@section('content')
{{-- Breadcrumb bar (dark, matching index) --}}
<div style="background:var(--dark-bg);border-bottom:1px solid #2a2a2a;" class="py-3 mb-4">
    <div class="container-fluid px-3 px-lg-4 px-xl-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('news.index') }}" class="text-secondary text-decoration-none">Noticias</a></li>
                @if(is_object($article->category))
                <li class="breadcrumb-item">
                    <a href="{{ route('news.category', $article->category->slug) }}"
                       class="text-decoration-none"
                       style="color:{{ $article->category->color ?? 'var(--primary-color)' }};">
                        {{ $article->category->name }}
                    </a>
                </li>
                @endif
                <li class="breadcrumb-item active text-light" aria-current="page">{{ Str::limit($article->title, 40) }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 px-xl-5 py-4">
    <div class="row g-4">
        <!-- Contenido Principal (Izquierda) -->
        <div class="col-lg-9">
            {{-- Categoría y Título --}}
            <div class="mb-3">
                @php $catColor = is_object($article->category) ? ($article->category->color ?? 'var(--primary-color)') : 'var(--primary-color)'; @endphp
                @if(is_object($article->category))
                <a href="{{ route('news.category', $article->category->slug) }}"
                   class="badge text-decoration-none mb-2 d-inline-block"
                   style="background:{{ $catColor }};font-size:.78rem;">
                    {{ $article->category->name }}
                </a>
                @else
                <span class="badge mb-2" style="background:var(--primary-color);font-size:.78rem;">{{ $article->category }}</span>
                @endif
                @if(($isPremiumContent ?? false))
                    <span class="badge bg-warning text-dark mb-2 d-inline-block ms-1" style="font-size:.78rem;">PRO</span>
                @endif
                <h1 class="mb-2">{{ $article->title }}</h1>
                @if($articleSummary)
                <p class="mb-3 news-summary-text" style="font-size:1rem;line-height:1.7;">{{ $articleSummary }}</p>
                @endif
                
                <!-- Autor y fecha -->
                <div class="d-flex align-items-center text-muted small mb-3">
                    <img src="{{ $articleAuthorAvatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($articleAuthorName) . '&background=random' }}" 
                        class="rounded-circle me-2" width="24" height="24" alt="{{ $articleAuthorName }}">
                    <span>Por {{ $articleAuthorName }}</span>
                    <span class="mx-2">•</span>
                    <span><i class="far fa-calendar-alt me-1"></i> {{ $article->created_at->locale('es')->isoFormat('D MMM, YYYY') }}</span>
                    <span class="mx-2">•</span>
                    <span><i class="far fa-clock me-1"></i> {{ $article->reading_time }} min de lectura</span>
                </div>

                {{-- Compartir + Guardar --}}
                <div class="d-flex align-items-center gap-2 mb-3">
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 bookmark-btn"
                            data-bookmark-id="{{ $article->id }}"
                            data-bookmark-title="{{ addslashes($article->title) }}"
                            data-bookmark-url="{{ route('news.show', $article->slug) }}"
                            data-bookmark-category="{{ $article->category?->name }}"
                            data-bookmark-image="{{ $article->image ?? '' }}"
                            title="Guardar artículo"
                            style="font-size:.78rem;">
                        <i class="far fa-bookmark me-1"></i>Guardar
                    </button>
                    <span class="small text-muted">Compartir:</span>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('news.show', $article)) }}&text={{ urlencode($article->title) }}" 
                    class="btn btn-sm btn-outline-secondary rounded-circle" target="_blank" rel="noopener">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('news.show', $article)) }}" 
                    class="btn btn-sm btn-outline-secondary rounded-circle" target="_blank" rel="noopener">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('news.show', $article)) }}&title={{ urlencode($article->title) }}" 
                    class="btn btn-sm btn-outline-secondary rounded-circle" target="_blank" rel="noopener">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="mailto:?subject={{ $article->title }}&body={{ route('news.show', $article) }}" 
                    class="btn btn-sm btn-outline-secondary rounded-circle">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
            </div>

            <!-- Verificación de imagen mejorada con rutas alternativas -->
            @php
                // Variable para controlar si mostrar o no la imagen
                $showImage = false;
                $imageSrc = null;
                
                // Solo verificar si existe una imagen para evitar consultas innecesarias
                if (!empty($article->image) &&
                    $article->image != 'default.jpg' &&
                    !str_contains($article->image, 'default') &&
                    !str_contains($article->image, 'placeholder')) {

                    $imageName = $article->image;

                    if (Str::startsWith($imageName, ['http://', 'https://'])) {
                        // URL completa (R2, CDN o imagen externa)
                        $showImage = true;
                        $imageSrc  = $imageName;
                    } elseif (Str::startsWith($imageName, 'storage/')) {
                        $showImage = true;
                        $imageSrc  = asset($imageName);
                    } else {
                        // Probar múltiples rutas posibles
                        foreach (["images/news/{$imageName}", "news/{$imageName}", $imageName] as $path) {
                            if (Storage::disk('public')->exists($path)) {
                                $showImage = true;
                                $imageSrc  = asset('storage/' . $path);
                                break;
                            }
                        }
                    }
                    
                    // Si todavía no encontramos la imagen, usar getImageUrl como respaldo
                    if (!$showImage && isset($getImageUrl) && is_callable($getImageUrl)) {
                        $imageSrc = $getImageUrl($article->image, 'news', 'large');
                        // Verificar que la URL generada no contiene 'default'
                        if (!str_contains($imageSrc, 'default')) {
                            $showImage = true;
                        }
                    }
                }
            @endphp

            @if($showImage)
            <div class="mb-4 article-hero-media" style="display:none;">
                <img src="{{ $imageSrc }}"
                    class="img-fluid rounded w-100"
                    alt="{{ $article->title }}"
                    fetchpriority="high"
                    loading="eager"
                    onload="this.closest('.article-hero-media').style.display='block';"
                    onerror="this.closest('.article-hero-media').remove();">
                @if($article->image_caption)
                    <p class="text-muted small mt-1 fst-italic">{{ $article->image_caption }}</p>
                @endif
            </div>
            @endif

            <!-- Fuente con URL si existe -->
            @if($article->source)
            <div class="mb-4">
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-dark me-2">
                        <i class="fas fa-external-link-alt me-1"></i> Fuente:
                    </span>
                    @if($article->source_url)
                        <a href="{{ $article->source_url }}" class="text-primary" target="_blank">{{ $article->source }}</a>
                    @else
                        {{ $article->source }}
                    @endif
                </div>
            </div>
            @endif

            <!-- Resumen -->
            @if($articleSummary)
            <div class="mb-4 rounded-3 p-4" style="background:rgba(56,182,255,.08);border-left:4px solid var(--primary-color);">
                <h6 class="fw-semibold mb-2" style="color:var(--primary-color);font-size:.82rem;letter-spacing:.05em;text-transform:uppercase;">Resumen</h6>
                <p class="mb-0 news-summary-text" style="font-size:.97rem;line-height:1.7;">{{ $articleSummary }}</p>
            </div>
            @endif

            @if(isset($insights) && $insights->count() > 0)
            <div class="mb-4 rounded-3 p-4" style="background:linear-gradient(135deg,rgba(10,16,32,.96),rgba(15,27,45,.96));border:1px solid rgba(56,182,255,.22);position:relative;overflow:hidden;">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <div class="small text-uppercase fw-bold" style="color:var(--primary-color);letter-spacing:.06em;">ConocIA Insights</div>
                        <h2 class="h5 text-white mb-1">Lectura estratégica generada por IA</h2>
                        <p class="text-muted mb-0" style="font-size:.9rem;">Resumen ejecutivo, impacto y señal accionable para entender por qué importa esta noticia.</p>
                    </div>
                    <span class="badge bg-warning text-dark">PRO</span>
                </div>

                @foreach($insights as $insight)
                    @php
                        $locked = $insight->is_premium && !$canAccessPremiumInsights;
                    @endphp
                    <div class="rounded-3 p-3 mb-3" style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);{{ $locked ? 'filter:blur(2px);user-select:none;' : '' }}">
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <span class="badge bg-info">{{ ucfirst($insight->tipo) }}</span>
                            <span class="badge bg-light text-dark">Relevancia {{ $insight->relevancia }}/100</span>
                        </div>
                        <p class="text-white mb-2">{{ $insight->resumen }}</p>
                        <p class="text-muted mb-2"><strong>Impacto:</strong> {{ $insight->impacto }}</p>
                        <p class="text-muted mb-0"><strong>Acción:</strong> {{ $insight->insight_accionable }}</p>
                    </div>
                @endforeach

                @if(!$canAccessPremiumInsights)
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-4" style="background:linear-gradient(180deg,rgba(10,16,32,.25),rgba(10,16,32,.92));">
                        <div class="text-center" style="max-width:420px;">
                            <div class="mb-2"><i class="fas fa-lock text-warning fa-2x"></i></div>
                            <h3 class="h5 text-white">Desbloquea este insight con ConocIA PRO</h3>
                            <p class="text-light mb-3">Accede a insights accionables, contenido premium y alertas personalizadas.</p>
                            <a href="{{ route('billing.plans') }}" class="btn btn-warning fw-semibold">Actualizar plan</a>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            <!-- Contenido Principal -->
            @if($canAccessPremiumContent ?? true)
                <div class="news-content mb-4">
                    {!! format_news_content($article->content) !!}
                </div>
            @else
                <div class="news-content mb-4 position-relative rounded-3 p-4" style="background:#fff;border:1px solid rgba(15,23,42,.08);overflow:hidden;">
                    <div style="filter:blur(3px);user-select:none;max-height:220px;overflow:hidden;">
                        {!! nl2br(e(Str::limit(strip_tags($article->content), 520))) !!}
                    </div>
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-4" style="background:linear-gradient(180deg,rgba(255,255,255,.55),rgba(255,255,255,.96));">
                        <div class="text-center" style="max-width:430px;">
                            <div class="mb-2"><i class="fas fa-lock text-warning fa-2x"></i></div>
                            <h3 class="h5 mb-2">Desbloquea este contenido con ConocIA PRO</h3>
                            <p class="text-muted mb-3">Accede a contenido premium, insights accionables y alertas personalizadas.</p>
                            <a href="{{ route('billing.plans') }}" class="btn btn-warning fw-semibold">Actualizar plan</a>
                        </div>
                    </div>
                </div>
            @endif

            @if($contentLooksIncomplete)
            <div class="mb-5 rounded-3 p-4" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.22);border-left:4px solid #f59e0b;">
                <div class="d-flex align-items-start gap-3">
                    <div style="width:38px;height:38px;border-radius:10px;background:rgba(245,158,11,.16);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#b45309;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <h6 class="fw-semibold mb-2" style="color:#92400e;">Esta nota llegó incompleta desde la fuente</h6>
                        <p class="mb-2" style="color:#78350f;line-height:1.65;">
                            Dejamos visible el contexto disponible para no mostrar una página rota. Mientras completamos esta entrada, puedes revisar la publicación original desde la fuente.
                        </p>
                        @if($article->source_url)
                            <a href="{{ $article->source_url }}" class="btn btn-sm btn-outline-warning fw-semibold" target="_blank" rel="noopener">
                                <i class="fas fa-external-link-alt me-1"></i> Ver fuente original
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif
           
            @unless($contentLooksIncomplete)
            {{-- Newsletter inline post-artículo --}}
            <div class="my-5 rounded-3 p-4" style="background:linear-gradient(135deg,#0a1020 0%,#0f1b2d 100%);border:1px solid rgba(56,182,255,.2);">
                <div class="row align-items-center g-3">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:40px;height:40px;background:var(--primary-color);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-robot text-white" style="font-size:.9rem;"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-white" style="font-size:.97rem;line-height:1.2;">¿Te gustó este artículo?</div>
                                <div style="color:#64748b;font-size:.78rem;margin-top:2px;">Recibí lo mejor de ConocIA cada semana en tu correo.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <form action="{{ route('newsletter.subscribe') }}" method="POST">
                            @csrf
                            <div class="input-group shadow-sm">
                                <input type="email" name="email" class="form-control border-0 rounded-start"
                                       placeholder="tu@correo.com" required style="font-size:.88rem;">
                                <button class="btn btn-primary px-4 fw-semibold" type="submit" style="font-size:.88rem;">
                                    Suscribirme
                                </button>
                            </div>
                            <div style="color:#475569;font-size:.7rem;margin-top:.35rem;">
                                <i class="fas fa-lock me-1"></i>Sin spam · Cancelá cuando quieras
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endunless

            <!-- Comentarios -->
            <div class="comments-section mt-5 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">
                        <i class="far fa-comments text-primary me-2"></i>
                        Comentarios
                        @if(count($article->comments ?? []) > 0)
                            <span class="badge bg-primary ms-2">{{ count($article->comments) }}</span>
                        @endif
                    </h4>
                    
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#commentForm" aria-expanded="false" aria-controls="commentForm">
                        <i class="fas fa-plus me-1"></i> Añadir comentario
                    </button>
                </div>
                
                <!-- Formulario de comentario (colapsable) -->
                <div class="collapse mb-4" id="commentForm">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3 border-bottom pb-2">Deja tu comentario</h5>
                            <form action="{{ url('/comments') }}" method="POST" id="newsCommentForm">
                                @csrf
                                <input type="hidden" name="commentable_type" value="App\Models\News">
                                <input type="hidden" name="commentable_id" value="{{ $article->id }}">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('guest_name') is-invalid @enderror" 
                                                id="name" name="guest_name" placeholder="Tu nombre"
                                                value="{{ old('guest_name') ?? Cookie::get('comment_name') }}" required>
                                            <label for="name">Nombre</label>
                                            @error('guest_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="email" class="form-control @error('guest_email') is-invalid @enderror" 
                                                id="email" name="guest_email" placeholder="tu@email.com"
                                                value="{{ old('guest_email') ?? Cookie::get('comment_email') }}" required>
                                            <label for="email">Email</label>
                                            @error('guest_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Tu email no será publicado.</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                                id="comment" name="content" style="height: 120px" 
                                                placeholder="Escribe tu comentario aquí" required>{{ old('content') }}</textarea>
                                        <label for="comment">Comentario</label>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="saveInfo" name="save_info" 
                                            {{ old('save_info') || Cookie::has('comment_name') ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="saveInfo">
                                            Guardar mi información para próximos comentarios
                                        </label>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-1"></i> Publicar comentario
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de comentarios -->
                <div class="comments-list">
                    @forelse($article->comments ?? [] as $comment)
                        <div class="comment-item card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <div class="d-flex mb-2">
                                    <div class="comment-avatar me-3">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                            style="width: 48px; height: 48px; font-size: 18px;">
                                            {{ strtoupper(substr($comment->guest_name ?? 'A', 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="comment-meta flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0 fs-5">{{ $comment->guest_name ?? 'Anónimo' }}</h5>
                                            <span class="text-muted small">
                                                <i class="far fa-clock me-1"></i> 
                                                {{ $comment->created_at ? $comment->created_at->diffForHumans() : 'Hace algún tiempo' }}
                                            </span>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-comment-dots me-1"></i> Comentario #{{ $loop->iteration }}
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-content mt-2 pt-2 border-top">
                                    <p class="mb-0">{{ $comment->content ?? 'Sin contenido' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-light shadow-sm">
                            <div class="d-flex align-items-center">
                                <i class="far fa-comment-dots text-primary me-3 fs-4"></i>
                                <p class="mb-0">No hay comentarios todavía. ¡Sé el primero en comentar!</p>
                            </div>
                        </div>
                    @endforelse
                </div>
                
                <!-- Paginación de comentarios (si es necesario) -->
                @if(isset($article->comments) && count($article->comments) > 0 && method_exists($article->comments, 'links'))
                    <div class="mt-3">
                        {{ $article->comments->links() }}
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Sidebar (Derecha) -->
        <div class="col-lg-3">
            @include('partials.table-of-contents', ['contentSelector' => '.news-content'])

            {{-- Artículos Relacionados --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Artículos relacionados</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse($relatedArticles as $rel)
                    @php
                        $relImg = null;
                        $relName = $rel->image ?? '';
                        if ($relName && !str_contains($relName, 'default') && !str_contains($relName, 'placeholder') && !str_contains($relName, 'via.placeholder')) {
                            if (Str::startsWith($relName, ['http://', 'https://'])) {
                                $relImg = $relName; // URL externa (R2/CDN)
                            } elseif (Str::startsWith($relName, 'storage/') || Str::startsWith($relName, '/storage/')) {
                                $relPath = ltrim(str_replace('storage/', '', $relName), '/');
                                if (Storage::disk('public')->exists($relPath)) {
                                    $relImg = asset('storage/' . $relPath);
                                }
                            } else {
                                foreach (["images/news/{$relName}", "news/{$relName}", $relName] as $p) {
                                    if (Storage::disk('public')->exists($p)) { $relImg = asset('storage/' . $p); break; }
                                }
                            }
                        }
                    @endphp
                    <a href="{{ route('news.show', $rel->slug) }}" class="text-decoration-none">
                        <div class="d-flex p-2 {{ !$loop->last ? 'border-bottom' : '' }} related-item">
                            @if($relImg)
                            <a href="{{ route('news.show', $rel->slug ?? $rel->id) }}"
                               class="flex-shrink-0 rounded overflow-hidden me-2"
                               style="width:64px;height:48px;min-width:64px;display:flex;align-items:center;justify-content:center;background:#eef1f5;">
                                <img src="{{ $relImg }}"
                                     alt="{{ $rel->title }}"
                                     class="w-100 h-100"
                                     style="object-fit:cover;"
                                     loading="lazy"
                                     onerror="this.closest('a.flex-shrink-0').remove();">
                            </a>
                            @endif
                            <div class="overflow-hidden">
                                @if(isset($rel->category) && is_object($rel->category))
                                <span class="badge mb-1" style="font-size:.65rem;background:{{ $rel->category->color ?? 'var(--primary-color)' }};">{{ $rel->category->name }}</span>
                                @endif
                                <p class="mb-0 small text-dark lh-sm" style="font-size:.8rem">
                                    {{ Str::limit($rel->title, 80) }}
                                </p>
                                <small class="text-muted" style="font-size:.7rem">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ ($rel->published_at ?? $rel->created_at)->locale('es')->isoFormat('D MMM, YYYY') }}
                                </small>
                            </div>
                        </div>
                    </a>
                    @empty
                    <p class="text-muted small p-3 mb-0">No hay artículos relacionados disponibles.</p>
                    @endforelse
                </div>
            </div>
            
            {{-- Lo más leído --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Lo más leído</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse($mostReadArticles as $idx => $mr)
                    <a href="{{ route('news.show', $mr->slug) }}" class="text-decoration-none">
                        <div class="d-flex align-items-start gap-2 px-3 py-2 most-read-item {{ !$loop->last ? 'border-bottom' : '' }}">
                            <span class="fw-bold flex-shrink-0 mt-1"
                                  style="font-size:1.1rem;min-width:22px;color:var(--primary-color);line-height:1;">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <h6 class="mb-1 text-dark" style="font-size:.82rem;line-height:1.35;">{{ $mr->title }}</h6>
                                <span class="text-muted" style="font-size:.75rem;">
                                    <i class="fas fa-eye me-1"></i>{{ number_format($mr->views) }}
                                    @if(isset($mr->category) && is_object($mr->category))
                                    <span class="ms-2 badge"
                                          style="font-size:.65rem;background:{{ $mr->category->color ?? 'var(--primary-color)' }};">
                                        {{ $mr->category->name }}
                                    </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </a>
                    @empty
                    <p class="text-muted small p-3 mb-0">No hay artículos disponibles.</p>
                    @endforelse
                </div>
            </div>

            {{-- Tags populares --}}
            @if($popularTags->count())
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Temas populares</h5>
                    </div>
                </div>
                <div class="card-body pt-1 pb-3 px-3">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($popularTags as $tag)
                        <a href="{{ route('news.by.tag', $tag->slug) }}"
                           class="badge bg-light text-dark text-decoration-none border"
                           style="font-size:.75rem;font-weight:500;">
                            #{{ $tag->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>


@endsection

@push('styles')
<!-- lo mas leido -->
<style>
    .hover-bg-light:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s;
    }
    
    .most-read-item {
        transition: transform 0.2s;
    }
    
    .most-read-item:hover {
        transform: translateX(5px);
    }
</style>


<!-- Estilos adicionales para los comentarios -->
<style>
    .comments-section .form-floating > .form-control {
        height: calc(3.5rem + 2px);
        line-height: 1.25;
    }
    
    .comments-section .form-floating > label {
        padding: 1rem 0.75rem;
    }
    
    .comments-section .comment-item {
        transition: all 0.3s ease;
    }
    
    .comments-section .comment-item:hover {
        transform: translateY(-2px);
    }
    
    .comments-section .comment-avatar div {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Animación para nuevos comentarios */
    @keyframes highlightComment {
        0% { background-color: rgba(13, 110, 253, 0.1); }
        100% { background-color: transparent; }
    }
    
    .comment-new {
        animation: highlightComment 2s ease-out;
    }
</style>


<style>
    /* ── Contenido del artículo — modo claro (default) ── */
    .news-content {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #1e293b !important;
    }
    .news-content p, .news-content li {
        margin-bottom: 1.25rem;
        color: #334155 !important;
    }
    .news-content h1 {
        font-size: 1.4rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #0f172a;
        line-height: 1.35;
    }
    .news-content h2 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #0f172a !important;
    }
    .news-content h3, .news-content h4 {
        font-size: 1.05rem;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: .75rem;
        color: #1e293b !important;
    }
    .news-content blockquote {
        border-left: 4px solid var(--primary-color, #38b6ff);
        background: rgba(56,182,255,.06) !important;
        border-radius: 0 .5rem .5rem 0;
        padding: 1.1rem 1.4rem;
        margin: 1.8rem 0;
        color: #475569 !important;
        font-style: italic;
        font-size: 1.05rem;
        line-height: 1.75;
    }
    .news-content a {
        color: #0369a1;
        text-decoration: none;
    }
    .news-content a:hover {
        text-decoration: underline;
        color: var(--primary-color, #38b6ff);
    }
    .news-content ul li strong {
        color: #0369a1;
    }
    .news-content img {
        max-width: 100%;
        height: auto;
        margin: 1.5rem 0;
        border-radius: 0.375rem;
    }
    .news-content pre, .news-content code {
        background-color: #f1f5f9;
        border-radius: 0.375rem;
        padding: 0.2rem 0.4rem;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.875em;
    }
    .news-content pre { padding: 1rem; margin-bottom: 1.5rem; overflow-x: auto; }
    .news-content ul, .news-content ol { margin-bottom: 1.5rem; padding-left: 2rem; }

    /* Resumen adaptivo */
    .news-summary-text { color: #334155; }
    body.theme-dark .news-summary-text { color: #cbd5e1; }

    /* ── Modo oscuro ── */
    body.theme-dark .news-content { color: #cbd5e1; }
    body.theme-dark .news-content p,
    body.theme-dark .news-content li { color: #cbd5e1; }
    body.theme-dark .news-content h1,
    body.theme-dark .news-content h2 { color: #e2e8f0; }
    body.theme-dark .news-content h3,
    body.theme-dark .news-content h4 { color: #e2e8f0; }
    body.theme-dark .news-content blockquote { color: #94a3b8; }
    body.theme-dark .news-content a { color: var(--primary-color, #38b6ff); }
    body.theme-dark .news-content a:hover { color: #7dd3fc; }
    body.theme-dark .news-content ul li strong { color: var(--primary-color, #38b6ff); }
    body.theme-dark .news-content pre,
    body.theme-dark .news-content code { background-color: #1e293b; color: #e2e8f0; }
    
    /* Estilos para el sidebar */
    .col-xl-3 .card-header h5, .col-lg-4 .card-header h5 {
        font-size: 0.9rem;
    }
    
    .col-xl-3 .card-body, .col-lg-4 .card-body {
        font-size: 0.85rem;
    }
    
    .col-xl-3 .card-body h6, .col-lg-4 .card-body h6 {
        font-size: 0.85rem;
        line-height: 1.3;
    }
    
    .col-xl-3 .card-body .small, .col-lg-4 .card-body .small {
        font-size: 0.75rem;
    }
    
    .related-item {
        transition: background-color 0.15s;
    }
    .related-item:hover {
        background-color: #f8f9fa;
    }
    
    .most-read-item .most-read-number {
        font-size: 1.1rem !important;
    }
    
    .most-read-content h6 {
        font-size: 0.8rem !important;
        margin-bottom: 0.2rem !important;
    }
    
    .badge {
        font-size: 0.7rem;
    }
    
    .form-text {
        font-size: 0.7rem !important;
    }
</style>
@endpush


<!-- JavaScript para el formulario de comentarios -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar formulario automáticamente si hay errores de validación
        @if($errors->any())
            var commentForm = document.getElementById('commentForm');
            var bsCollapse = new bootstrap.Collapse(commentForm, {
                toggle: true
            });
        @endif
        
        // Animar el comentario recién agregado (si existe)
        @if(session('comment_added'))
            const newCommentId = '{{ session('comment_added') }}';
            const newComment = document.getElementById('comment-' + newCommentId);
            if (newComment) {
                newComment.classList.add('comment-new');
                newComment.scrollIntoView({ behavior: 'smooth' });
            }
        @endif
    });
</script>
@endpush
