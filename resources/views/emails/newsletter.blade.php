<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ $subject }}</title>
    <style>
        /* Estilos base */
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 16px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            color: #333333;
        }
        
        /* Contenedor principal */
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        /* Encabezado */
        .header {
            background: linear-gradient(135deg, #2a2a72 0%, #38b6ff 100%);
            color: white;
            padding: 24px;
            text-align: center;
        }
        
        .logo {
            margin-bottom: 16px;
        }
        
        .logo-text {
            font-family: 'Montserrat', sans-serif;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #ffffff;
            margin: 0;
        }
        
        .text-highlight {
            position: relative;
            font-weight: 900;
            color: #00e1ff;
        }
        
        .header-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin: 8px 0 0;
        }
        
        /* Contenido principal */
        .content {
            padding: 24px;
        }
        
        .greeting {
            font-size: 18px;
            margin-bottom: 16px;
        }
        
        .intro {
            color: #555;
            margin-bottom: 24px;
            font-size: 16px;
        }
        
        /* Secciones */
        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            margin: 32px 0 16px;
            padding: 10px 15px;
            background: linear-gradient(135deg, #2a2a72 0%, #38b6ff 100%);
            border-radius: 6px;
            text-align: center;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }
        
        .section-title-icon {
            margin-right: 8px;
            color: #ffffff;
        }
        
        /* Artículos */
        .article {
            margin-bottom: 28px;
            background-color: #f8f9ff;
            border-radius: 8px;
            padding: 16px;
            border-left: 4px solid #38b6ff;
            text-align: left;
        }
        
        .article:last-child {
            margin-bottom: 16px;
        }
        
        .article-featured {
            background-color: #f8f9ff;
            border-radius: 8px;
            padding: 16px;
            border-left: 4px solid #38b6ff;
            margin-bottom: 32px;
            text-align: left;
        }
        
        /* Eliminamos los estilos de imágenes ya que no los usaremos */
        
        .article-badge {
            background-color: #38b6ff;
            color: white;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
            text-transform: uppercase;
            display: inline-block;
        }
        
        .article-category {
            display: inline-block;
            font-size: 13px;
            font-weight: 600;
            color: #38b6ff;
            margin-bottom: 8px;
            text-transform: uppercase;
            text-align: left;
        }
        
        .article-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 12px;
            color: #2a2a72;
            line-height: 1.3;
            text-align: left;
        }
        
        .article-title a {
            color: #2a2a72;
            text-decoration: none;
        }
        
        .article-title a:hover {
            color: #38b6ff;
        }
        
        .article-meta {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            font-size: 14px;
            color: #777;
            text-align: left;
        }
        
        .article-author {
            margin-right: 16px;
        }
        
        .article-date {
            margin-right: 16px;
        }
        
        .article-reading-time {
            display: flex;
            align-items: center;
        }
        
        .article-excerpt {
            color: #444;
            margin-bottom: 16px;
            line-height: 1.6;
            text-align: left;
        }
        
        .article-button-container {
            text-align: center;
            margin-top: 16px;
        }
        
        .article-button {
            display: inline-block;
            padding: 8px 20px;
            background-color: #38b6ff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: background 0.2s;
        }
        
        .article-button:hover {
            background-color: #00e1ff;
        }
        
        /* Sección de estadísticas */
        .stats-section {
            background-color: #f8faff;
            padding: 16px;
            border-radius: 8px;
            margin: 32px 0;
        }
        
        .stats-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #2a2a72;
        }
        
        .stats-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            text-align: center;
        }
        
        .stat-item {
            margin: 8px 0;
            flex-basis: 30%;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #38b6ff;
            margin-bottom: 4px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
        }
        
        /* Call to action */
        .cta-section {
            background-color: #2a2a72;
            color: white;
            padding: 24px;
            text-align: center;
            margin: 32px -24px;
        }
        
        .cta-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        
        .cta-subtitle {
            margin-bottom: 24px;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .cta-button {
            display: inline-block;
            background-color: white;
            color: #2a2a72;
            padding: 12px 24px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .cta-button:hover {
            background-color: #f8f9fa;
        }
        
        /* Footer */
        .footer {
            background-color: #f8f9fa;
            padding: 24px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }
        
        .social-links {
            margin: 16px 0;
        }
        
        .social-link {
            display: inline-block;
            width: 36px;
            height: 36px;
            line-height: 36px;
            background-color: #38b6ff;
            color: white;
            border-radius: 50%;
            margin: 0 6px;
            text-align: center;
            text-decoration: none;
        }
        
        .social-link:hover {
            background-color: #00e1ff;
        }
        
        .footer-links {
            margin: 16px 0;
        }
        
        .footer-link {
            color: #555;
            text-decoration: none;
            margin: 0 8px;
        }
        
        .footer-link:hover {
            color: #38b6ff;
            text-decoration: underline;
        }
        
        .unsubscribe {
            color: #888;
            margin-top: 16px;
        }
        
        .unsubscribe a {
            color: #888;
            text-decoration: underline;
        }
        
        /* Media queries para responsividad */
        @media only screen and (max-width: 620px) {
            .container {
                width: 100% !important;
                border-radius: 0;
            }
            
            .content {
                padding: 20px;
            }
            
            .article-title {
                font-size: 18px;
            }
            
            .stats-container {
                flex-direction: column;
            }
            
            .stat-item {
                margin: 8px 0;
                flex-basis: 100%;
            }
            
            .cta-section {
                margin: 32px -20px;
                padding: 24px 20px;
            }
        }

        /* Media queries específicas para correos electrónicos */
        @media all {
            .ExternalClass {
                width: 100%;
            }
            
            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }
            
            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important;
            }
            
            #MessageViewBody a {
                color: inherit;
                text-decoration: none;
                font-size: inherit;
                font-family: inherit;
                font-weight: inherit;
                line-height: inherit;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 24px 0;">
                <div class="container">
                    <!-- HEADER -->
                    <div class="header">
                        <div class="logo">
                            <h1 class="logo-text">Conoc<span class="text-highlight">IA</span></h1>
                        </div>
                        <p class="header-subtitle">{{ $subject ?? 'Tu actualización semanal de noticias e investigaciones en IA' }}</p>
                    </div>
                    
                    <!-- CONTENT -->
                    <div class="content">
                        <p class="greeting">Hola,</p>
                        <p class="intro">Bienvenido a nuestra newsletter semanal donde encontrarás las novedades más interesantes sobre Inteligencia Artificial, tecnología e investigación.</p>
                        
                        <!-- NOTICIAS DESTACADAS -->
                        @if(isset($featuredNews) && $featuredNews->count() > 0)
                        <div class="article-featured">
                            <span class="article-category">Noticia Destacada</span>
                            <h2 class="article-title">
                                <a href="{{ route('news.show', $featuredNews->first()->slug ?? $featuredNews->first()->id) }}" target="_blank">
                                    {{ $featuredNews->first()->title }}
                                </a>
                            </h2>
                            <div class="article-meta">
                                @if($featuredNews->first()->author)
                                <span class="article-author">
                                    <i class="fas fa-user-circle" style="color: #777;"></i> 
                                    {{ $featuredNews->first()->author->name ?? 'Editor' }}
                                </span>
                                @endif
                                <span class="article-date">
                                    <i class="fas fa-calendar-alt" style="color: #777;"></i> 
                                    {{ $featuredNews->first()->created_at->format('d M, Y') }}
                                </span>
                                @if($featuredNews->first()->reading_time)
                                <span class="article-reading-time">
                                    <i class="fas fa-clock" style="color: #777;"></i> 
                                    {{ $featuredNews->first()->reading_time }} min
                                </span>
                                @endif
                            </div>
                            <span class="article-badge" style="margin-bottom: 12px;">Destacado</span>
                            <p class="article-excerpt">{{ $featuredNews->first()->excerpt }}</p>
                            <div class="article-button-container">
                                <a href="{{ route('news.show', $featuredNews->first()->slug ?? $featuredNews->first()->id) }}" class="article-button" target="_blank">Leer artículo completo</a>
                            </div>
                        </div>
                        @endif
                        
                        <!-- NOTICIAS RECIENTES -->
                        <h2 class="section-title">
                            <i class="fas fa-newspaper section-title-icon"></i> Últimas Noticias
                        </h2>
                        
                        @foreach($news as $item)
                        @if(!isset($featuredNews) || (isset($featuredNews) && $featuredNews->first()->id != $item->id))
                        <div class="article">
                            @if($item->category)
                            <span class="article-category">{{ $item->category->name }}</span>
                            @endif
                            <h3 class="article-title">
                                <a href="{{ route('news.show', $item->slug ?? $item->id) }}" target="_blank">
                                    {{ $item->title }}
                                </a>
                            </h3>
                            <div class="article-meta">
                                @if($item->author)
                                <span class="article-author">
                                    <i class="fas fa-user-circle" style="color: #777;"></i> 
                                    {{ $item->author->name ?? 'Editor' }}
                                </span>
                                @endif
                                <span class="article-date">
                                    <i class="fas fa-calendar-alt" style="color: #777;"></i> 
                                    {{ $item->created_at->format('d M, Y') }}
                                </span>
                                @if($item->reading_time)
                                <span class="article-reading-time">
                                    <i class="fas fa-clock" style="color: #777;"></i> 
                                    {{ $item->reading_time }} min
                                </span>
                                @endif
                            </div>
                            <p class="article-excerpt">{{ $item->excerpt }}</p>
                            <div class="article-button-container">
                                <a href="{{ route('news.show', $item->slug ?? $item->id) }}" class="article-button" target="_blank">Leer artículo</a>
                            </div>
                        </div>
                        @endif
                        @endforeach
                        
                        <!-- INVESTIGACIONES RECIENTES -->
                        @if(isset($researches) && $researches->count() > 0)
                        <h2 class="section-title">
                            <i class="fas fa-flask section-title-icon"></i> Investigaciones Recientes
                        </h2>
                        
                        @foreach($researches as $research)
                        <div class="article">
                            @if($research->category)
                            <span class="article-category">{{ $research->category->name }}</span>
                            @endif
                            <h3 class="article-title">
                                <a href="{{ route('research.show', $research->slug ?? $research->id) }}" target="_blank">
                                    {{ $research->title }}
                                </a>
                            </h3>
                            <div class="article-meta">
                                @if($research->author || $research->user)
                                <span class="article-author">
                                    <i class="fas fa-user-circle" style="color: #777;"></i> 
                                    {{ $research->author ?? $research->user->name ?? 'Investigador' }}
                                </span>
                                @endif
                                @if($research->published_at || $research->created_at)
                                <span class="article-date">
                                    <i class="fas fa-calendar-alt" style="color: #777;"></i> 
                                    {{ ($research->published_at ?? $research->created_at)->format('d M, Y') }}
                                </span>
                                @endif
                            </div>
                            <p class="article-excerpt">{{ $research->abstract ?? $research->excerpt ?? $research->summary }}</p>
                            <div class="article-button-container">
                                <a href="{{ route('research.show', $research->slug ?? $research->id) }}" class="article-button" target="_blank">Ver investigación</a>
                            </div>
                        </div>
                        @endforeach
                        @endif
                        
                        <!-- COLUMNAS RECIENTES -->
                        @if(isset($columns) && $columns->count() > 0)
                        <h2 class="section-title">
                            <i class="fas fa-pen-fancy section-title-icon"></i> Columnas de Opinión
                        </h2>
                        
                        @foreach($columns as $column)
                        <div class="article">
                            @if($column->category)
                            <span class="article-category">{{ $column->category->name }}</span>
                            @endif
                            <h3 class="article-title">
                                <a href="{{ route('columns.show', $column->slug) }}" target="_blank">
                                    {{ $column->title }}
                                </a>
                            </h3>
                            <div class="article-meta">
                                @if($column->author)
                                <span class="article-author">
                                    <i class="fas fa-user-circle" style="color: #777;"></i> 
                                    {{ $column->author->name ?? 'Columnista' }}
                                </span>
                                @endif
                                <span class="article-date">
                                    <i class="fas fa-calendar-alt" style="color: #777;"></i> 
                                    {{ $column->published_at->format('d M, Y') }}
                                </span>
                                @if($column->reading_time)
                                <span class="article-reading-time">
                                    <i class="fas fa-clock" style="color: #777;"></i> 
                                    {{ $column->reading_time }} min
                                </span>
                                @endif
                            </div>
                            <p class="article-excerpt">{{ $column->excerpt }}</p>
                            <div class="article-button-container">
                                <a href="{{ route('columns.show', $column->slug) }}" class="article-button" target="_blank">Leer columna</a>
                            </div>
                        </div>
                        @endforeach
                        @endif
                        
                        <!-- SECTION: PARTICIPACIÓN / SUBMIT RESEARCH -->
                        <div class="cta-section">
                            <h3 class="cta-title">¿Tienes una investigación sobre IA o tecnología?</h3>
                            <p class="cta-subtitle">Comparte tus conocimientos con nuestra comunidad de expertos y entusiastas.</p>
                            <a href="{{ route('submit-research') }}" class="cta-button" target="_blank">
                                Enviar investigación
                            </a>
                        </div>
                        
                        <p style="margin-top: 32px;">¡Gracias por formar parte de nuestra comunidad! Esperamos que disfrutes de estos contenidos.</p>
                        <p>El equipo de ConocIA</p>
                    </div>
                    
                    <!-- FOOTER -->
                    <div class="footer">
                        <div class="social-links">
                            <a href="#" class="social-link" target="_blank" aria-label="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-link" target="_blank" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link" target="_blank" aria-label="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="social-link" target="_blank" aria-label="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                        
                        <div class="footer-links">
                            <a href="{{ route('home') }}" class="footer-link" target="_blank">Inicio</a>
                            <a href="{{ route('news.index') }}" class="footer-link" target="_blank">Noticias</a>
                            <a href="{{ route('research.index') }}" class="footer-link" target="_blank">Investigación</a>
                            <a href="{{ route('columns.index') }}" class="footer-link" target="_blank">Columnas</a>
                        </div>
                        
                        <p>&copy; {{ date('Y') }} ConocIA - Todos los derechos reservados</p>
                        
                        <div class="unsubscribe">
                            <p>
                                Estás recibiendo este correo porque te suscribiste a nuestro newsletter.<br>
                                <a href="{{ route('newsletter.unsubscribe', $unsubscribeToken) }}" target="_blank">Cancelar suscripción</a>
                            </p>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>