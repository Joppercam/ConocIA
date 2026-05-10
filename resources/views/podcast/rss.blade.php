<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0"
     xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
     xmlns:content="http://purl.org/rss/1.0/modules/content/">
  <channel>
    <title>ConocIA Podcast</title>
    <link>{{ url('/') }}</link>
    <description>Noticias y análisis sobre inteligencia artificial en español, desde Chile.</description>
    <language>es-cl</language>
    <itunes:author>ConocIA</itunes:author>
    <itunes:owner>
      <itunes:name>ConocIA</itunes:name>
      <itunes:email>conociacl@gmail.com</itunes:email>
    </itunes:owner>
    <itunes:category text="Technology">
      <itunes:category text="Tech News"/>
    </itunes:category>
    <itunes:explicit>false</itunes:explicit>
    <itunes:image href="{{ asset('images/podcast-cover.jpg') }}"/>
    <image>
      <url>{{ asset('images/podcast-cover.jpg') }}</url>
      <title>ConocIA Podcast</title>
      <link>{{ url('/') }}</link>
    </image>
    @foreach($episodes as $episode)
    <item>
      <title>{{ $episode->news->title }}</title>
      <link>{{ route('news.show', $episode->news->slug) }}</link>
      <description><![CDATA[{{ $episode->news->summary }}]]></description>
      <enclosure url="{{ $episode->audio_url }}"
                 length="{{ $episode->file_size ?? 0 }}"
                 type="audio/mpeg"/>
      <guid isPermaLink="false">conocia-podcast-{{ $episode->news_id }}</guid>
      <pubDate>{{ $episode->generated_at->toRfc2822String() }}</pubDate>
      <itunes:duration>{{ $episode->getItunesDuration() }}</itunes:duration>
      <itunes:author>ConocIA</itunes:author>
    </item>
    @endforeach
  </channel>
</rss>
