<!-- Archivo: resources/views/sitemap/index.blade.php -->
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>{{ url('sitemap-main.xml') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
    </sitemap>
    <sitemap>
        <loc>{{ url('sitemap-news.xml') }}</loc>
        <lastmod>{{ $news->first()->updated_at->toIso8601String() }}</lastmod>
    </sitemap>
    <sitemap>
        <loc>{{ url('sitemap-categories.xml') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
    </sitemap>
    <sitemap>
        <loc>{{ url('sitemap-research.xml') }}</loc>
        <lastmod>{{ $researches->count() > 0 ? $researches->first()->updated_at->toIso8601String() : now()->toIso8601String() }}</lastmod>
    </sitemap>
    <sitemap>
        <loc>{{ url('sitemap-columns.xml') }}</loc>
        <lastmod>{{ $columns->count() > 0 ? $columns->first()->updated_at->toIso8601String() : now()->toIso8601String() }}</lastmod>
    </sitemap>
</sitemapindex>

<!-- Archivo: resources/views/sitemap/main.blade.php -->
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ url('/news') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/investigacion') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ url('/columnas') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ url('/acerca-de') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ url('/contacto') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ url('/privacidad') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>{{ url('/terminos') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>{{ url('/cookies') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
</urlset>

<!-- Archivo: resources/views/sitemap/news.blade.php -->
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
    @foreach ($news as $article)
    <url>
        <loc>{{ route('news.show', $article->slug ?? $article->id) }}</loc>
        <lastmod>{{ $article->updated_at->toIso8601String() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
        <news:news>
            <news:publication>
                <news:name>{{ config('app.name', 'ConocIA') }}</news:name>
                <news:language>{{ app()->getLocale() }}</news:language>
            </news:publication>
            <news:publication_date>{{ $article->published_at ? $article->published_at->toIso8601String() : $article->created_at->toIso8601String() }}</news:publication_date>
            <news:title>{{ $article->title }}</news:title>
            @if(is_object($article->category) && isset($article->category->name))
            <news:keywords>{{ $article->category->name }}</news:keywords>
            @endif
        </news:news>
    </url>
    @endforeach
</urlset>

<!-- Archivo: resources/views/sitemap/categories.blade.php -->
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($categories as $category)
    <url>
        <loc>{{ route('news.by.category', $category->slug) }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>

<!-- Archivo: resources/views/sitemap/research.blade.php -->
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($researches as $research)
    <url>
        <loc>{{ route('research.show', $research->slug ?? $research->id) }}</loc>
        <lastmod>{{ $research->updated_at->toIso8601String() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>

<!-- Archivo: resources/views/sitemap/columns.blade.php -->
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($columns as $column)
    <url>
        <loc>{{ route('columns.show', $column->slug ?? $column->id) }}</loc>
        <lastmod>{{ $column->updated_at->toIso8601String() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>