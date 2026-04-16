{{-- JSON-LD global: Organization + WebSite + SearchAction
     Incluir UNA VEZ en layouts/app.blade.php dentro de <head>
--}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@graph": [
        {
            "@type": "Organization",
            "@id": "{{ url('/') }}/#organization",
            "name": "ConocIA",
            "url": "{{ url('/') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('storage/images/logo.png') }}",
                "width": 400,
                "height": 100
            },
            "sameAs": [
                "https://twitter.com/conocia_cl",
                "https://www.instagram.com/conocia.cl",
                "https://www.linkedin.com/company/conocia"
            ],
            "description": "Portal chileno de noticias, análisis y divulgación sobre inteligencia artificial y tecnología."
        },
        {
            "@type": "WebSite",
            "@id": "{{ url('/') }}/#website",
            "url": "{{ url('/') }}",
            "name": "ConocIA",
            "description": "Tu portal de noticias sobre inteligencia artificial en español",
            "publisher": {
                "@id": "{{ url('/') }}/#organization"
            },
            "potentialAction": {
                "@type": "SearchAction",
                "target": {
                    "@type": "EntryPoint",
                    "urlTemplate": "{{ url('/buscar') }}?query={search_term_string}"
                },
                "query-input": "required name=search_term_string"
            },
            "inLanguage": "es-CL"
        }
    ]
}
</script>
