<!-- resources/views/layouts/app.blade.php (versión mejorada) -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="@yield('meta_description', 'ConocIA - Tu portal de noticias sobre tecnología e inteligencia artificial')">
    <meta name="keywords" content="@yield('meta_keywords', 'IA, inteligencia artificial, tecnología, noticias IA, investigación tecnológica')">
    <meta name="google-site-verification" content="K7M1JtXEvnuDOAjHlVGsAczDqZzK8WPv1ze_ILdYmDk" />
    <title>@yield('title', 'ConocIA - Portal de Noticias de Tecnología e IA')</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @hasSection('meta')
        @yield('meta')
    @else
        <title>{{ config('app.name', 'ConocIA') }}</title>
        @include('partials.seo-meta')
    @endif

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}" />
    <link rel="alternate" type="application/rss+xml" title="ConocIA — Noticias de IA" href="{{ url('/feed') }}" />

    <!-- Preconexiones para optimizar carga -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/news-section.css') }}">
    
    <!-- Fuentes optimizadas -->
    <link rel="preload" as="font" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @stack('styles')

    {{-- JSON-LD global: Organization + WebSite + SearchAction --}}
    @include('partials.schema-organization')

    {{-- Barra de progreso de lectura (solo en páginas de artículo) --}}
    @hasSection('reading_progress')
    <style>
        #reading-progress-bar {
            position: fixed; top: 0; left: 0; z-index: 9999;
            height: 3px; width: 0%;
            background: linear-gradient(90deg, #38b6ff, #00e1ff);
            transition: width .1s linear;
            pointer-events: none;
        }
    </style>
    <div id="reading-progress-bar"></div>
    @endif

    <style>
        :root {
            --primary-color: #38b6ff;
            --primary-color-light: #00e1ff;
            --secondary-color: #2a2a72;
            --dark-bg: #121212;
            --dark-surface: #1e1e1e;
            --light-text: #e0e0e0;
            --card-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            --hover-transition: all 0.3s ease;
        }
        
        /* Estilos generales */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
            overflow-x: hidden; /* Prevenir scroll horizontal */
        }
        
        main {
            flex: 1; /* Permitir que el contenido principal crezca */
        }
        
        /* Estilos para modo oscuro */
        body.theme-dark {
            background-color: var(--dark-bg);
            color: var(--light-text);
        }
        
        body.theme-dark .navbar,
        body.theme-dark footer {
            background-color: var(--dark-surface) !important;
        }
        
        body.theme-dark .card {
            background-color: var(--dark-surface);
            color: var(--light-text);
        }
        
        body.theme-dark .bg-light {
            background-color: #1a1a1a !important;
        }
        
        body.theme-dark .text-dark {
            color: var(--light-text) !important;
        }
        
        body.theme-dark .text-muted {
            color: #adb5bd !important;
        }
        
        body.theme-dark input,
        body.theme-dark select,
        body.theme-dark textarea {
            background-color: #2a2a2a;
            border-color: #444;
            color: var(--light-text);
        }
        
        /* Barra de progreso de lectura */
        .reading-progress-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: transparent;
            z-index: 1090;
        }
        
        .reading-progress-bar {
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--primary-color-light));
            width: 0%;
            transition: width 0.12s ease-out;
            border-radius: 0 2px 2px 0;
        }
        
        /* Estilos del navbar */
        .navbar {
            transition: var(--hover-transition);
            padding: 0.8rem 0;
        }
        
        .navbar-brand {
            transition: var(--hover-transition);
        }
        
        .navbar-dark {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
        }
        
        .nav-item {
            position: relative;
            margin: 0 0.2rem;
        }
        
        .nav-link {
            position: relative;
            font-weight: 500;
            transition: var(--hover-transition);
            padding: 0.5rem 0.8rem !important;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: #fff;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateX(-50%);
        }
        
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 70%;
            opacity: 1;
        }
        
        /* Estilo del logo */
        .logo-text {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: -0.5px;
            color: #fff;
        }
        
        .footer-logo {
            font-size: 2.2rem;
            display: block;
            margin-bottom: 10px;
        }
        
        .text-highlight {
            position: relative;
            font-weight: 900;
            background: linear-gradient(to right, var(--primary-color), var(--primary-color-light));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* ── Dark mode pill toggle ── */
        .theme-toggle-pill {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .theme-toggle-track {
            display: flex;
            align-items: center;
            background: rgba(255,255,255,.18);
            border-radius: 20px;
            padding: 3px;
            width: 52px;
            height: 26px;
            transition: background .3s ease;
            border: 1px solid rgba(255,255,255,.25);
        }
        .theme-toggle-thumb {
            background: #fff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: var(--secondary-color);
            transition: transform .3s ease;
            flex-shrink: 0;
        }
        body.theme-dark .theme-toggle-track { background: rgba(56,182,255,.3); }
        body.theme-dark .theme-toggle-thumb { transform: translateX(26px); }

        /* ── Navbar search ── */
        #nav-search-input::placeholder { color: rgba(255,255,255,.55); }
        #nav-search-input:focus { background:rgba(255,255,255,.25) !important; box-shadow:none; color:#fff; }
        .search-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            z-index: 1100;
            max-height: 420px;
            overflow-y: auto;
        }
        .search-result-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 14px;
            text-decoration: none;
            color: #222;
            border-bottom: 1px solid #f0f0f0;
            transition: background .15s;
        }
        .search-result-item:last-child { border-bottom: none; }
        .search-result-item:hover { background: #f7f9ff; }
        .search-result-title { font-size:.83rem; font-weight:600; line-height:1.3; }
        .search-result-meta  { font-size:.72rem; color:#888; margin-top:2px; }
        .search-result-section {
            padding: 5px 14px 3px;
            font-size:.68rem;
            font-weight:700;
            letter-spacing:.05em;
            text-transform:uppercase;
            color:#aaa;
            background:#fafafa;
        }
        .search-more-link {
            display:block;
            padding: 9px 14px;
            text-align:center;
            font-size:.78rem;
            color: var(--primary-color);
            font-weight:600;
            background:#f7f9ff;
            text-decoration:none;
        }
        .search-more-link:hover { background:#eef5ff; }

        /* ── Saved count badge ── */
        .saved-count-badge {
            position: absolute;
            top: 4px;
            right: -2px;
            background: var(--primary-color);
            color: #fff;
            font-size: .6rem;
            font-weight: 700;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        /* ── Trending badge ── */
        .badge-trending {
            position: absolute;
            top: 8px;
            left: 8px;
            background: linear-gradient(135deg, #ff4757, #ff6b81);
            color: #fff;
            font-size: .68rem;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 10px;
            letter-spacing: .03em;
            z-index: 2;
            box-shadow: 0 2px 6px rgba(255,71,87,.4);
        }
        .badge-trending i { font-size: .65rem; }

        .text-highlight::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, rgba(56,182,255,0) 0%, rgba(56,182,255,1) 50%, rgba(56,182,255,0) 100%);
            border-radius: 2px;
        }
        
        /* Estilos para barra de búsqueda */
        .search-form {
            position: relative;
        }
        
        .search-form .form-control {
            border-radius: 20px;
            padding-left: 1rem;
            border: none;
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            transition: var(--hover-transition);
        }
        
        .search-form .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .search-form .form-control:focus {
            background-color: rgba(255, 255, 255, 0.25);
            box-shadow: none;
        }
        
        .search-form .btn {
            border-radius: 20px;
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
        }
        
        /* Estilos para las tarjetas */
        .card {
            transition: var(--hover-transition);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow);
        }
        
        .card-img-top {
            transition: transform 0.5s ease;
            object-fit: cover;
        }
        
        .card:hover .card-img-top {
            transform: scale(1.05);
        }

        /* Placeholder para imágenes faltantes */
        .img-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a1020 0%, #16213e 60%, #1a1a2e 100%);
            color: rgba(255,255,255,.25);
            font-size: 2rem;
        }
        .img-placeholder::after {
            content: '\f03e';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }
        img[src=""], img:not([src]) {
            visibility: hidden;
        }

        /* Estilos para botones */
        .btn {
            border-radius: 5px;
            font-weight: 500;
            transition: var(--hover-transition);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover, 
        .btn-primary:focus {
            background: var(--primary-color-light);
            border-color: var(--primary-color-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(56, 182, 255, 0.3);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Estilos para sección trending */
        .trending-carousel .card {
            transition: var(--hover-transition);
        }
        
        .trending-carousel .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow);
        }
        
        /* Estilos para barra de búsqueda en móvil */
        #mobile-search {
            transition: all 0.3s ease;
            overflow: hidden;
            max-height: 0;
        }
        
        #mobile-search.show {
            max-height: 60px;
            padding: 0.5rem 0;
        }
        
        /* Estilos para footer */
        footer {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: white;
            padding: 3rem 0 1.5rem;
        }
        
        footer a {
            transition: var(--hover-transition);
            text-decoration: none;
        }
        
        footer a:hover {
            color: var(--primary-color-light) !important;
            transform: translateX(3px);
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            transition: var(--hover-transition);
        }
        
        .social-links a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }

        /* ── Sección Profundiza ──────────────────────────────────────── */
        /* Hero: franja oscura solo al top */
        .profundiza-hero {
            background: linear-gradient(135deg, #0a1020 0%, #16213e 100%);
            border-bottom: 3px solid rgba(56,182,255,.25);
            padding: 3.5rem 0 3rem;
        }
        /* Tarjeta estándar: blanca con borde azul sutil */
        .profundiza-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: .75rem;
            transition: border-color .22s, transform .22s, box-shadow .22s;
        }
        .profundiza-card:hover {
            border-color: rgba(56,182,255,.5);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(56,182,255,.1);
        }
        /* Tarjeta destacada: fondo azul muy tenue */
        .profundiza-card-featured {
            background: #f0f8ff;
            border: 1.5px solid rgba(56,182,255,.3);
            border-radius: .75rem;
            transition: border-color .22s, transform .22s, box-shadow .22s;
        }
        .profundiza-card-featured:hover {
            border-color: rgba(56,182,255,.6);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(56,182,255,.12);
        }
        /* Badges de dificultad */
        .difficulty-badge-basico    { background: #dcfce7; color: #166534; }
        .difficulty-badge-intermedio{ background: #fef3c7; color: #92400e; }
        .difficulty-badge-avanzado  { background: #fee2e2; color: #991b1b; }
        /* Label de sección */
        .profundiza-section-label {
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 1rem;
        }

        /* ── Contenido editorial: texto oscuro sobre fondo blanco ────── */
        .article-content {
            font-size: 1.07rem;
            line-height: 1.85;
            color: #1e293b;
        }
        .article-content p {
            margin-bottom: 1.5rem;
            color: #334155;
        }
        .article-content h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            padding-bottom: .5rem;
            border-bottom: 2px solid rgba(56,182,255,.3);
        }
        .article-content h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-top: 2rem;
            margin-bottom: .75rem;
        }
        .article-content strong { color: #0f172a; font-weight: 600; }
        .article-content em { color: #475569; font-style: italic; }
        .article-content blockquote {
            border-left: 4px solid var(--primary-color);
            background: #f0f9ff;
            border-radius: 0 .6rem .6rem 0;
            padding: 1.1rem 1.5rem;
            margin: 2rem 0;
            color: #1e40af;
            font-style: italic;
            font-size: 1.05rem;
            line-height: 1.8;
        }
        .article-content ul, .article-content ol {
            margin-bottom: 1.5rem;
            padding-left: 1.6rem;
        }
        .article-content ul li, .article-content ol li {
            margin-bottom: .7rem;
            line-height: 1.7;
            color: #334155;
        }
        .article-content ul li strong,
        .article-content ol li strong { color: #0369a1; }
        .article-content a {
            color: var(--primary-color);
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        .article-content code {
            background: #f1f5f9;
            color: #0369a1;
            border-radius: .3rem;
            padding: .15rem .45rem;
            font-size: .87em;
            border: 1px solid #e2e8f0;
        }
        .article-content pre {
            background: #1e293b;
            border-radius: .6rem;
            padding: 1.2rem 1.4rem;
            overflow-x: auto;
            margin-bottom: 1.5rem;
        }
        .article-content pre code {
            background: none;
            color: #e2e8f0;
            border: none;
            padding: 0;
            font-size: .9rem;
        }
        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: .5rem;
            margin: 1.5rem 0;
        }

        /* Animaciones */
        .animate-fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }
        
        .animate-slide-up {
            animation: slideUp 0.8s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="theme-light" data-bs-theme="light">
    <!-- Barra de progreso de lectura -->
    <div class="reading-progress-container">
        <div class="reading-progress-bar" id="readingProgressBar"></div>
    </div>
    
    <!-- Header -->
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                    <span class="logo-text">Conoc<span class="text-highlight">IA</span></span>
                </a>
                <div class="d-flex align-items-center">
                    {{-- Dark mode toggle --}}
                    <button class="theme-toggle-pill me-2" id="theme-toggle" aria-label="Cambiar tema" title="Cambiar tema">
                        <span class="theme-toggle-track">
                            <span class="theme-toggle-thumb">
                                <i class="fas fa-moon icon-moon"></i>
                                <i class="fas fa-sun icon-sun" style="display:none;"></i>
                            </span>
                        </span>
                    </button>
                    <!-- Botón de búsqueda móvil -->
                    <button id="search-toggle-mobile" class="btn btn-link text-white d-lg-none" aria-label="Buscar" title="Buscar">
                        <i class="fas fa-search"></i>
                    </button>
                    <!-- Botón hamburguesa -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}" 
                               href="{{ route('news.index') }}"
                               aria-current="{{ request()->routeIs('news.*') ? 'page' : 'false' }}">
                               <i class="fas fa-newspaper me-1 d-lg-none"></i>Noticias
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('columns.*') ? 'active' : '' }}"
                            href="{{ route('columns.index') }}"
                            aria-current="{{ request()->routeIs('columns.*') ? 'page' : 'false' }}">
                            <i class="fas fa-pen-fancy me-1 d-lg-none"></i>Columnas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('videos.*') ? 'active' : '' }}"
                               href="{{ route('videos.index') }}"
                               aria-current="{{ request()->routeIs('videos.*') ? 'page' : 'false' }}">
                                <i class="fas fa-tv me-1 d-lg-none"></i>
                                <span class="d-lg-none">ConocIA TV</span>
                                <span class="d-none d-lg-inline">ConocIA <span style="color:var(--primary-color);">TV</span></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('radio.*') ? 'active' : '' }}"
                               href="{{ route('radio.index') }}"
                               aria-current="{{ request()->routeIs('radio.*') ? 'page' : 'false' }}">
                                <i class="fas fa-microphone me-1 d-lg-none"></i>
                                <span class="d-lg-none">ConocIA Radio</span>
                                <span class="d-none d-lg-inline">ConocIA <span style="color:var(--primary-color);">Radio</span></span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('agents.*','modelos.*','agenda.*') ? 'active' : '' }}"
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-layer-group me-1 d-lg-none"></i>Ecosistema
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark" style="min-width:240px;">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('agents.index') }}">
                                        <i class="fas fa-robot me-2" style="color:var(--primary-color);"></i>
                                        <strong>Agentes IA</strong>
                                        <div class="text-muted" style="font-size:.72rem;padding-left:1.4rem;">Frameworks y herramientas de agentes autónomos</div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('modelos.index') }}">
                                        <i class="fas fa-microchip me-2" style="color:var(--primary-color);"></i>
                                        <strong>Modelos IA</strong>
                                        <div class="text-muted" style="font-size:.72rem;padding-left:1.4rem;">Comparador de modelos de lenguaje</div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('agenda.index') }}">
                                        <i class="fas fa-calendar-alt me-2" style="color:var(--primary-color);"></i>
                                        <strong>Agenda IA</strong>
                                        <div class="text-muted" style="font-size:.72rem;padding-left:1.4rem;">Conferencias, webinars y eventos del sector</div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('conceptos.*','analisis.*','papers.*','estado-arte.*') ? 'active' : '' }}"
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-brain me-1 d-lg-none"></i>Profundiza
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark" style="min-width:240px;">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('conceptos.index') }}">
                                        <i class="fas fa-book-open me-2" style="color:var(--primary-color);"></i>
                                        <strong>Conceptos IA</strong>
                                        <div class="text-muted" style="font-size:.72rem;padding-left:1.4rem;">Enciclopedia de inteligencia artificial</div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('analisis.index') }}">
                                        <i class="fas fa-microscope me-2" style="color:var(--primary-color);"></i>
                                        <strong>Análisis de Fondo</strong>
                                        <div class="text-muted" style="font-size:.72rem;padding-left:1.4rem;">Editorial largo sobre un tema IA</div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('papers.index') }}">
                                        <i class="fas fa-file-alt me-2" style="color:var(--primary-color);"></i>
                                        <strong>ConocIA Papers</strong>
                                        <div class="text-muted" style="font-size:.72rem;padding-left:1.4rem;">Papers de arXiv explicados en español</div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('estado-arte.index') }}">
                                        <i class="fas fa-chart-line me-2" style="color:var(--primary-color);"></i>
                                        <strong>Estado del Arte</strong>
                                        <div class="text-muted" style="font-size:.72rem;padding-left:1.4rem;">Digest semanal por campo de IA</div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative {{ request()->routeIs('saved') ? 'active' : '' }}"
                               href="{{ route('saved') }}" title="Artículos guardados">
                                <i class="fas fa-bookmark me-1 d-lg-none"></i>
                                <span class="d-lg-none">Guardados</span>
                                <i class="fas fa-bookmark d-none d-lg-inline"></i>
                                <span class="saved-count-badge d-none" id="nav-saved-count">0</span>
                            </a>
                        </li>
                    </ul>

                    {{-- Search bar desktop --}}
                    <div class="position-relative d-none d-lg-block ms-2" style="width:220px;">
                        <form action="{{ route('search') }}" method="GET" autocomplete="off">
                            <input type="text"
                                   name="query"
                                   id="nav-search-input"
                                   class="form-control form-control-sm rounded-pill ps-3 pe-5"
                                   placeholder="Buscar..."
                                   style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:#fff;font-size:.82rem;"
                                   aria-label="Buscar en ConocIA">
                            <button class="btn btn-link text-white position-absolute end-0 top-50 translate-middle-y pe-2" type="submit">
                                <i class="fas fa-search" style="font-size:.78rem;"></i>
                            </button>
                        </form>
                        <div id="search-dropdown" class="search-dropdown shadow-lg" style="display:none;"></div>
                    </div>

                    {{-- Auth --}}
                    <div class="d-flex align-items-center ms-2 gap-2">
                        @auth
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-light rounded-pill dropdown-toggle d-flex align-items-center gap-1 px-3"
                                    style="font-size:.78rem;"
                                    data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i>
                                <span class="d-none d-lg-inline">{{ Str::limit(auth()->user()->name, 14) }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2"></i>Mi perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light rounded-pill px-3" style="font-size:.78rem;">
                            <i class="fas fa-sign-in-alt me-1"></i>Entrar
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-primary rounded-pill px-3" style="font-size:.78rem;">
                            <i class="fas fa-user-plus me-1"></i>Registrarse
                        </a>
                        @endauth
                    </div>

                </div>
            </div>
        </nav>
        
    </header>

    <!-- Notificaciones -->
    @if(session('success') || session('error'))
    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show animate-fade-in" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show animate-fade-in" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
    @endif

    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Lo más leído - Sección opcional -->
    @hasSection('exclude_trending')
    @else
    
    @endif

   
    






    <!-- Footer -->
    <footer class="pt-5 pb-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <div class="mb-4">
                        <span class="logo-text footer-logo">Conoc<span class="text-highlight">IA</span></span>
                        <p class="text-light mt-2">"El futuro del conocimiento es artificialmente inteligente"</p>
                    </div>
                    <p>Tu portal de noticias sobre tecnología e inteligencia artificial. Mantente al día con las últimas innovaciones, investigaciones y tendencias en el mundo de la IA.</p>
                    <div class="social-links mt-4">
                        <a href="#" class="text-white me-2" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-white me-2" aria-label="GitHub"><i class="fab fa-github"></i></a>
                        <a href="#" class="text-white" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h5>Explora</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Inicio</a></li>
                        <li class="mb-2"><a href="{{ route('news.index') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Noticias</a></li>
                        <li class="mb-2"><a href="{{ route('columns.index') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Columnas</a></li>
                        <li class="mb-2"><a href="{{ route('news.archive', date('Y')) }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Archivo</a></li>
                        <li class="mb-2"><a href="{{ route('submit-research') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Enviar Investigación</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h5>Legal</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('pages.privacy') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Privacidad</a></li>
                        <li class="mb-2"><a href="{{ route('pages.terms') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Términos</a></li>
                        <li class="mb-2"><a href="{{ route('pages.cookies') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Cookies</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <h5>Suscríbete</h5>
                    <p class="mb-4">Recibe las últimas noticias y actualizaciones directamente en tu bandeja de entrada.</p>
                    
                    <ul class="list-unstyled mt-4">
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@conocia.cl</li>
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> Santiago, Chile</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4" style="background-color: rgba(255,255,255,0.1);">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; {{ date('Y') }} ConocIA. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="{{ route('pages.privacy') }}" class="text-white">Política de privacidad</a></li>
                        <li class="list-inline-item">•</li>
                        <li class="list-inline-item"><a href="{{ route('pages.terms') }}" class="text-white">Términos de uso</a></li>
                        <li class="list-inline-item">•</li>
                        <li class="list-inline-item"><a href="{{ route('pages.cookies') }}" class="text-white">Cookies</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script>
        // Script para inicializar componentes cuando el DOM esté cargado
        document.addEventListener('DOMContentLoaded', function() {
            // Animaciones al hacer scroll
            const animateElements = document.querySelectorAll('.no-animation');
            
            if (animateElements.length > 0) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate-fade-in');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });
                
                animateElements.forEach(element => {
                    observer.observe(element);
                });
            }
            
            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            if (tooltipTriggerList.length > 0) {
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
            }
            
            // Barra de progreso de lectura
            function updateReadingProgress() {
                const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                const scrolled = (winScroll / height) * 100;
                const progressBar = document.getElementById("readingProgressBar");
                if (progressBar) {
                    progressBar.style.width = scrolled + "%";
                }
            }
            
            window.addEventListener('scroll', updateReadingProgress);
            
            // Dark mode pill toggle
            const themeToggle = document.getElementById('theme-toggle');
            function applyTheme(dark) {
                document.body.classList.toggle('theme-dark', dark);
                document.body.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
                const moon = themeToggle.querySelector('.icon-moon');
                const sun  = themeToggle.querySelector('.icon-sun');
                if (moon) moon.style.display = dark ? 'none'  : '';
                if (sun)  sun.style.display  = dark ? '' : 'none';
            }
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    const isDark = !document.body.classList.contains('theme-dark');
                    applyTheme(isDark);
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                });
                // Aplicar tema guardado o preferencia del sistema
                const savedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                applyTheme(savedTheme === 'dark' || (!savedTheme && prefersDark));
            }
            
            // Botón de búsqueda en móvil
            const searchToggle = document.getElementById('search-toggle-mobile');
            const mobileSearch = document.getElementById('mobile-search');
            if (searchToggle && mobileSearch) {
                searchToggle.addEventListener('click', function() {
                    mobileSearch.classList.toggle('show');
                    if (mobileSearch.classList.contains('show')) {
                        setTimeout(() => {
                            mobileSearch.querySelector('input').focus();
                        }, 300);
                    }
                });
            }
            
            // Inicializar carrusel con comportamiento touch mejorado en móvil
            var carouselList = document.querySelectorAll('.carousel');
            if (carouselList.length > 0) {
                carouselList.forEach(carousel => {
                    new bootstrap.Carousel(carousel, {
                        interval: 5000,
                        touch: true,
                        ride: true
                    });
                });
            }
            
            // Lazy loading de imágenes (polyfill para navegadores antiguos)
            if ('loading' in HTMLImageElement.prototype) {
                // El navegador soporta lazy-loading
                console.log('Native lazy loading supported');
            } else {
                // Navegador no soporta lazy-loading, cargamos un polyfill
                console.log('Native lazy loading not supported, loading polyfill');
                const lazyImages = document.querySelectorAll('img[loading="lazy"]');
                
                const lazyImageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const lazyImage = entry.target;
                            lazyImage.src = lazyImage.dataset.src || lazyImage.src;
                            if (lazyImage.dataset.srcset) {
                                lazyImage.srcset = lazyImage.dataset.srcset;
                            }
                            lazyImageObserver.unobserve(lazyImage);
                        }
                    });
                });
                
                lazyImages.forEach(image => {
                    // Guardar src original en dataset si aún no está ahí
                    if (!image.dataset.src) {
                        image.dataset.src = image.src;
                    }
                    // Solo aplicar el observador para imágenes que no estén ya en viewport
                    if (!isInViewport(image)) {
                        lazyImageObserver.observe(image);
                    }
                });
            }
            
            // Función para verificar si un elemento está en el viewport
            function isInViewport(element) {
                const rect = element.getBoundingClientRect();
                return (
                    rect.top >= 0 &&
                    rect.left >= 0 &&
                    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
                );
            }
        });
    </script>

    @stack('scripts')

    <!-- PWA — Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .catch(err => console.warn('SW no disponible:', err));
            });
        }
    </script>

    <!-- ── Live Search ── -->
    <script>
    (function () {
        const input    = document.getElementById('nav-search-input');
        const dropdown = document.getElementById('search-dropdown');
        if (!input || !dropdown) return;

        let timer;
        const LIVE_URL = '{{ route("search.live") }}';
        const CSRF     = document.querySelector('meta[name="csrf-token"]')?.content || '';

        input.addEventListener('input', function () {
            clearTimeout(timer);
            const q = this.value.trim();
            if (q.length < 2) { dropdown.style.display = 'none'; return; }
            timer = setTimeout(() => fetchResults(q), 280);
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { dropdown.style.display = 'none'; this.blur(); }
        });

        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        function fetchResults(q) {
            fetch(LIVE_URL + '?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => renderDropdown(data))
            .catch(() => {});
        }

        function renderDropdown(data) {
            let html = '';
            if (data.news && data.news.length) {
                html += '<div class="search-result-section">Noticias</div>';
                data.news.forEach(item => {
                    html += `<a class="search-result-item" href="${item.url}">
                        <div>
                            ${item.category ? `<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${item.color};margin-right:5px;flex-shrink:0;vertical-align:middle;"></span>` : ''}
                            <span class="search-result-title">${escHtml(item.title)}</span>
                            <div class="search-result-meta">${item.category ? escHtml(item.category) + ' · ' : ''}${item.date || ''}</div>
                        </div>
                    </a>`;
                });
            }
            if (data.research && data.research.length) {
                html += '<div class="search-result-section">Investigación</div>';
                data.research.forEach(item => {
                    html += `<a class="search-result-item" href="${item.url}">
                        <div><span class="search-result-title"><i class="fas fa-flask me-1" style="color:#38b6ff;font-size:.7rem;"></i>${escHtml(item.title)}</span></div>
                    </a>`;
                });
            }
            if (!data.news?.length && !data.research?.length) {
                html = '<div class="p-3 text-muted text-center" style="font-size:.82rem;">Sin resultados para <strong>' + escHtml(data.query) + '</strong></div>';
            } else {
                html += `<a class="search-more-link" href="${data.more_url}"><i class="fas fa-search me-1"></i>Ver todos los resultados</a>`;
            }
            dropdown.innerHTML = html;
            dropdown.style.display = '';
        }

        function escHtml(str) {
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
    })();
    </script>

    <!-- ── Bookmarks ── -->
    <script>
    const BOOKMARKS_KEY = 'conocia_bookmarks';

    function getBookmarks() {
        try { return JSON.parse(localStorage.getItem(BOOKMARKS_KEY) || '[]'); } catch { return []; }
    }
    function saveBookmarks(items) {
        localStorage.setItem(BOOKMARKS_KEY, JSON.stringify(items));
    }
    function toggleBookmark(id, title, url, category, image) {
        let items = getBookmarks();
        const idx = items.findIndex(b => b.id === id);
        if (idx >= 0) {
            items.splice(idx, 1);
        } else {
            items.unshift({ id, title, url, category: category || '', image: image || '', saved_at: Date.now() });
        }
        saveBookmarks(items);
        updateBookmarkUI(id, idx < 0);
        updateNavBadge();
    }
    function isBookmarked(id) {
        return getBookmarks().some(b => b.id === id);
    }
    function updateBookmarkUI(id, saved) {
        document.querySelectorAll('[data-bookmark-id="' + id + '"]').forEach(btn => {
            btn.classList.toggle('bookmarked', saved);
            btn.title = saved ? 'Quitar de guardados' : 'Guardar artículo';
            const icon = btn.querySelector('i');
            if (icon) icon.className = saved ? 'fas fa-bookmark' : 'far fa-bookmark';
        });
    }
    function updateNavBadge() {
        const count = getBookmarks().length;
        const badge = document.getElementById('nav-saved-count');
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    }

    // Inicializar badges al cargar
    document.addEventListener('DOMContentLoaded', function () {
        updateNavBadge();
        // Marcar botones de la página actual
        document.querySelectorAll('[data-bookmark-id]').forEach(btn => {
            const id = parseInt(btn.dataset.bookmarkId);
            if (isBookmarked(id)) updateBookmarkUI(id, true);
        });
        // Event delegation para clicks en botones bookmark
        document.body.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-bookmark-id]');
            if (!btn) return;
            e.preventDefault(); e.stopPropagation();
            const id       = parseInt(btn.dataset.bookmarkId);
            const title    = btn.dataset.bookmarkTitle    || '';
            const url      = btn.dataset.bookmarkUrl      || '';
            const category = btn.dataset.bookmarkCategory || '';
            const image    = btn.dataset.bookmarkImage    || '';
            toggleBookmark(id, title, url, category, image);
        });
    });
    </script>

    {{-- JS barra de progreso --}}
    @hasSection('reading_progress')
    <script>
    (function(){
        var bar = document.getElementById('reading-progress-bar');
        if (!bar) return;
        function update(){
            var s = document.documentElement;
            var pct = (s.scrollTop / (s.scrollHeight - s.clientHeight)) * 100;
            bar.style.width = Math.min(pct, 100) + '%';
        }
        window.addEventListener('scroll', update, { passive: true });
        update();
    })();
    </script>
    @endif
    {{-- Broken-image fallback: replaces missing images with placeholder div --}}
    <script>
    document.querySelectorAll('img[data-placeholder]').forEach(function(img){
        img.addEventListener('error', function(){
            var ph = document.createElement('div');
            ph.className = 'img-placeholder w-100 h-100';
            ph.style.minHeight = img.offsetHeight ? img.offsetHeight + 'px' : '200px';
            img.parentNode.replaceChild(ph, img);
        });
    });
    </script>
</body>
</html>