<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject }}</title>
    <style>
        /* Agrega estilos CSS inline para compatibilidad con clientes de correo */
        body { font-family: Arial, sans-serif; }
        .news-item { margin-bottom: 20px; }
        /* etc... */
    </style>
</head>
<body>
    <h1>Últimas Noticias</h1>
    
    @foreach($news as $item)
    <div class="news-item">
        <h2>{{ $item->title }}</h2>
        <p>{{ Str::limit($item->content, 150) }}</p>
        <a href="{{ route('news.show', $item) }}">Leer más</a>
    </div>
    @endforeach
    
    <div class="footer">
        <p>
            Si deseas darte de baja de este newsletter, 
            <a href="{{ route('newsletter.unsubscribe', $unsubscribeToken) }}">haz clic aquí</a>
        </p>
    </div>
</body>
</html>