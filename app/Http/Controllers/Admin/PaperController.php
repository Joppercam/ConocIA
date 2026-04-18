<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConocIaPaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class PaperController extends Controller
{
    public function index(Request $request)
    {
        $query = ConocIaPaper::query();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('original_title', 'like', "%{$search}%")
                  ->orWhere('arxiv_id', 'like', "%{$search}%");
            });
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        if ($category = $request->category) {
            $query->where('arxiv_category', 'like', "{$category}%");
        }

        $papers = $query->orderByDesc('arxiv_published_date')->paginate(20)->withQueryString();

        $categories = ConocIaPaper::selectRaw('SUBSTRING_INDEX(arxiv_category, ".", 1) as cat')
            ->distinct()->pluck('cat');

        return view('admin.papers.index', compact('papers', 'categories'));
    }

    public function edit(ConocIaPaper $paper)
    {
        return view('admin.papers.edit', compact('paper'));
    }

    public function update(Request $request, ConocIaPaper $paper)
    {
        $validated = $request->validate([
            'title'                  => 'required|max:255',
            'excerpt'                => 'nullable|string',
            'content'                => 'nullable|string',
            'difficulty_level'       => 'nullable|in:básico,intermedio,avanzado',
            'featured'               => 'boolean',
            'status'                 => 'required|in:draft,published',
            'key_contributions_raw'  => 'nullable|string',
            'practical_implications_raw' => 'nullable|string',
        ]);

        $paper->title   = $validated['title'];
        $paper->excerpt = $validated['excerpt'] ?? $paper->excerpt;
        $paper->content = $validated['content'] ?? $paper->content;
        $paper->difficulty_level = $validated['difficulty_level'] ?? $paper->difficulty_level;
        $paper->featured = $request->boolean('featured');
        $paper->status   = $validated['status'];

        if (!empty($validated['key_contributions_raw'])) {
            $paper->key_contributions = array_values(array_filter(
                array_map('trim', explode("\n", $validated['key_contributions_raw']))
            ));
        }

        if (!empty($validated['practical_implications_raw'])) {
            $paper->practical_implications = array_values(array_filter(
                array_map('trim', explode("\n", $validated['practical_implications_raw']))
            ));
        }

        if ($paper->status === 'published' && !$paper->published_at) {
            $paper->published_at = now();
        }

        if (!$paper->slug) {
            $paper->slug = Str::slug($paper->title);
        }

        $paper->save();

        return redirect()->route('admin.papers.index')
            ->with('success', "Paper \"{$paper->title}\" actualizado.");
    }

    public function destroy(ConocIaPaper $paper)
    {
        $paper->delete();
        return redirect()->route('admin.papers.index')
            ->with('success', 'Paper eliminado.');
    }

    public function toggleStatus(ConocIaPaper $paper)
    {
        $paper->status = $paper->status === 'published' ? 'draft' : 'published';
        if ($paper->status === 'published' && !$paper->published_at) {
            $paper->published_at = now();
        }
        $paper->save();

        return back()->with('success', "Estado cambiado a {$paper->status}.");
    }

    public function runFetch(Request $request)
    {
        $maxResults = (int) ($request->max_results ?? 2);
        $days       = (int) ($request->days ?? 4);

        Artisan::queue("papers:fetch-arxiv --max-results={$maxResults} --days={$days}");

        return back()->with('success', "Comando papers:fetch-arxiv lanzado (max {$maxResults} por categoría, últimos {$days} días). Los papers aparecerán en unos minutos.");
    }
}
