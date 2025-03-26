<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Panel de Administración - ConocIA')</title>
    
    <!-- Favicon -->
      <link rel="icon" type="image/svg+xml" href="{{ asset('favicon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}" />
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @stack('styles')
    <style>
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
            background: linear-gradient(to right, #38b6ff, #00e1ff); /* Gradiente de celeste a celeste brillante */
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
    </style>

    
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark">
            <div class="sidebar-header">
                <div class="d-flex justify-content-center py-4">
                    <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                        <span class="logo-text">Conoc<span class="text-highlight">IA</span></span>
                    </a>
                </div>
            </div>

            <ul class="list-unstyled components">
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                    <a href="#noticiasSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.noticias.*') ? 'true' : 'false' }}" class="dropdown-toggle">
                        <i class="fas fa-newspaper me-2"></i> Noticias
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.noticias.*') ? 'show' : '' }}" id="noticiasSubmenu">
                        <li>
                            <a href="{{ route('admin.news.index') }}">Ver todas</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.news.create') }}">Crear nueva</a>
                        </li>
                    </ul>
                </li>
                <li class="{{ request()->routeIs('admin.research.*') ? 'active' : '' }}">
                    <a href="#investigacionesSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.investigaciones.*') ? 'true' : 'false' }}" class="dropdown-toggle">
                        <i class="fas fa-flask me-2"></i> Investigaciones
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.research.*') ? 'show' : '' }}" id="investigacionesSubmenu">
                        <li>
                            <a href="{{ route('admin.research.index') }}">Ver todas</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.research.create') }}">Crear nueva</a>
                        </li>
                    </ul>
                </li>
                <li class="{{ request()->routeIs('admin.invitados.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.invitados.pending') }}">
                        <i class="fas fa-user-edit me-2"></i> Colaboraciones 
                        @if(isset($pendingGuestPostCount) && $pendingGuestPostCount > 0)
                            <span class="badge bg-danger float-end">{{ $pendingGuestPostCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-tag me-2"></i> Categorías
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.comments.pending') }}">
                        <i class="fas fa-comments me-2"></i> Comentarios 
                        @if(isset($pendingCommentsCount) && $pendingCommentsCount > 0)
                            <span class="badge bg-danger float-end">{{ $pendingCommentsCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="#usuariosSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.usuarios.*') ? 'true' : 'false' }}" class="dropdown-toggle">
                        <i class="fas fa-users me-2"></i> Usuarios
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="usuariosSubmenu">
                        <li>
                            <a href="{{ route('admin.users.index') }}">Ver todos</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.create') }}">Crear nuevo</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#settingsSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-cog me-2"></i> Configuración
                    </a>
                    <ul class="collapse list-unstyled" id="settingsSubmenu">
                        <li>
                            <a href="#">General</a>
                        </li>
                        <li>
                            <a href="#">SEO</a>
                        </li>
                        <li>
                            <a href="#">Email</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-sm btn-outline-dark">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto d-flex">
                        <div class="dropdown me-3">
                            <button class="btn btn-sm btn-outline-dark dropdown-toggle" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                @if(isset($notificationsCount) && $notificationsCount > 0)
                                    <span class="badge bg-danger ms-1">{{ $notificationsCount }}</span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                                <li><h6 class="dropdown-header">Notificaciones</h6></li>
                                @if(isset($notifications) && $notifications->count() > 0)
                                    @foreach($notifications as $notification)
                                        <li><a class="dropdown-item" href="#">{{ $notification->message }}</a></li>
                                    @endforeach
                                @else
                                    <li><span class="dropdown-item text-muted">No hay notificaciones</span></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                            </ul>
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : asset('images/avatar-placeholder.png') }}" alt="{{ Auth::user()->name }}" class="rounded-circle me-2" width="32" height="32">
                                <span>{{ Auth::user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-circle me-2"></i>Mi perfil</a></li>
                                <li><a class="dropdown-item" href="{{ route('home') }}"><i class="fas fa-external-link-alt me-2"></i>Ver sitio</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main>
                @if(session('success'))
                    <div class="container-fluid mt-3">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="container-fluid mt-3">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
                
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="footer py-3 bg-light mt-auto border-top">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-start">
                            <p class="mb-0">&copy; {{ date('Y') }} ConocIA. Todos los derechos reservados.</p>
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <p class="mb-0">Panel de administración v1.0</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle sidebar
            document.getElementById('sidebarCollapse').addEventListener('click', function () {
                document.getElementById('sidebar').classList.toggle('active');
                document.getElementById('content').classList.toggle('active');
            });
            
            // Initialize Summernote WYSIWYG editor
            if (document.querySelector('.summernote')) {
                $('.summernote').summernote({
                    height: 300,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
