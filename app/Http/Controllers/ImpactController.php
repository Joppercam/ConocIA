<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\ConocIaPaper;
use App\Models\Research;
use App\Models\ConceptoIa;
use App\Models\User;
use App\Models\EstadoArte;
use App\Models\DailyBriefing;
use App\Models\Column;
use App\Models\Newsletter;

class ImpactController extends Controller
{
    public function index()
    {
        $metrics = [
            'total_articles'   => News::where('status', 'published')->count(),
            'papers_explained' => ConocIaPaper::count(),
            'research_articles'=> Research::count(),
            'concepts'         => ConceptoIa::where('status', 'published')->count(),
            'registered_users' => User::count(),
            'radio_episodes'   => DailyBriefing::count(),
            'estado_arte'      => EstadoArte::count(),
            'columns'          => Column::where('status', 'published')->count(),
            'subscribers'      => Newsletter::where('is_active', true)->count(),
            'fields_covered'   => 6,
        ];

        return view('impact.index', compact('metrics'));
    }
}
