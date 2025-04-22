<div class="news-videos-component mb-4">
    <h3 class="section-title">Videos relacionados</h3>
    
    <div class="video-recommendations-container" id="video-recommendations-container" data-news-id="{{ $newsId }}">
        <div class="text-center p-3">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-2">Buscando videos relacionados...</p>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener todos los contenedores de recomendaciones
        const containers = document.querySelectorAll('.video-recommendations-container');
        
        containers.forEach(container => {
            const newsId = container.dataset.newsId;
            
            // Obtener contenido de la noticia para extraer palabras clave
            const newsContent = document.querySelector('.news-content') ? 
                document.querySelector('.news-content').innerText : '';
            
            if (newsContent) {
                fetchVideoRecommendations(container, newsId, newsContent);
            }
        });
    });
    
    function fetchVideoRecommendations(container, newsId, content) {
        fetch('{{ route("api.videos.news-recommendations") }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            // Usar URLSearchParams para manejar los parámetros correctamente
            url: '{{ route("api.videos.news-recommendations") }}?' + new URLSearchParams({
                news_id: newsId,
                content: content.substring(0, 1000) // Limitar a 1000 caracteres
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.videos.length > 0) {
                renderVideoRecommendations(container, data.videos);
            } else {
                container.innerHTML = '<p class="text-center text-muted">No se encontraron videos relacionados.</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching video recommendations:', error);
            container.innerHTML = '<p class="text-center text-muted">Error al cargar videos relacionados.</p>';
        });
    }
    
    function renderVideoRecommendations(container, videos) {
        let html = '<div class="video-grid row">';
        
        videos.forEach(video => {
            html += `
                <div class="col-md-4 mb-3">
                    <div class="card video-card h-100">
                        <div class="position-relative">
                            <img src="${video.thumbnail_url}" class="card-img-top" alt="${video.title}">
                            <div class="video-duration">${formatDuration(video.duration_seconds)}</div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="/videos/${video.id}" class="video-title">
                                    ${video.title.length > 60 ? video.title.substring(0, 60) + '...' : video.title}
                                </a>
                            </h5>
                            <p class="card-text small text-muted">
                                <i class="far fa-eye"></i> ${formatNumber(video.view_count)}
                            </p>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }
    
    function formatDuration(seconds) {
        if (!seconds) return '0:00';
        
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${minutes}:${secs.toString().padStart(2, '0')}`;
    }
    
    function formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }
</script>
@endpush

@push('styles')
<style>
    .video-card {
        transition: transform 0.3s ease;
        border-radius: 6px;
        overflow: hidden;
    }
    .video-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .video-title {
        color: #333;
        text-decoration: none;
        font-size: 0.9rem;
        line-height: 1.2;
    }
    .video-title:hover {
        color: #007bff;
    }
    .video-duration {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 12px;
    }
    .section-title {
        border-left: 4px solid #007bff;
        padding-left: 15px;
        font-size: 1.3rem;
        margin-bottom: 20px;
    }
</style>
@endpush
@endonce