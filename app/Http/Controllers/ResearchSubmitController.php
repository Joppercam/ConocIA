<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Research;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResearchSubmitController extends Controller
{
    /**
     * Constructor del controlador.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra el formulario para subir una investigación.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        
        return view('research.submit', compact('categories', 'tags'));
    }

    /**
     * Almacena una nueva investigación en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string|min:100',
            'content' => 'required|string|min:500',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'image' => 'nullable|image|max:2048',
            'research_type' => 'required|in:paper,case_study,analysis,review',
            'additional_authors' => 'nullable|string',
            'institution' => 'nullable|string|max:255',
            'references' => 'nullable|string',
        ]);
        
        $research = new Research();
        $research->title = $request->title;
        $research->slug = Str::slug($request->title) . '-' . Str::random(5);
        $research->abstract = $request->abstract;
        $research->content = $request->content;
        $research->user_id = Auth::id();
        $research->category_id = $request->category_id;
        $research->research_type = $request->research_type;
        $research->additional_authors = $request->additional_authors;
        $research->institution = $request->institution;
        $research->references = $request->references;
        $research->status = 'pending'; // Las investigaciones requieren aprobación
        
        // Manejar el documento adjunto
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('research-documents', 'public');
            $research->document_path = $documentPath;
        }
        
        // Manejar la imagen destacada
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('research-images', 'public');
            $research->image = $imagePath;
        }
        
        $research->save();
        
        // Asociar tags si se han seleccionado
        if ($request->has('tags')) {
            $research->tags()->attach($request->tags);
        }
        
        return redirect()->route('research.index')
            ->with('success', 'Tu investigación ha sido enviada correctamente y está pendiente de revisión. Te notificaremos cuando sea aprobada.');
    }
}