<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Research;
use App\Models\GuestPost;
use App\Models\ConocIaPaper;
use App\Models\ConceptoIa;
use App\Models\Column;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function live(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['news' => [], 'research' => [], 'papers' => [], 'conceptos' => [], 'query' => $q]);
        }

        $news = News::with('category')
            ->published()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('excerpt', 'like', "%{$q}%");
            })
            ->latest('published_at')
            ->limit(4)
            ->get()
            ->map(fn($n) => [
                'title'    => $n->title,
                'url'      => route('news.show', $n->slug),
                'category' => $n->category?->name,
                'date'     => $n->published_at?->locale('es')->diffForHumans(),
                'color'    => $n->category?->color ?? '#38b6ff',
            ]);

        $research = Research::published()
            ->where('title', 'like', "%{$q}%")
            ->limit(2)
            ->get()
            ->map(fn($r) => [
                'title' => $r->title,
                'url'   => route('research.show', $r->slug ?? $r->id),
            ]);

        $papers = ConocIaPaper::published()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('original_title', 'like', "%{$q}%")
                      ->orWhere('excerpt', 'like', "%{$q}%");
            })
            ->latest('published_at')
            ->limit(2)
            ->get()
            ->map(fn($p) => [
                'title' => $p->title,
                'url'   => route('papers.show', $p->slug),
            ]);

        $conceptos = ConceptoIa::published()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('definition', 'like', "%{$q}%")
                      ->orWhere('excerpt', 'like', "%{$q}%");
            })
            ->latest('published_at')
            ->limit(2)
            ->get()
            ->map(fn($c) => [
                'title' => $c->title,
                'url'   => route('conceptos.show', $c->slug),
            ]);

        return response()->json([
            'news'      => $news,
            'research'  => $research,
            'papers'    => $papers,
            'conceptos' => $conceptos,
            'query'     => $q,
            'more_url'  => route('search', ['query' => $q]),
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return redirect()->route('home');
        }

        $news = News::with(['category', 'user'])
            ->published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->latest('published_at')
            ->paginate(10);

        $researches = Research::with('user')
            ->published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('abstract', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->latest('published_at')
            ->paginate(5);

        $papers = ConocIaPaper::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('original_title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->latest('published_at')
            ->paginate(5);

        $conceptos = ConceptoIa::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('definition', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->latest('published_at')
            ->paginate(5);

        $columns = Column::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->latest('published_at')
            ->paginate(5);

        $guestPosts = GuestPost::with(['category', 'user'])
            ->published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->latest('published_at')
            ->paginate(5);

        return view('search', compact('news', 'researches', 'papers', 'conceptos', 'columns', 'guestPosts', 'query'));
    }
}
