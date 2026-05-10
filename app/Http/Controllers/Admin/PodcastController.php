<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePodcastEpisode;
use App\Models\News;
use App\Models\PodcastEpisode;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    public function index(Request $request)
    {
        $episodes = PodcastEpisode::with(['news.tiktokScript'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total'      => PodcastEpisode::count(),
            'ready'      => PodcastEpisode::where('status', 'ready')->count(),
            'pending'    => PodcastEpisode::whereIn('status', ['pending', 'processing'])->count(),
            'error'      => PodcastEpisode::where('status', 'error')->count(),
        ];

        return view('admin.podcast.index', compact('episodes', 'stats'));
    }

    public function generate(News $news)
    {
        GeneratePodcastEpisode::dispatch($news);

        return back()->with('success', 'Generación de podcast encolada para: ' . $news->title);
    }

    public function regenerate(PodcastEpisode $episode)
    {
        $episode->update(['status' => 'pending', 'error_message' => null]);
        GeneratePodcastEpisode::dispatch($episode->news);

        return back()->with('success', 'Re-generación encolada.');
    }

    public function destroy(PodcastEpisode $episode)
    {
        $episode->delete();

        return back()->with('success', 'Episodio eliminado.');
    }
}
