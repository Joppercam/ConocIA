<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $upcoming = Event::active()->upcoming()
            ->orderBy('start_date')
            ->get();

        $past = Event::active()->past()
            ->orderByDesc('start_date')
            ->limit(6)
            ->get();

        $featured = $upcoming->where('featured', true)->first();

        return view('agenda.index', compact('upcoming', 'past', 'featured'));
    }
}
