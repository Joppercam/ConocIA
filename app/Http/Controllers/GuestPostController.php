<?php

namespace App\Http\Controllers;

use App\Models\GuestPost;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FileUploadService;

class GuestPostController extends Controller
{
    protected $fileUploadService;
    
    /**
     * Constructor con inyección de dependencias.
     */
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }
    
    /**
     * Mostrar lista de publicaciones de invitados.
     */
    public function index()
    {
        $guestPosts = GuestPost::with(['category', 'user', 'tags'])
                              ->published()
                              ->latest('published_at')
                              ->paginate(8);
        
        $categories = Category::where('is_active', true)
                             ->withCount('guestPosts')
                             ->orderBy('guest_posts_count', 'desc')
                             ->get();
        
        $popularTags = Tag::withCount('guestPosts')
                         ->orderBy('guest_posts_count', 'desc')
                         ->take(15)
                         ->get();
        
        return view('guest-posts.index', compact('guestPosts', 'categories', 'popularTags'));
    }
    
    /**
     * Mostrar una publicación específica.
     */
    public function show($slug)
    {
        $guestPost = GuestPost::with(['category', 'user', 'tags', 'comments' => function($query) {
                $query->approved()->parents()->with(['user', 'replies' => function($q) {
                    $q->approved()->with('user');
                }]);
            }])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();
        
        // Incrementar vistas
        $guestPost->increment('views');
        
        // Posts relacionados
        $relatedPosts = GuestPost::with(['category', 'user'])
                                ->published()
                                ->where('id', '!=', $guestPost->id)
                                ->where(function($query) use ($guestPost) {
                                    $query->where('category_id', $guestPost->category_id)
                                          ->orWhereHas('tags', function($q) use ($guestPost) {
                                              $q->whereIn('tags.id', $guestPost->tags->pluck('id'));
                                          });
                                })
                                ->latest('published_at')
                                ->take(3)
                                ->get();
        
        // Categorías para el sidebar
        $categories = Category::where('is_active', true)
                             ->withCount('guestPosts')
                             ->orderBy('guest_posts_count', 'desc')
                             ->get();
        
        // Etiquetas populares para el sidebar
        $popularTags = Tag::withCount('guestPosts')
                         ->orderBy('guest_posts_count', 'desc')
                         ->take(15)
                         ->get();
        
        return view('guest-posts.show', compact('guestPost', 'relatedPosts', 'categories', 'popularTags'));
    }
    
    /**
     * Mostrar formulario para crear una nueva publicación.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        
        return view('guest-posts.create', compact('categories', 'tags'));
    }
    
    /**
     * Almacenar una nueva publicación.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|min:3|max:255',
            'excerpt' => 'required|min:10|max:500',
            'content' => 'required|min:50',
            'featured_image' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'author_bio' => 'required|min:10|max:500',
            'author_website' => 'nullable|url|max:255',
            'author_twitter' => 'nullable|max:255',
            'author_linkedin' => 'nullable|url|max:255',
        ]);
        
        $guestPost = new GuestPost;
        $guestPost->title = $validated['title'];
        $guestPost->slug = Str::slug($validated['title']) . '-' . time();
        $guestPost->excerpt = $validated['excerpt'];
        $guestPost->content = $validated['content'];
        $guestPost->category_id = $validated['category_id'];
        $guestPost->user_id = auth()->id();
        $guestPost->status = 'pending';
        $guestPost->author_bio = $validated['author_bio'];
        $guestPost->author_website = $validated['author_website'] ?? null;
        $guestPost->author_twitter = $validated['author_twitter'] ?? null;
        $guestPost->author_linkedin = $validated['author_linkedin'] ?? null;
        
        // Subir imagen si existe
        if ($request->hasFile('featured_image')) {
            $guestPost->featured_image = $this->fileUploadService->uploadImage(
                $request->file('featured_image'), 
                'guest_posts'
            );
        }
        
        $guestPost->save();
        
        // Asociar tags
        if (isset($validated['tags'])) {
            $guestPost->tags()->sync($validated['tags']);
        }
        
        return redirect()->route('guest-posts.index')
            ->with('success', 'Tu artículo ha sido enviado con éxito. Será revisado por nuestro equipo antes de ser publicado.');
    }
    
    /**
     * Mostrar publicaciones por categoría.
     */
    public function byCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $guestPosts = GuestPost::with(['category', 'user', 'tags'])
                              ->published()
                              ->where('category_id', $category->id)
                              ->latest('published_at')
                              ->paginate(8);
        
        return view('guest-posts.by-category', compact('guestPosts', 'category'));
    }
    
    /**
     * Mostrar publicaciones por etiqueta.
     */
    public function byTag($slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        
        $guestPosts = GuestPost::with(['category', 'user', 'tags'])
                              ->published()
                              ->whereHas('tags', function($query) use ($tag) {
                                  $query->where('tags.id', $tag->id);
                              })
                              ->latest('published_at')
                              ->paginate(8);
        
        return view('guest-posts.by-tag', compact('guestPosts', 'tag'));
    }
    
}