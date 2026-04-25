@php
    use App\Models\News;
    use Illuminate\Support\Str;

    $appUrl = rtrim(config('app.url'), '/');
    $currentTitle = old('title', $news->title ?? '');
    $currentSlug = old('slug', $news->slug ?? '');
    $currentSummary = old('summary', $news->summary ?? '');
    $currentContent = old('content', $news->content ?? '');

    $initialSeoTitle = News::seoTitleSuggestion($currentTitle);
    $initialSeoDescription = News::seoDescriptionSuggestion($currentSummary, null, $currentContent);
    $initialSeoSlug = $currentSlug !== '' ? $currentSlug : News::seoSlugSuggestion($currentTitle);
    $initialPreviewUrl = $appUrl . '/news/' . ltrim($initialSeoSlug, '/');
@endphp

<div class="card mb-3">
    <div class="card-header">Salud SEO</div>
    <div class="card-body">
        <div class="small text-muted mb-3">Guía rápida para mejorar CTR en Google desde esta noticia.</div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span>Título SEO</span>
            <span class="badge bg-secondary" id="seo-title-status">0</span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span>Slug</span>
            <span class="badge bg-secondary" id="seo-slug-status">0</span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <span>Resumen / description</span>
            <span class="badge bg-secondary" id="seo-summary-status">0</span>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Vista previa en Google</div>
    <div class="card-body">
        <div class="seo-google-preview">
            <div class="seo-google-url" id="seo-google-url">{{ $initialPreviewUrl }}</div>
            <div class="seo-google-title" id="seo-google-title">{{ $initialSeoTitle }}</div>
            <div class="seo-google-description" id="seo-google-description">{{ $initialSeoDescription }}</div>
        </div>
        <div class="small text-muted mt-3">Previsualización aproximada del resultado en búsqueda para esta URL.</div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Sugerencias rápidas</div>
    <div class="card-body">
        <div class="seo-suggestion-item mb-3">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <div class="fw-semibold">Título sugerido</div>
                    <div class="small text-muted" id="seo-suggested-title">{{ $initialSeoTitle }}</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="apply-seo-title">Aplicar</button>
            </div>
        </div>
        <div class="seo-suggestion-item mb-3">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <div class="fw-semibold">Slug sugerido</div>
                    <div class="small text-muted text-break" id="seo-suggested-slug">{{ $initialSeoSlug }}</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="apply-seo-slug">Aplicar</button>
            </div>
        </div>
        <div class="seo-suggestion-item">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <div class="fw-semibold">Snippet sugerido</div>
                    <div class="small text-muted" id="seo-suggested-summary">{{ $initialSeoDescription }}</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="apply-seo-summary">Aplicar</button>
            </div>
        </div>

        <hr>

        <ul class="small text-muted ps-3 mb-0" id="seo-guidance-list">
            <li>Intenta que el título quede entre 30 y 60 caracteres.</li>
            <li>Usa un slug claro y corto, sin palabras vacías innecesarias.</li>
            <li>El resumen debería explicar el valor de la nota en 110 a 155 caracteres.</li>
        </ul>
    </div>
</div>

