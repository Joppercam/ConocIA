<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Column;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ColumnController extends Controller
{
    /**
     * Mostrar el listado de columnas
     */
    public function index()
    {
        $columns = Column::with(['author', 'category'])
                    ->latest()
                    ->paginate(15);

        return view('admin.columns.index', compact('columns'));
    }

    /**
     * Mostrar el formulario para crear una columna
     */
    public function create()
    {
        $categories = Category::all();
        $authors = User::all(); // Idealmente filtrarías por roles específicos

        return view('admin.columns.create', compact('categories', 'authors'));
    }

    /**
     * Almacenar una nueva columna
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:columns',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'author_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        // Generar slug si no se proporcionó
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Calcular tiempo de lectura basado en el contenido
        $wordCount = str_word_count(strip_tags($validated['content']));
        $validated['reading_time'] = max(1, ceil($wordCount / 200)); // Aproximadamente 200 palabras por minuto

        // Establecer featured a false si no está presente
        $validated['featured'] = $request->has('featured');

        // Crear la columna
        Column::create($validated);

        return redirect()->route('admin.columns.index')
                ->with('success', 'Columna creada exitosamente');
    }

    /**
     * Mostrar los detalles de una columna
     */
    public function show(Column $column)
    {
        return view('admin.columns.show', compact('column'));
    }

    /**
     * Mostrar el formulario para editar una columna
     */
    public function edit(Column $column)
    {
        $categories = Category::all();
        $authors = User::all(); // Idealmente filtrarías por roles específicos

        return view('admin.columns.edit', compact('column', 'categories', 'authors'));
    }

    /**
     * Actualizar una columna
     */
    public function update(Request $request, Column $column)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:columns,slug,' . $column->id,
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'author_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        // Generar slug si no se proporcionó
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Calcular tiempo de lectura basado en el contenido
        $wordCount = str_word_count(strip_tags($validated['content']));
        $validated['reading_time'] = max(1, ceil($wordCount / 200)); // Aproximadamente 200 palabras por minuto

        // Establecer featured a false si no está presente
        $validated['featured'] = $request->has('featured');

        // Actualizar la columna
        $column->update($validated);

        return redirect()->route('admin.columns.index')
                ->with('success', 'Columna actualizada exitosamente');
    }

    /**
     * Eliminar una columna
     */
    public function destroy(Column $column)
    {
        $column->delete();

        return redirect()->route('admin.columns.index')
                ->with('success', 'Columna eliminada exitosamente');
    }
}