<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePodcastEpisode;
use App\Models\News;
use App\Models\PodcastEpisode;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PodcastController extends Controller
{
    public function index(): View
    {
        $episodes = PodcastEpisode::with('news')
            ->orderByDesc('created_at')
            ->paginate(20);

        $stats = [
            'total'      => PodcastEpisode::count(),
            'ready'      => PodcastEpisode::where('status', 'ready')->count(),
            'pending'    => PodcastEpisode::whereIn('status', ['pending', 'processing'])->count(),
            'error'      => PodcastEpisode::where('status', 'error')->count(),
        ];

        return view('admin.podcast.index', compact('episodes', 'stats'));
    }

    public function generate(News $news): RedirectResponse
    {
        $episode = PodcastEpisode::where('news_id', $news->id)->first();

        if ($episode && $episode->status === 'ready') {
            return back()->with('info', 'Este artículo ya tiene episodio generado.');
        }

        if ($episode && $episode->status === 'processing') {
            return back()->with('info', 'El episodio ya está siendo procesado.');
        }

        GeneratePodcastEpisode::dispatch($news);

        return back()->with('success', 'Generación de episodio encolada para "' . $news->title . '".');
    }

    public function regenerate(PodcastEpisode $episode): RedirectResponse
    {
        $episode->update(['status' => 'pending', 'error_message' => null]);

        GeneratePodcastEpisode::dispatch($episode->news);

        return back()->with('success', 'Regeneración encolada.');
    }

    public function destroy(PodcastEpisode $episode): RedirectResponse
    {
        $episode->delete();

        return back()->with('success', 'Episodio eliminado.');
    }
}