@push('styles')
<style>
    .seo-google-preview {
        border: 1px solid #dfe1e5;
        border-radius: 12px;
        padding: 14px 16px;
        background: #fff;
    }

    .seo-google-url {
        color: #1f7a3d;
        font-size: 0.85rem;
        line-height: 1.4;
        word-break: break-all;
    }

    .seo-google-title {
        color: #1a0dab;
        font-size: 1.15rem;
        line-height: 1.35;
        margin-top: 6px;
        margin-bottom: 4px;
        font-weight: 500;
    }

    .seo-google-description {
        color: #4d5156;
        font-size: 0.92rem;
        line-height: 1.5;
    }

    .seo-suggestion-item:last-child {
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    (function() {
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');
        const summaryInput = document.getElementById('summary');
        const contentInput = document.getElementById('content');
        const summaryCount = document.getElementById('summary-count');
        const appUrl = @json($appUrl);
        let slugEdited = slugInput.value.trim() !== '';

        function normalizeWhitespace(value) {
            return (value || '').replace(/\s+/g, ' ').trim();
        }

        function stripHtml(value) {
            const container = document.createElement('div');
            container.innerHTML = value || '';
            return normalizeWhitespace(container.textContent || container.innerText || '');
        }

        function limitText(value, limit) {
            const clean = normalizeWhitespace(value);
            if (clean.length <= limit) {
                return clean;
            }

            return clean.slice(0, Math.max(limit - 3, 0)).trimEnd() + '...';
        }

        function slugify(value) {
            return (value || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9\\s-]/g, ' ')
                .trim()
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .slice(0, 80);
        }

        function suggestedSeoTitle() {
            const title = normalizeWhitespace(titleInput.value);

            if (!title) {
                return 'ConocIA';
            }

            const lower = title.toLowerCase();
            if (lower.includes('conocia')) {
                return title;
            }

            if (title.length <= 52) {
                return title + ' | ConocIA';
            }

            return limitText(title, 60);
        }

        function suggestedSlug() {
            const manual = normalizeWhitespace(slugInput.value);
            if (manual) {
                return slugify(manual);
            }

            return slugify(titleInput.value);
        }

        function suggestedDescription() {
            const summary = normalizeWhitespace(summaryInput.value);
            const content = stripHtml(contentInput.value);

            if (summary.length >= 80) {
                return limitText(summary, 155);
            }

            if (summary) {
                return limitText(summary, 155);
            }

            if (content) {
                return limitText(content, 155);
            }

            return 'Noticias y análisis sobre inteligencia artificial y tecnología en ConocIA.';
        }

        function previewUrl() {
            const slug = suggestedSlug();
            return slug ? `${appUrl}/news/${slug}` : `${appUrl}/news`;
        }

        function setIndicator(id, value, isGood) {
            const el = document.getElementById(id);
            el.textContent = value;
            el.className = 'badge ' + (isGood ? 'bg-success' : 'bg-warning text-dark');
        }

        function updateGuidance(titleLength, slugLength, summaryLength) {
            const list = document.getElementById('seo-guidance-list');
            const items = [];

            if (titleLength < 30) {
                items.push('El título está corto: conviene sumar contexto o intención de búsqueda.');
            } else if (titleLength > 60) {
                items.push('El título está largo: intenta llevarlo a 60 caracteres o menos.');
            } else {
                items.push('El título está en un rango saludable para Google.');
            }

            if (slugLength < 12) {
                items.push('El slug puede quedar más descriptivo para captar mejor la búsqueda.');
            } else if (slugLength > 80) {
                items.push('El slug está muy largo: simplifícalo y deja solo la idea principal.');
            } else {
                items.push('El slug está claro y con un largo razonable.');
            }

            if (summaryLength < 110) {
                items.push('El resumen está corto: aprovecha el snippet para explicar mejor el valor de la nota.');
            } else if (summaryLength > 155) {
                items.push('El resumen está largo: Google probablemente lo cortará.');
            } else {
                items.push('El resumen está listo para funcionar como description.');
            }

            list.innerHTML = items.map(item => `<li>${item}</li>`).join('');
        }

        function updateSeoAssistant() {
            const titleLength = normalizeWhitespace(titleInput.value).length;
            const slugLength = normalizeWhitespace(slugInput.value).length;
            const summaryLength = normalizeWhitespace(summaryInput.value).length;

            if (summaryCount) {
                summaryCount.textContent = summaryLength;
            }

            setIndicator('seo-title-status', titleLength, titleLength >= 30 && titleLength <= 60);
            setIndicator('seo-slug-status', slugLength, slugLength >= 12 && slugLength <= 80);
            setIndicator('seo-summary-status', summaryLength, summaryLength >= 110 && summaryLength <= 155);

            const seoTitle = suggestedSeoTitle();
            const seoSlug = suggestedSlug();
            const seoDescription = suggestedDescription();

            document.getElementById('seo-google-title').textContent = seoTitle;
            document.getElementById('seo-google-url').textContent = previewUrl();
            document.getElementById('seo-google-description').textContent = seoDescription;

            document.getElementById('seo-suggested-title').textContent = seoTitle;
            document.getElementById('seo-suggested-slug').textContent = seoSlug;
            document.getElementById('seo-suggested-summary').textContent = seoDescription;

            updateGuidance(titleLength, seoSlug.length, summaryLength);
        }

        slugInput.addEventListener('input', function() {
            slugEdited = normalizeWhitespace(slugInput.value) !== '';
            updateSeoAssistant();
        });

        titleInput.addEventListener('input', function() {
            if (!slugEdited) {
                slugInput.value = slugify(titleInput.value);
            }

            updateSeoAssistant();
        });

        summaryInput.addEventListener('input', updateSeoAssistant);
        contentInput.addEventListener('input', updateSeoAssistant);

        document.getElementById('apply-seo-title').addEventListener('click', function() {
            titleInput.value = suggestedSeoTitle().replace(/\s+\|\s+ConocIA$/, '');
            updateSeoAssistant();
        });

        document.getElementById('apply-seo-slug').addEventListener('click', function() {
            slugInput.value = slugify(titleInput.value);
            slugEdited = true;
            updateSeoAssistant();
        });

        document.getElementById('apply-seo-summary').addEventListener('click', function() {
            summaryInput.value = suggestedDescription().replace(/\.\.\.$/, '');
            updateSeoAssistant();
        });

        updateSeoAssistant();
        window.updateNewsSeoAssistant = updateSeoAssistant;
    })();
</script>
@endpush
