<?php

namespace App\Http\Controllers;

use App\Models\ConocIaPaper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConocIaPaperController extends Controller
{
    public function index()
    {
        $featured = Cache::remember('papers_featured', 1800, fn() =>
            ConocIaPaper::published()->featured()->latest('published_at')->take(3)->get()
        );

        $papers = ConocIaPaper::published()
            ->latest('arxiv_published_date')
            ->paginate(12);

        $arxivCategories = Cache::remember('papers_arxiv_cats', 3600, fn() =>
            ConocIaPaper::published()
                ->selectRaw('arxiv_category, COUNT(*) as count')
                ->whereNotNull('arxiv_category')
                ->groupBy('arxiv_category')
                ->orderByDesc('count')
                ->pluck('count', 'arxiv_category')
        );

        return view('papers.index', compact('featured', 'papers', 'arxivCategories'));
    }

    public function show(string $slug)
    {
        $paper = ConocIaPaper::where('slug', $slug)->published()->firstOrFail();

        DB::table('conocia_papers')->where('id', $paper->id)->increment('views');

        $related = Cache::remember("paper_related_{$paper->id}", 3600, fn() =>
            ConocIaPaper::published()
                ->where('id', '!=', $paper->id)
                ->where('arxiv_category', $paper->arxiv_category)
                ->latest('arxiv_published_date')
                ->take(3)
                ->get()
        );

        return view('papers.show', compact('paper', 'related'));
    }

    public function byCategory(string $category)
    {
        $papers = ConocIaPaper::published()
            ->byArxivCategory($category)
            ->latest('arxiv_published_date')
            ->paginate(12);

        $arxivCategories = Cache::remember('papers_arxiv_cats', 3600, fn() =>
            ConocIaPaper::published()
                ->selectRaw('arxiv_category, COUNT(*) as count')
                ->whereNotNull('arxiv_category')
                ->groupBy('arxiv_category')
                ->orderByDesc('count')
                ->pluck('count', 'arxiv_category')
        );

        return view('papers.index', compact('papers', 'category', 'arxivCategories'));
    }
}
