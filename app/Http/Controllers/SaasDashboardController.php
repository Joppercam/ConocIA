<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Insight;
use App\Models\News;
use App\Support\MetricsTracker;
use Illuminate\Http\Request;

class SaasDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $recentInsights = Insight::with('noticia')
            ->latest()
            ->take($user->canAccessFeature('insights') ? 8 : 3)
            ->get();

        $importantNews = News::with('category')
            ->published()
            ->orderByDesc('views')
            ->take(6)
            ->get();

        $alerts = Alert::where('user_id', $user->id)->latest()->take(5)->get();

        MetricsTracker::track('dashboard_view', [
            'plan' => $user->plan(),
        ]);

        return view('saas.dashboard', compact('user', 'recentInsights', 'importantNews', 'alerts'));
    }
}
