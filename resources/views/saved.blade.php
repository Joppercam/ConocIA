@extends('layouts.app')

@section('content')

{{-- Page header --}}
<div style="background:var(--dark-bg); border-bottom:1px solid #2a2a2a;" class="py-4 mb-4">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active text-light">Artículos guardados</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div style="width:4px;height:32px;background:var(--primary-color);border-radius:2px;flex-shrink:0;"></div>
                <div>
                    <h1 class="mb-0 text-white fw-bold" style="font-size:1.6rem;">
                        <i class="fas fa-bookmark me-2" style="color:var(--primary-color);"></i>Artículos guardados
                    </h1>
                    <p class="mb-0 mt-1" style="color:#aaa;font-size:.85rem;">Tus artículos guardados localmente en este dispositivo</p>
                </div>
            </div>
            <button id="clear-all-btn" class="btn btn-sm btn-outline-danger rounded-pill px-3" style="font-size:.8rem;display:none;">
                <i class="fas fa-trash-alt me-1"></i>Limpiar todo
            </button>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">

            {{-- Empty state --}}
            <div id="empty-state" class="text-center py-5" style="display:none!important;">
                <div style="width:80px;height:80px;background:#f8f9fa;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                    <i class="far fa-bookmark" style="font-size:2rem;color:#ccc;"></i>
                </div>
                <h4 class="text-muted fw-semibold mb-2">Sin artículos guardados</h4>
                <p class="text-muted mb-4" style="font-size:.9rem;">Guarda artículos mientras navegas haciendo clic en el ícono <i class="far fa-bookmark"></i> para leerlos más tarde.</p>
                <a href="{{ route('news.index') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-newspaper me-2"></i>Ver últimas noticias
                </a>
            </div>

            {{-- Saved count --}}
            <div id="saved-count-bar" class="d-flex justify-content-between align-items-center mb-3" style="display:none!important;">
                <span class="text-muted" style="font-size:.85rem;">
                    <span id="saved-count-text"></span>
                </span>
                <span class="text-muted" style="font-size:.78rem;">
                    <i class="fas fa-info-circle me-1"></i>Guardados solo en este dispositivo
                </span>
            </div>

            {{-- Articles list --}}
            <div id="saved-list"></div>

        </div>
    </div>
</div>

@push('styles')
<style>
.saved-article-card {
    transition: transform .2s ease, box-shadow .2s ease;
    border-radius: .5rem;
    overflow: hidden;
}
.saved-article-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.1) !important;
}
.saved-article-card .remove-btn {
    opacity: 0;
    transition: opacity .18s ease;
}
.saved-article-card:hover .remove-btn {
    opacity: 1;
}
@media (max-width:575px) {
    .saved-article-card .remove-btn { opacity: 1; }
}
.saved-thumb {
    width: 100px;
    min-width: 100px;
    height: 70px;
    object-fit: cover;
    border-radius: .35rem;
}
.saved-thumb-placeholder {
    width: 100px;
    min-width: 100px;
    height: 70px;
    border-radius: .35rem;
    background: #f0f4f8;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ccc;
    font-size: 1.4rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const KEY = 'conocia_bookmarks';
    const listEl = document.getElementById('saved-list');
    const emptyEl = document.getElementById('empty-state');
    const countBar = document.getElementById('saved-count-bar');
    const countText = document.getElementById('saved-count-text');
    const clearBtn = document.getElementById('clear-all-btn');

    function getBookmarks() {
        try { return JSON.parse(localStorage.getItem(KEY) || '[]'); } catch { return []; }
    }
    function saveBookmarks(arr) {
        localStorage.setItem(KEY, JSON.stringify(arr));
    }
    function removeBookmark(id) {
        let bm = getBookmarks().filter(b => String(b.id) !== String(id));
        saveBookmarks(bm);
        render();
        // update nav badge
        if (window.updateNavBadge) window.updateNavBadge();
    }
    function clearAll() {
        if (!confirm('¿Eliminar todos los artículos guardados?')) return;
        saveBookmarks([]);
        render();
        if (window.updateNavBadge) window.updateNavBadge();
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function render() {
        const bookmarks = getBookmarks();
        listEl.innerHTML = '';

        if (bookmarks.length === 0) {
            emptyEl.style.cssText = 'display:block!important;';
            countBar.style.cssText = 'display:none!important;';
            clearBtn.style.display = 'none';
            return;
        }

        emptyEl.style.cssText = 'display:none!important;';
        countBar.style.cssText = 'display:flex!important;';
        clearBtn.style.display = '';
        countText.textContent = bookmarks.length === 1
            ? '1 artículo guardado'
            : `${bookmarks.length} artículos guardados`;

        bookmarks.slice().reverse().forEach(function (bm) {
            const thumbHtml = (bm.image && bm.image !== '' && !bm.image.includes('default') && !bm.image.includes('placeholder'))
                ? `<img src="${escHtml(bm.image.startsWith('storage/') ? '/' + bm.image : '/storage/news/' + bm.image)}"
                        class="saved-thumb me-3"
                        alt="${escHtml(bm.title)}"
                        onerror="this.outerHTML='<div class=\'saved-thumb-placeholder me-3\'><i class=\'far fa-newspaper\'></i></div>'">`
                : `<div class="saved-thumb-placeholder me-3"><i class="far fa-newspaper"></i></div>`;

            const catHtml = bm.category
                ? `<span class="badge me-2" style="background:var(--primary-color);font-size:.68rem;">${escHtml(bm.category)}</span>`
                : '';

            const savedAt = bm.savedAt
                ? new Date(bm.savedAt).toLocaleDateString('es-AR', {day:'numeric',month:'short',year:'numeric'})
                : '';

            const card = document.createElement('div');
            card.className = 'saved-article-card card border-0 shadow-sm mb-3';
            card.innerHTML = `
                <div class="card-body py-3 px-3">
                    <div class="d-flex align-items-center">
                        <a href="${escHtml(bm.url)}" class="d-block flex-shrink-0 text-decoration-none">
                            ${thumbHtml}
                        </a>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center mb-1">
                                ${catHtml}
                                ${savedAt ? `<span class="text-muted" style="font-size:.72rem;"><i class="far fa-clock me-1"></i>Guardado el ${savedAt}</span>` : ''}
                            </div>
                            <a href="${escHtml(bm.url)}" class="text-decoration-none text-dark">
                                <h6 class="mb-0 fw-semibold" style="font-size:.9rem;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                    ${escHtml(bm.title)}
                                </h6>
                            </a>
                        </div>
                        <button class="btn btn-sm btn-outline-danger remove-btn ms-3 flex-shrink-0 rounded-circle"
                                data-id="${escHtml(String(bm.id))}"
                                title="Quitar de guardados"
                                style="width:32px;height:32px;padding:0;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-times" style="font-size:.7rem;"></i>
                        </button>
                    </div>
                </div>`;

            listEl.appendChild(card);
        });

        // Remove buttons
        listEl.querySelectorAll('.remove-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                removeBookmark(this.dataset.id);
            });
        });
    }

    clearBtn.addEventListener('click', clearAll);

    render();

    // Re-render if bookmarks change in another tab
    window.addEventListener('storage', function (e) {
        if (e.key === KEY) render();
    });
});
</script>
@endpush

@endsection
