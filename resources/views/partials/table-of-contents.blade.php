{{--
  Table of Contents — partial reutilizable
  Uso: @include('partials.table-of-contents', ['contentSelector' => '.news-content'])
  Si no se pasa contentSelector, usa '.article-content'
--}}
@php $selector = $contentSelector ?? '.article-content'; @endphp

<div class="card border-0 shadow-sm mb-4" id="toc-card" style="display:none;">
    <div class="card-header bg-white py-2 border-0">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Contenido</h5>
            </div>
            <button class="btn btn-sm p-0 text-muted" id="toc-toggle" title="Colapsar">
                <i class="fas fa-chevron-up" id="toc-chevron" style="font-size:.75rem;"></i>
            </button>
        </div>
    </div>
    <div class="card-body py-2 px-3" id="toc-body">
        <nav id="toc-nav" aria-label="Tabla de contenidos"></nav>
    </div>
</div>

<style>
#toc-nav a {
    display: block;
    padding: 3px 0;
    font-size: .82rem;
    color: #555;
    text-decoration: none;
    border-left: 2px solid transparent;
    padding-left: 8px;
    transition: all .15s ease;
    line-height: 1.35;
}
#toc-nav a:hover { color: var(--primary-color); border-left-color: var(--primary-color); }
#toc-nav a.toc-active {
    color: var(--primary-color);
    border-left-color: var(--primary-color);
    font-weight: 600;
}
#toc-nav a.toc-h3 { padding-left: 20px; font-size: .78rem; color: #777; }
#toc-nav a.toc-h3:hover, #toc-nav a.toc-h3.toc-active { color: var(--primary-color); }
</style>

<script>
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const content = document.querySelector('{{ $selector }}');
        const tocCard = document.getElementById('toc-card');
        const tocNav  = document.getElementById('toc-nav');
        if (!content || !tocCard || !tocNav) return;

        // Recoger h2 y h3 del contenido
        const headings = content.querySelectorAll('h2, h3');
        if (headings.length < 2) return; // No vale la pena mostrar TOC con 1 heading

        const links = [];

        headings.forEach(function (h, i) {
            // Asignar ID si no tiene
            if (!h.id) h.id = 'toc-heading-' + i;

            const a = document.createElement('a');
            a.href    = '#' + h.id;
            a.textContent = h.textContent.trim();
            a.className   = h.tagName === 'H3' ? 'toc-h3' : '';
            a.addEventListener('click', function (e) {
                e.preventDefault();
                h.scrollIntoView({ behavior: 'smooth', block: 'start' });
                history.replaceState(null, '', '#' + h.id);
            });

            tocNav.appendChild(a);
            links.push({ el: h, link: a });
        });

        tocCard.style.display = '';

        // Highlight de sección activa via IntersectionObserver
        const io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    links.forEach(function (item) {
                        item.link.classList.toggle('toc-active', item.el === entry.target);
                    });
                }
            });
        }, { rootMargin: '-20% 0px -70% 0px' });

        links.forEach(function (item) { io.observe(item.el); });

        // Toggle colapsar
        const tocToggle  = document.getElementById('toc-toggle');
        const tocBody    = document.getElementById('toc-body');
        const tocChevron = document.getElementById('toc-chevron');
        if (tocToggle) {
            tocToggle.addEventListener('click', function () {
                const collapsed = tocBody.style.display === 'none';
                tocBody.style.display = collapsed ? '' : 'none';
                tocChevron.className  = collapsed ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
                tocChevron.style.fontSize = '.75rem';
            });
        }
    });
})();
</script>
