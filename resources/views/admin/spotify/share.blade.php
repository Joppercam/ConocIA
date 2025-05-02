@extends('admin.layouts.app')

@section('title', 'Compartir Podcast en Spotify')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Compartir Podcast en Spotify</h1>
        <a href="{{ route('admin.spotify.dashboard') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $podcast->title }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Información del Podcast</h5>
                        <p><strong>Fecha:</strong> {{ $podcast->published_at->format('d/m/Y') }}</p>
                        <p><strong>Duración:</strong> {{ $podcast->formatted_duration }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Reproductor de Audio</h5>
                        <audio controls class="w-100">
                            <source src="{{ asset('storage/' . $podcast->audio_path) }}" type="audio/mpeg">
                            Su navegador no soporta la reproducción de audio.
                        </audio>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Compartir en Spotify y Redes Sociales</h6>
                </div>
                <div class="card-body">
                    <!-- Enlace de Spotify -->
                    <h5>Enlace de Spotify</h5>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="spotifyUrl" value="{{ $spotifyShareUrl }}" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('spotifyUrl')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Botones de compartir en redes sociales -->
                    <h5>Compartir en Redes Sociales</h5>
                    <div class="share-buttons">
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode('Escucha nuestro podcast: ' . $podcast->title) }}&url={{ urlencode($spotifyShareUrl) }}"
                           target="_blank" class="btn btn-twitter mb-2">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($spotifyShareUrl) }}"
                           target="_blank" class="btn btn-facebook mb-2">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        
                        <a href="https://wa.me/?text={{ urlencode('Escucha nuestro podcast: ' . $podcast->title . ' ' . $spotifyShareUrl) }}"
                           target="_blank" class="btn btn-whatsapp mb-2">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode($spotifyShareUrl) }}&title={{ urlencode($podcast->title) }}"
                           target="_blank" class="btn btn-linkedin mb-2">
                            <i class="fab fa-linkedin-in"></i> LinkedIn
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-twitter {
    background-color: #1DA1F2;
    color: white;
    width: 100%;
}
.btn-facebook {
    background-color: #4267B2;
    color: white;
    width: 100%;
}
.btn-whatsapp {
    background-color: #25D366;
    color: white;
    width: 100%;
}
.btn-linkedin {
    background-color: #0e76a8;
    color: white;
    width: 100%;
}
</style>

<script>
function copyToClipboard(elementId) {
    var copyText = document.getElementById(elementId);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    
    // Feedback visual
    var button = copyText.nextElementSibling.querySelector('button');
    var originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i>';
    setTimeout(function() {
        button.innerHTML = originalHtml;
    }, 2000);
}
</script>
@endsection