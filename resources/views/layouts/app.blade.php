<!-- resources/views/layouts/app.blade.php (versión mejorada) -->
<!DOCTYPE html>
<html lang="es" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="@yield('meta_description', 'ConocIA - Tu portal de noticias sobre tecnología e inteligencia artificial')">
    <meta name="keywords" content="@yield('meta_keywords', 'IA, inteligencia artificial, tecnología, noticias IA, investigación tecnológica')">
    <title>@yield('title', 'ConocIA - Portal de Noticias de Tecnología e IA')</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    
    <!-- Preconexiones para optimizar carga -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- CSS con preload -->
    <link rel="preload" as="style" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/news-section.css') }}">
    
    <!-- Fuentes optimizadas -->
    <link rel="preload" as="font" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @stack('styles')
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
                    <!-- Botón de cambio de tema -->
                    <button class="btn btn-sm text-white me-2" id="theme-toggle" aria-label="Cambiar tema" title="Cambiar tema">
                        <i class="fas fa-moon"></i>
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
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" 
                               href="{{ route('home') }}" 
                               aria-current="{{ request()->routeIs('home') ? 'page' : 'false' }}">
                               <i class="fas fa-home me-1 d-lg-none"></i>Inicio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}" 
                               href="{{ route('news.index') }}"
                               aria-current="{{ request()->routeIs('news.*') ? 'page' : 'false' }}">
                               <i class="fas fa-newspaper me-1 d-lg-none"></i>Noticias
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('research.*') ? 'active' : '' }}" 
                               href="{{ route('research.index') }}"
                               aria-current="{{ request()->routeIs('research.*') ? 'page' : 'false' }}">
                               <i class="fas fa-flask me-1 d-lg-none"></i>Investigación
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
                            <a class="nav-link {{ request()->routeIs('submit-research') ? 'active' : '' }}" 
                               href="{{ route('submit-research') }}"
                               aria-current="{{ request()->routeIs('submit-research') ? 'page' : 'false' }}">
                               <i class="fas fa-upload me-1 d-lg-none"></i>Enviar Investigación
                            </a>
                        </li>
                    </ul>
                    <div class="d-none d-lg-flex">
                        <form action="{{ route('search') }}" method="GET" class="d-flex search-form">
                            <div class="input-group">
                                <input class="form-control" type="search" name="query" placeholder="Buscar..." aria-label="Search" required>
                                <button class="btn btn-outline-light" type="submit" aria-label="Buscar"><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Barra de búsqueda móvil expandible -->
        <div id="mobile-search" class="container">
            <form action="{{ route('search') }}" method="GET" class="d-flex w-100">
                <div class="input-group">
                    <input class="form-control" type="search" name="query" placeholder="Buscar..." aria-label="Search" required>
                    <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
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

   
    
    <!-- Sección para enviar investigaciones -->
    @hasSection('exclude_submit_research')
    @else
    <section class="py-5 bg-primary text-white submit-research-section no-animation">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <h2 class="mb-3">¿Tienes una investigación sobre IA o tecnología?</h2>
                    <p class="lead mb-4">Comparte tus conocimientos y descubrimientos con nuestra comunidad. Buscamos colaboradores que aporten contenido de valor sobre inteligencia artificial, tecnología emergente, y sus aplicaciones en el mundo real.</p>
                    <ul class="feature-list mb-0">
                        <li><i class="fas fa-check-circle me-2"></i> Difunde tu investigación a una audiencia especializada</li>
                        <li><i class="fas fa-check-circle me-2"></i> Conviértete en colaborador reconocido en nuestro portal</li>
                        <li><i class="fas fa-check-circle me-2"></i> Conecta con otros investigadores y profesionales del sector</li>
                    </ul>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <h5 class="card-title text-dark mb-4">Proceso de envío</h5>
                            <div class="d-flex mb-3">
                                <div class="process-icon me-3">
                                    <span class="bg-primary text-white">1</span>
                                </div>
                                <div>
                                    <h6 class="text-dark">Prepara tu contenido</h6>
                                    <p class="text-muted small mb-0">Artículos, papers, análisis o estudios de caso sobre IA y tecnología.</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="process-icon me-3">
                                    <span class="bg-primary text-white">2</span>
                                </div>
                                <div>
                                    <h6 class="text-dark">Envía tu propuesta</h6>
                                    <p class="text-muted small mb-0">Utiliza nuestro formulario para enviar un resumen o propuesta inicial.</p>
                                </div>
                            </div>
                            <div class="d-flex mb-4">
                                <div class="process-icon me-3">
                                    <span class="bg-primary text-white">3</span>
                                </div>
                                <div>
                                    <h6 class="text-dark">Revisión y publicación</h6>
                                    <p class="text-muted small mb-0">Nuestro equipo editorial revisará tu contenido y te guiará en el proceso.</p>
                                </div>
                            </div>
                            <a href="{{ route('submit-research') }}" class="btn btn-primary d-block">Enviar mi investigación</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
                        <li class="mb-2"><a href="{{ route('research.index') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Investigación</a></li>
                        <li class="mb-2"><a href="{{ route('columns.index') }}" class="text-white"><i class="fas fa-angle-right me-2"></i>Columnas</a></li>
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
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="footer-subscribe-form">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Tu correo electrónico" name="email" aria-label="Email" required>
                            <button class="btn btn-primary" type="submit">Suscribirse</button>
                        </div>
                    </form>
                    <ul class="list-unstyled mt-4">
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@conocia.com</li>
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> Madrid, España</li>
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
            
            // Botón para cambiar tema
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    document.body.classList.toggle('theme-dark');
                    const isDark = document.body.classList.contains('theme-dark');
                    document.body.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
                    this.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                });
                
                // Verificar preferencia guardada de tema
                if (localStorage.getItem('theme') === 'dark' || 
                    (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.body.classList.add('theme-dark');
                    document.body.setAttribute('data-bs-theme', 'dark');
                    themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
                }
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
</body>
</html>