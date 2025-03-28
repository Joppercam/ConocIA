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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; 

class HomeController extends Controller
{
   /**
     * Muestra la página de inicio
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Primero intentar obtener noticias destacadas y publicadas
        $allPublishedNews = News::with('category')
            ->where('status', 'published')
            ->whereNotNull('image')
            ->where(function($query) {
                $query->where('image', '!=', '')
                      ->where('image', '!=', 'null')
                      ->where('image', '!=', 'default.jpg')
                      ->whereRaw("image NOT LIKE '%default%'")
                      ->whereRaw("image NOT LIKE '%placeholder%'");
            })
            ->latest('published_at')
            ->take(5)
            ->get();


            // Filtrar para obtener solo las que tienen imágenes físicas
        $featuredNews = $this->filterNewsWithPhysicalImages($allPublishedNews, 5);


        //dd($featuredNews);
        // Si no hay suficientes noticias destacadas (menos de 5), obtener más noticias recientes
        if ($featuredNews->count() < 5) {
        // Obtener IDs de las noticias que ya están incluidas
        $existingIds = $featuredNews->pluck('id')->toArray();

        // Obtener noticias adicionales para completar 5 en total
        $additionalNews = News::with('category')
                ->where('featured', false)
                ->where('status', 'published')  // Añadido filtro para estado published
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
            ->where('status', 'published')  // Añadido filtro para estado published
            ->whereNotIn('id', $featuredNewsIds)  // Excluir las que ya están en destacados
            ->latest()
            ->take(28)  // Puedes mantener esto o ajustarlo
            ->get();


         // Modificación: Obtener 20 noticias recientes, incluir la cantidad de comentarios y aplicar orden aleatorio
         $recentNews = News::with(['category', 'author'])
         ->withCount('comments')  // Agregar conteo de comentarios
         ->where('status', 'published')
         ->whereNotIn('id', $featuredNewsIds)
         ->latest()
         ->take(28)
         ->get()
         ->shuffle();  // Orden aleatorio


        // Cargar noticias populares
        $popularNews = News::with('category')
            ->where('status', 'published')  // Añadido filtro para estado published
            ->orderBy('views', 'desc')
            ->take(10)
            ->get();
        
        // Cargar bloque últimas columnas destacadas - CORREGIDO SIN FILTRO DE STATUS
        try {
            $latestColumns = Column::with('author')
                ->where('featured', true)
                ->latest()
                ->take(2)
                ->get();
        } catch (\Exception $e) {
            // Si el modelo Column todavía no existe o hay otro error
            $latestColumns = collect([]);
        }
        
        // Cargar seccion últimas columnas destacadas - CORREGIDO SIN FILTRO DE STATUS
        try {
            $latestColumnsSectionFeatured = Column::with('author')
                ->where('featured', true)
                ->latest()
                ->take(8)
                ->get();
        } catch (\Exception $e) {
            // Si el modelo Column todavía no existe
            $latestColumnsSectionFeatured = collect([]);
        }

        // Cargar sección últimas columnas que NO sean destacadas - CORREGIDO 
        // Aumentando el número a tomar para compensar el skip(4) en la vista
        try {
            $latestColumnsSection = Column::with('author')
                ->where('featured', false)
                ->latest()
                ->take(9)  // Aumentado de 4 a 9 para compensar el skip(4)
                ->get();
        } catch (\Exception $e) {
            // Si el modelo Column todavía no existe
            $latestColumnsSection = collect([]);
        }

        // Debug: Verificar cuántas columnas hay de cada tipo
        $columnsFeaturedCount = $latestColumnsSectionFeatured->count();
        $columnsNonFeaturedCount = $latestColumnsSection->count();
        
        // Log para depuración (opcional)
        Log::info("Columnas destacadas: $columnsFeaturedCount, Columnas no destacadas: $columnsNonFeaturedCount");
                
        // Cargar artículos secundarios 
        $secondaryNews = News::where('featured', false)
            ->where('status', 'published')  // Añadido filtro para estado published
            ->with('category')
            ->latest()
            ->take(2)
            ->get();
            
        // Categorías destacadas
        $featuredCategories = Category::withCount(['news' => function($query) {
                $query->where('status', 'published');  // Contar solo noticias publicadas
            }])
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
            'latestColumnsSection',
            'latestColumnsSectionFeatured',
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
     * Filtra noticias para incluir solo aquellas con imágenes físicamente disponibles
     *
     * @param \Illuminate\Database\Eloquent\Collection $news Colección de noticias
     * @param int $minCount Número mínimo de noticias a retornar
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function filterNewsWithPhysicalImages($news, $minCount = 5)
    {
        // Filtrar noticias con imágenes físicamente existentes
        $newsWithValidImages = $news->filter(function ($item) {
            // Si la imagen no comienza con 'storage/', no es una imagen local
            if (!Str::startsWith($item->image, 'storage/')) {
                return false;
            }
            
            // Obtener la ruta física del archivo sin 'storage/'
            $physicalPath = str_replace('storage/', '', $item->image);
            
            // Verificar si el archivo existe físicamente
            $exists = Storage::disk('public')->exists($physicalPath);
            
            return $exists;
        });
        
        // Si tenemos suficientes noticias con imágenes válidas, devolver esas
        if ($newsWithValidImages->count() >= $minCount) {
            return $newsWithValidImages->take($minCount);
        }
        
        // Si no hay suficientes, complementar con otras noticias
        $additionalCount = $minCount - $newsWithValidImages->count();
        $newsWithoutValidImages = $news->diff($newsWithValidImages);
        
        return $newsWithValidImages->concat($newsWithoutValidImages->take($additionalCount));
    }






    
    // Resto del controlador se mantiene igual...
    
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