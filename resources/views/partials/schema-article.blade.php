{{--
    JSON-LD genérico para tipos Profundiza: TechArticle / Article
    Uso: @include('partials.schema-article', [
        'item'      => $concepto,          ← modelo con title, excerpt/abstract, content, slug, created_at, updated_at
        'routeName' => 'conceptos.show',   ← nombre de ruta para generar URL canónica
        'type'      => 'TechArticle',      ← NewsArticle | TechArticle | ScholarlyArticle | Article
        'section'   => 'Conceptos IA',
    ])
--}}
@php
    $item      = $item ?? null;
    $routeName = $routeName ?? null;
    $schemaType = $type ?? 'Article';
    $section   = $section ?? 'ConocIA';
@endphp

@if($item && $routeName)
@php
    $itemUrl   = route($routeName, $item->slug ?? $item->id);
    $itemImage = !empty($item->image) ? asset($item->image) : asset('images/defaults/social-share.jpg');
    $authorName = is_object($item->author ?? null) ? $item->author->name : ($item->author ?? 'ConocIA');
    $excerpt   = $item->excerpt ?? $item->abstract ?? Str::limit(strip_tags($item->content ?? ''), 160);
    $published = ($item->published_at ?? $item->created_at)?->toIso8601String();
    $modified  = $item->updated_at?->toIso8601String();
@endphp
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "{{ $schemaType }}",
    "headline": "{{ addslashes($item->title) }}",
    "description": "{{ addslashes($excerpt) }}",
    "image": "{{ $itemImage }}",
    "datePublished": "{{ $published }}",
    "dateModified": "{{ $modified }}",
    "author": {
        "@type": "Person",
        "name": "{{ $authorName }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "ConocIA",
        "@id": "{{ url('/') }}/#organization",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('storage/images/logo.png') }}"
        }
    },
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ $itemUrl }}"
    },
    "articleSection": "{{ $section }}",
    "inLanguage": "es-CL",
    "wordCount": {{ str_word_count(strip_tags($item->content ?? '')) }}
}
</script>
@endif
