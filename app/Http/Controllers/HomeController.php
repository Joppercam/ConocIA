<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Research;
use App\Models\GuestPost;
use App\Models\Category;
use App\Models\Column;
use App\Models\Verification;
use App\Models\Claim;
use App\Models\ClaimCategory;
use Illuminate\Support\Str;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
   /**
     * Muestra la página de inicio
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Cachear solo los datos, no las vistas ni las funciones anónimas
        $viewData = Cache::remember('home_page_data', 1800, function () {
            // Primero intentar obtener noticias destacadas y publicadas
            $allPublishedNews = Cache::remember('all_published_news', 1800, function () {
                return News::with('category')
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
            });

            // Filtrar para obtener solo las que tienen imágenes físicas
            $featuredNews = $this->filterNewsWithPhysicalImages($allPublishedNews, 5);

            // Si no hay suficientes noticias destacadas (menos de 5), obtener más noticias recientes
            if ($featuredNews->count() < 5) {
                // Obtener IDs de las noticias que ya están incluidas
                $existingIds = $featuredNews->pluck('id')->toArray();

                // Obtener noticias adicionales para completar 5 en total
                $additionalNews = Cache::remember('additional_news_'.implode(',', $existingIds), 1800, function () use ($existingIds, $featuredNews) {
                    return News::with('category')
                            ->where('featured', false)
                            ->where('status', 'published')
                            ->whereNotIn('id', $existingIds)
                            ->latest()
                            ->take(5 - $featuredNews->count())
                            ->get();
                });

                // Combinar ambas colecciones
                $featuredNews = $featuredNews->concat($additionalNews);
            }

            // Obtener más noticias recientes (excluyendo las destacadas)
            $featuredNewsIds = $featuredNews->pluck('id')->toArray();

            // Obtener últimas noticias (para otras secciones)
            $latestNews = Cache::remember('latest_news_'.md5(implode(',', $featuredNewsIds)), 1800, function () use ($featuredNewsIds) {
                return News::with('category')
                    ->where('status', 'published')
                    ->whereNotIn('id', $featuredNewsIds)
                    ->latest()
                    ->take(28)
                    ->get();
            });

            // Modificación: Obtener noticias recientes, incluir la cantidad de comentarios
            $recentNews = Cache::remember('recent_news_'.md5(implode(',', $featuredNewsIds)), 1800, function () use ($featuredNewsIds) {
                return News::with(['category', 'author'])
                    ->withCount('comments')
                    ->where('status', 'published')
                    ->whereNotIn('id', $featuredNewsIds)
                    ->latest()
                    ->take(28)
                    ->get();
            });

            // Cargar noticias populares
            $popularNews = Cache::remember('popular_news', 1800, function () {
                return News::with('category')
                    ->where('status', 'published')
                    ->orderBy('views', 'desc')
                    ->take(10)
                    ->get();
            });
            
            // Cargar bloque últimas columnas destacadas - CORREGIDO SIN FILTRO DE STATUS
            try {
                $latestColumns = Cache::remember('latest_columns', 1800, function () {
                    return Column::with('author')
                        ->where('featured', true)
                        ->latest()
                        ->take(2)
                        ->get();
                });
            } catch (\Exception $e) {
                // Si el modelo Column todavía no existe o hay otro error
                $latestColumns = collect([]);
            }
            
            // Cargar seccion últimas columnas destacadas - CORREGIDO SIN FILTRO DE STATUS
            try {
                $latestColumnsSectionFeatured = Cache::remember('latest_columns_section_featured', 1800, function () {
                    return Column::with('author')
                        ->where('featured', true)
                        ->latest()
                        ->take(8)
                        ->get();
                });
            } catch (\Exception $e) {
                // Si el modelo Column todavía no existe
                $latestColumnsSectionFeatured = collect([]);
            }

            // Cargar sección últimas columnas que NO sean destacadas - CORREGIDO 
            // Aumentando el número a tomar para compensar el skip(4) en la vista
            try {
                $latestColumnsSection = Cache::remember('latest_columns_section', 1800, function () {
                    return Column::with('author')
                        ->where('featured', false)
                        ->latest()
                        ->take(9)  // Aumentado de 4 a 9 para compensar el skip(4)
                        ->get();
                });
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
            $secondaryNews = Cache::remember('secondary_news', 1800, function () {
                return News::where('featured', false)
                    ->where('status', 'published')
                    ->with('category')
                    ->latest()
                    ->take(2)
                    ->get();
            });
                
            // Categorías destacadas
            $featuredCategories = Cache::remember('featured_categories', 3600, function () {
                return Category::withCount(['news' => function($query) {
                        $query->where('status', 'published');
                    }])
                    ->orderBy('news_count', 'desc')
                    ->take(10)
                    ->get();
            });
                
            // Artículos de investigación
            $researchArticles = Cache::remember('research_articles', 1800, function () {
                return Research::with('category')
                    ->where(function($query) {
                        $query->where('status', 'published')
                              ->orWhere('status', 'active');
                    })
                    ->latest()
                    ->take(8)
                    ->get();
            });
                
            // Investigaciones destacadas
            $featuredResearch = Cache::remember('featured_research', 1800, function () {
                return Research::with('category')
                    ->where('featured', true)
                    ->where(function($query) {
                        $query->where('status', 'published')
                              ->orWhere('status', 'active');
                    })
                    ->orderBy('citations', 'desc')
                    ->take(5)
                    ->get();
            });
                
            // Investigaciones más comentadas
            $mostCommented = Cache::remember('most_commented_research', 1800, function () {
                return Research::with('category')
                    ->where(function($query) {
                        $query->where('status', 'published')
                              ->orWhere('status', 'active');
                    })
                    ->orderBy('comments_count', 'desc')
                    ->take(3)
                    ->get();
            });
            
            // INICIO: NUEVAS CONSULTAS PARA EL VERIFICADOR AUTÓNOMO - Con verificación de tablas
            $featuredVerifications = collect([]);
            $recentVerifications = collect([]);
            $verificationCategories = collect([]);
            
            // Verificar si existen las tablas necesarias
            $verificationsTableExists = Schema::hasTable('verifications');
            $claimsTableExists = Schema::hasTable('claims');
            $claimCategoriesTableExists = Schema::hasTable('claim_categories');
            
            if ($verificationsTableExists && $claimsTableExists && $claimCategoriesTableExists) {
                try {
                    // Verificaciones destacadas (las más vistas en los últimos 7 días)
                    $featuredVerifications = Cache::remember('home_featured_verifications', 1800, function () {
                        return Verification::with(['claim.categories'])
                            ->orderBy('views_count', 'desc')
                            ->whereBetween('created_at', [now()->subDays(7), now()])
                            ->take(2)
                            ->get()
                            ->each(function ($verification) {
                                $this->addVerdictAttributes($verification);
                            });
                    });
                } catch (\Exception $e) {
                    Log::error("Error al cargar featuredVerifications: " . $e->getMessage());
                    $featuredVerifications = collect([]);
                }
                
                try {
                    // Verificaciones recientes
                    $recentVerifications = Cache::remember('home_recent_verifications', 1800, function () {
                        return Verification::with(['claim.categories'])
                            ->orderBy('created_at', 'desc')
                            ->take(4)
                            ->get()
                            ->each(function ($verification) {
                                $this->addVerdictAttributes($verification);
                            });
                    });
                } catch (\Exception $e) {
                    Log::error("Error al cargar recentVerifications: " . $e->getMessage());
                    $recentVerifications = collect([]);
                }
                
                try {
                    // Categorías de verificaciones
                    $verificationCategories = Cache::remember('home_claim_categories', 3600, function () {
                        return ClaimCategory::withCount(['claims' => function($query) {
                                $query->whereHas('verification');
                            }])
                            ->orderBy('claims_count', 'desc')
                            ->get();
                    });
                } catch (\Exception $e) {
                    Log::error("Error al cargar claim categories: " . $e->getMessage());
                    $verificationCategories = collect([]);
                }
            } else {
                Log::info("Tablas del verificador aún no existen: verificationsTable=" . 
                         ($verificationsTableExists ? 'sí' : 'no') . ", claimsTable=" . 
                         ($claimsTableExists ? 'sí' : 'no') . ", categoriesTable=" . 
                         ($claimCategoriesTableExists ? 'sí' : 'no'));
            }
            
            // FIN: NUEVAS CONSULTAS PARA EL VERIFICADOR AUTÓNOMO
            
            // Devolver solo los datos, no las funciones
            return [
                'featuredNews' => $featuredNews,
                'recentNews' => $recentNews, // Datos sin shuffle (lo aplicaremos después)
                'popularNews' => $popularNews,
                'latestColumns' => $latestColumns,
                'latestColumnsSection' => $latestColumnsSection,
                'latestColumnsSectionFeatured' => $latestColumnsSectionFeatured,
                'secondaryNews' => $secondaryNews,
                'featuredCategories' => $featuredCategories,
                'researchArticles' => $researchArticles,
                'featuredResearch' => $featuredResearch,
                'mostCommented' => $mostCommented,
                // Nuevas variables para el verificador autónomo
                'featuredVerifications' => $featuredVerifications,
                'recentVerifications' => $recentVerifications,
                'categories' => $verificationCategories
            ];
        });
        
        // Extraer los datos de la caché
        extract($viewData);
        
        // Aplicar el shuffle después de extraer los datos de la caché
        // Esto evita problemas de serialización
        $recentNews = $recentNews->shuffle();
        
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
        
        // IMPORTANTE: Asegurar que todas las variables existan, incluso si están vacías
        // Esto evita errores con la función compact()
        if (!isset($featuredVerifications)) $featuredVerifications = collect([]);
        if (!isset($recentVerifications)) $recentVerifications = collect([]);
        if (!isset($categories)) $categories = collect([]);
        
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
            'mostCommented',
            // Nuevas variables para el verificador autónomo
            'featuredVerifications',
            'recentVerifications',
            'categories'
        ))->with([
            'getImageUrl' => $getImageUrl,
            'getCategoryStyle' => $getCategoryStyle,
            'getCategoryIcon' => $getCategoryIcon,
            'getCategoryName' => function($verification) {
                return $this->getCategoryName($verification);
            }
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
        // Filtramos con caché para evitar verificar los archivos físicos repetidamente
        $newsIds = $news->pluck('id')->toArray();
        $cacheKey = 'news_with_physical_images_' . md5(implode(',', $newsIds));
        
        return Cache::remember($cacheKey, 1800, function () use ($news, $minCount) {
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
        });
    }

    /**
     * Añadir atributos relacionados con el veredicto a un modelo de verificación
     * 
     * @param \App\Models\Verification $verification
     * @return \App\Models\Verification
     */
    private function addVerdictAttributes($verification)
    {
        // Añadir clase CSS para el veredicto
        $verification->verdict_class = match($verification->verdict) {
            'true' => 'true',
            'partially_true' => 'partially_true',
            'false' => 'false',
            default => 'unverifiable',
        };
        
        // Añadir etiqueta para el veredicto
        $verification->verdict_label = match($verification->verdict) {
            'true' => 'Verdadero',
            'partially_true' => 'Parcialmente verdadero',
            'false' => 'Falso',
            default => 'No verificable',
        };
        
        return $verification;
    }

    /**
     * Mostrar la página "Acerca de".
     */
    public function about()
    {
        // No cacheamos la vista completa para evitar problemas con las closures
        return view('about');
    }
    
    /**
     * Mostrar la página de contacto.
     */
    public function contact()
    {
        // No cacheamos la vista completa para evitar problemas con las closures
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
        
        // Las búsquedas no se cachean porque son específicas para cada consulta
        
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
        
        // Buscar en verificaciones (NUEVO) - Con verificación de tabla
        $verifications = collect([]);
        if (Schema::hasTable('verifications') && Schema::hasTable('claims')) {
            try {
                $verifications = Verification::with('claim.categories')
                    ->whereHas('claim', function($q) use ($query) {
                        $q->where('statement', 'like', "%{$query}%")
                          ->orWhere('source', 'like', "%{$query}%")
                          ->orWhere('context', 'like', "%{$query}%");
                    })
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhere('analysis', 'like', "%{$query}%")
                    ->latest()
                    ->paginate(5)
                    ->each(function ($verification) {
                        $this->addVerdictAttributes($verification);
                    });
            } catch (\Exception $e) {
                Log::error("Error al buscar verificaciones: " . $e->getMessage());
                $verifications = collect([]);
            }
        }
        
        return view('search', compact('news', 'researches', 'guestPosts', 'verifications', 'query'));
    }

    // En HomeController.php
    private function getCategoryName($verification) {
        if (!$verification || !$verification->claim) {
            return 'Sin categoría';
        }
        
        $categories = $verification->claim->categories;
        
        if (!$categories || $categories->isEmpty()) {
            return 'Sin categoría';
        }
        
        return $categories->first()->name;
    }
}