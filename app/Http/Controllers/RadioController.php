<?php

namespace App\Http\Controllers;

use App\Models\DailyBriefing;
use Illuminate\Support\Facades\Cache;

class RadioController extends Controller
{
    public function index()
    {
        $episodes = Cache::remember('radio_episodes', 600, function () {
            return DailyBriefing::orderBy('date', 'desc')->get();
        });

        $featured = $episodes->first();
        $archive  = $episodes->skip(1)->values();

        return view('radio.index', compact('featured', 'archive'));
    }

    public function show(string $date)
    {
        $episode = DailyBriefing::where('date', $date)->firstOrFail();

        $prev = DailyBriefing::where('date', '<', $date)->orderBy('date', 'desc')->first();
        $next = DailyBriefing::where('date', '>', $date)->orderBy('date', 'asc')->first();

        return view('radio.show', compact('episode', 'prev', 'next'));
    }
}
