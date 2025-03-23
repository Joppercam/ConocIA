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
                        <div id="heroNewsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000" data-bs-wrap="true">
                            <div class="carousel-indicators">
                                @foreach($featuredNews as $index => $featured)
                                <button type="button" data-bs-target="#heroNewsCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner">
                                @foreach($featuredNews as $index => $featured)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}" data-bs-interval="5000">
                                    <a href="{{ route('news.show', $featured->slug ?? $featured->id) }}" class="hero-news-link">
                                        <div class="hero-news-item">
                                            <img src="{{ $getImageUrl($featured->image, 'news', 'large') }}" class="d-block w-100" alt="{{ $featured->title }}" onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/news-default-large.jpg') }}';">
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
                                            <span class="me-2"><i class="far fa-eye me-1"></i>{{ $column->views ?? rand(100, 999) }}</span>
                                            <span><i class="far fa-comment me-1"></i>{{ $column->comments_count ?? rand(0, 15) }}</span>
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


    <!-- Sección de Categorías Destacadas -->
    <section class="py-3 border-bottom bg-light">
       <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3">
                <h5 class="mb-md-0 mb-3 d-flex align-items-center fw-bold" style="font-size: 0.95rem;">
                    <i class="fas fa-tags text-primary me-2"></i> Categorías Destacadas
                    <span class="badge bg-primary rounded-pill ms-2 fs-9">{{ $featuredCategories->count() }}</span>
                </h5>
            </div>
            <div class="col-md-9">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    @foreach($featuredCategories as $category)
                    <a href="{{ route('news.by.category', $category->slug) }}" class="text-decoration-none" aria-label="Ver artículos de {{ $category->name }}">
                        <span class="badge category-badge mb-1 position-relative" 
                              style="{{ $getCategoryStyle($category) }} font-size: 0.8rem; transition: all 0.3s ease;">
                            <i class="fas {{ $getCategoryIcon($category) }} me-1"></i>
                            {{ $category->name }}
                            @if(isset($category->news_count))
                            <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $category->news_count }}</span>
                            @endif
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none d-lg-inline-block fs-9">
                                @php
                                    $newArticles = $category->news_count_recent ?? rand(1, 5);
                                @endphp
                                @if($newArticles > 0)
                                    +{{ $newArticles }} <span class="d-none d-xl-inline">nuevo{{ $newArticles > 1 ? 's' : '' }}</span>
                                @endif
                            </span>
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



                <!-- Columna izquierda: Noticias Recientes -->
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
                            <div class="row g-3">
                                @foreach($recentNews as $recent)
                                <!-- Noticia mejorada -->
                                <div class="col-12">
                                    <div class="row g-0 {{ !$loop->last ? 'border-bottom pb-3 mb-3' : '' }}">
                                        <div class="col-md-4">
                                            <div class="position-relative overflow-hidden rounded">
                                                @if(isset($recent->category))
                                                <span class="position-absolute top-0 end-0 text-white px-2 py-0 m-2 rounded-pill"
                                                    style="{{ $getCategoryStyle($recent->category) }} font-size: 0.7rem;">
                                                    <i class="fas {{ $getCategoryIcon($recent->category) }} me-1"></i>
                                                    {{ $recent->category->name }}
                                                </span>
                                                @endif
                                                <!-- Indicador de nuevo si es menor a 48 horas -->
                                                @if($recent->created_at->locale('es')->diffInHours(now()) < 48)
                                                <span class="position-absolute top-0 start-0 bg-danger text-white px-2 py-0 m-2 rounded-pill fs-9">
                                                    <i class="fas fa-star me-1"></i>NUEVO
                                                </span>
                                                @endif
                                                <a href="{{ route('news.show', $recent->slug ?? $recent->id) }}" class="d-block overflow-hidden">
                                                    <img src="{{ $getImageUrl($recent->image, 'news', 'medium') }}" 
                                                        class="img-fluid rounded news-img w-100" 
                                                        loading="lazy" 
                                                        alt="{{ $recent->title }}" 
                                                        onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/news-default-medium.jpg') }}';">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-8 ps-md-3 mt-3 mt-md-0">
                                            <!-- Metadatos en la parte superior -->
                                            <div class="d-flex align-items-center mb-2 flex-wrap">
                                                <span class="text-muted small me-3">
                                                    <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($recent->created_at)->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
                                                </span>
                                                <span class="text-muted small me-3">
                                                    <i class="far fa-eye me-1"></i> {{ number_format($recent->views ?? rand(150, 3000)) }} lecturas
                                                </span>
                                                <span class="text-muted small">
                                                    <i class="far fa-clock me-1"></i> {{ ceil(str_word_count($recent->content ?? '') / 200) ?? rand(3, 8) }} min lectura
                                                </span>
                                            </div>
                                            
                                            <!-- Título con enlace -->
                                            <h6 class="card-title fw-bold mb-2">
                                                <a href="{{ route('news.show', $recent->slug ?? $recent->id) }}" class="text-decoration-none text-dark">
                                                    {{ $recent->title }}
                                                </a>
                                            </h6>
                                            
                                            <!-- Extracto con limitador de líneas -->
                                            <p class="card-text text-muted small line-clamp-3 mb-2">{{ $recent->excerpt }}</p>
                                            
                                            <!-- Autor y botón de lectura -->
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $getImageUrl($recent->author->avatar ?? null, 'avatars', 'small') }}" 
                                                        class="rounded-circle me-2" 
                                                        width="24" height="24" 
                                                        alt="{{ $recent->author->name ?? 'Autor' }}"
                                                        loading="lazy"
                                                        onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/avatar-default.jpg') }}';">
                                                    <span class="small text-muted">{{ $recent->author->name ?? 'ConocIA' }}</span>
                                                </div>
                                                <a href="{{ route('news.show', $recent->slug ?? $recent->id) }}" class="btn btn-sm btn-outline-primary small">
                                                    Leer más <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
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
                                <i class="fas fa-chart-line text-primary me-2"></i> Lo más leído
                            </h5>
                            <span class="badge bg-danger rounded-pill px-2 fs-9">POPULAR</span>
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
                                            <span class="badge me-2" style="{{ $getCategoryStyle($popular->category) }} font-size: 0.65rem;">
                                                <i class="fas {{ $getCategoryIcon($popular->category) }} me-1"></i>
                                                {{ $popular->category->name }}
                                            </span>
                                            @endif
                                            <span class="text-muted fs-9">{{ $popular->created_at->locale('es')->diffForHumans() }}</span>
                                        </div>
                                        <!-- Estadísticas de popularidad -->
                                        <div class="d-flex align-items-center">
                                            <span class="me-2 text-muted fs-9">
                                                <i class="far fa-eye me-1"></i>{{ number_format($popular->views ?? rand(1500, 10000)) }}
                                            </span>
                                            <span class="me-2 text-muted fs-9">
                                                <i class="fas fa-fire text-danger me-1"></i>{{ rand(20, 95) }}% 
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <!-- Newsletter dentro de lo más leído -->
                            <div class="mt-3 pt-3 border-top">
                                <h5 class="text-center fs-6 mb-3">Suscríbete al newsletter</h5>
                                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form">
                                    @csrf
                                    <div class="input-group mb-2">
                                        <input type="email" class="form-control" name="email" placeholder="Tu correo electrónico" required>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                    <p class="text-muted text-center small mb-0">
                                        <i class="fas fa-shield-alt me-1"></i> Recibirás las últimas noticias sin spam
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>






            </div>
        </div>
    </section>




    <!-- Banner de título destacado para Investigación -->
    <div class="py-4 bg-gradient-primary text-white mb-0 position-relative overflow-hidden">
        <!-- Elementos decorativos de fondo -->
        <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden">
            <div class="position-absolute start-0 top-50 translate-middle-y opacity-10">
                <i class="fas fa-brain fa-5x"></i>
            </div>
            <div class="position-absolute end-0 top-50 translate-middle-y opacity-10">
                <i class="fas fa-microchip fa-5x"></i>
            </div>
        </div>
        
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h2 class="mb-0 text-uppercase fw-bold">
                        <span class="d-inline-block border-bottom border-2 pb-1">Investigación y Análisis</span>
                    </h2>
                    <p class="lead mb-0 mt-2 text-light">Explorando la tecnología e inteligencia artificial del futuro</p>
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
                            @if($research->status === 'published' || $research->status === 'active')
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden hover-scale transition-300">
                                    <div class="position-relative">
                                        <a href="{{ route('research.show', $research->slug ?? $research->id) }}" class="ratio ratio-16x9 overflow-hidden bg-light">
                                            <img src="{{ $getImageUrl($research->image, 'research', 'medium') }}" 
                                                class="card-img-top object-fit-cover" 
                                                alt="{{ $research->title }}" 
                                                onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/research-default-medium.jpg') }}';">
                                        </a>
                                        @if(isset($research->category))
                                        <div class="position-absolute top-0 start-0 m-3">
                                            <span class="badge rounded-pill shadow-sm" style="{{ $getCategoryStyle($research->category) }} padding: 0.35rem 0.6rem; font-size: 0.65rem; font-weight: 600;">
                                                <i class="fas {{ $getCategoryIcon($research->category) }} me-1"></i>
                                                {{ $research->category->name }}
                                            </span>
                                        </div>
                                        @endif
                                        
                                        <div class="position-absolute bottom-0 start-0 w-100 bg-dark bg-opacity-75 p-2">
                                            <div class="d-flex align-items-center">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($research->author) }}&background=random" 
                                                    class="rounded-circle border border-2 border-white me-2" 
                                                    width="28" height="28" 
                                                    alt="{{ $research->author }}">
                                                <span class="fw-bold text-white" style="font-size: 0.75rem; text-shadow: 0 1px 2px rgba(0,0,0,0.8);">
                                                    {{ $research->author }} <span class="ms-1 fw-normal opacity-75">· {{ $research->created_at->locale('es')->diffForHumans() }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body p-3">
                                        <h5 class="card-title mb-2 fw-bold fs-6">
                                            <a href="{{ route('research.show', $research->slug ?? $research->id) }}" class="text-decoration-none text-dark stretched-link">
                                                {{ $research->title }}
                                            </a>
                                        </h5>
                                        <p class="card-text text-secondary mb-0 small">{{ Str::limit($research->excerpt, 100) }}</p>
                                    </div>
                                    
                                    <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center p-3 pt-0">
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="far fa-eye me-1"></i> {{ $research->views ?? 0 }}
                                            <span class="mx-2">•</span>
                                            <i class="far fa-comment me-1"></i> {{ $research->comments_count ?? 0 }}
                                        </div>
                                        <div class="text-end">
                                            <span class="btn btn-sm btn-outline-primary rounded-pill px-2" style="font-size: 0.7rem;">Leer más</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
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
                        <div class="card-header bg-gradient-primary text-white p-2">
                            <h5 class="mb-0 d-flex align-items-center fw-bold fs-6">
                                <i class="fas fa-star me-2"></i> Investigaciones Destacadas
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="featured-research-list">
                                @foreach($featuredResearch as $featured)
                                    @if($featured->status === 'published' || $featured->status === 'active')
                                    <!-- Investigación -->
                                    <div class="p-2 {{ !$loop->last ? 'border-bottom' : '' }} hover-bg-light transition-300">
                                        <div class="row g-0">
                                            <div class="col-auto position-relative me-2">
                                                <div class="position-absolute top-0 start-0 bg-warning text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 20px; height: 20px; font-size: 0.65rem; z-index: 10;">
                                                    <i class="fas fa-star"></i>
                                                </div>
                                                <a href="{{ route('research.show', $featured->slug ?? $featured->id) }}">
                                                    <div class="rounded overflow-hidden" style="width: 60px; height: 60px;">
                                                        <img src="{{ $getImageUrl($featured->image, 'research', 'small') }}" 
                                                            class="w-100 h-100" 
                                                            alt="{{ $featured->title }}" 
                                                            style="object-fit: cover;" 
                                                            onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/research-default-small.jpg') }}';">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <h6 class="mb-1 fw-semibold lh-sm fs-6">
                                                    <a href="{{ route('research.show', $featured->slug ?? $featured->id) }}" class="text-decoration-none stretched-link text-dark">
                                                        {{ Str::limit($featured->title, 55) }}
                                                    </a>
                                                </h6>
                                                
                                                @if(isset($featured->category))
                                                <span class="badge rounded-pill mb-1" style="{{ $getCategoryStyle($featured->category) }} font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                                                    <i class="fas {{ $getCategoryIcon($featured->category) }} me-1"></i>
                                                    {{ $featured->category->name }}
                                                </span>
                                                @endif
                                                
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
                        <div class="card-header bg-gradient-info text-white p-2">
                            <h5 class="mb-0 d-flex align-items-center fw-bold fs-6">
                                <i class="fas fa-comments me-2"></i> Más Comentados
                            </h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach($mostCommented as $commented)
                                @if($commented->status === 'published' || $commented->status === 'active')
                                <a href="{{ route('research.show', $commented->slug ?? $commented->id) }}" 
                                class="list-group-item list-group-item-action px-3 py-2 border-bottom hover-bg-light transition-300">
                                    <div class="d-flex align-items-center mb-1">
                                        @if(isset($commented->category) && $commented->category)
                                        <span class="badge rounded-pill me-2" style="{{ $getCategoryStyle($commented->category) }} font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                                            <i class="fas {{ $getCategoryIcon($commented->category) }} me-1"></i>
                                            {{ $commented->category->name }}
                                        </span>
                                        @endif
                                        <span class="text-info small fw-bold ms-auto" style="font-size: 0.7rem;">
                                            <i class="fas fa-comment-dots me-1"></i> {{ $commented->comments_count }}
                                        </span>
                                    </div>
                                    
                                    <h6 class="mb-1 fw-semibold fs-6">
                                        {{ Str::limit($commented->title, 65) }}
                                    </h6>
                                    
                                    <div class="d-flex align-items-center text-muted" style="font-size: 0.7rem;">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($commented->author) }}&size=18&background=random" 
                                            class="rounded-circle me-1" 
                                            alt="{{ $commented->author }}">
                                        {{ $commented->author }}
                                        <span class="ms-auto">{{ $commented->created_at->locale('es')->diffForHumans() }}</span>
                                    </div>
                                </a>
                                @endif
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
                            <h6 class="fw-bold mb-2 fs-6">Categorías populares</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($availableCategories as $category)
                                <a href="{{ route('research.index', ['category' => $category->id]) }}" class="badge rounded-pill text-decoration-none" style="{{ $getCategoryStyle($category) }} font-size: 0.7rem;">
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



