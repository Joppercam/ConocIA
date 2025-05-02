<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title>{{ config('app.name') }} - Resúmenes Diarios de Noticias</title>
        <link>{{ route('podcasts.index') }}</link>
        <language>es-es</language>
        <copyright>Copyright {{ date('Y') }} {{ config('app.name') }}</copyright>
        <itunes:author>{{ config('app.name') }}</itunes:author>
        <description>Resúmenes diarios en formato podcast con las 8 noticias más importantes sobre inteligencia artificial y tecnología.</description>
        <itunes:owner>
            <itunes:name>{{ config('app.name') }}</itunes:name>
            <itunes:email>{{ config('mail.from.address') }}</itunes:email>
        </itunes:owner>
        <itunes:image href="{{ asset('storage/images/podcast-cover.jpg') }}" />
        <itunes:category text="Technology">
            <itunes:category text="Artificial Intelligence" />
        </itunes:category>
        <itunes:explicit>false</itunes:explicit>
        
        @foreach($podcasts as $podcast)
        <item>
            <title>{{ $podcast->title }}</title>
            <itunes:author>{{ config('app.name') }}</itunes:author>
            <description>{{ strip_tags($podcast->description) }}</description>
            <content:encoded><![CDATA[{!! $podcast->description !!}]]></content:encoded>
            <enclosure url="{{ $podcast->audio_url }}" type="audio/mpeg" length="{{ $podcast->audio_size ?? '10000000' }}" />
            <guid>{{ route('podcasts.show', $podcast) }}</guid>
            <pubDate>{{ $podcast->published_at->toRfc2822String() }}</pubDate>
            <itunes:duration>{{ $podcast->duration ?? '10:00' }}</itunes:duration>
            <link>{{ route('podcasts.show', $podcast) }}</link>
            
            @if(isset($podcast->transcript) && !empty($podcast->transcript))
            <itunes:subtitle>{{ Str::limit(strip_tags($podcast->transcript), 255) }}</itunes:subtitle>
            @endif
            
            <itunes:explicit>false</itunes:explicit>
        </item>
        @endforeach
    </channel>
</rss>