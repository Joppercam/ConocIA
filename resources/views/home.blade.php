<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('content')

    <!-- ═══ BREAKING NEWS TICKER ═══ -->
    <div class="breaking-ticker" style="background:#1a1a1a; border-bottom:2px solid var(--primary-color);">
        <div class="d-flex align-items-center overflow-hidden" style="height:36px;">
            <div class="flex-shrink-0 d-flex align-items-center px-3 py-1 h-100"
                 style="background:var(--primary-color); min-width:110px; gap:.4rem;">
                <i class="fas fa-bolt text-white" style="font-size:.7rem;"></i>
                <span class="text-white fw-bold text-uppercase" style="font-size:.7rem; letter-spacing:.08em;">Últimas</span>
            </div>
            <div class="ticker-track overflow-hidden flex-grow-1 px-2">
                <div class="ticker-inner d-flex gap-4 align-items-center">
                    @foreach(($recentNews ?? collect())->take(6)->concat(($recentNews ?? collect())->take(6)) as $t)
                    <a href="{{ route('news.show', $t->slug) }}"
                       class="text-decoration-none text-nowrap"
                       style="color:#ccc; font-size:.8rem;">
                        <span class="text-primary me-2">›</span>{{ $t->title }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ TOPIC NAVIGATION BAR ═══ -->
    <div class="topic-nav sticky-top hide-scrollbar" style="background:var(--dark-bg);border-bottom:1px solid #2a2a2a;z-index:900;top:56px;">
        <div class="container">
            <div class="d-flex gap-2 overflow-auto hide-scrollbar py-2 align-items-center">
                <a href="{{ route('news.index') }}"
                   class="btn btn-primary btn-sm flex-shrink-0 rounded-pill px-3" style="font-size:.78rem;">
                    <i class="fas fa-th me-1"></i>Todo
                </a>
                @foreach(($featuredCategories ?? collect())->take(8) as $tcat)
                <a href="{{ route('news.by.category', $tcat->slug) }}"
                   class="btn btn-sm flex-shrink-0 rounded-pill px-3 text-nowrap"
                   style="font-size:.78rem;background:rgba(255,255,255,.08);color:#ccc;border:1px solid rgba(255,255,255,.15);">
                    <i class="fas {{ $getCategoryIcon($tcat) }} me-1"></i>{{ $tcat->name }}
                </a>
                @endforeach
                <a href="{{ route('news.index') }}"
                   class="btn btn-link btn-sm flex-shrink-0 text-nowrap ms-auto" style="font-size:.75rem;color:#888;">
                    Ver todas <i class="fas fa-chevron-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- ═══ AI DAILY BRIEFING PLAYER ═══ --}}
    @include('partials.daily-briefing')

        <!-- ═══ HERO EDITORIAL GRID ═══ -->
    <section class="hero-news-section">
        <div class="hero-overlay">
            <div class="container py-3">
                <div class="row g-2">

                    {{-- ── Noticia principal (grande) ── --}}
                    @if($featuredNews->count() > 0)
                    @php $hero = $featuredNews->first(); @endphp
                    <div class="col-lg-5 col-md-7">
                        <a href="{{ route('news.show', $hero->slug ?? $hero->id) }}" class="text-decoration-none d-block h-100">
                            <div class="editorial-card editorial-card-main position-relative rounded-3 overflow-hidden h-100">
                                <img src="{{ $getImageUrl($hero->image, 'news', 'large') }}"
                                     class="editorial-img"
                                     alt="{{ $hero->title }}"
                                     loading="eager"
                                     onerror="this.src='{{ asset('storage/images/defaults/news-default-large.jpg') }}';">
                                <div class="editorial-gradient"></div>
                                @if(in_array($hero->id, $trendingIds ?? []))
                                <span class="badge-trending" style="top:12px;left:12px;"><i class="fas fa-fire me-1"></i>Trending</span>
                                @endif
                                <div class="editorial-body p-3 text-white">
                                    @if(isset($hero->category))
                                    <span class="badge mb-2" style="{{ $getCategoryStyle($hero->category) }}">
                                        <i class="fas {{ $getCategoryIcon($hero->category) }} me-1"></i>{{ $hero->category->name }}
                                    </span>
                                    @endif
                                    <h2 class="fw-bold lh-sm mb-2" style="font-size:1.2rem;">{{ $hero->title }}</h2>
                                    <p class="mb-2 d-none d-md-block" style="font-size:.85rem;opacity:.85;">
                                        {{ Str::limit($hero->excerpt, 110) }}
                                    </p>
                                    <div class="d-flex gap-3" style="font-size:.72rem;opacity:.7;">
                                        <span><i class="far fa-clock me-1"></i>{{ $hero->created_at->locale('es')->diffForHumans() }}</span>
                                        <span><i class="far fa-eye me-1"></i>{{ number_format($hero->views) }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif

                    {{-- ── Grid 2x2 de secundarias ── --}}
                    <div class="col-lg-4 col-md-5">
                        <div class="row g-2 h-100">
                            @foreach($featuredNews->skip(1)->take(4) as $sec)
                            <div class="col-6">
                                <a href="{{ route('news.show', $sec->slug ?? $sec->id) }}" class="text-decoration-none d-block h-100">
                                    <div class="editorial-card position-relative rounded-3 overflow-hidden h-100" style="min-height:175px;">
                                        <img src="{{ $getImageUrl($sec->image, 'news', 'medium') }}"
                                             class="editorial-img"
                                             alt="{{ $sec->title }}"
                                             loading="lazy"
                                             onerror="this.src='{{ asset('storage/images/defaults/news-default-medium.jpg') }}';">
                                        <div class="editorial-gradient"></div>
                                        @if(in_array($sec->id, $trendingIds ?? []))
                                        <span class="badge-trending" style="top:6px;left:6px;padding:1px 5px;font-size:.6rem;">
                                            <i class="fas fa-fire"></i>
                                        </span>
                                        @endif
                                        <div class="editorial-body p-2 text-white">
                                            @if(isset($sec->category))
                                            <span class="badge mb-1" style="{{ $getCategoryStyle($sec->category) }}; font-size:.6rem;">
                                                {{ $sec->category->name }}
                                            </span>
                                            @endif
                                            <h6 class="fw-bold lh-sm mb-1" style="font-size:.78rem;">
                                                {{ Str::limit($sec->title, 65) }}
                                            </h6>
                                            <div style="font-size:.65rem;opacity:.7;">
                                                <i class="far fa-clock me-1"></i>{{ $sec->created_at->locale('es')->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── Sidebar columnas ── --}}
                    <div class="col-lg-3">





                        <!-- Sección de Últimas Columnas -->
                        <div class="hero-columns-section h-100 shadow-lg rounded overflow-hidden">
                            <!-- Header compacto -->
                            <div class="hero-columns-header bg-primary text-white py-1 px-2 d-flex justify-content-between align-items-center">
                                <h3 class="hero-columns-title mb-0 d-flex align-items-center fs-6">
                                    <i class="fas fa-feather-alt me-1"></i>Últimas Columnas
                                </h3>
                                <span class="badge bg-white text-primary rounded-pill px-1 fs-9">
                                    {{ $latestColumns->count() }} artículos
                                </span>
                            </div>
                            
                            <div class="hero-columns-content" style="max-height: 340px;">
                                @if($latestColumns->count() > 0)
                                    @foreach($latestColumns as $column)
                                    <!-- Columna con extracto más largo -->
                                    <a href="{{ route('columns.show', $column->slug ?? $column->id) }}" class="hero-column-item d-block text-decoration-none text-dark transition py-1 px-2">
                                        
                                    
                                        <!-- Reemplaza el bloque original con este código actualizado -->
                                        <div class="hero-column-author d-flex align-items-center">
                                            @php
                                                // Determina el nombre del autor de forma segura
                                                $authorName = is_object($column->author) ? $column->author->name : ($column->author ?? 'Autor');
                                                
                                                // Obtiene el avatar con el helper actualizado
                                                $avatarPath = App\ImageHelper::getImageUrl(
                                                    is_object($column->author) && isset($column->author->avatar) ? $column->author->avatar : null,
                                                    'avatars',
                                                    'small'
                                                );
                                            @endphp
                                            
                                            <img src="{{ $avatarPath }}" 
                                                class="hero-column-avatar rounded-circle border border-1 border-light" 
                                                width="32" height="32"
                                                loading="lazy"
                                                alt="{{ $authorName }}">
                                            
                                            <div class="hero-column-author-info ms-1">
                                                <h4 class="hero-column-author-name fw-semibold text-primary mb-0 fs-7">{{ $authorName }}</h4>
                                                <div class="d-flex align-items-center">
                                                    <span class="hero-column-date text-muted fs-9">{{ $column->created_at->locale('es')->diffForHumans() }}</span>
                                                    <span class="text-muted fs-9 ms-1 ps-1 border-start">
                                                        <i class="far fa-clock me-1"></i>{{ ceil(str_word_count($column->content ?? '') / 200) ?? 5 }} min
                                                    </span>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Título algo más corto para dar espacio al resumen -->
                                        <h5 class="hero-column-title mt-1 mb-0 fw-bold fs-7">{{ Str::limit($column->title, 100) }}</h5>
                                        
                                        <!-- Extracto más largo con diseño de truncamiento visual -->
                                        <p class="text-muted mb-0 fs-9 line-clamp-2">{{ $column->excerpt ?? 'Extracto no disponible para esta publicación. Haga clic para leer el artículo completo.' }}</p>
                                        
                                        <!-- Estadísticas mínimas -->
                                        <div class="d-flex align-items-center text-muted fs-9">
                                            <span class="me-2"><i class="far fa-eye me-1"></i>{{ $column->views ?? 0 }}</span>
                                            <span><i class="far fa-comment me-1"></i>{{ $column->comments_count ?? 0 }}</span>
                                        </div>
                                        
                                        @if(!$loop->last)
                                        <hr class="my-1 text-muted opacity-25">
                                        @endif
                                    </a>
                                    @endforeach
                                @else
                                    <!-- Noticias secundarias con extractos más largos -->
                                    @foreach($secondaryNews as $secondary)
                                    <a href="{{ route('news.show', $secondary->slug ?? $secondary->id) }}" class="hero-column-item d-block text-decoration-none text-dark transition py-1 px-2">
                                        <div class="hero-column-content">
                                            @if(isset($secondary->category))
                                            <span class="hero-column-category badge mb-1 d-inline-block fs-9" style="{{ $getCategoryStyle($secondary->category) }}">
                                                <i class="fas {{ $getCategoryIcon($secondary->category) }} me-1"></i>
                                                {{ $secondary->category->name }}
                                            </span>
                                            @endif
                                            <h5 class="hero-column-title fw-bold fs-7 mb-0">{{ Str::limit($secondary->title, 50) }}</h5>
                                            <p class="hero-column-excerpt text-muted mb-0 fs-9 line-clamp-2">{{ $secondary->excerpt }}</p>
                                            
                                            <div class="d-flex align-items-center text-muted fs-9">
                                                <span class="me-2"><i class="far fa-calendar-alt me-1"></i>{{ $secondary->created_at->format('d M') }}</span>
                                                <span><i class="far fa-eye me-1"></i>{{ number_format($secondary->views ?? rand(500, 2000)) }}</span>
                                            </div>
                                            
                                            @if(!$loop->last)
                                            <hr class="my-1 text-muted opacity-25">
                                            @endif
                                        </div>
                                    </a>
                                    @endforeach
                                @endif
                            </div>
                            
                            @if($latestColumns->count() > 0)
                            <!-- Footer ultra compacto -->
                            <div class="hero-columns-footer bg-light p-1 border-top d-flex justify-content-between align-items-center">
                                <span class="text-muted ms-1 fs-9">
                                    <i class="fas fa-rss me-1"></i>Actualizado
                                </span>
                                <a href="{{ route('columns.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2 fs-9">
                                    Ver todas <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            </div>
                            @endif
                        </div>











                    </div>{{-- /col sidebar --}}

                </div>{{-- /row --}}
            </div>{{-- /container --}}
        </div>{{-- /hero-overlay --}}
    </section>


        <!-- Sección Noticias Recientes y Lo Más Leído (COMPLETA) -->
    <section class="py-3 border-top">
        <div class="container">
            <div class="row">



                
                <!-- Noticias Recientes - Ancho completo -->
<div class="col-12">
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-header bg-white py-2 px-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:4px;height:20px;background:var(--primary-color);border-radius:2px;"></div>
                    <h5 class="mb-0 fw-bold" style="font-size:1rem;">Noticias Recientes</h5>
                </div>
                <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size:.75rem;">
                    Ver todas <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <div class="card-body py-3">
            <div class="row g-3">
                @foreach($recentNews->take(12) as $recent)
                <div class="col-md-6">
                    <div class="d-flex gap-2 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                        {{-- Thumbnail --}}
                        <a href="{{ route('news.show', $recent->slug ?? $recent->id) }}" class="flex-shrink-0">
                            <img src="{{ $getImageUrl($recent->image ?? null, 'news', 'small') }}"
                                 alt="{{ $recent->title }}"
                                 class="rounded"
                                 style="width:80px;height:60px;object-fit:cover;"
                                 loading="lazy"
                                 onerror="this.style.display='none'">
                        </a>
                        <div class="overflow-hidden">
                            <div class="d-flex align-items-center gap-1 mb-1 flex-wrap">
                                @if(isset($recent->category))
                                <span class="badge rounded-pill" style="{{ $getCategoryStyle($recent->category) }}; font-size:.6rem;">
                                    {{ $recent->category->name }}
                                </span>
                                @endif
                                @if($recent->created_at->diffInHours(now()) < 48)
                                <span class="badge bg-light text-secondary border" style="font-size:.6rem;">Nuevo</span>
                                @endif
                            </div>
                            <h6 class="fw-bold mb-1 lh-sm" style="font-size:.82rem;">
                                <a href="{{ route('news.show', $recent->slug ?? $recent->id) }}" class="text-decoration-none text-dark">
                                    {{ Str::limit($recent->title, 72) }}
                                </a>
                            </h6>
                            <div class="d-flex gap-2 text-muted" style="font-size:.7rem;">
                                <span><i class="far fa-calendar-alt me-1"></i>{{ $recent->created_at->locale('es')->isoFormat('D MMM') }}</span>
                                <span><i class="far fa-eye me-1"></i>{{ number_format($recent->views ?? 0) }}</span>
                                <span><i class="far fa-comment me-1"></i>{{ $recent->comments_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('news.index') }}" class="btn btn-primary btn-sm px-4">
                    Ver más noticias <i class="fas fa-newspaper ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>






                






            </div>
        </div>
    </section>


    <!-- ═══ NEWSLETTER INLINE ═══ -->
    <div style="background:linear-gradient(135deg,#0a1020 0%,#0f1b2d 100%);border-top:2px solid rgba(56,182,255,.2);border-bottom:2px solid rgba(56,182,255,.1);" class="py-4">
        <div class="container">
            <div class="row align-items-center g-3">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;background:var(--primary-color);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-robot text-white" style="font-size:.9rem;"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-white" style="font-size:.97rem;line-height:1.2;">No te pierdas nada sobre IA</div>
                            <div style="color:#64748b;font-size:.78rem;margin-top:2px;">Digest semanal · Sin ruido · Solo lo que importa</div>
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
                                <i class="fas fa-paper-plane me-2"></i>Suscribirme
                            </button>
                        </div>
                        <div style="color:#475569;font-size:.7rem;margin-top:.35rem;">
                            <i class="fas fa-lock me-1"></i>Sin spam · Cancelá cuando quieras
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ PROFUNDIZA ═══ -->
    <section class="py-5" style="background:#f0f4f8;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;">
        <div class="container">

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div style="width:4px;height:20px;background:var(--primary-color);border-radius:2px;"></div>
                        <h3 class="mb-0 fw-bold" style="font-size:1rem;color:#0f172a;">Profundiza</h3>
                    </div>
                    <p style="color:#64748b;font-size:.82rem;margin:0 0 0 18px;">Ciencia, análisis y conocimiento de frontera sobre IA</p>
                </div>
            </div>

            <div class="row g-4">

                {{-- Conceptos IA --}}
                <div class="col-md-6 col-lg-3">
                    <div class="h-100 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:28px;height:28px;background:rgba(56,182,255,.15);border-radius:7px;display:flex;align-items:center;justify-content:center;">
                                    <i class="fas fa-book-open" style="color:var(--primary-color);font-size:.75rem;"></i>
                                </div>
                                <span class="fw-bold" style="color:#0f172a;font-size:.88rem;">Conceptos IA</span>
                            </div>
                            <a href="{{ route('conceptos.index') }}" style="color:var(--primary-color);font-size:.73rem;" class="text-decoration-none">Ver todos <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                        <div class="d-flex flex-column gap-2 flex-grow-1">
                            @forelse($latestConceptos as $item)
                            <a href="{{ route('conceptos.show', $item->slug) }}" class="text-decoration-none">
                                <div class="profundiza-card p-3">
                                    <div class="d-flex gap-2 mb-1 flex-wrap">
                                        @if($item->difficulty_level)<span class="badge difficulty-badge-{{ $item->difficulty_level }}" style="font-size:.62rem;">{{ ucfirst($item->difficulty_level) }}</span>@endif
                                        @if($item->category)<span class="badge" style="background:rgba(56,182,255,.1);color:#0369a1;font-size:.62rem;">{{ $item->category }}</span>@endif
                                    </div>
                                    <div class="fw-semibold" style="color:#1e293b;font-size:.82rem;line-height:1.35;">{{ Str::limit($item->title, 55) }}</div>
                                    @if($item->short_definition)<div style="color:#64748b;font-size:.74rem;margin-top:3px;line-height:1.4;">{{ Str::limit($item->short_definition, 70) }}</div>@endif
                                </div>
                            </a>
                            @empty
                            <div class="profundiza-card p-3 text-center" style="flex:1;">
                                <i class="fas fa-book-open mb-2 d-block" style="color:var(--primary-color);opacity:.3;font-size:1.4rem;"></i>
                                <span style="color:#94a3b8;font-size:.78rem;">Próximamente</span>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Análisis de Fondo --}}
                <div class="col-md-6 col-lg-3">
                    <div class="h-100 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:28px;height:28px;background:rgba(56,182,255,.15);border-radius:7px;display:flex;align-items:center;justify-content:center;">
                                    <i class="fas fa-microscope" style="color:var(--primary-color);font-size:.75rem;"></i>
                                </div>
                                <span class="fw-bold" style="color:#0f172a;font-size:.88rem;">Análisis de Fondo</span>
                            </div>
                            <a href="{{ route('analisis.index') }}" style="color:var(--primary-color);font-size:.73rem;" class="text-decoration-none">Ver todos <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                        <div class="d-flex flex-column gap-2 flex-grow-1">
                            @forelse($latestAnalises as $item)
                            <a href="{{ route('analisis.show', $item->slug) }}" class="text-decoration-none">
                                <div class="profundiza-card p-3">
                                    @if($item->category)<span class="badge mb-1" style="background:rgba(56,182,255,.1);color:#0369a1;font-size:.62rem;">{{ strtoupper($item->category) }}</span>@endif
                                    <div class="fw-semibold" style="color:#1e293b;font-size:.82rem;line-height:1.35;">{{ Str::limit($item->title, 55) }}</div>
                                    @if($item->excerpt)<div style="color:#64748b;font-size:.74rem;margin-top:3px;line-height:1.4;">{{ Str::limit($item->excerpt, 70) }}</div>@endif
                                    <div style="color:#94a3b8;font-size:.7rem;margin-top:5px;"><i class="fas fa-clock me-1"></i>{{ $item->reading_time ?? 10 }} min</div>
                                </div>
                            </a>
                            @empty
                            <div class="profundiza-card p-3 text-center" style="flex:1;">
                                <i class="fas fa-microscope mb-2 d-block" style="color:var(--primary-color);opacity:.3;font-size:1.4rem;"></i>
                                <span style="color:#94a3b8;font-size:.78rem;">Próximamente</span>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ConocIA Papers --}}
                <div class="col-md-6 col-lg-3">
                    <div class="h-100 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:28px;height:28px;background:rgba(56,182,255,.15);border-radius:7px;display:flex;align-items:center;justify-content:center;">
                                    <i class="fas fa-file-alt" style="color:var(--primary-color);font-size:.75rem;"></i>
                                </div>
                                <span class="fw-bold" style="color:#0f172a;font-size:.88rem;">ConocIA Papers</span>
                            </div>
                            <a href="{{ route('papers.index') }}" style="color:var(--primary-color);font-size:.73rem;" class="text-decoration-none">Ver todos <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                        <div class="d-flex flex-column gap-2 flex-grow-1">
                            @forelse($latestPapers as $item)
                            <a href="{{ route('papers.show', $item->slug) }}" class="text-decoration-none">
                                <div class="profundiza-card p-3">
                                    <div class="d-flex gap-2 mb-1 flex-wrap">
                                        @if($item->arxiv_category)<span class="badge" style="background:rgba(56,182,255,.1);color:#0369a1;font-size:.62rem;">{{ $item->arxiv_category }}</span>@endif
                                        @if($item->difficulty_level)<span class="badge difficulty-badge-{{ $item->difficulty_level }}" style="font-size:.62rem;">{{ ucfirst($item->difficulty_level) }}</span>@endif
                                    </div>
                                    <div class="fw-semibold" style="color:#1e293b;font-size:.82rem;line-height:1.35;">{{ Str::limit($item->title, 55) }}</div>
                                    @if($item->original_title)<div style="color:#94a3b8;font-size:.7rem;font-style:italic;margin-top:2px;">{{ Str::limit($item->original_title, 55) }}</div>@endif
                                </div>
                            </a>
                            @empty
                            <div class="profundiza-card p-3 text-center" style="flex:1;">
                                <i class="fas fa-file-alt mb-2 d-block" style="color:var(--primary-color);opacity:.3;font-size:1.4rem;"></i>
                                <span style="color:#94a3b8;font-size:.78rem;">Próximamente</span>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Estado del Arte --}}
                <div class="col-md-6 col-lg-3">
                    <div class="h-100 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:28px;height:28px;background:rgba(56,182,255,.15);border-radius:7px;display:flex;align-items:center;justify-content:center;">
                                    <i class="fas fa-chart-line" style="color:var(--primary-color);font-size:.75rem;"></i>
                                </div>
                                <span class="fw-bold" style="color:#0f172a;font-size:.88rem;">Estado del Arte</span>
                            </div>
                            <a href="{{ route('estado-arte.index') }}" style="color:var(--primary-color);font-size:.73rem;" class="text-decoration-none">Ver todos <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                        <div class="d-flex flex-column gap-2 flex-grow-1">
                            @forelse($latestDigests as $item)
                            <a href="{{ route('estado-arte.show', $item->slug) }}" class="text-decoration-none">
                                <div class="profundiza-card p-3">
                                    <div class="d-flex gap-2 mb-1 flex-wrap">
                                        @if($item->subfield_label)<span class="badge" style="background:rgba(56,182,255,.1);color:#0369a1;font-size:.62rem;">{{ $item->subfield_label }}</span>@endif
                                    </div>
                                    <div class="fw-semibold" style="color:#1e293b;font-size:.82rem;line-height:1.35;">{{ Str::limit($item->title, 55) }}</div>
                                    @if($item->period_label)<div style="color:#64748b;font-size:.74rem;margin-top:3px;">{{ $item->period_label }}</div>@endif
                                </div>
                            </a>
                            @empty
                            <div class="profundiza-card p-3 text-center" style="flex:1;">
                                <i class="fas fa-chart-line mb-2 d-block" style="color:var(--primary-color);opacity:.3;font-size:1.4rem;"></i>
                                <span style="color:#94a3b8;font-size:.78rem;">Próximamente</span>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>{{-- /row --}}

            <div class="d-flex justify-content-center gap-3 mt-4 pt-2" style="border-top:1px solid #e2e8f0;">
                <a href="{{ route('conceptos.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-book-open me-2"></i>Conceptos IA</a>
                <a href="{{ route('analisis.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-microscope me-2"></i>Análisis de Fondo</a>
                <a href="{{ route('papers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-alt me-2"></i>Papers</a>
                <a href="{{ route('estado-arte.index') }}" class="btn btn-sm text-white" style="background:var(--primary-color);"><i class="fas fa-chart-line me-2"></i>Estado del Arte</a>
            </div>

        </div>
    </section>

    <!-- Sección Columnas - 4 destacadas sin imágenes -->
    <section class="py-4 bg-white">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:4px;height:20px;background:var(--primary-color);border-radius:2px;"></div>
                    <h3 class="mb-0 fw-bold" style="font-size:1rem;">Columnas de Opinión</h3>
                </div>
                <a href="{{ route('columns.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size:.75rem;">
                    Ver todas <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            
            <!-- Grid de 4 columnas destacadas - Solo texto -->
            <div class="row g-4">
                @foreach($latestColumnsSectionFeatured->take(4) as $index => $column)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 hover-shadow transition-300">
                        <div class="card-body d-flex flex-column p-4">
                            <!-- Información del autor -->
                            <div class="d-flex align-items-center mb-3">
                                @php
                                    $authorName = is_object($column->author) 
                                        ? $column->author->name 
                                        : ($column->author ?? 'Columnista');
                                    
                                    $avatarPath = App\ImageHelper::getImageUrl(
                                        is_object($column->author) && isset($column->author->avatar) 
                                            ? $column->author->avatar 
                                            : null,
                                        'avatars',
                                        'small'
                                    );
                                @endphp
                                
                                <img src="{{ $avatarPath }}" 
                                    class="rounded-circle border border-2 border-light me-2" 
                                    width="40" height="40" 
                                    alt="{{ $authorName }}"
                                    style="object-fit: cover;">
                                
                                <div>
                                    <h6 class="fw-bold mb-0 fs-7">{{ $authorName }}</h6>
                                    <span class="text-muted fs-9">{{ $column->created_at->locale('es')->diffForHumans() }}</span>
                                </div>
                            </div>
                            
                            <!-- Título de la columna -->
                            <h5 class="card-title mb-3 fs-6">
                                <a href="{{ route('columns.show', $column->slug ?? $column->id) }}" class="text-decoration-none text-dark">
                                    {{ Str::limit($column->title, 80) }}
                                </a>
                            </h5>
                            
                            <!-- Extracto más visible -->
                            <p class="card-text flex-grow-1 mb-3 text-muted small line-clamp-4">{{ Str::limit($column->excerpt, 150) }}</p>
                            
                            <!-- Footer con metadatos y botón de lectura -->
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted small">
                                        <i class="far fa-clock me-1"></i>
                                        {{ ceil(str_word_count($column->content ?? '') / 200) ?? 5 }} min lectura
                                    </span>
                                    <span class="text-muted small">
                                        <i class="far fa-eye me-1"></i>
                                        {{ number_format($column->views ?? rand(150, 950)) }}
                                    </span>
                                </div>
                                
                                <a href="{{ route('columns.show', $column->slug ?? $column->id) }}" class="btn btn-sm btn-outline-secondary w-100">
                                    Leer columna <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Separador -->
            <hr class="my-4">
            
            <!-- Últimas columnas (listado) -->
            <div class="mb-4">
                <div class="d-flex align-items-center">
                    <div class="bg-secondary me-2" style="width: 4px; height: 20px;"></div>
                    <h4 class="fs-5 mb-0">Últimas Columnas</h4>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card border-0 shadow-sm rounded-3">
                        
                    
                        

                    <div class="list-group list-group-flush rounded-3">
                        @if($latestColumnsSection->count() > 0)
                            @foreach($latestColumnsSection->take(5) as $column)
                            <div class="list-group-item border-0 border-bottom py-3 px-4 hover-bg-light transition-300">
                                <div class="row g-0">
                                    <!-- Avatar e info del autor -->
                                    <div class="col-auto me-3">
                                        @php
                                            $authorName = is_object($column->author) 
                                                ? $column->author->name 
                                                : ($column->author ?? 'Columnista');
                                            
                                            $avatarPath = App\ImageHelper::getImageUrl(
                                                is_object($column->author) && isset($column->author->avatar) 
                                                    ? $column->author->avatar 
                                                    : null,
                                                'avatars',
                                                'small'
                                            );
                                        @endphp
                                        
                                        <img src="{{ $avatarPath }}" 
                                            class="rounded-circle border" 
                                            width="48" height="48" 
                                            alt="{{ $authorName }}"
                                            style="object-fit: cover;">
                                    </div>
                                    
                                    <!-- Contenido de la columna -->
                                    <div class="col">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="fw-bold text-secondary mb-0 fs-7">{{ $authorName }}</h6>
                                            <small class="text-muted">{{ $column->created_at->locale('es')->diffForHumans() }}</small>
                                        </div>
                                        
                                        <h5 class="mb-2 fs-6">
                                            <a href="{{ route('columns.show', $column->slug ?? $column->id) }}" class="text-decoration-none text-dark stretched-link">
                                                {{ Str::limit($column->title, 80) }}
                                            </a>
                                        </h5>
                                        
                                        <p class="text-muted small mb-2 line-clamp-2">{{ Str::limit($column->excerpt, 100) }}</p>
                                        
                                        <!-- Metadatos adicionales -->
                                        <div class="d-flex align-items-center text-muted small">
                                            <span class="me-3">
                                                <i class="far fa-clock me-1"></i>
                                                {{ ceil(str_word_count($column->content ?? '') / 200) ?? 5 }} min
                                            </span>
                                            <span class="me-3">
                                                <i class="far fa-eye me-1"></i>
                                                {{ number_format($column->views ?? rand(100, 800)) }}
                                            </span>
                                            <span>
                                                <i class="far fa-comment me-1"></i>
                                                {{ $column->comments_count ?? rand(0, 15) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="list-group-item border-0 py-4 text-center">
                                <p class="text-muted mb-0">No hay columnas recientes disponibles en este momento.</p>
                            </div>
                        @endif
                    </div>

                        
                        <!-- Footer con enlace a todas las columnas -->
                        <div class="card-footer bg-light py-3 text-center">
                            <a href="{{ route('columns.index') }}" class="btn btn-sm btn-secondary px-4">
                                Ver todas las columnas <i class="fas fa-external-link-alt ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ═══ CONOCIA TV SPOTLIGHT ═══ --}}
    <section style="background:linear-gradient(135deg,#0d1117 0%,#0f1b2d 60%,#0d1117 100%);border-top:1px solid #1a1a2e;border-bottom:1px solid #1a1a2e;" class="py-5">
        <div class="container">
            <div class="row align-items-center g-4">

                {{-- Left: branding + CTA --}}
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:44px;height:44px;background:var(--primary-color);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-tv text-white" style="font-size:1.1rem;"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-white" style="font-size:1.4rem;line-height:1;">
                                ConocIA <span style="color:var(--primary-color);">TV</span>
                            </div>
                            <div style="color:#555;font-size:.72rem;margin-top:1px;">Canal de video sobre IA</div>
                        </div>
                    </div>

                    <p style="color:#888;font-size:.88rem;line-height:1.65;" class="mb-4">
                        Documentales, conferencias y análisis en video sobre inteligencia artificial,
                        machine learning y las tecnologías que están cambiando el mundo.
                    </p>

                    @php
                        $tvVideoCount    = \App\Models\Video::count();
                        $tvPlatformCount = \App\Models\VideoPlatform::whereHas('videos')->count();
                    @endphp

                    <div class="d-flex gap-3 mb-4">
                        <div style="background:rgba(255,255,255,.04);border:1px solid #1e2540;border-radius:10px;padding:10px 16px;text-align:center;">
                            <div class="fw-bold text-white" style="font-size:1.3rem;line-height:1;">{{ $tvVideoCount }}</div>
                            <div style="color:#666;font-size:.7rem;margin-top:2px;">Videos</div>
                        </div>
                        <div style="background:rgba(255,255,255,.04);border:1px solid #1e2540;border-radius:10px;padding:10px 16px;text-align:center;">
                            <div class="fw-bold text-white" style="font-size:1.3rem;line-height:1;">{{ $tvPlatformCount }}</div>
                            <div style="color:#666;font-size:.7rem;margin-top:2px;">Plataformas</div>
                        </div>
                    </div>

                    <a href="{{ route('videos.index') }}"
                       class="btn btn-primary rounded-pill px-4"
                       style="font-size:.85rem;">
                        <i class="fas fa-play me-2"></i>Ir a ConocIA TV
                    </a>
                </div>

                {{-- Right: video cards --}}
                <div class="col-lg-8">
                    @if(isset($featuredVideos) && $featuredVideos->count())
                    @php
                        $tvHero = $featuredVideos->first();
                        $tvSide = $featuredVideos->skip(1)->take(2);
                        $platColors = ['youtube' => '#ff0000', 'vimeo' => '#1ab7ea'];
                        $platIcons  = ['youtube' => 'fa-youtube', 'vimeo' => 'fa-vimeo-v'];
                    @endphp
                    <div class="row g-3">

                        {{-- Hero card --}}
                        <div class="col-md-7">
                            <a href="{{ route('videos.show', $tvHero->id) }}"
                               class="d-block position-relative rounded-3 overflow-hidden tv-spot-card"
                               style="aspect-ratio:16/9;">
                                <img src="{{ $tvHero->thumbnail_url }}"
                                     alt="{{ $tvHero->title }}"
                                     class="w-100 h-100"
                                     style="object-fit:cover;transition:transform .4s ease;">
                                <div class="tv-spot-overlay"></div>
                                <div class="tv-spot-play">
                                    <div style="width:52px;height:52px;background:rgba(255,255,255,.15);backdrop-filter:blur(4px);border:2px solid rgba(255,255,255,.4);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;">
                                        <i class="fas fa-play ms-1"></i>
                                    </div>
                                </div>
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge" style="background:{{ $platColors[$tvHero->platform->code] ?? '#38b6ff' }};">
                                        <i class="fab {{ $platIcons[$tvHero->platform->code] ?? 'fa-play-circle' }} me-1"></i>{{ $tvHero->platform->name }}
                                    </span>
                                </div>
                                <div class="position-absolute bottom-0 start-0 end-0 p-3"
                                     style="background:linear-gradient(to top,rgba(0,0,0,.85),transparent);">
                                    <div class="text-white fw-semibold" style="font-size:.88rem;line-height:1.35;">
                                        {{ Str::limit($tvHero->title, 70) }}
                                    </div>
                                    <div style="color:#aaa;font-size:.72rem;margin-top:3px;">
                                        <i class="far fa-eye me-1"></i>{{ number_format($tvHero->view_count) }}
                                        <span class="ms-2"><i class="far fa-clock me-1"></i>{{ $tvHero->duration }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        {{-- Side cards --}}
                        <div class="col-md-5 d-flex flex-column gap-3">
                            @forelse($tvSide as $tvv)
                            <a href="{{ route('videos.show', $tvv->id) }}"
                               class="d-flex gap-3 align-items-center text-decoration-none tv-spot-mini rounded-2 p-2"
                               style="background:rgba(255,255,255,.03);border:1px solid #1e2540;transition:background .15s;">
                                <div class="position-relative flex-shrink-0 rounded overflow-hidden"
                                     style="width:90px;height:56px;">
                                    <img src="{{ $tvv->thumbnail_url }}"
                                         alt="{{ $tvv->title }}"
                                         class="w-100 h-100"
                                         style="object-fit:cover;">
                                    <div class="position-absolute inset-0 d-flex align-items-center justify-content-center"
                                         style="background:rgba(0,0,0,.3);">
                                        <i class="fas fa-play text-white" style="font-size:.6rem;"></i>
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-white fw-semibold"
                                         style="font-size:.78rem;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                        {{ $tvv->title }}
                                    </div>
                                    <div style="color:#555;font-size:.7rem;margin-top:3px;">
                                        <i class="far fa-clock me-1"></i>{{ $tvv->duration }}
                                    </div>
                                </div>
                            </a>
                            @empty
                            <div></div>
                            @endforelse
                        </div>

                    </div>
                    @endif
                </div>

            </div>
        </div>
    </section>


<!-- Al final de tu archivo de vista -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <!-- Toast para éxito -->
    <div id="subscriptionSuccessToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i> {{ session('subscription_success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
    </div>
    
    <!-- Toast para información -->
    <div id="subscriptionInfoToast" class="toast align-items-center text-white bg-info border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-info-circle me-2"></i> {{ session('subscription_info') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
    </div>
</div>


@endsection

@push('styles')

<!-- Hero Editorial Grid -->
<style>
.hero-news-section { background: var(--dark-bg); }
.hero-overlay { background: transparent; }

.editorial-card {
    display: block;
    background: #1a1a1a;
    transition: transform .25s ease, box-shadow .25s ease;
}
.editorial-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(0,0,0,.5) !important;
}
.editorial-card-main { min-height: 380px; }

.editorial-img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s ease;
}
.editorial-card:hover .editorial-img { transform: scale(1.04); }

.editorial-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.88) 40%, rgba(0,0,0,.05) 100%);
}
.editorial-body {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
}
</style>

