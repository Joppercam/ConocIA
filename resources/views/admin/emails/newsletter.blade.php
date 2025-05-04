<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        /* Estilos del email */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 650px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .section-title {
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
            margin-top: 30px;
            color: #007bff;
            font-weight: bold;
        }
        .news-item {
            margin-bottom: 25px;
        }
        .news-item img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
        .news-item h3 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .news-item p {
            margin-top: 0;
        }
        .featured {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin-bottom: 30px;
        }
        .featured h2 {
            margin-top: 0;
            color: #007bff;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white !important;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
        }
        .footer a {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
        <h1>{{ $subject }}</h1>
        @if($subscriber && $subscriber->name)
            <p>Hola, {{ $subscriber->name }}!</p>
        @else
            <p>Hola!</p>
        @endif
    </div>
    
    @if($featuredNews->isNotEmpty())
        <div class="featured">
            @foreach($featuredNews as $item)
                <h2>{{ $item->title }}</h2>
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}">
                @endif
                <p>{{ $item->excerpt }}</p>
                <a href="{{ route('news.show', $item->slug) }}" class="button">Leer más</a>
            @endforeach
        </div>
    @endif
    
    <h2 class="section-title">Últimas Noticias</h2>
    
    @forelse($news as $item)
        <div class="news-item">
            <h3>{{ $item->title }}</h3>
            @if($item->image)
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}">
            @endif
            <p>{{ $item->excerpt }}</p>
            <a href="{{ route('news.show', $item->slug) }}" class="button">Leer más</a>
        </div>
    @empty
        <p>No hay noticias disponibles en este momento.</p>
    @endforelse
    
    @if($researches->isNotEmpty())
        <h2 class="section-title">Investigaciones Recientes</h2>
        
        @foreach($researches as $item)
            <div class="news-item">
                <h3>{{ $item->title }}</h3>
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}">
                @endif
                <p>{{ $item->excerpt }}</p>
                <a href="{{ route('research.show', $item->slug) }}" class="button">Leer más</a>
            </div>
        @endforeach
    @endif
    
    @if($columns->isNotEmpty())
        <h2 class="section-title">Columnas Destacadas</h2>
        
        @foreach($columns as $item)
            <div class="news-item">
                <h3>{{ $item->title }}</h3>
                @if($item->author)
                    <p><em>Por {{ $item->author->name }}</em></p>
                @endif
                <p>{{ $item->excerpt }}</p>
                <a href="{{ route('column.show', $item->slug) }}" class="button">Leer más</a>
            </div>
        @endforeach
    @endif
    
    <div class="footer">
        <p>
            Este correo fue enviado a {{ $subscriber->email ?? 'tu dirección de email' }} porque te suscribiste a nuestro newsletter.
        </p>
        
        @if($subscriber && $subscriber->categories->isNotEmpty())
            <p>
                Estás suscrito a las siguientes categorías: 
                {{ $subscriber->categories->pluck('name')->implode(', ') }}.
            </p>
        @endif
        
        <p>
            <a href="{{ $unsubscribeUrl }}">Cancelar suscripción</a> | 
            <a href="{{ $viewInBrowserUrl }}">Ver en el navegador</a>
        </p>
        
        <p>&copy; {{ date('Y') }} Tu Empresa. Todos los derechos reservados.</p>
    </div>
</body>
</html>