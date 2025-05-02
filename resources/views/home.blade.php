<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('content')
    <!-- Hero Section con Noticias y Columnas -->
    <section class="hero-news-section">
        <div class="hero-overlay">
            <div class="container">
                <div class="row">
                    
                
                
                    <div class="col-lg-8">
                        <!-- Slider de noticias principales -->
                    <div id="heroNewsCarousel" class="carousel slide carousel-fade" data-bs-ride="false">
                        <div class="carousel-indicators">
                            @foreach($featuredNews as $index => $featured)
                            <button type="button" data-bs-target="#heroNewsCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                            @endforeach
                        </div>
                        <div class="carousel-inner">
                            @foreach($featuredNews as $index => $featured)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <a href="{{ route('news.show', $featured->slug ?? $featured->id) }}" class="hero-news-link">
                                    <div class="hero-news-item">
                                        <img src="{{ $getImageUrl($featured->image, 'news', 'large') }}" class="d-block w-100" alt="{{ $featured->title }}" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/news-default-large.jpg') }}';">
                                        <div class="hero-news-content">
                                            @if(isset($featured->category))
                                            <span class="hero-news-category" style="{{ $getCategoryStyle($featured->category) }}">
                                                <i class="fas {{ $getCategoryIcon($featured->category) }} me-1"></i>
                                                {{ $featured->category->name }}
                                            </span>
                                            @endif
                                            <h2 class="hero-news-title">{{ $featured->title }}</h2>
                                            <p class="hero-news-excerpt">{{ $featured->excerpt }}</p>
                                            <div class="hero-news-meta">
                                                <span><i class="far fa-clock"></i> {{ $featured->created_at->locale('es')->diffForHumans() }}</span>
                                                <span><i class="far fa-eye"></i> {{ number_format($featured->views / 1000, 1) }}k lecturas</span>
                                            </div>
                                            <a href="{{ route('news.show', $featured->slug ?? $featured->id) }}" class="btn btn-primary btn-sm">Leer artículo</a>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#heroNewsCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#heroNewsCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                    </div>

            
                    <div class="col-lg-4">








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











                    </div>
                </div>
            </div>
        </div>
    </section>

   
    <!-- Sección de Videos Destacados - Diseño Compacto -->
    <section class="py-3 border-top border-bottom" style="background-color: #f0f2f5;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <h5 class="mb-md-0 mb-3 d-flex align-items-center fw-semibold" style="font-size: 0.95rem;">
                        <i class="fas fa-video text-primary me-2"></i> Videos Destacados
                        <span class="badge bg-primary bg-opacity-75 rounded-pill ms-2 fs-9">{{ $featuredVideos->count() }}</span>
                    </h5>
                </div>
                <div class="col-md-9">
                    <div class="video-scroll-container">
                        <div class="d-flex gap-3 overflow-auto pb-1 hide-scrollbar">
                            @foreach($featuredVideos->take(5) as $video)
                            <div class="video-item position-relative flex-shrink-0" style="width: 180px;">
                                <a href="{{ route('videos.show', $video->id) }}" class="text-decoration-none">
                                    <div class="position-relative rounded overflow-hidden" style="height: 100px;">
                                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-100 h-100" style="object-fit: cover;">
                                        <div class="position-absolute bottom-0 end-0 bg-dark bg-opacity-75 text-white px-2 py-1 m-1 rounded-pill fs-9">
                                            <i class="fas fa-play-circle me-1"></i> {{ $video->duration }}
                                        </div>
                                        <div class="position-absolute top-0 start-0 m-1">
                                            <span class="badge bg-{{ $video->platform->code === 'youtube' ? 'danger' : ($video->platform->code === 'vimeo' ? 'info' : 'primary') }} bg-opacity-85 fs-9">
                                                <i class="fab fa-{{ strtolower($video->platform->code) }}"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <h6 class="mt-2 mb-0 text-dark" style="font-size: 0.85rem; line-height: 1.2; min-height: auto; height: auto; overflow: visible;">{{ $video->title }}</h6>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="text-end mt-2 d-none d-md-block">
                        <a href="{{ route('videos.index') }}" class="btn btn-sm btn-outline-primary px-3 fs-9">
                            Ver galería completa <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Sección de Categorías Destacadas - Diseño Equilibrado -->
    <section class="py-3 border-bottom" style="background-color: #f9f9f9;">
    <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <h5 class="mb-md-0 mb-3 d-flex align-items-center fw-semibold" style="font-size: 0.95rem;">
                        <i class="fas fa-th-large text-primary me-2"></i> Categorías Destacadas
                        <span class="badge bg-primary bg-opacity-75 rounded-pill ms-2 fs-9">{{ $featuredCategories->count() }}</span>
                    </h5>
                </div>
                <div class="col-md-9">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                        @foreach($featuredCategories as $category)
                        <a href="{{ route('news.by.category', $category->slug) }}" class="text-decoration-none" aria-label="Ver artículos de {{ $category->name }}">
                            <span class="badge category-badge mb-1" 
                                style="{{ $getCategoryStyle($category) }} opacity: 0.85; font-size: 0.8rem; font-weight: 500; transition: all 0.3s ease;">
                                <i class="fas {{ $getCategoryIcon($category) }} me-1"></i>
                                {{ $category->name }}
                                @if(isset($category->news_count))
                                <span class="badge bg-white text-dark ms-1 rounded-pill" style="opacity: 0.9;">{{ $category->news_count }}</span>
                                @endif
                                @php
                                    $newArticles = $category->news_count_recent ?? rand(1, 5);
                                @endphp
                                @if($newArticles > 0)
                                    <span class="badge bg-white text-primary border-start ms-1 fs-9 d-none d-lg-inline-block">
                                        +{{ $newArticles }}
                                    </span>
                                @endif
                            </span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>


  

    <!-- Sección Noticias Recientes y Lo Más Leído (COMPLETA) -->
    <section class="py-3 border-top">
        <div class="container">
            <div class="row">



                
                <!-- Columna izquierda: Noticias Recientes - Diseño compacto sin imágenes -->