<!-- Ticker + Topic Nav -->
<style>
.ticker-track { position: relative; }
.ticker-inner {
    white-space: nowrap;
    animation: ticker-scroll 50s linear infinite;
}
.ticker-inner:hover { animation-play-state: paused; }
@keyframes ticker-scroll {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.topic-nav .btn { font-size: .78rem !important; }
.topic-nav::-webkit-scrollbar { display: none; }
</style>

<!-- Estilos ConocIA TV Spotlight -->
<style>
.tv-spot-card img { transition: transform .4s ease; }
.tv-spot-card:hover img { transform: scale(1.04); }
.tv-spot-overlay {
    position:absolute; inset:0;
    background:rgba(0,0,0,0);
    transition:background .2s ease;
}
.tv-spot-card:hover .tv-spot-overlay { background:rgba(0,0,0,.25); }
.tv-spot-play {
    position:absolute; inset:0;
    display:flex; align-items:center; justify-content:center;
    opacity:0; transition:opacity .2s ease;
}
.tv-spot-card:hover .tv-spot-play { opacity:1; }
.tv-spot-mini:hover { background:rgba(255,255,255,.06) !important; }
</style>

<!-- Estilos adicionales para la sección de columnas -->
<style>
    /* Efectos para las cards de columnas */
    .hover-shadow {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-shadow:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }
    
    .hover-bg-light:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .transition-300 {
        transition: all 0.3s ease;
    }
    
    /* Limitador de líneas para extractos */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-4 {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Tamaños de fuente específicos */
    .fs-7 {
        font-size: 0.875rem !important;
    }
    
    .fs-9 {
        font-size: 0.75rem !important;
    }
</style>

<!-- Estilos para la sección Hero -->
<style>
/* Estilos para la nueva sección hero */
/* Mejoras para el carrusel de la sección hero */

/* Transiciones más suaves */
.carousel-fade .carousel-item {
    opacity: 0;
    transition: opacity .8s ease-in-out;
}

.carousel-fade .carousel-item.active {
    opacity: 1;
}

/* Mejorar controles del carrusel */
.carousel-control-prev,
.carousel-control-next {
    width: 5%;
    opacity: 0;
    transition: opacity 0.3s ease;
}

#heroNewsCarousel:hover .carousel-control-prev,
#heroNewsCarousel:hover .carousel-control-next {
    opacity: 0.7;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    opacity: 0.9 !important;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    background-size: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Indicadores más estilizados */
.carousel-indicators {
    bottom: -5px;
}

.carousel-indicators button {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin: 0 5px;
    background-color: rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.carousel-indicators button.active {
    background-color: var(--bs-primary, #007bff);
    width: 12px;
    height: 12px;
}

/* Animación para noticias */
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.carousel-item.active .hero-news-content {
    animation: fadeUp 0.5s ease-out forwards;
}

/* Efecto de escala para imágenes al hacer hover */
.hero-news-item img {
    transition: transform 0.5s ease-in-out;
}

.hero-news-item:hover img {
    transform: scale(1.05);
}

.hero-news-section {
    position: relative;
    background: #ffffff; /* Fondo blanco */
    color: #333;
    padding: 2rem 0;
    margin-bottom: 2rem;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
}

.hero-overlay {
    position: relative;
    z-index: 1;
    background: #ffffff; /* Aseguramos que el overlay también sea blanco */
}

/* Estilos para el carousel principal */
.hero-news-item {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    height: 480px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    background-color: #ffffff;
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.hero-news-item img {
    width: 100%;
    height: 280px; /* Altura fija para la imagen */
    object-fit: cover;
    transition: transform 0.5s ease;
}

.hero-news-item:hover img {
    transform: scale(1.02);
}

.hero-news-content {
    position: relative;
    padding: 1.5rem;
    background: #ffffff; /* Fondo blanco para el contenido */
    border-top: 1px solid rgba(0, 0, 0, 0.05); /* Borde sutil para separar de la imagen */
    flex-grow: 1; /* Para que ocupe el espacio restante */
}

.hero-news-category {
    display: inline-block;
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 50px;
    font-size: 0.65rem;
    font-weight: 600;
    margin-bottom: 0.6rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.hero-news-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.6rem;
    line-height: 1.2;
    color: #2c3e50; /* Azul oscuro para el título */
}

.hero-news-excerpt {
    color: #5d6778; /* Gris azulado para el extracto */
    margin-bottom: 0.8rem;
    font-size: 0.8rem;
    max-width: 100%;
    line-height: 1.4;
}

.hero-news-meta {
    display: flex;
    margin-bottom: 0.8rem;
    font-size: 0.7rem;
    color: #8c9bab; /* Gris más claro para los metadatos */
}

.hero-news-meta span {
    margin-right: 1.5rem;
}

.hero-news-meta i {
    margin-right: 0.3rem;
}

/* Estilos para enlaces en el carrusel */
.hero-news-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.hero-news-link:hover .hero-news-title {
    text-decoration: underline;
}

/* Estilos para sección de columnas */
.hero-columns-section {
    height: 100%;
    display: flex;
    flex-direction: column;
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.hero-columns-header {
    padding: 1rem 1.5rem;
    background-color: var(--bs-primary, #007bff);
    color: white;
}

.hero-columns-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.hero-columns-content {
    flex-grow: 1;
    overflow-y: auto;
    max-height: 400px;
}

.hero-column-item {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    text-decoration: none;
    color: inherit;
    display: block;
    transition: background-color 0.2s;
}

.hero-column-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.hero-column-author {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.hero-column-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 0.75rem;
    object-fit: cover;
}

.hero-column-author-name {
    font-size: 0.85rem;
    margin: 0;
    font-weight: 600;
    color: var(--bs-primary, #007bff);
}

.hero-column-date {
    font-size: 0.7rem;
    color: #8c9bab;
}

.hero-column-title {
    font-size: 0.95rem;
    margin: 0;
    color: #2c3e50;
    line-height: 1.3;
}

.hero-column-category {
    display: inline-block;
    color: white;
    padding: 0.15rem 0.6rem;
    border-radius: 50px;
    font-size: 0.6rem;
    font-weight: 600;
    margin-bottom: 0.4rem;
}

.hero-column-excerpt {
    color: #5d6778;
    font-size: 0.75rem;
    margin: 0.5rem 0 0;
}

.hero-columns-footer {
    padding: 0.75rem;
    text-align: center;
    background-color: rgba(0, 0, 0, 0.02);
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

/* Estilos para el cuadro de slogan */
.hero-slogan-box {
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    padding: 1rem 1.2rem;
    margin: 0.5rem 1rem;
    text-align: center;
}

.hero-slogan {
    font-size: 0.85rem;
    font-style: italic;
    font-weight: 500;
    margin-bottom: 0.8rem;
    color: #2a2a2a;
}

/* Ajustes para el carousel */
.carousel-control-prev, .carousel-control-next {
    width: 8%;
    opacity: 0.7;
}

.carousel-control-prev-icon, .carousel-control-next-icon {
    background-color: rgba(0,0,0,0.3);
    border-radius: 50%;
    padding: 10px;
}

.carousel-indicators {
    bottom: -5px;
}

.carousel-indicators button {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    margin: 0 4px;
    background-color: var(--bs-primary, #007bff);
}

/* Media queries */
@media (max-width: 992px) {
    .hero-news-item {
        height: 450px;
    }
    
    .hero-news-item img {
        height: 250px;
    }
    
    .hero-news-title {
        font-size: 1.1rem;
    }
    
    .hero-news-excerpt {
        font-size: 0.75rem;
    }
    
    .hero-column-title {
        font-size: 0.85rem;
    }
    
    .hero-column-excerpt {
        font-size: 0.7rem;
    }
}

@media (max-width: 768px) {
    .hero-news-section {
        padding: 1.2rem 0;
    }
    
    .hero-news-item {
        height: 420px;
        margin-bottom: 15px;
    }
    
    .hero-news-item img {
        height: 220px;
    }
    
    .hero-columns-section {
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .hero-news-item {
        height: 380px;
    }
    
    .hero-news-item img {
        height: 200px;
    }
    
    .hero-news-content {
        padding: 1rem;
    }
    
    .hero-news-title {
        font-size: 1rem;
    }
    
    .hero-news-excerpt {
        font-size: 0.75rem;
        margin-bottom: 0.7rem;
    }
}
</style>
@endpush

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('newsletterForm');
    if (!form) return;
    
    const responseDiv = document.getElementById('newsletterResponse');
    const submitButton = document.getElementById('newsletterSubmit');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Cambiar estado del botón
        const originalButtonHtml = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        
        // Limpiar mensajes anteriores
        responseDiv.innerHTML = '';
        
        // Obtener datos del formulario
        const formData = new FormData(form);
        
        // Enviar solicitud al servidor
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Restaurar botón
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonHtml;
            
            // Manejar respuesta
            if (data.success) {
                // Éxito
                responseDiv.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show p-2 mt-2 small" role="alert">
                        <i class="fas fa-check-circle me-1"></i> ${data.message}
                        <button type="button" class="btn-close btn-sm p-1" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                form.reset();
                
                // Mostrar SweetAlert si está disponible
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Suscripción Exitosa!',
                        text: data.message,
                        timer: 3000
                    });
                }
            } else if (data.info) {
                // Información (por ejemplo, ya suscrito)
                responseDiv.innerHTML = `
                    <div class="alert alert-info alert-dismissible fade show p-2 mt-2 small" role="alert">
                        <i class="fas fa-info-circle me-1"></i> ${data.message}
                        <button type="button" class="btn-close btn-sm p-1" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                
                // Mostrar SweetAlert si está disponible
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Información',
                        text: data.message
                    });
                }
            } else {
                // Error genérico
                responseDiv.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show p-2 mt-2 small" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> ${data.message || 'Ocurrió un error al procesar tu solicitud.'}
                        <button type="button" class="btn-close btn-sm p-1" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }
        })
        .catch(error => {
            // Error de red u otra causa
            console.error('Error:', error);
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonHtml;
            
            responseDiv.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show p-2 mt-2 small" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> Error de conexión. Inténtalo de nuevo más tarde.
                    <button type="button" class="btn-close btn-sm p-1" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        });
    });
});
</script>



<script>
/**
 * Script mejorado para el carrusel de la sección hero
 */
document.addEventListener('DOMContentLoaded', function() {
    // Esperar a que todos los recursos se carguen completamente
    window.addEventListener('load', function() {
        initializeCarousel();
    });
    
    function initializeCarousel() {
        const carouselElement = document.getElementById('heroNewsCarousel');
        
        if (!carouselElement) {
            console.error('Carousel no encontrado en el DOM');
            return;
        }
        
        // Primero, verificar si hay un carrusel ya inicializado y destruirlo
        if (carouselElement.carousel) {
            carouselElement.carousel.dispose();
        }
        
        // Verificar cuántos slides hay
        const items = carouselElement.querySelectorAll('.carousel-item');
        
        if (items.length <= 1) {
            console.warn('El carousel tiene muy pocos elementos para mostrar transiciones');
            return;
        }
        
        try {
            // Configuración óptima para transiciones suaves
            const carouselOptions = {
                interval: 5000,         // 5 segundos entre transiciones
                keyboard: true,         // Permitir navegación con teclado
                pause: 'hover',         // Pausar en hover para mejor UX
                ride: 'carousel',       // Iniciar automáticamente
                wrap: true,             // Continuar al inicio después del último slide
                touch: true             // Habilitar gestos táctiles
            };
            
            // Inicializar carrusel con opciones optimizadas
            const carousel = new bootstrap.Carousel(carouselElement, carouselOptions);
            
            // Guardar referencia para posible uso futuro
            carouselElement.carousel = carousel;
            
            // Mejorar las transiciones mediante CSS personalizado
            const transitionStyle = document.createElement('style');
            transitionStyle.textContent = `
                #heroNewsCarousel .carousel-item {
                    transition: transform 0.8s ease-in-out;
                }
                
                #heroNewsCarousel .carousel-fade .carousel-item {
                    opacity: 0;
                    transition: opacity 0.8s ease-in-out;
                }
                
                #heroNewsCarousel .carousel-fade .carousel-item.active {
                    opacity: 1;
                }
            `;
            document.head.appendChild(transitionStyle);
            
            // Forzar un ciclo inicial después de un breve retraso
            setTimeout(() => {
                carousel.cycle();
            }, 200);
            
            console.log('Carrusel inicializado correctamente con transiciones mejoradas');
        } catch (error) {
            console.error('Error al inicializar el carrusel:', error);
        }
    }
});
</script>


<!-- Script para manejar errores de carga de imágenes -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Agregar una clase CSS para facilitar el ajuste de texto
    const style = document.createElement('style');
    style.textContent = `
        .line-clamp-4 {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .h5 {
            font-size: 1.25rem;
        }
    `;
    document.head.appendChild(style);
});
</script>
@endpush