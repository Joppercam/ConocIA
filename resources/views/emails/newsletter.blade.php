<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ $subject }}</title>
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .wrapper {
            max-width: 620px;
            margin: 0 auto;
            padding: 24px 0;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        /* HEADER */
        .header {
            background: linear-gradient(135deg, #1a1a5e 0%, #2a6dd9 100%);
            padding: 32px 24px 28px;
            text-align: center;
        }
        .logo-text {
            font-size: 34px;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 8px;
            letter-spacing: -0.5px;
        }
        .logo-text span {
            color: #00e1ff;
        }
        .header-subtitle {
            font-size: 14px;
            color: rgba(255,255,255,0.8);
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* GREETING */
        .greeting-section {
            padding: 24px 28px 8px;
        }
        .greeting-section p {
            margin: 0 0 8px;
            color: #444;
            font-size: 16px;
        }

        /* SECTION TITLES */
        .section-header {
            margin: 28px 28px 16px;
            border-left: 4px solid #2a6dd9;
            padding-left: 12px;
        }
        .section-header h2 {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #2a6dd9;
            margin: 0;
        }

        /* CARD BASE */
        .card {
            margin: 0 28px 16px;
            border-radius: 8px;
            padding: 18px 20px;
            background: #f8f9ff;
            border: 1px solid #e8ecf8;
        }

        /* NOTICIA DESTACADA */
        .card-featured {
            background: linear-gradient(135deg, #1a1a5e 0%, #2a6dd9 100%);
            color: white;
            border: none;
        }
        .card-featured .article-category {
            color: #00e1ff;
        }
        .card-featured .article-title a {
            color: #ffffff;
        }
        .card-featured .article-meta {
            color: rgba(255,255,255,0.7);
        }
        .card-featured .article-excerpt {
            color: rgba(255,255,255,0.85);
        }
        .badge-featured {
            display: inline-block;
            background: #00e1ff;
            color: #1a1a5e;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 10px;
        }

        /* ARTICLE ELEMENTS */
        .article-category {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #2a6dd9;
            margin-bottom: 6px;
        }
        .article-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 10px;
            line-height: 1.3;
            color: #1a1a5e;
        }
        .article-title a {
            color: #1a1a5e;
            text-decoration: none;
        }
        .article-meta {
            font-size: 13px;
            color: #888;
            margin-bottom: 10px;
        }
        .article-excerpt {
            font-size: 15px;
            color: #555;
            line-height: 1.6;
            margin: 0 0 14px;
        }
        .btn {
            display: inline-block;
            padding: 9px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }
        .btn-primary {
            background-color: #2a6dd9;
            color: #ffffff;
        }
        .btn-white {
            background-color: #ffffff;
            color: #1a1a5e;
        }
        .btn-outline {
            background-color: transparent;
            color: #2a6dd9;
            border: 1.5px solid #2a6dd9;
        }

        /* STARTUP CARD */
        .card-startup {
            background: #fffdf5;
            border: 1px solid #f0e8cc;
        }
        .startup-tag {
            display: inline-block;
            background: #f7c948;
            color: #7a5a00;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 10px;
        }
        .startup-meta {
            font-size: 13px;
            color: #888;
            margin-bottom: 10px;
        }
        .startup-tagline {
            font-size: 15px;
            font-style: italic;
            color: #666;
            margin: 0 0 12px;
            border-left: 3px solid #f7c948;
            padding-left: 10px;
        }

        /* PAPER CARD */
        .card-paper {
            background: #f5f9ff;
            border: 1px solid #d0e3ff;
        }
        .paper-tag {
            display: inline-block;
            background: #2a6dd9;
            color: white;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 10px;
        }

        /* DIVIDER */
        .divider {
            border: none;
            border-top: 1px solid #eaecf0;
            margin: 4px 28px;
        }

        /* CTA SECTION */
        .cta-section {
            background: linear-gradient(135deg, #1a1a5e 0%, #2a6dd9 100%);
            padding: 28px 28px;
            text-align: center;
            margin-top: 28px;
        }
        .cta-section h3 {
            color: #ffffff;
            font-size: 18px;
            margin: 0 0 8px;
        }
        .cta-section p {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            margin: 0 0 18px;
        }

        /* FOOTER */
        .footer {
            background-color: #f8f9fa;
            padding: 24px 28px;
            text-align: center;
            color: #888;
            font-size: 13px;
        }
        .footer-links {
            margin: 12px 0;
        }
        .footer-link {
            color: #666;
            text-decoration: none;
            margin: 0 8px;
        }
        .unsubscribe {
            margin-top: 12px;
            font-size: 12px;
            color: #aaa;
        }
        .unsubscribe a {
            color: #aaa;
            text-decoration: underline;
        }

        @media only screen and (max-width: 620px) {
            .wrapper { padding: 0; }
            .container { border-radius: 0; }
            .card, .section-header, .divider { margin-left: 16px; margin-right: 16px; }
            .greeting-section { padding: 20px 16px 8px; }
            .cta-section, .footer { padding-left: 16px; padding-right: 16px; }
            .article-title { font-size: 16px; }
        }
    </style>
</head>
<body>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td>
<div class="wrapper">
<div class="container">

    {{-- HEADER --}}
    <div class="header">
        <h1 class="logo-text">Conoc<span>IA</span></h1>
        <p class="header-subtitle">{{ $subject }}</p>
    </div>

    {{-- GREETING --}}
    <div class="greeting-section">
        <p>Hola{{ isset($subscriber->name) && $subscriber->name ? ', ' . $subscriber->name : '' }},</p>
        <p>Aquí tienes lo más relevante en Inteligencia Artificial que seleccionamos para ti.</p>
    </div>

    {{-- NOTICIA DESTACADA --}}
    @if(isset($featuredNews) && $featuredNews->count() > 0)
    @php $featured = $featuredNews->first(); @endphp
    <div class="section-header">
        <h2>Noticia Destacada</h2>
    </div>
    <div class="card card-featured">
        <span class="badge-featured">Destacado</span>
        @if($featured->category)
        <span class="article-category">{{ $featured->category->name }}</span>
        @endif
        <div class="article-title">
            <a href="{{ route('news.show', $featured->slug ?? $featured->id) }}" target="_blank">{{ $featured->title }}</a>
        </div>
        <div class="article-meta">{{ $featured->created_at->format('d M, Y') }}</div>
        <p class="article-excerpt">{{ $featured->excerpt }}</p>
        <a href="{{ route('news.show', $featured->slug ?? $featured->id) }}" class="btn btn-white" target="_blank">Leer artículo completo</a>
    </div>
    @endif

    {{-- ÚLTIMAS NOTICIAS --}}
    @if($news->count() > 0)
    <hr class="divider">
    <div class="section-header">
        <h2>Últimas Noticias</h2>
    </div>
    @foreach($news as $item)
    <div class="card">
        @if($item->category)
        <span class="article-category">{{ $item->category->name }}</span>
        @endif
        <div class="article-title">
            <a href="{{ route('news.show', $item->slug ?? $item->id) }}" target="_blank">{{ $item->title }}</a>
        </div>
        <div class="article-meta">{{ $item->created_at->format('d M, Y') }}</div>
        <p class="article-excerpt">{{ $item->excerpt }}</p>
        <a href="{{ route('news.show', $item->slug ?? $item->id) }}" class="btn btn-outline" target="_blank">Leer más</a>
    </div>
    @endforeach
    @endif

    {{-- PAPERS DE IA --}}
    @if(isset($papers) && $papers->count() > 0)
    <hr class="divider">
    <div class="section-header">
        <h2>Papers de IA</h2>
    </div>
    @foreach($papers as $paper)
    <div class="card card-paper">
        <span class="paper-tag">Investigación</span>
        @if($paper->arxiv_category)
        <span class="article-category">{{ $paper->arxiv_category }}</span>
        @endif
        <div class="article-title">
            <a href="{{ route('papers.show', $paper->slug) }}" target="_blank">{{ $paper->title }}</a>
        </div>
        @if($paper->authors && count($paper->authors) > 0)
        <div class="article-meta">{{ implode(', ', array_slice($paper->authors, 0, 3)) }}{{ count($paper->authors) > 3 ? ' y otros' : '' }}</div>
        @endif
        <p class="article-excerpt">{{ $paper->excerpt }}</p>
        <a href="{{ route('papers.show', $paper->slug) }}" class="btn btn-outline" target="_blank">Ver paper</a>
    </div>
    @endforeach
    @endif

    {{-- STARTUP DE LA SEMANA --}}
    @if(isset($startup) && $startup)
    <hr class="divider">
    <div class="section-header">
        <h2>Startup de la Semana</h2>
    </div>
    <div class="card card-startup">
        <span class="startup-tag">Startup</span>
        <div class="article-title" style="color:#1a1a5e;">{{ $startup->name }}</div>
        <div class="startup-meta">
            {{ $startup->sector ?? '' }}{{ ($startup->country && $startup->sector) ? ' · ' : '' }}{{ $startup->country ?? '' }}
            @if($startup->stage) · {{ $startup->stage_label }} @endif
        </div>
        @if($startup->tagline)
        <p class="startup-tagline">{{ $startup->tagline }}</p>
        @endif
        @if($startup->why_it_matters)
        <p class="article-excerpt">{{ Str::limit($startup->why_it_matters, 200) }}</p>
        @elseif($startup->description)
        <p class="article-excerpt">{{ Str::limit($startup->description, 200) }}</p>
        @endif
        <a href="{{ route('startups.show', $startup->slug) }}" class="btn btn-outline" target="_blank">Ver startup</a>
    </div>
    @endif

    {{-- INVESTIGACIONES --}}
    @if(isset($researches) && $researches->count() > 0)
    <hr class="divider">
    <div class="section-header">
        <h2>Investigación</h2>
    </div>
    @foreach($researches as $research)
    <div class="card">
        @if($research->category)
        <span class="article-category">{{ $research->category->name }}</span>
        @endif
        <div class="article-title">
            <a href="{{ route('research.show', $research->slug ?? $research->id) }}" target="_blank">{{ $research->title }}</a>
        </div>
        <div class="article-meta">{{ ($research->published_at ?? $research->created_at)->format('d M, Y') }}</div>
        <p class="article-excerpt">{{ Str::limit($research->abstract ?? $research->excerpt ?? $research->summary, 200) }}</p>
        <a href="{{ route('research.show', $research->slug ?? $research->id) }}" class="btn btn-outline" target="_blank">Ver investigación</a>
    </div>
    @endforeach
    @endif

    {{-- CTA --}}
    <div class="cta-section">
        <h3>Explora más en ConocIA</h3>
        <p>Noticias, papers, investigaciones y startups de inteligencia artificial en un solo lugar.</p>
        <a href="{{ route('home') }}" class="btn btn-white" target="_blank">Visitar ConocIA</a>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-links">
            <a href="{{ route('home') }}" class="footer-link" target="_blank">Inicio</a>
            <a href="{{ route('news.index') }}" class="footer-link" target="_blank">Noticias</a>
            <a href="{{ route('papers.index') }}" class="footer-link" target="_blank">Papers</a>
            <a href="{{ route('research.index') }}" class="footer-link" target="_blank">Investigación</a>
            <a href="{{ route('startups.index') }}" class="footer-link" target="_blank">Startups</a>
        </div>
        <p>&copy; {{ date('Y') }} ConocIA — Todos los derechos reservados</p>
        <div class="unsubscribe">
            Recibís este correo porque te suscribiste a ConocIA.<br>
            <a href="{{ route('newsletter.unsubscribe', $unsubscribeToken) }}" target="_blank">Cancelar suscripción</a>
        </div>
    </div>

</div>
</div>
</td></tr>
</table>
</body>
</html>
