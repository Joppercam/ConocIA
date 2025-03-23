<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Research;
use App\Models\GuestPost;
use App\Models\Category;
use App\Models\Column;
use Illuminate\Support\Str;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
   /**
     * Muestra la página de inicio
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Primero intentar obtener noticias destacadas
        $featuredNews = News::with('category')
        ->where('featured', true)  // Priorizar noticias marcadas como destacadas
        ->latest()
        ->take(5)
        ->get();

        // Si no hay suficientes noticias destacadas (menos de 5), obtener más noticias recientes
        if ($featuredNews->count() < 5) {
        // Obtener IDs de las noticias que ya están incluidas
        $existingIds = $featuredNews->pluck('id')->toArray();

        // Obtener noticias adicionales para completar 5 en total
        $additionalNews = News::with('category')
                ->where('featured', false)
                ->whereNotIn('id', $existingIds)
                ->latest()
                ->take(5 - $featuredNews->count())
                ->get();

        // Combinar ambas colecciones
        $featuredNews = $featuredNews->concat($additionalNews);
        }

        // Obtener más noticias recientes (excluyendo las destacadas)
        $featuredNewsIds = $featuredNews->pluck('id')->toArray();

        // Obtener últimas noticias (para otras secciones)
        $latestNews = News::with('category')
            ->whereNotIn('id', $featuredNewsIds)  // Excluir las que ya están en destacados
            ->latest()
            ->take(28)  // Puedes mantener esto o ajustarlo
            ->get();

        // Tomar 4 para la sección de noticias recientes
        $recentNews = $latestNews->take(6);


        // Cargar noticias populares
        $popularNews = News::with('category')
            ->orderBy('views', 'desc')
            ->take(10)
            ->get();
        
        // Cargar últimas columnas
        try {
            $latestColumns = Column::with('author')
                ->latest()
                ->take(3)
                ->get();
        } catch (\Exception $e) {
            // Si el modelo Column todavía no existe
            $latestColumns = collect([]);
        }
        
        // Cargar artículos secundarios 
        $secondaryNews = News::where('featured', false)
            ->with('category')
            ->latest()
            ->take(2)
            ->get();
            
        // Categorías destacadas
        $featuredCategories = Category::withCount('news')
            ->orderBy('news_count', 'desc')
            ->take(10)
            ->get();
            
        // Artículos de investigación
        $researchArticles = Research::with('category')
        ->where(function($query) {
            $query->where('status', 'published')
                  ->orWhere('status', 'active');
        })
        ->latest()
        ->take(8) // Mantener 8 para mostrar más investigaciones
        ->get();
            
        // Investigaciones destacadas
        $featuredResearch = Research::with('category')
        ->where('featured', true)
        ->where(function($query) {
            $query->where('status', 'published')
                  ->orWhere('status', 'active');
        })
        ->orderBy('citations', 'desc')
        ->take(5)
        ->get();
            
        // Investigaciones más comentadas
        $mostCommented = Research::with('category')
        ->where(function($query) {
            $query->where('status', 'published')
                  ->orWhere('status', 'active');
        })
        ->orderBy('comments_count', 'desc')
        ->take(3)
        ->get();
        
        // Helper function para manejar correctamente las rutas de imágenes
        $getImageUrl = function($imagePath, $type = 'news', $size = 'large') {
            // Ruta para imágenes predeterminadas según el tipo y tamaño
            $defaultImages = [
                'news' => [
                    'large' => 'storage/images/defaults/news-default-large.jpg',
                    'medium' => 'storage/images/defaults/news-default-medium.jpg',
                    'small' => 'storage/images/defaults/news-default-small.jpg',
                ],
                'research' => [
                    'large' => 'storage/images/defaults/research-default-large.jpg',
                    'medium' => 'storage/images/defaults/research-default-medium.jpg',
                    'small' => 'storage/images/defaults/research-default-small.jpg',
                ],
                'profile' => 'storage/images/defaults/user-profile.jpg',
                'avatars' => 'storage/images/defaults/avatar-default.jpg'
            ];
            
            // Si no hay imagen, devolver la imagen predeterminada según el tipo
            if (!$imagePath || $imagePath == '' || $imagePath == 'null') {
                // Verificar si el tipo tiene diferentes tamaños (es un array) o es una imagen única
                if (is_array($defaultImages[$type] ?? [])) {
                    return asset($defaultImages[$type][$size] ?? $defaultImages[$type]['medium']);
                } else {
                    // Si es una cadena (string), devolver directamente
                    return asset($defaultImages[$type] ?? 'storage/images/defaults/default.jpg');
                }
            }
            
            // Si la ruta ya comienza con 'storage/', solo usamos asset()
            if (Str::startsWith($imagePath, 'storage/')) {
                return asset($imagePath);
            }
            
            // De lo contrario, construimos la ruta completa
            return asset('storage/' . $imagePath);
        };
        
        // Función para obtener el estilo de una categoría
        $getCategoryStyle = function($category) {
            if (!$category || !isset($category->color)) {
                return 'background-color: var(--primary-color);';
            }
            
            return 'background-color: ' . $category->color . ';';
        };
        
        // Función para obtener el icono de una categoría
        $getCategoryIcon = function($category) {
            if (!$category || !isset($category->icon)) {
                return 'fa-tag';
            }
            
            return $category->icon;
        };
        
        // Pasar todas las variables y funciones a la vista
            return view('home', compact(
            'featuredNews',
            'recentNews',
            'popularNews',
            'latestColumns',
            'secondaryNews',
            'featuredCategories',
            'researchArticles',
            'featuredResearch',
            'mostCommented'
        ))->with([
            'getImageUrl' => $getImageUrl,
            'getCategoryStyle' => $getCategoryStyle,
            'getCategoryIcon' => $getCategoryIcon
        ]);
    }
    
    /**
     * Mostrar la página "Acerca de".
     */
    public function about()
    {
        return view('about');
    }
    
    /**
     * Mostrar la página de contacto.
     */
    public function contact()
    {
        return view('contact');
    }
    
    /**
     * Procesar el formulario de contacto.
     */
    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        // Aquí iría el código para enviar el email
        // Por ejemplo, usando la fachada Mail:
        /*
        Mail::send('emails.contact', $validated, function($message) use ($validated) {
            $message->to('contacto@conocia.com', 'ConocIA');
            $message->subject('Formulario de contacto: ' . $validated['subject']);
            $message->from($validated['email'], $validated['name']);
        });
        */
        
        return redirect()->back()->with('success', 'Tu mensaje ha sido enviado correctamente. Te responderemos a la brevedad.');
    }
    
    /**
     * Búsqueda en el sitio.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query)) {
            return redirect()->route('home');
        }
        
        // Buscar en noticias
        $news = News::with(['category', 'user'])
                   ->published()
                   ->where(function($q) use ($query) {
                       $q->where('title', 'like', "%{$query}%")
                         ->orWhere('excerpt', 'like', "%{$query}%")
                         ->orWhere('content', 'like', "%{$query}%");
                   })
                   ->latest('published_at')
                   ->paginate(10);
        
        // Buscar en investigaciones
        $researches = Research::with('user')
                             ->published()
                             ->where(function($q) use ($query) {
                                 $q->where('title', 'like', "%{$query}%")
                                   ->orWhere('abstract', 'like', "%{$query}%")
                                   ->orWhere('content', 'like', "%{$query}%");
                             })
                             ->latest('published_at')
                             ->paginate(5);
        
        // Buscar en publicaciones de invitados
        $guestPosts = GuestPost::with(['category', 'user'])
                              ->published()
                              ->where(function($q) use ($query) {
                                  $q->where('title', 'like', "%{$query}%")
                                    ->orWhere('excerpt', 'like', "%{$query}%")
                                    ->orWhere('content', 'like', "%{$query}%");
                              })
                              ->latest('published_at')
                              ->paginate(5);
        
        return view('search', compact('news', 'researches', 'guestPosts', 'query'));
    }
}
