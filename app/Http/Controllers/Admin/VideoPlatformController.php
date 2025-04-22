<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoPlatform;
use Illuminate\Http\Request;

class VideoPlatformController extends Controller
{
    public function index()
    {
        $platforms = VideoPlatform::all();
        return view('admin.videos.platforms.index', compact('platforms'));
    }
    
    public function create()
    {
        return view('admin.videos.platforms.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:video_platforms,code',
            'embed_pattern' => 'required|string',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        VideoPlatform::create([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'embed_pattern' => $request->input('embed_pattern'),
            'api_key' => $request->input('api_key'),
            'api_secret' => $request->input('api_secret'),
            'is_active' => $request->has('is_active')
        ]);
        
        return redirect()->route('admin.videos.platforms.index')
            ->with('success', 'Plataforma creada correctamente');
    }
    
    public function edit(VideoPlatform $platform)
    {
        return view('admin.videos.platforms.edit', compact('platform'));
    }
    
    public function update(Request $request, VideoPlatform $platform)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:video_platforms,code,' . $platform->id,
            'embed_pattern' => 'required|string',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        $platform->update([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'embed_pattern' => $request->input('embed_pattern'),
            'api_key' => $request->input('api_key'),
            'api_secret' => $request->input('api_secret'),
            'is_active' => $request->has('is_active')
        ]);
        
        return redirect()->route('admin.videos.platforms.index')
            ->with('success', 'Plataforma actualizada correctamente');
    }
    
    public function destroy(VideoPlatform $platform)
    {
        // Verificar si hay videos asociados
        if ($platform->videos()->count() > 0) {
            return redirect()->route('admin.videos.platforms.index')
                ->with('error', 'No se puede eliminar la plataforma porque tiene videos asociados.');
        }
        
        $platform->delete();
        
        return redirect()->route('admin.videos.platforms.index')
            ->with('success', 'Plataforma eliminada correctamente');
    }
}