<!-- resources/views/partials/seo-meta.blade.php -->
@php
// Variables por defecto que se pueden sobreescribir en cada vista
$metaTitle = $metaTitle ?? ($title ?? config('app.name', 'ConocIA'));
$metaDescription = $metaDescription ?? ($description ?? 'Noticias de tecnología e inteligencia artificial - ConocIA');
$metaKeywords = $metaKeywords ?? ($keywords ?? 'inteligencia artificial, tecnología, noticias tecnología, IA');
$metaImage = $metaImage ?? ($image ?? asset('storage/images/defaults/social-share.jpg'));
$metaType = $metaType ?? ($type ?? 'website');
$metaUrl = $metaUrl ?? ($url ?? url()->current());
$metaAuthor = $metaAuthor ?? ($author ?? 'ConocIA');
$metaTwitterCard = $metaTwitterCard ?? ($twitterCard ?? 'summary_large_image');
$metaPublished = $metaPublished ?? ($published ?? null);
$metaModified = $metaModified ?? ($modified ?? null);
@endphp

<!-- SEO Meta Tags Básicos -->
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<link rel="canonical" href="{{ $metaUrl }}">

<!-- Meta Tags para Open Graph (Facebook, LinkedIn) -->
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:url" content="{{ $metaUrl }}">
<meta property="og:type" content="{{ $metaType }}">
<meta property="og:site_name" content="{{ config('app.name', 'ConocIA') }}">
@if($metaPublished)
<meta property="article:published_time" content="{{ $metaPublished }}">
@endif
@if($metaModified)
<meta property="article:modified_time" content="{{ $metaModified }}">
@endif

<!-- Meta Tags para Twitter -->
<meta name="twitter:card" content="{{ $metaTwitterCard }}">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">

<!-- Meta Tags para estructuración de datos -->
<meta name="author" content="{{ $metaAuthor }}">
<meta name="robots" content="index, follow">
<meta name="revisit-after" content="7 days">