@endsection

@push('styles')
<!-- Estilos para la sección Hero -->
<style>
/* Estilos para la nueva sección hero */
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
        // Función para inicializar el carousel con logging para depuración
        function initializeCarousel() {
            console.log('Inicializando carousel...');
            
            var myCarousel = document.getElementById('heroNewsCarousel');
            if (!myCarousel) {
                console.error('Carousel no encontrado en el DOM');
                return;
            }
            
            // Verificar cuántos slides hay
            var items = myCarousel.querySelectorAll('.carousel-item');
            console.log('El carousel tiene ' + items.length + ' elementos');
            
            if (items.length <= 1) {
                console.warn('El carousel solo tiene ' + items.length + ' elemento(s), no será visible la rotación');
                return;
            }
            
            try {
                // Intentar usar la API de Bootstrap 5
                if (typeof bootstrap !== 'undefined' && typeof bootstrap.Carousel !== 'undefined') {
                    var carousel = new bootstrap.Carousel(myCarousel, {
                        interval: 5000,    // Tiempo entre slides (5 segundos)
                        keyboard: true,    // Permitir control con teclado
                        pause: false,      // No pausar al hover
                        ride: 'carousel',  // Iniciar automáticamente
                        wrap: true         // Volver al principio cuando termina
                    });
                    console.log('Carousel inicializado con Bootstrap 5');
                    
                    // Iniciar manualmente el carousel
                    carousel.cycle();
                } else {
                    // Implementación fallback cuando Bootstrap no está disponible
                    console.warn('Bootstrap no disponible, usando fallback manual');
                    
                    var currentIndex = 0;
                    setInterval(function() {
                        items[currentIndex].classList.remove('active');
                        currentIndex = (currentIndex + 1) % items.length;
                        items[currentIndex].classList.add('active');
                    }, 5000);
                }
            } catch (error) {
                console.error('Error al inicializar el carousel:', error);
            }
        }
        
        // Inicializar después de que el DOM esté completamente cargado
        initializeCarousel();
        
        // Intentar nuevamente después de 1 segundo para mayor seguridad
        setTimeout(initializeCarousel, 1000);
    });
</script>
@endpush