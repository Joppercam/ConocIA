@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    
    /**
     * Obtiene la URL de la imagen de la noticia o una imagen predeterminada
     * @param string|null $imagePath Ruta de la imagen
     * @param string $size Tamaño deseado: 'small', 'medium', o 'large'
     * @return string URL de la imagen
     */
    function getNewsImage($imagePath, $size = 'medium') {
        // Si es una ruta que comienza con 'storage/'
        if ($imagePath && Str::startsWith($imagePath, 'storage/')) {
            return asset($imagePath); // Agrega la URL base
        }
        
        // Si es una URL completa (comienza con http o https)
        if ($imagePath && (Str::startsWith($imagePath, 'http://') || Str::startsWith($imagePath, 'https://'))) {
            return $imagePath;
        }
        
        // Si es solo un nombre de archivo
        if ($imagePath && Storage::disk('public')->exists('images/news/' . $imagePath)) {
            return Storage::url('images/news/' . $imagePath);
        }
        
        // Imagen predeterminada según el tamaño (usar asset directamente)
        return asset('storage/images/defaults/news-default-' . $size . '.jpg');
    }
@endphp

@section('content')
<div class="container py-4">
    <div class="row mb-3">
        <div class="col-12">
            <h1 class="fs-3 mb-0">Últimas Noticias</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Noticias -->
            @foreach($news as $article)
            <div class="card border-0 shadow-sm mb-3 news-card">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="{{ getNewsImage($article->image, 'medium') }}" 
                             class="img-fluid rounded-start h-100" 
                             style="object-fit: cover;" 
                             alt="{{ $article->title }}"
                             onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/news-default-medium.jpg') }}';">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-primary">{{ $article->category->name ?? 'General' }}</span>
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i>{{ $article->created_at->locale('es')->isoFormat('D MMM, YYYY') }}
                                </small>
                            </div>
                            <h5 class="card-title fs-5 mb-1">
                                <a href="{{ route('news.show', $article->slug) }}" class="text-decoration-none text-dark stretched-link">
                                    {{ $article->title }}
                                </a>
                            </h5>
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <span class="me-3">
                                    <i class="fas fa-user-edit me-1"></i>{{ $article->author ?? 'Staff' }}
                                </span>
                                <span>
                                    <i class="fas fa-eye me-1"></i>{{ number_format($article->views) }} lecturas
                                </span>
                            </div>
                            <p class="card-text small mb-2">{{ Str::limit($article->summary ?? $article->excerpt, 120) }}</p>
                            <a href="{{ route('news.show', $article->slug) }}" class="btn btn-sm btn-outline-primary">
                                Leer más <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
            <!-- Paginación con tamaño reducido -->
            <div class="d-flex justify-content-center mt-4">
                {{ $news->links('vendor.pagination.bootstrap-5-small') }}
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Categorías -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-2">
                    <h5 class="mb-0 fs-6">Categorías</h5>
                </div>
                <div class="card-body py-2">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($categories as $cat)
                            @php
                                // Verificar si esta categoría está activa
                                $isActive = isset($category) && $category->id === $cat->id;
                                // Determinar la clase del badge según si está activo o no
                                $badgeClass = $isActive ? 'bg-dark' : 'bg-primary';
                            @endphp
                            <a href="{{ route('news.category', $cat->slug) }}" 
                            class="badge {{ $badgeClass }} text-white text-decoration-none p-2">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Lo más leído -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-2">
                    <h5 class="mb-0 fs-6 d-flex align-items-center">
                        <i class="fas fa-chart-line text-primary me-2"></i> Lo más leído
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="most-read-list">
                        @foreach($mostReadArticles ?? [] as $index => $mostReadArticle)
                            <a href="{{ route('news.show', $mostReadArticle->slug) }}" class="text-decoration-none">
                                <div class="most-read-item d-flex p-2 {{ !$loop->last ? 'border-bottom' : '' }} hover-bg-light">
                                    <div class="most-read-number me-2 text-primary fw-bold" style="font-size: 1.2rem; min-width: 24px;">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="most-read-content">
                                        <h6 class="mb-1 small">{{ $mostReadArticle->title }}</h6>
                                        <div class="text-muted small">
                                            <i class="fas fa-eye me-1"></i>{{ number_format($mostReadArticle->views) }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Newsletter -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-2 fs-6">Suscríbete al newsletter</h5>
                    <p class="text-muted small mb-3">Recibe las últimas noticias directamente en tu correo.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="Tu correo electrónico" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-paper-plane me-1"></i> Suscribirse
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Estilos para tarjetas de noticias */
.news-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.news-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
}

/* Estilo para los elementos más leídos */
.hover-bg-light:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s;
}

/* Ajuste para las flechas de paginación */
.pagination svg {
    width: 20px;
    height: 20px;
}

/* Ajuste general de tamaños de texto */
.card-title {
    font-size: 0.85rem; /* Reducido aún más */
    line-height: 1.3;
    font-weight: 600; /* Para mantener legibilidad */
}

.card-text {
    font-size: 0.65rem; /* Reducido de 0.85rem */
    line-height: 1.4;
}

.badge {
    font-size: 0.65rem; /* Reducido de 0.7rem */
}

.small, .text-muted.small, .card-body .small {
    font-size: 0.7rem !important;
}

.btn-sm {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
}

.most-read-content h6 {
    font-size: 0.75rem !important; /* Reducido de 0.8rem */
    line-height: 1.3;
}
</style>
@endpush
@endsection