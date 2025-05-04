<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Mostrar listado de categorías
     */
    public function index()
    {
        $categories = Category::withCount(['news', 'researches'])
            ->orderBy('is_active','desc')
            ->paginate(15);
            
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Mostrar formulario para crear categoría
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Almacenar una nueva categoría
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'nullable|string|unique:categories,slug',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'search_terms' => 'nullable|string',
        ]);
        
        // Generar slug si no se proporcionó
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Establecer is_active según el checkbox
        $validated['is_active'] = $request->has('is_active');
        
        Category::create($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Mostrar detalle de una categoría
     */
    public function show(Category $category)
    {
        $category->loadCount(['news', 'researches']);
        
        // Cargar algunos artículos relacionados
        $news = $category->news()->latest()->take(5)->get();
        $researches = $category->researches()->latest()->take(5)->get();
        
        return view('admin.categories.show', compact('category', 'news', 'researches'));
    }

    /**
     * Mostrar formulario para editar categoría
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Actualizar una categoría
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'search_terms' => 'nullable|string',
        ]);
        
        // Generar slug si no se proporcionó
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Establecer is_active según el checkbox
        $validated['is_active'] = $request->has('is_active');
        
        $category->update($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Desactivar una categoría (en lugar de eliminarla)
     */
    public function destroy(Category $category)
    {
        $category->update(['is_active' => false]);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría desactivada exitosamente.');
    }

    /**
     * Restaurar una categoría desactivada
     */
    public function restore(Category $category)
    {
        $category->update(['is_active' => true]);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría activada exitosamente.');
    }
}