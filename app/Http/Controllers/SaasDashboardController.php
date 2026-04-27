<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Insight;
use App\Models\News;
use App\Support\MetricsTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SaasDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $recentInsights = Schema::hasTable('insights')
            ? Insight::with('noticia')
                ->latest()
                ->take($user->canAccessFeature('insights') ? 8 : 3)
                ->get()
            : collect();

        $importantNews = News::with('category')
            ->published()
            ->orderByDesc('views')
            ->take(6)
            ->get();

        $alerts = Schema::hasTable('alerts')
            ? Alert::where('user_id', $user->id)->latest()->take(5)->get()
            : collect();

        MetricsTracker::track('dashboard_view', [
            'plan' => $user->plan(),
        ]);

        return view('saas.dashboard', compact('user', 'recentInsights', 'importantNews', 'alerts'));
    }
}