<!-- Columna izquierda: Noticias Recientes - Diseño compacto sin imágenes -->
<div class="col-lg-8">
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 d-flex align-items-center fs-6">
                <i class="fas fa-newspaper text-primary me-2"></i> Noticias Recientes
            </h5>
            <span class="badge bg-primary rounded-pill px-2 py-1 fs-9">
                {{ $recentNews->count() }} artículos
            </span>
        </div>
        <div class="card-body py-3">
            <!-- Uso de grilla para mostrar dos columnas en pantallas medianas y grandes -->
            <div class="row g-3">
                <!-- Aumentamos el número de noticias al doble -->
                @foreach($recentNews->take(16) as $recent)
                <!-- Noticia en formato reducido - 6 columnas en pantallas md y superiores -->
                <div class="col-md-6">
                    <div class="border-bottom pb-2 mb-2 h-100">
                        <!-- Metadatos superiores más compactos -->
                        <div class="d-flex justify-content-between mb-1 flex-wrap">
                            <!-- Categoría - Ahora con color en el texto en lugar del fondo -->
                            @if(isset($recent->category))
                            <span class="badge bg-light border px-2 py-1 rounded-pill"
                                style="color: {{ str_replace('background-color:', '', $getCategoryStyle($recent->category)) }} font-size: 0.65rem;">
                                <i class="fas {{ $getCategoryIcon($recent->category) }} me-1"></i>
                                {{ $recent->category->name }}
                            </span>
                            @endif
                            
                            <!-- Indicador de nuevo más sobrio -->
                            @if($recent->created_at->locale('es')->diffInHours(now()) < 48)
                            <span class="badge bg-light text-secondary px-2 py-1 rounded-pill fs-9 border">
                                <i class="far fa-clock me-1"></i>Nuevo
                            </span>
                            @endif
                        </div>

                        <!-- Título completo con letra más pequeña -->
                        <h6 class="fw-bold mb-1 fs-7">
                            <a href="{{ route('news.show', $recent->slug ?? $recent->id) }}" class="text-decoration-none text-dark">
                                {{ $recent->title }}
                            </a>
                        </h6>
                        
                        <!-- Extracto más corto -->
                        <p class="card-text text-muted small line-clamp-2 mb-2">{{ Str::limit($recent->excerpt, 100) }}</p>
                        
                        <!-- Fuente con URL -->
                        @if($recent->source)
                        <div class="mb-2">
                            <span class="text-muted small">
                                <i class="fas fa-external-link-alt me-1"></i> 
                                @if($recent->source_url)
                                    <a href="{{ $recent->source_url }}" class="text-primary" target="_blank">{{ $recent->source }}</a>
                                @else
                                    {{ $recent->source }}
                                @endif
                            </span>
                        </div>
                        @endif
                        
                        <!-- Metadatos inferiores más compactos -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="text-muted small me-2">
                                    <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($recent->created_at)->locale('es')->format('d/m/Y') }}
                                </span>
                                <span class="text-muted small me-2">
                                    <i class="far fa-eye me-1"></i> {{ number_format($recent->views ?? rand(150, 3000)) }}
                                </span>
                                <span class="text-muted small">
                                    <i class="far fa-comment me-1"></i> {{ number_format($recent->comments_count ?? 0) }}
                                </span>
                            </div>
                            <a href="{{ route('news.show', $recent->slug ?? $recent->id) }}" class="btn btn-sm btn-link text-primary p-0 small">
                                Leer <i class="fas fa-arrow-right ms-1"></i>
                            </a>
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






                
                <!-- Columna derecha: Lo Más Leído -->
                <div class="col-lg-4">
                    <!-- Slogan Box mejorado -->
                    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
                        <div class="hero-slogan-box bg-gradient-primary text-white text-center p-4">
                            <i class="fas fa-lightbulb fs-1 mb-2 text-warning opacity-75"></i>
                            <p class="hero-slogan h5 mb-3">"El futuro del conocimiento es artificialmente inteligente"</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('news.index') }}" class="btn btn-light btn-sm px-3">
                                    <i class="fas fa-newspaper me-1"></i> Más noticias
                                </a>
                                <a href="{{ route('research.index') }}" class="btn btn-outline-light btn-sm px-3">
                                    <i class="fas fa-flask me-1"></i> Investigación
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lo más leído -->
                    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
                        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 d-flex align-items-center fs-6">
                                <i class="fas fa-chart-line text-primary me-2"></i> Lo más leído - Noticias
                            </h5>
                           
                        </div>
                        <div class="card-body py-3">
                        <div class="most-read-list">
                            @foreach($popularNews as $index => $popular)
                            <!-- Artículo mejorado -->
                            <div class="d-flex mb-3 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="position-relative me-3 flex-shrink-0">
                                    <div class="position-absolute top-0 start-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                        style="width: 24px; height: 24px; font-size: 0.75rem; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <a href="{{ route('news.show', $popular->slug ?? $popular->id) }}" class="d-block overflow-hidden rounded">
                                        <img src="{{ $getImageUrl($popular->image, 'news', 'small') }}" 
                                            class="rounded popular-news-img" 
                                            width="60" height="60" 
                                            alt="{{ $popular->title }}" 
                                            loading="lazy"
                                            style="object-fit: cover;" 
                                            onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/news-default-small.jpg') }}';">
                                    </a>
                                </div>
                                <div class="overflow-hidden">
                                    <h6 class="mb-1 fs-7 line-clamp-2">
                                        <a href="{{ route('news.show', $popular->slug ?? $popular->id) }}" class="text-decoration-none text-dark">
                                            {{ $popular->title }}
                                        </a>
                                    </h6>
                                    <div class="d-flex flex-wrap align-items-center mb-1">
                                        @if(isset($popular->category))
                                        <span class="badge bg-light border me-2" style="color: {{ str_replace('background-color:', '', $getCategoryStyle($popular->category)) }} font-size: 0.65rem;">
                                            <i class="fas {{ $getCategoryIcon($popular->category) }} me-1"></i>
                                            {{ $popular->category->name }}
                                        </span>
                                        @endif
                                        <span class="text-muted fs-9">{{ $popular->created_at->locale('es')->diffForHumans() }}</span>
                                    </div>
                                    <!-- Estadísticas de popularidad -->
                                    <div class="d-flex align-items-center">
                                        <span class="me-2 text-muted fs-9">
                                            <i class="far fa-eye me-1"></i>{{ number_format($popular->views ?? 0) }}
                                        </span>
                                        <span class="me-2 text-muted fs-9">
                                            <i class="fas fa-fire text-danger me-1"></i>{{ rand(20, 95) }}% 
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                            
                                                        
                            <!-- Newsletter dentro de lo más leído - Estilo mejorado -->
                            <div class="mt-3 pt-3 border-top">
                                <div class="bg-dark text-white py-3 rounded position-relative overflow-hidden">
                                    <!-- Elementos decorativos de fondo -->
                                    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden">
                                        <div class="position-absolute end-0 top-50 translate-middle-y opacity-10">
                                            <i class="fas fa-paper-plane fa-2x"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="position-relative px-3">
                                        <h5 class="text-center mb-2 fs-6 fw-bold">
                                            <span class="d-inline-block border-bottom border-2 pb-1">Suscríbete al Newsletter</span>
                                        </h5>
                                        
                                        <form id="newsletterForm" class="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST">
                                            @csrf
                                            <div class="input-group mb-2">
                                                <input type="email" class="form-control" id="newsletterEmail" name="email" placeholder="Tu correo electrónico" required>
                                                <button class="btn btn-primary" type="submit" id="newsletterSubmit">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                            <p class="text-white-50 text-center small mb-0">
                                                <i class="fas fa-shield-alt me-1"></i> Recibirás las últimas noticias sin spam
                                            </p>
                                            
                                            <!-- Contenedor para mensajes de respuesta -->
                                            <div id="newsletterResponse" class="mt-2">
                                                @if(session('subscription_success'))
                                                <div class="alert alert-success alert-dismissible fade show p-2 small" role="alert">
                                                    <i class="fas fa-check-circle me-1"></i> {{ session('subscription_success') }}
                                                    <button type="button" class="btn-close btn-sm p-1" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                                @endif
                                                
                                                @if(session('subscription_info'))
                                                <div class="alert alert-info alert-dismissible fade show p-2 small" role="alert">
                                                    <i class="fas fa-info-circle me-1"></i> {{ session('subscription_info') }}
                                                    <button type="button" class="btn-close btn-sm p-1" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                                @endif
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>




                        </div>
                    </div>
                </div>






            </div>
        </div>
    </section>




    <!-- Banner de título destacado para Investigación (estilo actualizado) -->
    <div class="py-3 bg-dark text-white mb-0 position-relative overflow-hidden">
        <!-- Elementos decorativos de fondo -->
        <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden">
            <div class="position-absolute start-0 top-50 translate-middle-y opacity-10">
                <i class="fas fa-brain fa-3x"></i>
            </div>
            <div class="position-absolute end-0 top-50 translate-middle-y opacity-10">
                <i class="fas fa-microchip fa-3x"></i>
            </div>
        </div>
        
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h3 class="mb-0 text-uppercase fw-bold fs-4">
                        <span class="d-inline-block border-bottom border-2 pb-1">Investigación y Análisis</span>
                    </h3>
                    <p class="mb-0 mt-1 text-white-50 fs-6">Explorando la tecnología e inteligencia artificial del futuro</p>
                </div>
            </div>
        </div>
    </div>




    <!-- Artículos de Investigación -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="row mb-3">
                <div class="col-md-8">
                    <h3 class="section-title fs-5">Investigación y Análisis</h3>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('research.index') }}" class="btn btn-outline-primary btn-sm">
                        Ver todos <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            
            <div class="row g-3">
                
            
            
            
            
           <!-- Contenido principal (izquierda) -->
            <div class="col-lg-8">
                <div class="mb-3">
                    <h3 class="border-start border-4 border-primary ps-3 fs-6">Investigaciones Recientes</h3>
                    <p class="text-muted small">Descubre nuestros últimos artículos de investigación</p>
                </div>
                
                <div class="row g-4">
                @foreach($researchArticles as $research)
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden hover-scale transition-300">
                            <div class="p-3 border-left border-3" style="border-color: {{ str_replace('background-color:', '', $getCategoryStyle($research->category)) }} !important;">
                                <!-- Categoría -->
                                @if(isset($research->category))
                                <div class="mb-2">
                                    <span class="badge bg-light border rounded-pill" 
                                        style="color: {{ str_replace('background-color:', '', $getCategoryStyle($research->category)) }}; padding: 0.35rem 0.6rem; font-size: 0.65rem; font-weight: 600;">
                                        <i class="fas {{ $getCategoryIcon($research->category) }} me-1"></i>
                                        {{ $research->category->name }}
                                    </span>
                                </div>
                                @endif
                                
                                <!-- Título -->
                                <h5 class="card-title mb-2 fw-bold fs-6">
                                    <a href="{{ route('research.show', $research->slug ?? $research->id) }}" class="text-decoration-none text-dark stretched-link">
                                        {{ $research->title }}
                                    </a>
                                </h5>
                                
                                <!-- Texto extracto -->
                                <p class="card-text text-secondary mb-3 small">{{ Str::limit($research->excerpt, 120) }}</p>
                                
                                <!-- Información del autor y fecha -->
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark" style="font-size: 0.75rem;">
                                            {{ $research->author }}
                                        </span>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            <i class="far fa-calendar-alt me-1"></i>{{ $research->created_at->locale('es')->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Métricas y botón de leer más -->
                                <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-auto">
                                    <div class="d-flex align-items-center text-muted small">
                                        <span class="me-2"><i class="far fa-eye me-1"></i>{{ $research->views ?? 0 }}</span>
                                        <span><i class="far fa-comment me-1"></i>{{ $research->comments_count ?? 0 }}</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="btn btn-sm btn-outline-primary rounded-pill px-2" style="font-size: 0.7rem;">Leer más</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
                
                <!-- Paginación (solo si es una instancia de paginación) -->
                @if(isset($researchArticles) && method_exists($researchArticles, 'hasPages') && $researchArticles->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $researchArticles->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>


                <!-- Sidebar derecho: Investigaciones destacadas y más -->
                <div class="col-lg-4">



                    <!-- Investigaciones Destacadas -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
                        <div class="card-header bg-primary text-white p-2">
                            <h5 class="mb-0 d-flex align-items-center fw-bold fs-6">
                                <i class="fas fa-star me-2"></i> Investigaciones Destacadas
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="featured-research-list">
                                @foreach($featuredResearch as $featured)
                                    @if($featured->status === 'published' || $featured->status === 'active')
                                    <!-- Investigación -->
                                    <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }} hover-bg-light transition-300">
                                        <div class="row g-0">
                                            <div class="col">
                                                <!-- Indicador de destacado -->
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-warning text-white rounded-pill me-2" style="font-size: 0.65rem; padding: 0.15rem 0.5rem;">
                                                        <i class="fas fa-star me-1"></i> Destacado
                                                    </span>
                                                    
                                                    @if(isset($featured->category))
                                                    <span class="badge bg-light border rounded-pill" 
                                                        style="color: {{ str_replace('background-color:', '', $getCategoryStyle($featured->category)) }}; font-size: 0.65rem; padding: 0.15rem 0.5rem;">
                                                        <i class="fas {{ $getCategoryIcon($featured->category) }} me-1"></i>
                                                        {{ $featured->category->name }}
                                                    </span>
                                                    @endif
                                                </div>
                                                
                                                <!-- Título -->
                                                <h6 class="mb-2 fw-semibold lh-sm fs-7">
                                                    <a href="{{ route('research.show', $featured->slug ?? $featured->id) }}" class="text-decoration-none stretched-link text-dark">
                                                        {{ Str::limit($featured->title, 80) }}
                                                    </a>
                                                </h6>
                                                
                                                <!-- Información adicional -->
                                                <div class="d-flex justify-content-between align-items-center" style="font-size: 0.7rem; color: #6c757d;">
                                                    <div>
                                                        <i class="fas fa-user-edit me-1"></i> {{ $featured->author }}
                                                    </div>
                                                    <div>
                                                        <span class="me-2"><i class="fas fa-comment me-1"></i> {{ $featured->comments_count }}</span>
                                                        <span><i class="fas fa-quote-right me-1"></i> {{ $featured->citations }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer bg-light text-center p-2">
                            <a href="{{ route('research.index') }}" class="btn btn-primary rounded-pill px-3" style="font-size: 0.75rem;">
                                Ver todas las investigaciones
                            </a>
                        </div>
                    </div>
                    



                    <!-- Más comentados -->
<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="card-header bg-info text-white p-2">
        <h5 class="mb-0 d-flex align-items-center fw-bold fs-6">
            <i class="fas fa-comments me-2"></i> Más Comentados - Investigaciones
        </h5>
    </div>
    <div class="list-group list-group-flush">
        @foreach($mostCommented as $commented)
            <a href="{{ route('research.show', $commented->slug ?? $commented->id) }}" 
            class="list-group-item list-group-item-action px-3 py-2 border-bottom hover-bg-light transition-300">
                <div class="d-flex align-items-center mb-1">
                    @if(isset($commented->category) && $commented->category)
                    <span class="badge bg-light border rounded-pill me-2" style="color: {{ str_replace('background-color:', '', $getCategoryStyle($commented->category)) }}; font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                        <i class="fas {{ $getCategoryIcon($commented->category) }} me-1"></i>
                        {{ $commented->category->name }}
                    </span>
                    @endif
                    <span class="text-info small fw-bold ms-auto" style="font-size: 0.7rem;">
                        <i class="fas fa-comment-dots me-1"></i> {{ $commented->comments_count }}
                    </span>
                </div>
                
                <h6 class="mb-1 fw-semibold fs-7 text-dark">
                    {{ Str::limit($commented->title, 65) }}
                </h6>
                
                <div class="d-flex align-items-center text-muted" style="font-size: 0.7rem;">
                    <i class="fas fa-user-circle me-1"></i>
                    {{ $commented->author }}
                    <span class="ms-auto"><i class="far fa-clock me-1"></i>{{ $commented->created_at->locale('es')->diffForHumans() }}</span>
                </div>
            </a>
        @endforeach
    </div>
    
    <!-- Panel de categorías populares -->
    @php
        $availableCategories = collect();
        
        // Intentar obtener categorías de las colecciones existentes
        if(isset($researchCategories) && count($researchCategories) > 0) {
            $availableCategories = $researchCategories;
        } elseif(isset($featuredResearch)) {
            $availableCategories = $featuredResearch->pluck('category')
                                ->filter(function($category) { return !is_null($category); })
                                ->unique('id');
        }
    @endphp
    
    @if(count($availableCategories) > 0)
    <div class="bg-light p-2">
        <h6 class="fw-bold mb-2 fs-7 text-dark">Categorías populares</h6>
        <div class="d-flex flex-wrap gap-2">
            @foreach($availableCategories as $category)
            <a href="{{ route('research.index', ['category' => $category->id]) }}" 
               class="badge bg-light border rounded-pill text-decoration-none" 
               style="color: {{ str_replace('background-color:', '', $getCategoryStyle($category)) }}; font-size: 0.7rem;">
                <i class="fas {{ $getCategoryIcon($category) }} me-1"></i> {{ $category->name }}
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>






                </div>
            </div>
        </div>
    </section>




    <!-- Banner de título para Columnas - Versión compacta -->
    <div class="py-3 bg-dark text-white mb-0 position-relative overflow-hidden">
        <!-- Elementos decorativos de fondo -->
        <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden">
            <div class="position-absolute start-0 top-50 translate-middle-y opacity-10">
                <i class="fas fa-feather-alt fa-3x"></i>
            </div>
            <div class="position-absolute end-0 top-50 translate-middle-y opacity-10">
                <i class="fas fa-pen fa-3x"></i>
            </div>
        </div>
        
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h3 class="mb-0 text-uppercase fw-bold fs-4">
                        <span class="d-inline-block border-bottom border-2 pb-1">Columnas de Opinión</span>
                    </h3>
                    <p class="mb-0 mt-1 text-white-50 fs-6">Análisis y perspectivas exclusivas de nuestros columnistas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Columnas - 4 destacadas sin imágenes -->
    <section class="py-4 bg-white">
        <div class="container">
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-secondary me-2" style="width: 4px; height: 20px;"></div>
                        <h4 class="fs-5 mb-0">Columnas Destacadas</h4>
                    </div>
                    <a href="{{ route('columns.index') }}" class="btn btn-sm btn-outline-secondary">
                        Ver todas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
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
    


    @include('components.home-podcasts-section')
    



    @include('components.home-videos-section')

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

<!-- Estilos para sección de videos compacta -->
<style>
.video-scroll-container {
    position: relative;
}

.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.hide-scrollbar::-webkit-scrollbar {
    display: none;
}

.video-item {
    transition: transform 0.2s ease;
}

.video-item:hover {
    transform: translateY(-3px);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
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