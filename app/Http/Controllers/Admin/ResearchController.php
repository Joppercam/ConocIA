<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Research;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResearchController extends Controller
{
    /**
     * Mostrar listado de investigaciones
     */
    public function index(Request $request)
    {
        $query = Research::query()->with(['category', 'tags', 'author']);
        
        // Filtrado por búsqueda
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }
        
        // Filtrado por categoría
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Filtrado por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Ordenamiento
        $orderBy = $request->order_by ?? 'created_at';
        $orderDir = $request->order_dir ?? 'desc';
        $query->orderBy($orderBy, $orderDir);
        
        $researches = $query->paginate(15);
        $categories = Category::all();
        
        return view('admin.research.index', compact('researches', 'categories'));
    }

    /**
     * Mostrar formulario para crear investigación
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.research.create', compact('categories', 'tags'));
    }

    /**
     * Almacenar nueva investigación
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:researches,slug',
            'summary' => 'required',
            'content' => 'required',
            'featured_image' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'research_type' => 'required|in:paper,report,analysis,study',
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
        ]);
        
        // Generar slug si no se proporcionó
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // Manejar la imagen destacada
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('research/images', 'public');
            $validated['featured_image'] = $path;
        }
        
        // Manejar archivo PDF
        if ($request->hasFile('pdf_file')) {
            $path = $request->file('pdf_file')->store('research/documents', 'public');
            $validated['pdf_file'] = $path;
        }
        
        // Configurar fecha de publicación
        if ($validated['status'] == 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }
        
        // Añadir autor
        $validated['user_id'] = auth()->id();
        
        // Crear investigación
        $research = Research::create($validated);
        
        // Asociar etiquetas
        if (isset($validated['tags'])) {
            $research->tags()->sync($validated['tags']);
        }
        
        return redirect()->route('admin.research.index')
            ->with('success', 'Investigación creada exitosamente.');
    }

    /**
     * Mostrar detalle de investigación
     */
    public function show(Research $research)
    {
        $research->load(['category', 'tags', 'author', 'comments']);
        return view('admin.research.show', compact('research'));
    }

    /**
     * Mostrar formulario para editar investigación
     */
    public function edit(Research $research)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.research.edit', compact('research', 'categories', 'tags'));
    }

    /**
     * Actualizar investigación
     */
    public function update(Request $request, Research $research)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:researches,slug,' . $research->id,
            'summary' => 'required',
            'content' => 'required',
            'featured_image' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'research_type' => 'required|in:paper,report,analysis,study',
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
        ]);
        
        // Generar slug si no se proporcionó
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // Manejar la imagen destacada
        if ($request->hasFile('featured_image')) {
            // Eliminar imagen anterior si existe
            if ($research->featured_image) {
                Storage::disk('public')->delete($research->featured_image);
            }
            
            $path = $request->file('featured_image')->store('research/images', 'public');
            $validated['featured_image'] = $path;
        }
        
        // Manejar archivo PDF
        if ($request->hasFile('pdf_file')) {
            // Eliminar PDF anterior si existe
            if ($research->pdf_file) {
                Storage::disk('public')->delete($research->pdf_file);
            }
            
            $path = $request->file('pdf_file')->store('research/documents', 'public');
            $validated['pdf_file'] = $path;
        }
        
        // Actualizar investigación
        $research->update($validated);
        
        // Asociar etiquetas
        if (isset($validated['tags'])) {
            $research->tags()->sync($validated['tags']);
        }
        
        return redirect()->route('admin.research.index')
            ->with('success', 'Investigación actualizada exitosamente.');
    }

    /**
     * Eliminar investigación
     */
    public function destroy(Research $research)
    {
        // Eliminar archivos asociados
        if ($research->featured_image) {
            Storage::disk('public')->delete($research->featured_image);
        }
        
        if ($research->pdf_file) {
            Storage::disk('public')->delete($research->pdf_file);
        }
        
        // Eliminar investigación
        $research->delete();
        
        return redirect()->route('admin.research.index')
            ->with('success', 'Investigación eliminada exitosamente.');
    }
    
    /**
     * Ver investigaciones pendientes de aprobación
     */
    public function pendingPosts()
    {
        $pendingPosts = Research::where('status', 'pending')
            ->with(['author', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.research.pending', compact('pendingPosts'));
    }
    
    /**
     * Aprobar una investigación
     */
    public function approvePost($id)
    {
        $research = Research::findOrFail($id);
        $research->status = 'published';
        $research->published_at = now();
        $research->save();
        
        // Opcionalmente, enviar notificación al autor
        
        return redirect()->route('admin.invitados.pending')
            ->with('success', 'Investigación aprobada y publicada.');
    }
    
    /**
     * Rechazar una investigación
     */
    public function rejectPost(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string'
        ]);
        
        $research = Research::findOrFail($id);
        $research->status = 'rejected';
        $research->rejection_reason = $validated['rejection_reason'];
        $research->save();
        
        // Opcionalmente, enviar notificación al autor
        
        return redirect()->route('admin.invitados.pending')
            ->with('success', 'Investigación rechazada.');
    }
    
    /**
     * Exportar investigaciones a CSV
     */
    public function export()
    {
        $fileName = 'researches_' . date('Y-m-d') . '.csv';
        
        $researches = Research::with(['category', 'tags', 'author'])
            ->select('id', 'title', 'slug', 'summary', 'content', 'category_id', 'user_id', 'status', 'published_at', 'created_at', 'research_type')
            ->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];
        
        $callback = function() use ($researches) {
            $file = fopen('php://output', 'w');
            
            // Encabezados
            fputcsv($file, ['ID', 'Título', 'Slug', 'Resumen', 'Categoría', 'Autor', 'Estado', 'Tipo', 'Fecha de Publicación', 'Fecha de Creación', 'Etiquetas']);
            
            foreach ($researches as $research) {
                $row = [
                    $research->id,
                    $research->title,
                    $research->slug,
                    $research->summary,
                    $research->category->name ?? 'Sin categoría',
                    $research->author->name ?? 'Sin autor',
                    $research->status,
                    $research->research_type,
                    $research->published_at,
                    $research->created_at,
                    $research->tags->pluck('name')->implode(', ')
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}