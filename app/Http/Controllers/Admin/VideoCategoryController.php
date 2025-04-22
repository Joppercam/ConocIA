<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VideoCategoryController extends Controller
{
    public function index()
    {
        $categories = VideoCategory::withCount('videos')
            ->orderBy('name')
            ->paginate(20);
            
        return view('admin.videos.categories.index', compact('categories'));
    }
    
    public function create()
    {
        return view('admin.videos.categories.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:video_categories,name',
            'description' => 'nullable|string'
        ]);
        
        VideoCategory::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'description' => $request->input('description')
        ]);
        
        return redirect()->route('admin.videos.categories.index')
            ->with('success', 'Categoría creada correctamente');
    }
    
    public function edit(VideoCategory $category)
    {
        return view('admin.videos.categories.edit', compact('category'));
    }
    
    public function update(Request $request, VideoCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:video_categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);
        
        $category->update([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'description' => $request->input('description')
        ]);
        
        return redirect()->route('admin.videos.categories.index')
            ->with('success', 'Categoría actualizada correctamente');
    }
    
    public function destroy(VideoCategory $category)
    {
        $category->delete();
        
        return redirect()->route('admin.videos.categories.index')
            ->with('success', 'Categoría eliminada correctamente');
    }
}