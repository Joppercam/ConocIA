<?php

namespace App\Http\Controllers;

use App\Models\PodcastEpisode;

class PodcastController extends Controller
{
    public function rss()
    {
        $episodes = PodcastEpisode::with('news')
            ->where('status', 'ready')
            ->whereHas('news', fn($q) => $q->where('status', 'published'))
            ->latest('generated_at')
            ->take(100)
            ->get();

        return response()
            ->view('podcast.rss', compact('episodes'))
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
