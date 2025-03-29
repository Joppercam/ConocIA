<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración') - {{ config('app.name') }}</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/smaller-font.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .admin-container {
            display: flex;
            flex: 1;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            transition: all 0.3s;
            min-height: 100vh;
        }
        
        .sidebar.minimized {
            width: 80px;
        }
        
        .sidebar .sidebar-header {
            padding: 20px;
            background-color: #212529;
        }
        
        .sidebar ul.components {
            padding: 20px 0;
        }
        
        .sidebar ul li {
            border-bottom: 1px solid #4b545c;
        }
        
        .sidebar ul li a {
            padding: 10px 20px;
            display: block;
            color: #ced4da;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar ul li a:hover,
        .sidebar ul li.active > a {
            color: #fff;
            background-color: #212529;
        }
        
        .sidebar ul li ul.collapse {
            background-color: #2c3136;
        }
        
        .sidebar ul li ul li {
            border-bottom: none;
        }
        
        .sidebar ul li ul li a {
            padding-left: 40px;
        }
        
        .sidebar-footer {
            padding: 20px;
            text-align: center;
        }
        
        /* Content */
        .content {
            flex: 1;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }
        
        .content.expanded {
            margin-left: -170px;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .footer {
            margin-top: auto;
        }
        
        /* Mobile */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
                position: fixed;
                z-index: 1000;
                height: 100%;
            }
            
            .sidebar.active {
                margin-left: 0;
            }
            
            .content {
                width: 100%;
            }
            
            .content.expanded {
                margin-left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
                <button type="button" id="sidebarCollapse" class="btn btn-sm d-md-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <ul class="list-unstyled components">
                <li class="{{ request()->is('admin') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="{{ request()->is('admin/news*') ? 'active' : '' }}">
                    <a href="#newsSubmenu" data-bs-toggle="collapse" 
                       aria-expanded="{{ request()->is('admin/news*') ? 'true' : 'false' }}" 
                       class="dropdown-toggle">
                        <i class="fas fa-newspaper"></i> Noticias
                    </a>
                    <ul class="collapse list-unstyled {{ request()->is('admin/news*') ? 'show' : '' }}" id="newsSubmenu">
                        <li><a href="{{ route('admin.news.index') }}">Todas las noticias</a></li>
                        <li><a href="{{ route('admin.news.create') }}">Añadir nueva</a></li>
                        <li><a href="{{ route('admin.api.index') }}"><i class="fas fa-sync-alt"></i> Ejecutar API</a></li>
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
                <li class="{{ request()->is('admin/columns*') ? 'active' : '' }}">
                    <a href="{{ route('admin.columns.index') }}">
                        <i class="fas fa-pen-fancy"></i> Columnas
                    </a>
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
                <li class="{{ request()->is('admin/tags*') ? 'active' : '' }}">
                    <a href="{{ route('admin.news.index') }}">
                        <i class="fas fa-tags"></i> Etiquetas
                    </a>
                </li>

                <li class="{{ request()->is('admin/newsletter*') ? 'active' : '' }}">
                    <a href="#newsletterSubmenu" data-bs-toggle="collapse" 
                    aria-expanded="{{ request()->is('admin/newsletter*') ? 'true' : 'false' }}" 
                    class="dropdown-toggle">
                        <i class="fas fa-envelope"></i> Newsletter
                    </a>
                    <ul class="collapse list-unstyled {{ request()->is('admin/newsletter*') ? 'show' : '' }}" id="newsletterSubmenu">
                        <li><a href="{{ route('admin.newsletter.index') }}">Suscriptores</a></li>
                        <li><a href="{{ route('admin.newsletter.send') }}">Enviar Newsletter</a></li>
                    </ul>
                </li>
                <li class="{{ request()->routeIs('admin.social-media.*') ? 'active' : '' }}">
                    <a href="#socialMediaSubmenu" data-bs-toggle="collapse" 
                    aria-expanded="{{ request()->routeIs('admin.social-media.*') ? 'true' : 'false' }}" 
                    class="dropdown-toggle">
                        <i class="fas fa-share-alt"></i> Redes Sociales 
                        @if(isset($pendingSocialCount) && $pendingSocialCount > 0)
                            <span class="badge bg-danger float-end">{{ $pendingSocialCount }}</span>
                        @endif
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.social-media.*') ? 'show' : '' }}" id="socialMediaSubmenu">
                        <li><a href="{{ route('admin.social-media.queue') }}">Cola de Publicación</a></li>
                    </ul>
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

            <div class="sidebar-footer">
                <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-light">
                    <i class="fas fa-external-link-alt"></i> Ver sitio
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content" class="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapseDesktop" class="btn d-none d-md-block">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="ms-auto d-flex">




                        <!-- Localiza esta parte en tu layout -->
                        <div class="ms-auto d-flex">
                            <!-- Añade el código de notificación aquí, justo antes del dropdown del usuario -->
                            
                            <!-- Nav Item - Social Media Alerts -->
                            <div class="dropdown mx-2">
                                <a class="nav-link position-relative" href="#" role="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-share-alt"></i>
                                    <!-- Counter - Alerts -->
                                    @if(isset($pendingSocialCount) && $pendingSocialCount > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $pendingSocialCount }}
                                            <span class="visually-hidden">publicaciones pendientes</span>
                                        </span>
                                    @endif
                                </a>
                                <!-- Dropdown - Social Media Alerts -->
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <h6 class="dropdown-header">Publicaciones Pendientes</h6>
                                    </li>
                                    
                                    @if(isset($pendingSocialPosts) && $pendingSocialPosts->count() > 0)
                                        @foreach($pendingSocialPosts as $item)
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.social-media.queue') }}">
                                                    <div class="me-3">
                                                        <div class="rounded-circle bg-primary p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                            @if($item->network == 'twitter')
                                                                <i class="fab fa-twitter text-white"></i>
                                                            @elseif($item->network == 'facebook')
                                                                <i class="fab fa-facebook-f text-white"></i>
                                                            @elseif($item->network == 'linkedin')
                                                                <i class="fab fa-linkedin-in text-white"></i>
                                                            @else
                                                                <i class="fas fa-share-alt text-white"></i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="small text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</div>
                                                        <span>{{ Str::limit($item->content, 40) }}</span>
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                        
                                        <li><hr class="dropdown-divider"></li>
                                        <li>                                                     
                                            <a class="dropdown-item text-center" href="{{ route('admin.social-media.queue') }}">
                                                Ver todas las publicaciones pendientes
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" href="#">
                                                <div class="me-3">
                                                    <div class="rounded-circle bg-success p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-check text-white"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span>No hay publicaciones pendientes</span>
                                                </div>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <!-- Dropdown de usuario (ya existente) -->
                            <div class="dropdown">
                                <!-- Resto del código del dropdown del usuario -->



                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ Auth::user()->avatar ?? asset('img/default-avatar.png') }}" 
                                     class="rounded-circle me-1" width="32" height="32" alt="Avatar">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Mi Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Cerrar sesión
                                    </a>
                                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="main-content p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="footer mt-auto py-3 bg-light">
                <div class="container text-center">
                    <span class="text-muted">© {{ date('Y') }} Panel de Administración | Todos los derechos reservados</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarCollapse');
            const desktopToggle = document.getElementById('sidebarCollapseDesktop');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }

            if (desktopToggle) {
                desktopToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('minimized');
                    content.classList.toggle('expanded');
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>