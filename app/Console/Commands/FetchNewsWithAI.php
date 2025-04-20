<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use App\Models\SocialMediaQueue;
use App\Services\SimpleImageDownloader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use OpenAI\Laravel\Facades\OpenAI;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class FetchNewsWithAI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch {--category=} {--count=5} {--language=es} {--queue-social=1} {--generate-comments=1} {--min-comments=3} {--max-comments=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene noticias tecnológicas recientes utilizando IA, las almacena en la base de datos y genera comentarios automáticos';

    /**
     * Categorías de tecnología disponibles (slug => nombre)
     */
    protected $validCategories = [
        // Categorías técnicas
        'inteligencia-artificial' => 'Inteligencia Artificial',
        'machine-learning' => 'Machine Learning',
        'deep-learning' => 'Deep Learning',
        'nlp' => 'NLP',
        'computer-vision' => 'Computer Vision',
        'robotica' => 'Robótica',
        'computacion-cuantica' => 'Computación Cuántica',
        
        // Categorías empresariales
        'openai' => 'OpenAI',
        'google-ai' => 'Google AI',
        'microsoft-ai' => 'Microsoft AI',
        'meta-ai' => 'Meta AI',
        'amazon-ai' => 'Amazon AI',
        'anthropic' => 'Anthropic',
        'startups-de-ia' => 'Startups de IA',
        
        // Categorías de aplicación
        'ia-generativa' => 'IA Generativa',
        'automatizacion' => 'Automatización',
        'ia-en-salud' => 'IA en Salud',
        'ia-en-finanzas' => 'IA en Finanzas',
        'ia-en-educacion' => 'IA en Educación',
        
        // Categorías sociales/impacto
        'etica-de-la-ia' => 'Ética de la IA',
        'regulacion-de-ia' => 'Regulación de IA',
        'impacto-laboral' => 'Impacto Laboral',
        'privacidad-y-seguridad' => 'Privacidad y Seguridad',
        
        // Categorías generales (para compatibilidad)
        'tecnologia' => 'Tecnología',
        'investigacion' => 'Investigación',
        'ciberseguridad' => 'Ciberseguridad',
        'innovacion' => 'Innovación',
        'etica' => 'Ética',
    ];

    /**
     * Términos de búsqueda por categoría
     */
    protected $searchTermsByCategory = [
        'inteligencia-artificial' => 'inteligencia artificial OR IA OR AI OR artificial intelligence',
        'machine-learning' => 'machine learning OR aprendizaje automático OR ML OR algoritmos de aprendizaje',
        'deep-learning' => 'deep learning OR aprendizaje profundo OR redes neuronales OR neural networks',
        'nlp' => 'procesamiento de lenguaje natural OR NLP OR natural language processing OR modelos de lenguaje',
        'computer-vision' => 'computer vision OR visión artificial OR reconocimiento de imágenes OR image recognition',
        'robotica' => 'robótica OR robots OR robotics OR automatización robótica OR robot automation',
        'computacion-cuantica' => 'computación cuántica OR quantum computing OR qubits OR algoritmos cuánticos',
        
        'openai' => 'OpenAI OR GPT-4 OR ChatGPT OR DALL-E OR GPT',
        'google-ai' => 'Google AI OR DeepMind OR Gemini OR Google Bard OR PaLM',
        'microsoft-ai' => 'Microsoft AI OR Copilot OR Microsoft OpenAI OR Azure AI OR Bing Chat',
        'meta-ai' => 'Meta AI OR Facebook AI OR LLaMA OR Meta AI Research OR Galactica',
        'amazon-ai' => 'Amazon AI OR AWS AI OR Alexa AI OR Amazon Bedrock OR SageMaker',
        'anthropic' => 'Anthropic OR Claude OR Claude AI OR Anthropic AI OR Claude Opus',
        'startups-de-ia' => 'startup IA OR AI startup OR nuevas empresas IA OR financiación IA OR AI funding',
        
        'ia-generativa' => 'IA generativa OR generative AI OR contenido IA OR AI content OR creative AI',
        'automatizacion' => 'automatización IA OR AI automation OR procesos automatizados OR flujos de trabajo IA',
        'ia-en-salud' => 'IA salud OR AI healthcare OR IA médica OR medical AI OR diagnóstico IA',
        'ia-en-finanzas' => 'IA finanzas OR AI finance OR fintech IA OR IA trading OR AI banking',
        'ia-en-educacion' => 'IA educación OR AI education OR educación personalizada OR tutores virtuales',
        
        'etica-de-la-ia' => 'ética IA OR AI ethics OR sesgos IA OR AI bias OR IA responsable',
        'regulacion-de-ia' => 'regulación IA OR AI regulation OR legislación IA OR AI Act OR ley IA',
        'impacto-laboral' => 'IA empleo OR AI jobs OR automatización trabajo OR futuro trabajo OR AI workforce',
        'privacidad-y-seguridad' => 'privacidad IA OR AI privacy OR seguridad IA OR datos personales OR AI security',
        
        // Categorías generales (para compatibilidad)
        'tecnologia' => 'tecnología OR tech OR digital OR informática OR computación OR smartphone OR internet',
        'investigacion' => 'investigación tecnológica OR research OR avances científicos OR I+D OR innovación científica OR descubrimiento',
        'ciberseguridad' => 'ciberseguridad OR hacking OR seguridad informática OR hackers OR ciberataques OR ransomware OR vulnerabilidad',
        'innovacion' => 'innovación tecnológica OR startups OR emprendimiento tech OR disruption OR nuevas tecnologías',
        'etica' => 'ética tecnológica OR privacidad datos OR regulación tecnológica OR dilemas éticos OR bioética',
    ];
    
    /**
     * Colores hexadecimales para las categorías
     */
    protected $categoryColors = [
        // Categorías técnicas
        'inteligencia-artificial' => '4285F4', // Azul
        'machine-learning' => '0F9D58', // Verde
        'deep-learning' => 'DB4437', // Rojo
        'nlp' => '673AB7', // Púrpura
        'computer-vision' => 'FF9800', // Naranja
        'robotica' => '795548', // Marrón
        'computacion-cuantica' => '9C27B0', // Violeta
        
        // Categorías empresariales
        'openai' => '412991', // Púrpura oscuro (OpenAI)
        'google-ai' => '4285F4', // Azul (Google)
        'microsoft-ai' => '00A4EF', // Azul (Microsoft)
        'meta-ai' => '1877F2', // Azul (Facebook)
        'amazon-ai' => 'FF9900', // Naranja (Amazon)
        'anthropic' => '5A008E', // Morado (Anthropic)
        'startups-de-ia' => '00BCD4', // Cyan
        
        // Categorías de aplicación
        'ia-generativa' => 'E91E63', // Rosa
        'automatizacion' => '607D8B', // Gris azulado
        'ia-en-salud' => '4CAF50', // Verde
        'ia-en-finanzas' => '009688', // Verde azulado
        'ia-en-educacion' => '3F51B5', // Índigo
        
        // Categorías sociales/impacto
        'etica-de-la-ia' => 'FF5722', // Naranja oscuro
        'regulacion-de-ia' => '2196F3', // Azul
        'impacto-laboral' => 'FFEB3B', // Amarillo
        'privacidad-y-seguridad' => 'F44336', // Rojo
        
        // Categorías generales (para compatibilidad)
        'tecnologia' => '2ecc71',
        'investigacion' => '9b59b6',
        'ciberseguridad' => 'f39c12',
        'innovacion' => '1abc9c',
        'etica' => '8e44ad',
    ];

    /**
     * Servicio de descarga de imágenes
     */
    protected $imageDownloader;

    /**
     * Constructor
     */
    public function __construct(SimpleImageDownloader $imageDownloader)
    {
        parent::__construct();
        $this->imageDownloader = $imageDownloader;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categorySlug = $this->option('category');
        $queueSocial = $this->option('queue-social');
        $generateComments = $this->option('generate-comments');
        $minComments = $this->option('min-comments');
        $maxComments = $this->option('max-comments');
        
        if (empty($categorySlug)) {
            // Si no se especifica categoría, mostrar lista de categorías disponibles
            $this->info("Categorías disponibles:");
            $categoriesByGroup = [
                'Categorías técnicas' => ['inteligencia-artificial', 'machine-learning', 'deep-learning', 'nlp', 'computer-vision', 'robotica', 'computacion-cuantica'],
                'Categorías empresariales' => ['openai', 'google-ai', 'microsoft-ai', 'meta-ai', 'amazon-ai', 'anthropic', 'startups-de-ia'],
                'Categorías de aplicación' => ['ia-generativa', 'automatizacion', 'ia-en-salud', 'ia-en-finanzas', 'ia-en-educacion'],
                'Categorías sociales/impacto' => ['etica-de-la-ia', 'regulacion-de-ia', 'impacto-laboral', 'privacidad-y-seguridad'],
                'Categorías generales' => ['tecnologia', 'investigacion', 'ciberseguridad', 'innovacion', 'etica']
            ];
            
            foreach ($categoriesByGroup as $group => $categoryList) {
                $this->info("\n" . $group . ":");
                foreach ($categoryList as $slug) {
                    $this->line(" - $slug ({$this->validCategories[$slug]})");
                }
            }
            
            return 1;
        }
        
        // Validar la categoría
        if (!array_key_exists($categorySlug, $this->validCategories)) {
            $this->error("Categoría no válida. Usa el comando sin parámetros para ver la lista de categorías.");
            return 1;
        }
        
        $categoryName = $this->validCategories[$categorySlug];
        $count = $this->option('count');
        $language = $this->option('language') ?? 'es';

        $this->info("Obteniendo {$count} noticias de la categoría {$categoryName}...");
        
        // Consultar la API de noticias
        $newsApiKey = config('services.newsapi.key');
        $this->info("Consultando API de noticias...");
        $newsData = $this->fetchNewsFromEverything($newsApiKey, $categorySlug, $count, $language);
        
        if (empty($newsData)) {
            $this->error('No se pudieron obtener noticias de la API.');
            return 1;
        }
        
        $this->info("Se obtuvieron " . count($newsData) . " artículos de noticias de {$categoryName}.");
        
        // Obtener o crear la categoría en la base de datos
        $category = Category::firstOrCreate(
            ['slug' => $categorySlug],
            [
                'name' => $categoryName,
                'description' => "Noticias sobre {$categoryName}",
                'color' => $this->categoryColors[$categorySlug] ?? '2c3e50', // Sin el símbolo # incluido
                'icon' => $this->getCategoryIcon($categorySlug)
            ]
        );
        
        $bar = $this->output->createProgressBar(count($newsData));
        $bar->start();
        
        $savedCount = 0;
        
        // Colección de imágenes para descargar en lote al final
        $imagesToDownload = [];
        
        // Colección de noticias creadas para luego crear las entradas en la cola de redes sociales
        $createdNews = [];
        
        foreach ($newsData as $newsItem) {
            // Utilizamos OpenAI para mejorar y extender el contenido
            $enhancedContent = $this->processNewsWithMultipleStrategies($newsItem, $categoryName);
            
            if (!$enhancedContent) {
                // Si falla la IA, usamos el contenido original
                $this->warn("Usando contenido original para: {$newsItem['title']}");
                $enhancedContent = [
                    'title' => $newsItem['title'],
                    'content' => $newsItem['content'],
                    'excerpt' => Str::limit(strip_tags($newsItem['content']), 150)
                ];
            }
            
            // Guardamos en la base de datos
            try {
                $this->info("Guardando en la base de datos: {$enhancedContent['title']}");
                
                // Inicialmente usamos la imagen predeterminada
                $imageUrl = $this->getDefaultImageForCategory($categorySlug);
                
                // Verificar si existe una noticia con el mismo título
                $slug = Str::slug($enhancedContent['title']);
                $existingNews = News::where('slug', $slug)->first();
                
                if ($existingNews) {
                    $this->warn("Ya existe una noticia con título similar. Omitiendo...");
                    $bar->advance();
                    continue;
                }
                
                // Adaptar los campos según la estructura real de la tabla news
                $readingTime = max(1, ceil(str_word_count(strip_tags($enhancedContent['content'])) / 200));
                
                $news = new News([
                    'title' => $enhancedContent['title'],
                    'slug' => $slug,
                    'content' => $enhancedContent['content'],
                    'excerpt' => $enhancedContent['excerpt'],
                    'image' => $imageUrl,
                    'author' => $newsItem['source'] ?? 'AI News Service',
                    'source' => $newsItem['source'] ?? 'News Service',
                    'source_url' => $newsItem['url'] ?? '',
                    'featured' => true,
                    'status' => 'published',
                    'is_published' => 1,
                    'reading_time' => $readingTime,
                    'views' => rand(50, 500),
                    'published_at' => now(),
                ]);
                
                // Asignar categoría
                $news->category_id = $category->id;
                $news->save();
                
                // Guardar la noticia en la colección para luego crear entradas en la cola
                $createdNews[] = $news;
                
                // Agregar a la lista de imágenes por descargar en lote
                if (!empty($newsItem['image'])) {
                    $imagesToDownload[$newsItem['image']] = [
                        'categorySlug' => $categorySlug,
                        'newsId' => $news->id
                    ];
                }
                
                $this->info("Noticia guardada correctamente.");
                $savedCount++;
            } catch (\Exception $e) {
                $this->error("Error al guardar en la base de datos: " . $e->getMessage());
                
                // Si falla, probamos con método alternativo
                try {
                    $this->info("Intentando método alternativo (inserción directa en DB)...");
                    
                    $readingTime = max(1, ceil(str_word_count(strip_tags($enhancedContent['content'])) / 200));
                    
                    DB::table('news')->insert([
                        'title' => $enhancedContent['title'],
                        'slug' => $slug,
                        'content' => $enhancedContent['content'],
                        'excerpt' => $enhancedContent['excerpt'],
                        'image' => $imageUrl,
                        'author' => $newsItem['source'] ?? 'AI News Service',
                        'source' => $newsItem['source'] ?? 'News Service',
                        'source_url' => $newsItem['url'] ?? '',
                        'category_id' => $category->id,
                        'featured' => true,
                        'status' => 'published',
                        'is_published' => 1,
                        'reading_time' => $readingTime,
                        'views' => rand(50, 500),
                        'published_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Obtenemos el ID de la noticia recién insertada
                    $newsId = DB::getPdo()->lastInsertId();
                    
                    // Crear objeto News para nuestra colección con los datos recién insertados
                    $insertedNews = News::find($newsId);
                    if ($insertedNews) {
                        $createdNews[] = $insertedNews;
                    }
                    
                    // Agregar a la lista de imágenes por descargar en lote
                    if (!empty($newsItem['image'])) {
                        $imagesToDownload[$newsItem['image']] = [
                            'categorySlug' => $categorySlug,
                            'newsId' => $newsId
                        ];
                    }
                    
                    $this->info("Noticia guardada correctamente mediante método alternativo.");
                    $savedCount++;
                } catch (\Exception $innerEx) {
                    $this->error("Error también con método alternativo: " . $innerEx->getMessage());
                }
            }
            
            $bar->advance();
            
            // Pequeña pausa para no sobrecargar la API de IA
            sleep(1);
        }
        
        // Después de guardar todas las noticias, intentar descargar las imágenes en lote
        if (!empty($imagesToDownload)) {
            $this->info("\nDescargando " . count($imagesToDownload) . " imágenes en paralelo...");
            
            // Descargar múltiples imágenes en paralelo
            $downloadResults = $this->imageDownloader->downloadMultiple($imagesToDownload);
            
            $successCount = 0;
            $failCount = 0;
            
            // Actualizar las noticias con las rutas de imágenes
            foreach ($downloadResults as $imageUrl => $localPath) {
                if ($localPath) {
                    $newsId = $imagesToDownload[$imageUrl]['newsId'];
                    
                    try {
                        // Actualizar la noticia con la ruta de la imagen
                        News::where('id', $newsId)->update(['image' => $localPath]);
                        $this->info("Imagen actualizada para noticia #$newsId");
                        $successCount++;
                    } catch (\Exception $e) {
                        $this->error("Error al actualizar imagen para noticia #$newsId: " . $e->getMessage());
                        $failCount++;
                    }
                } else {
                    $newsId = $imagesToDownload[$imageUrl]['newsId'];
                    $this->warn("No se pudo descargar imagen para noticia #$newsId");
                    $failCount++;
                }
            }
            
            $this->info("Resumen de descarga de imágenes: $successCount exitosas, $failCount fallidas");
        }

        // NUEVO: Generar comentarios automáticos para las noticias si está activada la opción
        if ($generateComments && count($createdNews) > 0) {
            $this->info("\nGenerando comentarios automáticos para las noticias...");
            try {
                // Verificar si hay usuarios en el sistema
                $usersCount = User::count();
                
                if ($usersCount < 5) {
                    $this->info("Se encontraron menos de 5 usuarios en el sistema. Creando usuarios ficticios para comentarios...");
                    
                    // Determinar cuántos usuarios crear (entre 5 y 10)
                    $usersToCreate = max(5, min(10, 5 - $usersCount));
                    
                    // Crear usuarios ficticios
                    $createdUsers = $this->createFakeUsers($usersToCreate);
                    
                    $this->info("Se crearon {$usersToCreate} usuarios ficticios para asignar a comentarios.");
                } else {
                    $this->info("Ya hay {$usersCount} usuarios en el sistema. No es necesario crear más.");
                }
                
                // Iniciar transacción de base de datos para mejor manejo de errores
                DB::beginTransaction();
                
                // Usar el método mejorado que proporciona mejor debugging y manejo de errores
                $this->generateCommentsForNews($createdNews, $minComments, $maxComments, $categoryName);
                
                // Confirmar transacción
                DB::commit();
                
                $this->info("Transacción de comentarios completada con éxito");
            } catch (\Exception $e) {
                // Revertir transacción en caso de error
                DB::rollBack();
                
                $this->error("Error en la generación de comentarios: " . $e->getMessage());
                Log::error("Error en generación de comentarios: " . $e->getMessage());
                Log::error("Traza: " . $e->getTraceAsString());
            }
        }

        // NUEVO: Crear entradas en la cola de redes sociales si está activada la opción
        if ($queueSocial && count($createdNews) > 0) {
            $this->info("\nCreando entradas en la cola de redes sociales...");
            $this->queueSocialMediaPosts($createdNews, $categoryName);
        }

        $bar->finish();
        $this->newLine();
        $this->info("Noticias obtenidas y guardadas correctamente: {$savedCount} nuevas noticias de {$categoryName}.");
        
        return 0;
    }
    /**
     * Obtiene un icono FontAwesome para la categoría
     */
    private function getCategoryIcon($categorySlug)
    {
        $icons = [
            // Categorías técnicas
            'inteligencia-artificial' => 'fa-brain',
            'machine-learning' => 'fa-cogs',
            'deep-learning' => 'fa-network-wired',
            'nlp' => 'fa-comment-alt',
            'computer-vision' => 'fa-eye',
            'robotica' => 'fa-robot',
            'computacion-cuantica' => 'fa-atom',
            
            // Categorías empresariales
            'openai' => 'fa-cube',
            'google-ai' => 'fa-google',
            'microsoft-ai' => 'fa-microsoft',
            'meta-ai' => 'fa-facebook',
            'amazon-ai' => 'fa-amazon',
            'anthropic' => 'fa-comment',
            'startups-de-ia' => 'fa-rocket',
            
            // Categorías de aplicación
            'ia-generativa' => 'fa-paint-brush',
            'automatizacion' => 'fa-industry',
            'ia-en-salud' => 'fa-heartbeat',
            'ia-en-finanzas' => 'fa-chart-line',
            'ia-en-educacion' => 'fa-graduation-cap',
            
            // Categorías sociales/impacto
            'etica-de-la-ia' => 'fa-balance-scale',
            'regulacion-de-ia' => 'fa-gavel',
            'impacto-laboral' => 'fa-briefcase',
            'privacidad-y-seguridad' => 'fa-shield-alt',
            
            // Categorías generales (para compatibilidad)
            'tecnologia' => 'fa-microchip',
            'investigacion' => 'fa-flask',
            'ciberseguridad' => 'fa-lock',
            'innovacion' => 'fa-lightbulb',
            'etica' => 'fa-balance-scale',
        ];
        
        return $icons[$categorySlug] ?? 'fa-tag';
    }
    
    /**
     * Método para obtener una imagen predeterminada para una categoría
     */
    private function getDefaultImageForCategory($categorySlug)
    {
        // Obtener color para la categoría
        $color = $this->categoryColors[$categorySlug] ?? '2c3e50';
        $text = urlencode(str_replace('-', ' ', $categorySlug));
        
        // Generar URL de placeholder
        $placeholderUrl = "https://via.placeholder.com/1200x630/{$color}/FFFFFF?text={$text}";
        
        // Intentar descargar el placeholder (solo la primera vez)
        $localPath = 'news/' . $categorySlug . '/default.jpg';
        
        if (!Storage::disk('custom_public')->exists($localPath)) {
            $this->info("Descargando imagen predeterminada para {$categorySlug}...");
            
            try {
                $imageContent = @file_get_contents($placeholderUrl);
                if ($imageContent !== false) {
                    // Asegurar que el directorio existe
                    if (!Storage::disk('custom_public')->exists('news/' . $categorySlug)) {
                        Storage::disk('custom_public')->makeDirectory('news/' . $categorySlug, 0755, true);
                    }
                    
                    // Guardar la imagen
                    Storage::disk('custom_public')->put($localPath, $imageContent);
                    return '/storage/' . $localPath;
                }
            } catch (\Exception $e) {
                $this->warn("Error al descargar imagen predeterminada: " . $e->getMessage());
            }
        } else {
            return '/storage/' . $localPath;
        }
        
        // Si todo falla, devolver la URL del placeholder
        return $placeholderUrl;
    }
    

    /**
     * Obtiene noticias del endpoint "everything" de NewsAPI
     */
    private function fetchNewsFromEverything($apiKey, $categorySlug, $count, $language = 'es')
    {
        try {
            // Obtener los términos de búsqueda para la categoría
            $query = $this->searchTermsByCategory[$categorySlug] ?? '';
            
            if (empty($query)) {
                $this->error("No se encontraron términos de búsqueda para la categoría: {$categorySlug}");
                return [];
            }
            
            $this->info("Consultando endpoint 'everything' con parámetros: q=$query, language=$language");
            
            $response = Http::withOptions([
                'verify' => false, // Eliminar esta línea en producción
            ])->get('https://newsapi.org/v2/everything', [
                'apiKey' => $apiKey,
                'q' => $query,
                'language' => $language,
                'sortBy' => 'publishedAt',
                'pageSize' => $count,
            ]);
            
            // Mostrar más información sobre la respuesta para debugging
            $this->info("Código de estado de la API: " . $response->status());
            $responseData = $response->json();
            
            if (!isset($responseData['status'])) {
                $this->error("Respuesta de API inválida: " . json_encode(array_keys($responseData)));
                return [];
            }
            
            if ($response->successful() && $responseData['status'] === 'ok') {
                if (empty($responseData['articles'] ?? [])) {
                    $this->warn("La API devolvió una respuesta exitosa pero sin artículos.");
                    return [];
                }
                
                return collect($responseData['articles'] ?? [])->map(function ($article) {
                    // Obtener el contenido y limpiar truncamiento
                    $content = $article['content'] ?? $article['description'] ?? '';
                    
                    // Detectar y limpiar contenido truncado (patrón "[+X chars]")
                    $content = preg_replace('/\s*\[\+\d+ chars\]$/', '', $content);
                    
                    // Detectar y limpiar otras formas de truncamiento comunes
                    $content = preg_replace('/\.\.\.\s*$/', '', $content);
                    
                    // Eliminar cualquier problema de caracteres extraños al final
                    $content = trim($content);
                    
                    $this->info("Contenido procesado: " . substr($content, 0, 50) . "...");
                    
                    return [
                        'title' => $article['title'] ?? '',
                        'content' => $content,
                        'url' => $article['url'] ?? '',
                        'image' => $article['urlToImage'] ?? null,
                        'source' => $article['source']['name'] ?? 'Unknown',
                        'is_truncated' => (
                            strpos($article['content'] ?? '', '[+') !== false || 
                            strlen($content) < 100
                        ), // Marcar si detectamos truncamiento
                    ];
                })->toArray();
            }
            
            $this->error("Error en endpoint 'everything': " . ($responseData['message'] ?? 'Error desconocido'));
            
            // Si el mensaje de error indica un problema con la API key, lo mostramos claramente
            if (isset($responseData['message']) && strpos($responseData['message'], 'apiKey') !== false) {
                $this->error("Problema con la API key: " . $responseData['message']);
                $this->info("Verifica que tu API key de NewsAPI sea válida y tenga permisos suficientes.");
            }
            
            return [];
            
        } catch (\Exception $e) {
            $this->error("Excepción en endpoint 'everything': " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Utiliza OpenAI para mejorar y extender el contenido de las noticias
     */
    private function enhanceContentWithAI($news, $categoryName)
    {
        try {
            $this->info("Procesando con IA el artículo: {$news['title']}");
            
            // Extraer palabras clave del título y contenido para enriquecer el contexto
            $keywords = $this->extractKeywords($news['title'] . ' ' . $news['content']);
            
            // Determinar el enfoque del prompt basado en la detección de truncamiento
            $isTruncated = $news['is_truncated'] ?? false;
            $contentLength = strlen($news['content']);
            
            $this->info("Contenido: " . ($isTruncated ? "TRUNCADO" : "COMPLETO") . " - Longitud: $contentLength caracteres");
            
            // Preparamos el prompt para la IA específico para noticias de tecnología
            $prompt = "Actúa como un periodista especializado en tecnología y noticias tech, con enfoque en {$categoryName}. A continuación hay un fragmento de una noticia:\n\n";
            $prompt .= "Título: {$news['title']}\n";
            $prompt .= "Contenido: {$news['content']}\n";
            $prompt .= "Palabras clave: " . implode(', ', $keywords) . "\n\n";
            
            // Instrucciones adicionales si detectamos que el contenido está truncado
            if ($isTruncated || $contentLength < 500) {
                $prompt .= "IMPORTANTE: El contenido proporcionado está TRUNCADO o INCOMPLETO. Es tu tarea COMPLETAR la noticia basándote en el título y el fragmento disponible.\n";
                $prompt .= "Debes CREAR Y EXPANDIR el contenido para formar un artículo periodístico completo y coherente, manteniendo la temática y el enfoque del fragmento original.\n";
                $prompt .= "La noticia debe tener al menos 800 palabras y estar estructurada como un artículo profesional completo. NO menciones en el texto que la noticia estaba truncada.\n\n";
            } else {
                $prompt .= "El contenido proporcionado está completo, pero debe ser mejorado y expandido.\n\n";
            }

            $prompt .= "Por favor, reescribe y expande esta noticia en un formato periodístico profesional en español, con un enfoque especializado en {$categoryName}, siguiendo estas instrucciones:\n\n";
            $prompt .= "1. Si la noticia está en inglés, tradúcela al español manteniendo la terminología técnica precisa.\n";
            $prompt .= "2. El artículo debe tener una extensión mínima de " . ($isTruncated ? "800" : "600") . " palabras, estructurado con introducción, desarrollo (dividido en 2-3 subtemas) y conclusión.\n";
            $prompt .= "3. Mantén los hechos principales pero mejora el estilo, añade más contexto técnico y detalles relevantes.\n";
            $prompt .= "4. Incluye subtítulos en formato <h2> para estructurar el contenido.\n";
            $prompt .= "5. Si es relevante, menciona el impacto de esta noticia en el campo de {$categoryName} y su posible evolución futura.\n";
            $prompt .= "6. Utiliza terminología precisa del campo de {$categoryName}.\n";
            $prompt .= "7. " . ($isTruncated ? "Puedes añadir información contextual y detalles plausibles para completar el artículo." : "No inventes hechos que no estén en el contenido original, pero sí puedes añadir contexto relevante sobre la tecnología mencionada.") . "\n";
            $prompt .= "8. Asegúrate de que el excerpt sea atractivo y capture la esencia de la noticia en 2-3 oraciones (máximo 200 caracteres).\n\n";
            
            // IMPORTANTE: Incluir la palabra JSON explícitamente para que funcione el parámetro response_format
            $prompt .= "Devuelve tu respuesta en formato JSON con estas claves:\n";
            $prompt .= "- 'title' (título mejorado en español, atractivo y SEO-friendly)\n";
            $prompt .= "- 'content' (contenido completo en español con formato HTML, bien estructurado con párrafos y subtítulos)\n";
            $prompt .= "- 'excerpt' (resumen atractivo de 2-3 oraciones en español que capture la esencia de la noticia)";
            
            // Configuración directa del cliente HTTP
            $this->info("Configurando cliente OpenAI...");
            $apiKey = config('services.openai.api_key');
            
            try {
                $client = \OpenAI::factory()
                    ->withApiKey($apiKey)
                    ->withHttpClient(new Client(['verify' => false]))
                    ->make();
                
                $this->info("Cliente OpenAI configurado correctamente");
            } catch (\Exception $e) {
                $this->error("Error al configurar cliente OpenAI: " . $e->getMessage());
                throw $e;
            }
            
            $this->info("Enviando solicitud a OpenAI...");
            
            // Usamos siempre GPT-4o para mejor calidad y soporte de JSON
            $modelToUse = 'gpt-4o';
            $temperature = $isTruncated ? 0.8 : 0.7; // Más creatividad si necesitamos completar
            
            $this->info("Usando modelo: $modelToUse con temperatura: $temperature");
            
            // IMPORTANTE: También incluimos la palabra JSON en el mensaje del sistema
            $result = $client->chat()->create([
                'model' => $modelToUse,
                'messages' => [
                    ['role' => 'system', 'content' => "Eres un periodista especializado en tecnología, con enfoque particular en {$categoryName}. Crea contenido de alta calidad bien estructurado y responde siempre en formato JSON según se te indique."],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => $temperature,
                'max_tokens' => 2500, // Asegurar suficiente espacio para respuestas largas
            ]);
            
            $this->info("Respuesta recibida de OpenAI");
            
            $content = $result->choices[0]->message->content;
            $enhancedContent = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->warn("Error al decodificar JSON de la respuesta de IA: " . json_last_error_msg());
                $this->warn("Contenido recibido: " . substr($content, 0, 100) . "...");
                return null;
            }
            
            // Verificar y garantizar que tenemos un excerpt
            if (empty($enhancedContent['excerpt'])) {
                $this->warn("Excerpt vacío en la respuesta de IA, generando uno a partir del contenido...");
                $enhancedContent['excerpt'] = Str::limit(strip_tags($enhancedContent['content']), 200);
            }
            
            // Verificar longitud mínima del contenido
            $contentLength = str_word_count(strip_tags($enhancedContent['content']));
            if ($contentLength < 300) { // Si el contenido es muy corto
                $this->warn("Contenido demasiado corto ($contentLength palabras), solicitando ampliación...");
                
                // Intento de expandir el contenido con una segunda llamada a la API
                $expansionPrompt = "El siguiente es un artículo corto sobre {$categoryName}. Por favor, expándelo significativamente añadiendo más contexto, detalles técnicos, y posibles implicaciones futuras, manteniendo el tono periodístico profesional. Devuelve el resultado como texto plano, no en formato JSON:\n\n" . $enhancedContent['content'];
                
                $expansionResult = $client->chat()->create([
                    'model' => $modelToUse,
                    'messages' => [
                        ['role' => 'system', 'content' => "Expande este artículo sobre {$categoryName} a una longitud de al menos 600 palabras, añadiendo contexto, detalles técnicos y estructura con subtítulos."],
                        ['role' => 'user', 'content' => $expansionPrompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 2500,
                ]);
                
                $expandedContent = $expansionResult->choices[0]->message->content;
                $enhancedContent['content'] = $expandedContent;
                $this->info("Contenido expandido exitosamente");
            }
            
            $this->info("Contenido mejorado con IA correctamente");
            return $enhancedContent;
            
        } catch (\Exception $e) {
            $this->error("Error al procesar con IA: {$news['title']} - " . $e->getMessage());
            return null;
        }
    }

    /**
     * Extrae palabras clave de un texto para mejorar el contexto
     */
    private function extractKeywords($text)
    {
        // Eliminar palabras comunes y extraer términos relevantes
        $commonWords = ['el', 'la', 'los', 'las', 'un', 'una', 'y', 'o', 'de', 'del', 'a', 'en', 'con', 'por', 'para', 'es', 'son', 'fue', 'fueron', 'como', 'pero', 'si', 'no', 'que', 'al', 'ha', 'han', 'se', 'su', 'sus', 'the', 'a', 'an', 'and', 'of', 'to', 'in', 'on', 'for', 'with', 'is', 'are', 'was', 'were'];
        
        // Convertir a minúsculas y eliminar caracteres especiales
        $text = strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        
        // Dividir en palabras y filtrar
        $words = explode(' ', $text);
        $filteredWords = array_filter($words, function($word) use ($commonWords) {
            return strlen($word) > 3 && !in_array($word, $commonWords);
        });
        
        // Contar frecuencia
        $wordCounts = array_count_values($filteredWords);
        
        // Ordenar por frecuencia
        arsort($wordCounts);
        
        // Devolver las 10 palabras más frecuentes
        return array_slice(array_keys($wordCounts), 0, 10);
    }
    /**
     * Crea entradas automáticas en la cola de redes sociales para cada noticia
     * 
     * @param array $newsItems Lista de objetos News para los que crear entradas
     * @param string $categoryName Nombre de la categoría para incluir en los mensajes
     * @return void
     */
    private function queueSocialMediaPosts($newsItems, $categoryName)
    {
        if (empty($newsItems)) {
            $this->warn("No hay noticias para crear entradas en redes sociales.");
            return;
        }
        
        $this->info("Creando entradas en la cola de redes sociales para " . count($newsItems) . " noticias...");
        
        $successCount = 0;
        $failCount = 0;
        
        // Redes sociales disponibles
        //$networks = ['twitter', 'facebook', 'linkedin'];
        $networks = ['twitter'];
        
        foreach ($newsItems as $news) {
            try {
                // Generar un mensaje para cada red social con formato adecuado
                foreach ($networks as $network) {
                    try {
                        // Generar contenido específico según la red social
                        $content = $this->generateSocialContent($news, $network, $categoryName);
                        
                        // Generar la URL para publicación manual (similar a lo que hace la vista)
                        $manualUrl = $this->generateManualUrl($news, $network, $content);
                        
                        // Crear entrada en la tabla de cola de social media
                        $queueItem = new SocialMediaQueue([
                            'news_id' => $news->id,
                            'network' => $network,
                            'content' => $content,
                            'status' => 'pending', // Pendiente de publicación
                            'manual_url' => $manualUrl,
                            'media_paths' => [], // Array vacío para medios
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        $queueItem->save();
                        $this->info("Entrada creada para noticia '{$news->title}' en $network");
                        $successCount++;
                    } catch (\Exception $e) {
                        $this->error("Error al crear entrada para {$news->title} en $network: " . $e->getMessage());
                        $failCount++;
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error general al procesar noticia para redes sociales: " . $e->getMessage());
                $failCount++;
            }
        }
        
        $this->info("Resumen de creación de entradas en la cola: $successCount exitosas, $failCount fallidas");
    }
    
    /**
     * Genera contenido específico para cada red social
     * 
     * @param News $news Noticia para la que generar contenido
     * @param string $network Red social (twitter, facebook, linkedin)
     * @param string $categoryName Nombre de la categoría
     * @return string Contenido formateado para la red social
     */
    private function generateSocialContent($news, $network, $categoryName)
    {
        // URL base del sitio para incluir en los posts
        $siteUrl = config('app.url');
        $newsUrl = "{$siteUrl}news/{$news->slug}";
        
        // Hashtags relacionados con la categoría (máximo 3)
        $hashtag = '#' . Str::camel($categoryName);
        $hashtag = str_replace(' ', '', $hashtag);
        
        // Extraer palabras clave para hashtags adicionales
        $keywords = $this->extractKeywords($news->title);
        $additionalHashtags = [];
        foreach (array_slice($keywords, 0, 2) as $keyword) {
            if (strlen($keyword) > 3) {
                $additionalHashtags[] = '#' . Str::camel($keyword);
            }
        }
        
        $allHashtags = array_merge([$hashtag], $additionalHashtags);
        
        // Formatear el contenido según la red social
        switch ($network) {
            case 'twitter':
                // Twitter tiene un límite de 280 caracteres
                $title = Str::limit($news->title, 100);
                $hashtagString = implode(' ', $allHashtags);
                $content = "📰 {$title}\n\n{$newsUrl}\n\n{$hashtagString}";
                // Asegurar que no exceda el límite de Twitter
                return Str::limit($content, 280);
                
            case 'facebook':
                // Facebook permite contenido más extenso
                $excerpt = Str::limit($news->excerpt, 150);
                $hashtagString = implode(' ', $allHashtags);
                return "📰 {$news->title}\n\n{$excerpt}\n\n👉 Lee más en: {$newsUrl}\n\n{$hashtagString}";
                
            case 'linkedin':
                // LinkedIn es más profesional, usamos el excerpt completo
                $hashtagString = implode(' ', $allHashtags);
                return "📰 {$news->title}\n\n{$news->excerpt}\n\n👉 Artículo completo: {$newsUrl}\n\n{$hashtagString}";
                
            default:
                // Formato genérico para otras redes
                return "{$news->title}\n\n{$newsUrl}";
        }
    }
    
    /**
     * Genera una URL para publicación manual en redes sociales
     * 
     * @param News $news Noticia a publicar
     * @param string $network Red social
     * @param string $content Contenido preparado
     * @return string URL para publicación manual
     */
    private function generateManualUrl($news, $network, $content)
    {
        $siteUrl = config('app.url');
        $newsUrl = "{$siteUrl}/noticias/{$news->slug}";
        $encodedContent = urlencode($content);
        
        switch ($network) {
            case 'twitter':
                return "https://twitter.com/intent/tweet?text={$encodedContent}";
                
            case 'facebook':
                return "https://www.facebook.com/sharer/sharer.php?u={$newsUrl}";
                
            case 'linkedin':
                return "https://www.linkedin.com/shareArticle?mini=true&url={$newsUrl}&title=" . urlencode($news->title);
                
            default:
                return "";
        }
    }
    
    /**
     * Verifica la compatibilidad del modelo User para crear usuarios ficticios
     * 
     * @return array Información sobre la compatibilidad
     */
    private function checkUserModelCompatibility()
    {
        $this->info("Verificando compatibilidad del modelo User...");
        
        try {
            // Verificar si existe la tabla users
            if (!Schema::hasTable('users')) {
                $this->error("La tabla 'users' no existe en la base de datos");
                return ['compatible' => false, 'error' => "La tabla 'users' no existe"];
            }
            
            // Obtener columnas de la tabla users
            $columns = Schema::getColumnListing('users');
            $this->info("Columnas encontradas en tabla 'users': " . implode(", ", $columns));
            
            // Campos requeridos mínimos
            $requiredFields = ['name', 'email', 'password'];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (!in_array($field, $columns)) {
                    $missingFields[] = $field;
                }
            }
            
            if (count($missingFields) > 0) {
                $this->error("Faltan campos requeridos en la tabla 'users': " . implode(", ", $missingFields));
                return ['compatible' => false, 'error' => "Faltan campos requeridos: " . implode(", ", $missingFields)];
            }
            
            // Verificar si existe campo username (algunos sistemas lo usan)
            $hasUsername = in_array('username', $columns);
            
            // Verificar campos opcionales comunes
            $optionalFields = ['bio', 'avatar', 'email_verified_at', 'remember_token'];
            $availableOptionalFields = array_intersect($optionalFields, $columns);
            
            $this->info("La tabla 'users' es compatible para crear usuarios ficticios");
            $this->info("Campo username: " . ($hasUsername ? "Disponible" : "No disponible"));
            $this->info("Campos opcionales disponibles: " . implode(", ", $availableOptionalFields));
            
            return [
                'compatible' => true,
                'hasUsername' => $hasUsername,
                'optionalFields' => $availableOptionalFields
            ];
        } catch (\Exception $e) {
            $this->error("Error al verificar modelo User: " . $e->getMessage());
            return ['compatible' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Crea usuarios ficticios para usar en comentarios
     * 
     * @param int $count Número de usuarios a crear
     * @return array Usuarios creados
     */
    private function createFakeUsers($count = 5)
    {
        $this->info("Creando {$count} usuarios ficticios para comentarios...");
        
        // Verificar compatibilidad del modelo User
        $compatibility = $this->checkUserModelCompatibility();
        
        if (!$compatibility['compatible']) {
            $this->error("No se pueden crear usuarios ficticios: " . ($compatibility['error'] ?? 'Error desconocido'));
            return [];
        }
        
        $createdUsers = [];
        $defaultPassword = bcrypt('password'); // Contraseña genérica para todos los usuarios ficticios
        
        // Nombres para los usuarios ficticios
        $maleNames = ['Carlos', 'Miguel', 'Javier', 'David', 'Alejandro', 'Pablo', 'Diego', 'Sergio', 'Fernando', 'José'];
        $femaleNames = ['Ana', 'María', 'Laura', 'Sofía', 'Carmen', 'Elena', 'Lucía', 'Patricia', 'Isabel', 'Marta'];
        $lastNames = ['García', 'Rodríguez', 'López', 'Martínez', 'González', 'Pérez', 'Sánchez', 'Fernández', 'Ramírez', 'Torres', 
                     'Ruiz', 'Díaz', 'Hernández', 'Álvarez', 'Moreno', 'Muñoz', 'Romero', 'Alonso', 'Gutiérrez', 'Navarro'];
        
        // Dominios para correos
        $domains = ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com', 'protonmail.com'];
        
        // Intereses/ocupaciones para bios
        $interests = ['tecnología', 'programación', 'inteligencia artificial', 'desarrollo web', 'ciberseguridad', 
                     'ciencia de datos', 'blockchain', 'startups', 'innovación', 'cloud computing', 'IoT', 
                     'realidad virtual', 'robótica', 'diseño UX', 'marketing digital'];
        
        $occupations = ['desarrollador', 'ingeniero de software', 'analista de datos', 'diseñador web', 'consultor IT', 
                       'product manager', 'CEO', 'estudiante', 'profesor', 'investigador', 'freelancer'];
        
        for ($i = 0; $i < $count; $i++) {
            try {
                // Determinar género y seleccionar nombre
                $isMale = rand(0, 1) == 1;
                $firstName = $isMale ? $maleNames[array_rand($maleNames)] : $femaleNames[array_rand($femaleNames)];
                $lastName1 = $lastNames[array_rand($lastNames)];
                $lastName2 = $lastNames[array_rand($lastNames)]; // Segundo apellido (estilo español)
                
                $fullName = $firstName . ' ' . $lastName1 . ' ' . $lastName2;
                
                // Crear email
                $emailName = strtolower($firstName . '.' . $lastName1);
                $emailName = preg_replace('/\s+/', '', $emailName); // Eliminar espacios
                $emailName = preg_replace('/[^a-zA-Z0-9.]/', '', $emailName); // Solo alfanuméricos y punto
                $domain = $domains[array_rand($domains)];
                $email = $emailName . rand(1, 999) . '@' . $domain;
                
                // Preparar datos básicos del usuario
                $userData = [
                    'name' => $fullName,
                    'email' => $email,
                    'password' => $defaultPassword,
                    'created_at' => now()->subDays(rand(1, 365)), // Fecha de registro aleatoria en el último año
                    'updated_at' => now(),
                ];
                
                // Añadir username si el modelo lo soporta
                if ($compatibility['hasUsername']) {
                    $username = strtolower($firstName . '.' . $lastName1 . rand(1, 99));
                    $username = preg_replace('/\s+/', '', $username); // Eliminar espacios
                    $username = preg_replace('/[^a-zA-Z0-9.]/', '', $username); // Solo alfanuméricos y punto
                    $userData['username'] = $username;
                }
                
                // Añadir campos opcionales si están disponibles
                if (in_array('email_verified_at', $compatibility['optionalFields'])) {
                    $userData['email_verified_at'] = now();
                }
                
                if (in_array('bio', $compatibility['optionalFields'])) {
                    $interest = $interests[array_rand($interests)];
                    $occupation = $occupations[array_rand($occupations)];
                    $userData['bio'] = "Soy {$occupation} especializado en {$interest}. " . 
                                       "Me apasiona compartir conocimientos y estar al día de las últimas novedades tecnológicas.";
                }
                
                if (in_array('remember_token', $compatibility['optionalFields'])) {
                    $userData['remember_token'] = Str::random(10);
                }
                
                // Crear usuario en la base de datos
                $user = new User($userData);
                $user->save();
                
                $this->info("Usuario creado: {$user->name} ({$user->email})");
                $createdUsers[] = $user;
                
            } catch (\Exception $e) {
                $this->error("Error al crear usuario #{$i}: " . $e->getMessage());
            }
        }
        
        $this->info("Se crearon " . count($createdUsers) . " usuarios ficticios con éxito");
        return $createdUsers;
    }
    
    /**
     * Genera comentarios automáticos para un conjunto de noticias
     * 
     * @param array $newsItems Lista de objetos News para los que crear comentarios
     * @param int $minComments Número mínimo de comentarios a generar por noticia
     * @param int $maxComments Número máximo de comentarios a generar por noticia
     * @param string $categoryName Nombre de la categoría para contextualizar los comentarios
     * @return void
     */
    private function generateCommentsForNews($newsItems, $minComments, $maxComments, $categoryName)
    {
        if (empty($newsItems)) {
            $this->warn("No hay noticias para generar comentarios.");
            return;
        }
        
        $this->info("Generando entre {$minComments} y {$maxComments} comentarios para cada una de las " . count($newsItems) . " noticias...");
        
        $successCount = 0;
        $failCount = 0;
        
        // Obtener algunos usuarios aleatorios para comentarios "reales" (si existen)
        $users = User::inRandomOrder()->limit(10)->get();
        $hasUsers = $users->count() > 0;
        
        // Lista de comentarios fallback por si falla la generación con IA
        $fallbackComments = [
            "Interesante artículo sobre {$categoryName}. Gracias por compartirlo.",
            "Me gustaría ver más sobre este tema en el futuro.",
            "¿Alguien tiene experiencia práctica con esta tecnología?",
            "Estoy de acuerdo con el análisis. Muy acertado.",
            "Creo que hay puntos interesantes, pero me gustaría más profundidad técnica.",
        ];
        
        foreach ($newsItems as $news) {
            // Determinar aleatoriamente cuántos comentarios generar para esta noticia
            $randomCommentsCount = rand($minComments, $maxComments);
            $this->info("Generando {$randomCommentsCount} comentarios para noticia: {$news->title}");
            
            try {
                // Intentar generar comentarios con IA
                $comments = $this->generateCommentsWithAI($news, $randomCommentsCount, $categoryName);
                
                // DEBUG: Ver qué devuelve la función
                $this->info("DEBUG - Tipo de datos devuelto: " . gettype($comments));
                $this->info("DEBUG - Contenido: " . (is_array($comments) ? 'Array con ' . count($comments) . ' elementos' : 'No es un array'));
                
                // Si no obtuvimos comentarios, usar los fallback
                if (empty($comments) || !is_array($comments)) {
                    $this->warn("No se pudieron generar comentarios con IA. Usando comentarios predefinidos.");
                    $comments = $fallbackComments;
                }
                
                foreach ($comments as $index => $commentContent) {
                    if (empty($commentContent)) {
                        $this->warn("Comentario vacío detectado, saltando...");
                        continue;
                    }
                    
                    try {
                        // Verificar que el comentario sea una cadena de texto (string)
                        if (is_array($commentContent)) {
                            // Si recibimos un array (posiblemente un objeto), intentar convertirlo a string
                            if (isset($commentContent['text'])) {
                                $commentText = $commentContent['text'];
                            } elseif (isset($commentContent['content'])) {
                                $commentText = $commentContent['content'];
                            } elseif (isset($commentContent['comment'])) {
                                $commentText = $commentContent['comment'];
                            } else {
                                // Si no encontramos una clave obvia, convertir el array a JSON
                                $commentText = json_encode($commentContent);
                            }
                            
                            $this->warn("Comentario recibido como array, convertido a string: " . substr($commentText, 0, 30) . "...");
                        } else {
                            $commentText = (string)$commentContent;
                        }
                        
                        // Crear el objeto comentario
                        $comment = new Comment();
                        $comment->commentable_type = 'App\\Models\\News'; // Usamos string completa para evitar problemas
                        $comment->commentable_id = $news->id;
                        $comment->content = $commentText;
                        $comment->status = 'approved'; // Automáticamente aprobado
                        
                        // Determinar si este comentario será de usuario o invitado
                        $isUserComment = $hasUsers && rand(0, 10) > 5; // 50% de probabilidad si hay usuarios
                        
                        if ($isUserComment) {
                            // Comentario de usuario autenticado
                            $randomUser = $users->random();
                            $comment->user_id = $randomUser->id;
                        } else {
                            // Comentario de invitado
                            $comment->guest_name = $this->getRandomName();
                            $comment->guest_email = $this->generateFakeEmail($comment->guest_name);
                        }
                        
                        // Si no es el primer comentario, hay posibilidad de que sea respuesta a otro
                        if ($index > 0 && rand(0, 10) > 7) { // 30% de probabilidad de ser respuesta
                            // Buscar un comentario anterior para responder
                            $parentComment = Comment::where('commentable_type', 'App\\Models\\News')
                                ->where('commentable_id', $news->id)
                                ->inRandomOrder()
                                ->first();
                                
                            if ($parentComment) {
                                $comment->parent_id = $parentComment->id;
                            }
                        }
                        
                        // Debugging antes de guardar
                        $this->info("DEBUG - Guardando comentario: " . substr($commentText, 0, 30) . "...");
                        
                        // Guardar el comentario
                        $saved = $comment->save();
                        
                        if ($saved) {
                            // Retroceder la fecha creada para simular más naturalidad
                            $randomMinutes = rand(5, 60 * 24); // Entre 5 minutos y 24 horas
                            $comment->created_at = now()->subMinutes($randomMinutes);
                            $comment->updated_at = $comment->created_at;
                            $comment->save();
                            
                            $this->info("Comentario #{$index} guardado con ID {$comment->id}: " . substr($commentText, 0, 50) . "...");
                            $successCount++;
                        } else {
                            $this->error("No se pudo guardar el comentario (sin excepción)");
                            $failCount++;
                        }
                        
                    } catch (\Exception $e) {
                        $this->error("Error al guardar comentario: " . $e->getMessage());
                        $failCount++;
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error en la generación de comentarios para la noticia: " . $e->getMessage());
                $failCount++;
            }
        }
        
        $this->info("Resumen de generación de comentarios: $successCount exitosos, $failCount fallidos");
    }

    /**
     * Utiliza OpenAI (GPT-4o) para generar comentarios relacionados con una noticia
     */
    private function generateCommentsWithAI($news, $count, $categoryName)
    {
        try {
            $this->info("Generando {$count} comentarios con IA para: {$news->title}");
            
            // Extraer el contenido principal para dar más contexto
            $content = strip_tags($news->content);
            $content = Str::limit($content, 1000); // Limitar a 1000 caracteres para no sobrecargar
            
            // Preparamos el prompt para generar comentarios variados y con contexto
            $prompt = "Genera {$count} comentarios en español para una noticia de tecnología con el siguiente contenido:\n\n";
            $prompt .= "Título: {$news->title}\n";
            $prompt .= "Extracto: {$news->excerpt}\n";
            $prompt .= "Contenido principal: {$content}\n\n";
            $prompt .= "Categoría: {$categoryName}\n\n";
            $prompt .= "Instrucciones para los comentarios:\n";
            $prompt .= "1. Los comentarios deben ser ESPECÍFICOS al contenido del artículo, referenciando detalles o puntos concretos mencionados.\n";
            $prompt .= "2. Deben demostrar conocimiento técnico y expertise en {$categoryName}, como si fueran escritos por profesionales o aficionados informados.\n";
            $prompt .= "3. Variar las opiniones: algunos entusiastas, otros críticos o escépticos, otros analíticos o que hagan preguntas profundas.\n";
            $prompt .= "4. Variar la extensión: algunos breves (1-2 oraciones) y otros más elaborados (3-5 oraciones).\n";
            $prompt .= "5. Algunos pueden incluir comparaciones con otras tecnologías o tendencias relevantes en el campo.\n";
            $prompt .= "6. El lenguaje debe ser natural y conversacional, como el que usaría un experto real en un foro.\n";
            $prompt .= "7. Evitar comentarios genéricos que podrían aplicarse a cualquier artículo.\n\n";
            
            // IMPORTANTE: Incluir la palabra JSON explícitamente
            $prompt .= "Devuelve tu respuesta en formato JSON con un array llamado 'comments' donde cada elemento es un comentario.";
            
            // Configuración del cliente OpenAI
            $apiKey = config('services.openai.api_key');
            
            if (empty($apiKey)) {
                $this->warn("API Key de OpenAI no configurada. Configure OPENAI_API_KEY en .env");
                return [];
            }
            
            // Log para debugging
            $this->info("API Key configurada: " . substr($apiKey, 0, 3) . "..." . substr($apiKey, -3));
            
            try {
                $client = \OpenAI::factory()
                    ->withApiKey($apiKey)
                    ->withHttpClient(new Client(['verify' => false]))
                    ->make();
                
                $this->info("Cliente OpenAI configurado correctamente");
            } catch (\Exception $e) {
                $this->error("Error al configurar cliente OpenAI: " . $e->getMessage());
                throw $e;
            }
            
            $this->info("Enviando solicitud a OpenAI para generar comentarios...");
            
            // Usar el modelo GPT-4o para mejor rendimiento y compatibilidad con JSON
            $modelToUse = 'gpt-4o';
            
            // Configuración para la llamada a ChatGPT
            $result = $client->chat()->create([
                'model' => $modelToUse,
                'messages' => [
                    [
                        'role' => 'system', 
                        'content' => "Eres un generador de comentarios realistas y técnicamente precisos para noticias. Genera respuestas en formato JSON según las instrucciones."
                    ],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.8, // Mayor temperatura para más variación
                'max_tokens' => 1000,
            ]);
            
            $this->info("Respuesta recibida de OpenAI");
            
            // Obtener el contenido de la respuesta
            $content = $result->choices[0]->message->content;
            $this->info("Contenido recibido: " . substr($content, 0, 100) . "...");
            
            // Decodificar el JSON
            $decoded = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("Error al decodificar JSON: " . json_last_error_msg());
                return [];
            }
            
            // Extraer los comentarios del JSON
            if (isset($decoded['comments']) && is_array($decoded['comments'])) {
                return $decoded['comments'];
            }
            
            // Alternativa: si los comentarios están en la raíz del JSON
            if (is_array($decoded) && isset($decoded[0])) {
                return $decoded;
            }
            
            $this->error("No se encontró el array de comentarios en la respuesta");
            return [];
            
        } catch (\Exception $e) {
            $this->error("Error al generar comentarios con IA: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Genera un nombre aleatorio para comentarios de invitados
     * 
     * @return string Nombre aleatorio
     */
    private function getRandomName()
    {
        $firstNames = [
            'Miguel', 'Laura', 'Carlos', 'Ana', 'Javier', 'Sofía', 'Pablo', 
            'Elena', 'Diego', 'Lucía', 'Fernando', 'Marta', 'Sergio', 'Carmen',
            'Daniel', 'Julia', 'Jorge', 'Cristina', 'Ricardo', 'Patricia'
        ];
        
        $lastNames = [
            'García', 'López', 'Martínez', 'Rodríguez', 'González', 'Pérez', 
            'Sánchez', 'Fernández', 'Ruiz', 'Hernández', 'Jiménez', 'Torres',
            'Vega', 'Moreno', 'Castro', 'Silva', 'Ramírez', 'Flores', 'Vargas'
        ];
        
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        
        // 50% de probabilidad de incluir apellido
        if (rand(0, 1) == 1) {
            return $firstName . ' ' . $lastName;
        }
        
        return $firstName;
    }
    
    /**
     * Genera un email falso a partir de un nombre
     * 
     * @param string $name Nombre para generar email
     * @return string Email generado
     */
    private function generateFakeEmail($name) 
    {
        $domains = ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com', 'protonmail.com'];
        $domain = $domains[array_rand($domains)];
        
        // Normalizar nombre para email (quitar espacios, tildes, etc.)
        $emailName = Str::slug(str_replace(' ', '.', $name), '.');
        
        // Añadir número aleatorio al 30% de los emails
        if (rand(0, 10) > 7) {
            $emailName .= rand(1, 999);
        }
        
        return $emailName . '@' . $domain;
    }

    /**
     * Intenta obtener el contenido completo de un artículo mediante web scraping
     * Se utiliza como último recurso cuando detectamos contenido truncado
     * 
     * @param string $url URL del artículo original
     * @return string|null Contenido obtenido o null si falla
     */
    private function attemptContentScrapingFromUrl($url)
    {
        if (empty($url)) {
            $this->warn("URL vacía, no se puede realizar scraping");
            return null;
        }

        $this->info("Intentando obtener contenido completo desde: $url");
        
        try {
            // Configurar opciones para intentar evitar restricciones
            $options = [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3',
                    'Connection' => 'keep-alive',
                    'Upgrade-Insecure-Requests' => '1',
                    'Cache-Control' => 'max-age=0',
                ],
                'timeout' => 10,
                'verify' => false, // Eliminar en producción
            ];
            
            // Realizar la petición HTTP
            $response = Http::withOptions($options)->get($url);
            
            if (!$response->successful()) {
                $this->warn("Error al obtener contenido: HTTP " . $response->status());
                return null;
            }
            
            $html = $response->body();
            
            // Detectar si la respuesta es válida y contiene HTML real
            if (empty($html) || strlen($html) < 1000) {
                $this->warn("Contenido obtenido demasiado corto o vacío");
                return null;
            }
            
            // Intentar extraer contenido principal con heurísticas simples
            // Esto es muy básico y puede necesitar ajustes según las fuentes que se estén procesando
            
            // Eliminar scripts, estilos, comentarios, etc.
            $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
            $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
            $html = preg_replace('/<!--(.*?)-->/s', '', $html);
            
            // Intentar detectar contenido principal
            $contentPatterns = [
                // Patrones comunes para contenido de artículos
                '/<article[^>]*>(.*?)<\/article>/is',
                '/<div[^>]*class=["\'](?:.*?article.*?|.*?content.*?|.*?main.*?)["\'][^>]*>(.*?)<\/div>/is',
                '/<section[^>]*class=["\'](?:.*?article.*?|.*?content.*?|.*?main.*?)["\'][^>]*>(.*?)<\/section>/is',
                '/<div[^>]*id=["\'](?:.*?article.*?|.*?content.*?|.*?main.*?)["\'][^>]*>(.*?)<\/div>/is',
            ];
            
            $extractedContent = null;
            
            foreach ($contentPatterns as $pattern) {
                if (preg_match($pattern, $html, $matches)) {
                    $extractedContent = $matches[1];
                    
                    // Verificar si el contenido extraído tiene una longitud razonable
                    if (strlen(strip_tags($extractedContent)) > 300) {
                        $this->info("Contenido extraído con patrón: " . substr($pattern, 0, 40) . "...");
                        break;
                    } else {
                        $extractedContent = null; // Demasiado corto, seguir buscando
                    }
                }
            }
            
            if (!$extractedContent) {
                // Si no se encontró contenido con los patrones, usar una extracción básica de párrafos
                preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $html, $paragraphs);
                
                if (!empty($paragraphs[0])) {
                    // Tomar solo párrafos que parezcan contenido real (más de 100 caracteres)
                    $validParagraphs = array_filter($paragraphs[0], function($p) {
                        return strlen(strip_tags($p)) > 100;
                    });
                    
                    if (count($validParagraphs) > 2) {
                        $extractedContent = implode("\n", array_slice($validParagraphs, 0, 10)); // Tomar hasta 10 párrafos
                        $this->info("Contenido extraído usando método de párrafos");
                    }
                }
            }
            
            if ($extractedContent) {
                // Limpiar el contenido de etiquetas innecesarias
                $cleanContent = strip_tags($extractedContent, '<p><h1><h2><h3><h4><h5><h6><ul><ol><li><strong><em><b><i>');
                
                // Eliminar espacios en blanco excesivos
                $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
                $cleanContent = trim($cleanContent);
                
                $this->info("Scraping exitoso, contenido obtenido: " . strlen($cleanContent) . " caracteres");
                return $cleanContent;
            }
            
            $this->warn("No se pudo extraer contenido significativo");
            return null;
            
        } catch (\Exception $e) {
            $this->error("Error durante scraping: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Método para procesar una noticia con múltiples estrategias:
     * 1. Si el contenido original está completo, usarlo
     * 2. Si está truncado, intentar scraping
     * 3. Si el scraping falla, usar IA para completar
     */
    private function processNewsWithMultipleStrategies($newsItem, $categoryName)
    {
        // Verificar si el contenido está truncado o es demasiado corto
        $isTruncated = $newsItem['is_truncated'] ?? false;
        $contentLength = strlen($newsItem['content']);
        
        $this->info("Procesando noticia: {$newsItem['title']}");
        $this->info("Estado del contenido: " . ($isTruncated ? "TRUNCADO" : "COMPLETO") . " - Longitud: $contentLength caracteres");
        
        // Si el contenido está truncado y tenemos URL, intentar scraping
        if (($isTruncated || $contentLength < 500) && !empty($newsItem['url'])) {
            $this->info("Contenido truncado detectado, intentando scraping...");
            
            $scrapedContent = $this->attemptContentScrapingFromUrl($newsItem['url']);
            
            if ($scrapedContent && strlen($scrapedContent) > $contentLength) {
                $this->info("Scraping exitoso! Reemplazando contenido truncado por contenido completo");
                
                // Actualizar el item de noticia con el contenido scrapeado
                $newsItem['content'] = $scrapedContent;
                $newsItem['is_truncated'] = false; // Ya no está truncado
                
                // Usar IA para mejorar el contenido scrapeado (que ahora está completo)
                return $this->enhanceContentWithAI($newsItem, $categoryName);
            } else {
                $this->warn("Scraping fallido o contenido obtenido no suficientemente mejor que el original");
            }
        }
        
        // Si llegamos aquí, o bien el contenido no está truncado,
        // o no pudimos obtener mejor contenido mediante scraping.
        // Usamos IA directamente para mejorar/completar el contenido.
        return $this->enhanceContentWithAI($newsItem, $categoryName);
    }

    /**
     * Modificación al método handle para integrar las nuevas funcionalidades
     * Reemplaza la llamada a enhanceContentWithAI con processNewsWithMultipleStrategies
     */
    // En el método handle(), reemplaza:
    // $enhancedContent = $this->enhanceContentWithAI($newsItem, $categoryName);
    // Por:
    // $enhancedContent = $this->processNewsWithMultipleStrategies($newsItem, $categoryName);
}