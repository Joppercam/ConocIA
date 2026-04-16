{{--
    JSON-LD BreadcrumbList — incluir en cada show page
    Uso: @include('partials.schema-breadcrumb', ['crumbs' => [
        ['name' => 'Inicio',    'url' => url('/')],
        ['name' => 'Noticias',  'url' => route('news.index')],
        ['name' => $news->title],  ← último sin url
    ]])
--}}
@php $crumbs = $crumbs ?? []; @endphp
@if(count($crumbs) > 0)
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        @foreach($crumbs as $i => $crumb)
        {
            "@type": "ListItem",
            "position": {{ $i + 1 }},
            "name": "{{ $crumb['name'] }}"
            @if(isset($crumb['url'])),"item": "{{ $crumb['url'] }}"@endif
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endif
