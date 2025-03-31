@extends('layouts.app')

@section('title', 'Investigación y Análisis - ConocIA')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fs-4 mb-3">Investigación y Análisis</h1>
            <p class="text-muted fs-6 small">Descubre los últimos avances, estudios y análisis en el campo de la inteligencia artificial y tecnología.</p>
        </div>
    </div>
    
    <div class="row">
        <!-- Contenido principal -->
        <div class="col-lg-8">
            <div class="row g-4">
                @if($researches->count() > 0)
                    @foreach($researches as $research)
                    @if($research->status === 'published' || $research->status === 'active')
                    @php
                        // Método directo: Nunca usar getImageUrl, construir manualmente la URL solo si hay imagen
                        $imageSrc = null;
                        $hasImage = false;
                        
                        if (!empty($research->image) && 
                            $research->image != 'default.jpg' && 
                            !str_contains($research->image, 'default') && 
                            !str_contains($research->image, 'placeholder')) {
                            
                            // Construir la URL directamente sin pasar por getImageUrl
                            if (Str::startsWith($research->image, 'storage/')) {
                                $imageSrc = asset($research->image);
                            } else {
                                $imageSrc = asset('storage/research/' . $research->image);
                            }
                            
                            $hasImage = true;
                        }
                    @endphp
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="position-relative">
                                <a href="{{ route('research.show', $research->slug ?? $research->id) }}">
                                    @if($hasImage)
                                    <img src="{{ $imageSrc }}" 
                                         class="card-img-top" 
                                         alt="{{ $research->title }}" 
                                         style="height: 180px; object-fit: cover;"
                                         onError="this.style.display='none';">
                                    @else
                                    <div style="height: 120px; background-color: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-microscope text-muted" style="font-size: 2rem;"></i>
                                    </div>
                                    @endif
                                </a>
                                @if(isset($research->category))
                                <div class="position-absolute bottom-0 end-0 m-2">
                                    <span class="badge" style="{{ $getCategoryStyle($research->category) }} font-size: 0.7rem;">
                                        <i class="fas {{ $getCategoryIcon($research->category) }} me-1"></i>
                                        {{ $research->category->name }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fs-6">
                                    <a href="{{ route('research.show', $research->slug ?? $research->id) }}" class="text-decoration-none text-dark">
                                        {{ $research->title }}
                                    </a>
                                </h5>
                                <p class="card-text text-muted small mb-2 fs-7">{{ $research->created_at->format('d M, Y') }} • {{ $research->views }} lecturas</p>
                                <p class="card-text small">{{ Str::limit($research->excerpt, 120) }}</p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($research->author) }}&background=random" class="rounded-circle me-2" width="30" height="30" alt="{{ $research->author }}">
                                        <span class="small text-muted">{{ $research->author }}</span>
                                    </div>
                                    <a href="{{ route('research.show', $research->slug ?? $research->id) }}" class="btn btn-sm btn-outline-primary" style="font-size: 0.75rem;">Leer más</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info">
                            <p class="mb-0 fs-6">No se encontraron artículos de investigación.</p>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $researches->links() }}
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Filtro de Categorías -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fs-6 small">Categorías</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($categories as $category)
                            <a href="{{ route('research.category', $category->slug) }}" class="badge text-white text-decoration-none p-1 mb-2" style="{{ $getCategoryStyle($category) }} font-size: 0.7rem;">
                                <i class="fas {{ $getCategoryIcon($category) }} me-1"></i>
                                {{ $category->name }}
                                @if(isset($category->research_count))
                                <span class="badge bg-light text-dark ms-1" style="font-size: 0.65rem;">{{ $category->research_count }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Investigaciones Destacadas -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fs-6 small">Investigaciones Destacadas</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($featuredResearch as $featured)
                        @if($featured->status === 'published' || $featured->status === 'active')
                        @php
                            // Método directo para las imágenes de investigaciones destacadas
                            $featuredImageSrc = null;
                            $featuredHasImage = false;
                            
                            if (!empty($featured->image) && 
                                $featured->image != 'default.jpg' && 
                                !str_contains($featured->image, 'default') && 
                                !str_contains($featured->image, 'placeholder')) {
                                
                                // Construir la URL directamente
                                if (Str::startsWith($featured->image, 'storage/')) {
                                    $featuredImageSrc = asset($featured->image);
                                } else {
                                    $featuredImageSrc = asset('storage/research/' . $featured->image);
                                }
                                
                                $featuredHasImage = true;
                            }
                        @endphp
                        <li class="list-group-item px-2 py-1 border-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    @if($featuredHasImage)
                                    <img src="{{ $featuredImageSrc }}" 
                                         class="rounded" 
                                         width="50" 
                                         height="50" 
                                         alt="{{ $featured->title }}" 
                                         style="object-fit: cover;"
                                         onError="this.style.display='none'; this.parentElement.innerHTML='<div class=\'rounded bg-light d-flex align-items-center justify-content-center\' style=\'width:50px;height:50px;\'><i class=\'fas fa-microscope text-muted\'></i></div>';">
                                    @else
                                    <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                                        <i class="fas fa-microscope text-muted"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" style="font-size: 0.7rem; line-height: 1.1;">
                                        <a href="{{ route('research.show', $featured->slug ?? $featured->id) }}" class="text-decoration-none text-dark">{{ Str::limit($featured->title, 55) }}</a>
                                    </h6>
                                    <div class="d-flex align-items-center mt-1">
                                        @if(isset($featured->category))
                                        <span class="badge me-1" style="{{ $getCategoryStyle($featured->category) }} font-size: 0.65rem; padding: 0.15rem 0.35rem;">
                                            {{ $featured->category->name }}
                                        </span>
                                        @endif
                                        <small class="text-muted" style="font-size: 0.65rem;">{{ $featured->created_at->locale('es')->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <!-- Newsletter -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3 small">Suscríbete al newsletter</h5>
                    <p class="text-muted" style="font-size: 0.7rem;">Recibe las últimas investigaciones y análisis directamente en tu correo.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="research">
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="Tu correo electrónico" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Suscribirse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection