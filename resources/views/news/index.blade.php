@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
    
    /**
     * Obtiene la URL de la imagen de la noticia o una imagen predeterminada
     * @param string|null $imageName Nombre de la imagen
     * @param string $size Tamaño deseado: 'small', 'medium', o 'large'
     * @return string URL de la imagen
     */
  
    /**
     * Obtiene la URL de la imagen de la noticia o una imagen predeterminada
     * @param string|null $imageName Nombre de la imagen
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
        
        // Si no, devuelve la imagen predeterminada según el tamaño
        return asset('storage/images/defaults/news-default-' . $size . '.jpg');
    }
@endphp

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fs-2 mb-4">Últimas Noticias</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Noticias -->
            @foreach($news as $article)
            <div class="card border-0 shadow-sm mb-4">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="{{ getNewsImage($article->image, 'medium') }}" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="{{ $article->title }}">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <span class="badge bg-primary mb-2">{{ $article->category->name }}</span>
                            <h5 class="card-title mb-2"><a href="{{ route('news.show', $article->id) }}" class="text-decoration-none text-dark">{{ $article->title }}</a></h5>
                            <p class="card-text text-muted small mb-2">{{ $article->created_at->format('d M, Y') }} • {{ $article->views }} lecturas</p>
                            <p class="card-text">{{ Str::limit($article->excerpt, 150) }}</p>
                            <a href="{{ route('news.show', $article->slug) }}" class="btn btn-sm btn-outline-primary">Leer más <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
            <!-- Paginación con tamaño reducido -->
            <div class="d-flex justify-content-center mt-4">
                <div class="pagination pagination-sm">
                    {{ $news->links() }}
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Categorías -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fs-6">Categorías</h5>
                </div>
                <div class="card-body">
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
            
            <!-- Newsletter -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3 fs-6">Suscríbete al newsletter</h5>
                    <p class="text-muted small">Recibe las últimas noticias directamente en tu correo.</p>
                    <form>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Tu correo electrónico">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Suscribirse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection