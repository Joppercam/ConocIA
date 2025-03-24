<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Research;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResearchSubmitController extends Controller
{
    // Eliminar o comentar el constructor que tenía el middleware auth
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

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
        // Reglas de validación base
        $validationRules = [
            'title' => 'required|string|max:255',
            'abstract' => 'required|string|min:100',
            'content' => 'required|string|min:500',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'new_tags' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'image' => 'nullable|image|max:2048',
            'research_type' => 'required|in:paper,case_study,analysis,review',
            'additional_authors' => 'nullable|string',
            'institution' => 'nullable|string|max:255',
            'references' => 'nullable|string',
        ];
        
        // Si el usuario no está autenticado, agregar reglas para información de contacto
        if (!Auth::check()) {
            $validationRules['author_name'] = 'required|string|max:255';
            $validationRules['author_email'] = 'required|email|max:255';
            // Si has instalado el paquete de captcha, descomenta la siguiente línea
            // $validationRules['g-recaptcha-response'] = 'required|captcha'; 
        }
        
        $validated = $request->validate($validationRules);
        
        $research = new Research();
        $research->title = $request->title;
        $research->slug = Str::slug($request->title) . '-' . Str::random(5);
        $research->excerpt = Str::limit(strip_tags($request->abstract), 150);
        $research->abstract = $request->abstract;
        $research->content = $request->content;
        $research->type = $request->research_type;
        $research->research_type = $request->research_type;
        
        // Determinar información del autor basado en si está autenticado o no
        if (Auth::check()) {
            $research->user_id = Auth::id();
            $research->author = Auth::user()->name;
            $research->author_email = Auth::user()->email;
        } else {
            $research->user_id = null; // No hay usuario asociado
            $research->author = $request->author_name;
            $research->author_email = $request->author_email;
        }
        
        $research->category_id = $request->category_id;
        $research->additional_authors = $request->additional_authors;
        $research->institution = $request->institution;
        $research->references = $request->references;
        $research->is_published = false;
        $research->status = 'pending';
        $research->views = 0;
        $research->featured = false;
        
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
        
        // Manejar etiquetas
        $tagIds = [];

        // Procesar etiquetas existentes si se seleccionaron
        if ($request->has('tags') && is_array($request->tags)) {
            $tagIds = $request->tags;
        }

        // Procesar etiquetas nuevas ingresadas como texto
        if ($request->has('new_tags') && !empty($request->new_tags)) {
            $newTagNames = array_map('trim', explode(',', $request->new_tags));
            
            foreach ($newTagNames as $tagName) {
                if (!empty($tagName)) {
                    $tagSlug = Str::slug($tagName);
                    
                    // Buscar si la etiqueta ya existe por su slug
                    $existingTag = DB::table('tags')->where('slug', $tagSlug)->first();
                    
                    if ($existingTag) {
                        // Usar la etiqueta existente
                        $tagIds[] = $existingTag->id;
                    } else {
                        // Crear una nueva etiqueta
                        try {
                            $newTagId = DB::table('tags')->insertGetId([
                                'name' => $tagName,
                                'slug' => $tagSlug,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                            $tagIds[] = $newTagId;
                        } catch (\Exception $e) {
                            // Capturar cualquier error al insertar (como duplicados)
                            Log::warning("No se pudo crear la etiqueta '$tagName': " . $e->getMessage());
                        }
                    }
                }
            }
        }

        // Asociar las etiquetas a la investigación
        if (!empty($tagIds) && isset($research->id)) {
            try {
                // Eliminar cualquier asociación previa (por si acaso)
                DB::table('research_tag')->where('research_id', $research->id)->delete();
                
                // Insertar las nuevas asociaciones
                foreach (array_unique($tagIds) as $tagId) {
                    DB::table('research_tag')->insert([
                        'research_id' => $research->id,
                        'tag_id' => $tagId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            } catch (\Exception $e) {
                // Registrar el error pero permitir que continúe
                Log::error("Error al asociar etiquetas a investigación #{$research->id}: " . $e->getMessage());
            }
        }
        
        // Mensaje dependiendo de si está autenticado o no
        $successMessage = Auth::check() 
            ? 'Tu investigación ha sido enviada correctamente y está pendiente de revisión. Te notificaremos cuando sea aprobada.' 
            : 'Tu investigación ha sido enviada correctamente y está pendiente de revisión. Te notificaremos al correo proporcionado cuando sea aprobada.';
        
        return redirect()->route('research.index')
            ->with('success', $successMessage);
    }
}