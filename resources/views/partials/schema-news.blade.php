<!-- resources/views/partials/schema-news.blade.php -->
@php
$article = $article ?? null;
if ($article) {
    $authorName = is_object($article->author) ? $article->author->name : ($article->author ?? 'ConocIA');
    $imageUrl = !empty($article->image) && !str_contains($article->image, 'default') 
    ? $getImageUrl($article->image, 'news', 'large') 
    : asset('storage/images/defaults/social-share.jpg');
    $categoryName = is_object($article->category) ? $article->category->name : ($article->category ?? 'Tecnología');
    
    $articleUrl = route('news.show', $article->slug ?? $article->id);
    $publishedTime = $article->published_at ? $article->published_at->toIso8601String() : $article->created_at->toIso8601String();
    $modifiedTime = $article->updated_at ? $article->updated_at->toIso8601String() : $article->created_at->toIso8601String();
}
@endphp

@if($article)
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "headline": "{{ $article->title }}",
    "description": "{{ $article->excerpt }}",
    "image": "{{ $imageUrl }}",
    "datePublished": "{{ $publishedTime }}",
    "dateModified": "{{ $modifiedTime }}",
    "author": {
        "@type": "Person",
        "name": "{{ $authorName }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "{{ config('app.name', 'ConocIA') }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('storage/images/logo.png') }}"
        }
    },
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ $articleUrl }}"
    },
    "articleSection": "{{ $categoryName }}",
    "wordCount": "{{ str_word_count(strip_tags($article->content)) }}"
}
</script>
@endif