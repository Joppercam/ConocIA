<?php

namespace App\Http\Controllers;

use App\Models\PodcastEpisode;
use Illuminate\Http\Response;

class PodcastController extends Controller
{
    public function rss(): Response
    {
        $episodes = PodcastEpisode::with('news.category')
            ->where('status', 'ready')
            ->orderByDesc('generated_at')
            ->limit(100)
            ->get();

        $xml = $this->buildRss($episodes);

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }

    private function buildRss($episodes): string
    {
        $appUrl     = config('app.url');
        $appName    = config('app.name', 'ConocIA');
        $logoUrl    = $appUrl . '/images/logo-podcast.jpg';
        $feedUrl    = $appUrl . '/podcast.rss';
        $now        = now()->toRfc2822String();

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0"' . "\n";
        $xml .= '  xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"' . "\n";
        $xml .= '  xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        $xml .= '<channel>' . "\n";

        $xml .= '<title>' . e($appName) . '</title>' . "\n";
        $xml .= '<link>' . $appUrl . '</link>' . "\n";
        $xml .= '<description>Noticias y análisis sobre Inteligencia Artificial en español.</description>' . "\n";
        $xml .= '<language>es</language>' . "\n";
        $xml .= '<lastBuildDate>' . $now . '</lastBuildDate>' . "\n";
        $xml .= '<atom:link href="' . $feedUrl . '" rel="self" type="application/rss+xml"/>' . "\n";

        $xml .= '<itunes:author>' . e($appName) . '</itunes:author>' . "\n";
        $xml .= '<itunes:summary>Noticias y análisis sobre Inteligencia Artificial en español.</itunes:summary>' . "\n";
        $xml .= '<itunes:image href="' . $logoUrl . '"/>' . "\n";
        $xml .= '<itunes:category text="Technology"/>' . "\n";
        $xml .= '<itunes:explicit>no</itunes:explicit>' . "\n";
        $xml .= '<itunes:type>episodic</itunes:type>' . "\n";

        foreach ($episodes as $episode) {
            $news    = $episode->news;
            $pubDate = ($news->published_at ?? $news->created_at)->toRfc2822String();
            $guid    = route('news.show', $news->slug);
            $title   = htmlspecialchars($news->title, ENT_XML1, 'UTF-8');
            $summary = htmlspecialchars($news->summary ?? $news->excerpt ?? '', ENT_XML1, 'UTF-8');

            $xml .= '<item>' . "\n";
            $xml .= '  <title>' . $title . '</title>' . "\n";
            $xml .= '  <link>' . $guid . '</link>' . "\n";
            $xml .= '  <description>' . $summary . '</description>' . "\n";
            $xml .= '  <guid isPermaLink="true">' . $guid . '</guid>' . "\n";
            $xml .= '  <pubDate>' . $pubDate . '</pubDate>' . "\n";
            $xml .= '  <enclosure url="' . $episode->audio_url . '"'
                . ' length="' . ($episode->file_size ?? 0) . '"'
                . ' type="audio/mpeg"/>' . "\n";
            $xml .= '  <itunes:title>' . $title . '</itunes:title>' . "\n";
            $xml .= '  <itunes:summary>' . $summary . '</itunes:summary>' . "\n";
            $xml .= '  <itunes:duration>' . $episode->getItunesDuration() . '</itunes:duration>' . "\n";
            $xml .= '  <itunes:explicit>no</itunes:explicit>' . "\n";
            if ($news->category) {
                $xml .= '  <itunes:keywords>' . htmlspecialchars($news->category->name, ENT_XML1, 'UTF-8') . '</itunes:keywords>' . "\n";
            }
            $xml .= '</item>' . "\n";
        }

        $xml .= '</channel>' . "\n";
        $xml .= '</rss>';

        return $xml;
    }
}
