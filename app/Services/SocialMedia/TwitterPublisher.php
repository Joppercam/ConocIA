<?php

namespace App\Services\SocialMedia;

use App\Models\News;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TwitterPublisher implements SocialMediaPublisher
{
    protected $connection;
    protected $configured = false;
    protected $maxTextLength = 280;
    protected $monthlyLimit = 100; // Límite de publicaciones mensuales
    
    public function __construct()
    {
        $this->initialize();
    }
    
    protected function initialize()
    {
        $consumerKey = config('services.twitter.consumer_key');
        $consumerSecret = config('services.twitter.consumer_secret');
        $accessToken = config('services.twitter.access_token');
        $accessTokenSecret = config('services.twitter.access_token_secret');
        
        if (!empty($consumerKey) && !empty($consumerSecret) && 
            !empty($accessToken) && !empty($accessTokenSecret)) {
            
            try {
                $this->connection = new TwitterOAuth(
                    $consumerKey,
                    $consumerSecret,
                    $accessToken,
                    $accessTokenSecret
                );
                
                // Específicamente usar API v1.1 para la publicación
                $this->connection->setApiVersion('1.1');
                $this->configured = true;
            } catch (\Exception $e) {
                Log::error('Error al inicializar Twitter API: ' . $e->getMessage());
                $this->configured = false;
            }
        } else {
            Log::warning('Twitter API no está configurada correctamente.');
            $this->configured = false;
        }
    }
    
    public function isConfigured(): bool
    {
        return $this->configured;
    }
    
    /**
     * Verifica si hemos excedido nuestro límite mensual de publicaciones
     * 
     * @return bool True si podemos publicar, False si llegamos al límite
     */
    public function canPublish(): bool
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $usedCount = $this->getMonthlyUsageCount();
        
        return $usedCount < $this->monthlyLimit;
    }
    
    /**
     * Obtiene la cantidad de publicaciones realizadas en el mes actual
     * 
     * @return int Número de publicaciones realizadas este mes
     */
    public function getMonthlyUsageCount(): int
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $cacheKey = "twitter_posts_count_{$currentMonth}";
        
        // Intentar obtener del caché primero
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Si no está en caché, contar desde la base de datos
        $count = DB::table('social_media_posts')
            ->where('network', 'twitter')
            ->where('status', 'success')
            ->whereYear('published_at', Carbon::now()->year)
            ->whereMonth('published_at', Carbon::now()->month)
            ->count();
        
        // Guardar en caché por una hora
        Cache::put($cacheKey, $count, 60 * 60);
        
        return $count;
    }
    
    /**
     * Devuelve el número de publicaciones restantes para este mes
     * 
     * @return int Número de publicaciones restantes
     */
    public function getRemainingPosts(): int
    {
        $used = $this->getMonthlyUsageCount();
        return max(0, $this->monthlyLimit - $used);
    }
    
    /**
     * Incrementar el contador de publicaciones después de cada publicación exitosa
     */
    protected function incrementUsageCount(): void
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $cacheKey = "twitter_posts_count_{$currentMonth}";
        
        $currentCount = $this->getMonthlyUsageCount();
        Cache::put($cacheKey, $currentCount + 1, 60 * 60);
    }
    
    public function publish(News $article): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'post_id' => null,
                'post_url' => null,
                'message' => 'Twitter API no está configurada.'
            ];
        }
        
        // Verificar si tenemos disponibilidad de publicaciones este mes
        if (!$this->canPublish()) {
            Log::warning("Límite mensual de publicaciones en Twitter alcanzado. No se publicará la noticia ID {$article->id}");
            return [
                'success' => false,
                'post_id' => null,
                'post_url' => null,
                'message' => 'Límite mensual de publicaciones alcanzado (100/100).'
            ];
        }
        
        try {
            $content = $this->formatContent($article);
            $mediaIds = [];
            
            // Registrar el contenido para depuración
            Log::info('Contenido formateado para Twitter:', [
                'text' => $content['text'],
                'media' => $content['media']
            ]);
            
            // Subir imágenes si existen
            if (!empty($content['media'])) {
                foreach ($content['media'] as $mediaPath) {
                    $mediaData = $this->uploadMedia($mediaPath);
                    if ($mediaData && isset($mediaData->media_id_string)) {
                        $mediaIds[] = $mediaData->media_id_string;
                        Log::info('Imagen subida a Twitter:', ['media_id' => $mediaData->media_id_string]);
                    }
                }
            }
            
            // Usar la sintaxis de API v1.1 para publicar
            $parameters = [
                'status' => $content['text'] // En v1.1 es 'status', no 'text'
            ];
            
            if (!empty($mediaIds)) {
                $parameters['media_ids'] = implode(',', $mediaIds); // En v1.1 es una cadena separada por comas
            }
            
            // Registrar los parámetros para depuración
            Log::info('Parámetros para publicación en Twitter:', $parameters);
            
            // Usar el endpoint de API v1.1 para publicar tweets
            $response = $this->connection->post('statuses/update', $parameters);
            
            // Registrar la respuesta completa para depuración
            Log::info('Respuesta de Twitter API:', ['response' => json_encode($response)]);
            
            if (isset($response->id_str) || isset($response->id)) {
                $tweetId = isset($response->id_str) ? $response->id_str : $response->id;
                $tweetUrl = "https://twitter.com/i/web/status/{$tweetId}";
                
                // Incrementar el contador de uso
                $this->incrementUsageCount();
                
                return [
                    'success' => true,
                    'post_id' => $tweetId,
                    'post_url' => $tweetUrl,
                    'message' => null
                ];
            } else {
                // Obtener más información del error
                $errorDetail = '';
                
                if (isset($response->errors) && is_array($response->errors)) {
                    foreach ($response->errors as $error) {
                        $errorDetail .= isset($error->message) ? $error->message . ' ' : '';
                        $errorDetail .= isset($error->code) ? '(Código: ' . $error->code . ') ' : '';
                    }
                }
                else if (isset($response->detail)) {
                    $errorDetail = $response->detail;
                    if (isset($response->type)) {
                        $errorDetail .= ' (Tipo: ' . $response->type . ')';
                    }
                }
                
                $errorMessage = !empty($errorDetail) ? $errorDetail : 'Error desconocido al publicar en Twitter';
                Log::error('Error detallado de Twitter:', ['error' => $errorMessage, 'response' => $response]);
                
                return [
                    'success' => false,
                    'post_id' => null,
                    'post_url' => null,
                    'message' => $errorMessage
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error al publicar en Twitter: ' . $e->getMessage(), [
                'news_id' => $article->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'post_id' => null,
                'post_url' => null,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function formatContent(News $article): array
    {
        // Preparar el texto del tweet
        $title = $article->title;
        $url = route('news.show', $article->slug);
        
        // Acortar título si es necesario para asegurar que quepa el texto completo
        $maxTitleLength = $this->maxTextLength - (strlen($url) + 5); // 5 caracteres para espacio y hashtags
        
        if (strlen($title) > $maxTitleLength) {
            $title = Str::limit($title, $maxTitleLength - 3); // -3 para los puntos suspensivos
        }
        
        // Construir texto del tweet
        $text = $title . "\n" . $url;
        
        // Añadir hashtags relevantes basados en tags
        if ($article->tags && $article->tags->isNotEmpty()) {
            $hashtags = $article->tags->map(function ($tag) {
                return '#' . Str::camel($tag->name);
            })->take(2)->implode(' ');
            
            if (strlen($text . ' ' . $hashtags) <= $this->maxTextLength) {
                $text .= ' ' . $hashtags;
            }
        } 
        // Si no hay tags, usar la categoría
        else if ($article->category) {
            $hashtag = '#' . Str::camel($article->category->name);
            
            if (strlen($text . ' ' . $hashtag) <= $this->maxTextLength) {
                $text .= ' ' . $hashtag;
            }
        }
        
        // Preparar imágenes
        $media = [];
        if ($article->image) {
            $media[] = $this->getImagePath($article->image);
        }
        
        return [
            'text' => $text,
            'media' => $media,
            'link' => null // No necesitamos un link separado ya que está incluido en el texto
        ];
    }
    
    /**
     * Obtiene las estadísticas de uso actual
     * 
     * @return array Información sobre el uso y límites
     */
    public function getUsageStats(): array
    {
        $currentUsage = $this->getMonthlyUsageCount();
        $remaining = $this->getRemainingPosts();
        $percentageUsed = ($currentUsage / $this->monthlyLimit) * 100;
        
        $currentMonth = Carbon::now()->format('F Y');
        $nextReset = Carbon::now()->endOfMonth()->format('Y-m-d');
        
        return [
            'current_month' => $currentMonth,
            'posts_used' => $currentUsage,
            'posts_limit' => $this->monthlyLimit,
            'posts_remaining' => $remaining,
            'percentage_used' => round($percentageUsed, 2),
            'next_reset_date' => $nextReset,
            'can_publish' => $this->canPublish(),
        ];
    }
    
    protected function uploadMedia($mediaPath)
    {
        try {
            if (filter_var($mediaPath, FILTER_VALIDATE_URL)) {
                // Si es una URL, descargar temporalmente la imagen
                $tempFile = tempnam(sys_get_temp_dir(), 'twitter_');
                file_put_contents($tempFile, file_get_contents($mediaPath));
                $mediaFile = $tempFile;
            } else if (Storage::exists($mediaPath)) {
                // Si es una ruta en el storage
                $mediaFile = Storage::path($mediaPath);
            } else {
                // Si es una ruta absoluta en el servidor
                $mediaFile = $mediaPath;
            }
            
            // Verificar que el archivo existe
            if (!file_exists($mediaFile)) {
                Log::error("No se encontró el archivo de imagen: {$mediaPath}");
                return null;
            }
            
            // Nota: La API v2 no tiene un endpoint directo para subir medios
            // Se sigue usando el endpoint de la v1.1 para esto
            $media = $this->connection->upload('media/upload', ['media' => $mediaFile]);
            
            // Limpiar archivo temporal si se creó
            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }
            
            return $media;
        } catch (\Exception $e) {
            Log::error("Error al subir imagen a Twitter: " . $e->getMessage(), [
                'mediaPath' => $mediaPath,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Limpiar archivo temporal si se creó
            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }
            
            return null;
        }
    }
    
    protected function getImagePath($imagePath)
    {
        // Si es una URL completa, usarla directamente
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }
        
        // Si es una ruta relativa en el storage
        if (Storage::exists($imagePath)) {
            return Storage::path($imagePath);
        }
        
        // Si es una ruta relativa a public
        $publicPath = public_path($imagePath);
        if (file_exists($publicPath)) {
            return $publicPath;
        }
        
        // No se pudo determinar la ruta
        Log::warning("No se pudo determinar la ruta correcta para la imagen: {$imagePath}");
        return null;
    }


    protected function extractErrorDetails($response): string
    {
        $errorDetail = '';
        
        // Formato de error en API v2
        if (isset($response->errors) && is_array($response->errors)) {
            foreach ($response->errors as $error) {
                $errorDetail .= isset($error->message) ? $error->message . ' ' : '';
                $errorDetail .= isset($error->code) ? '(Código: ' . $error->code . ') ' : '';
            }
        }
        // Formato alternativo de error
        else if (isset($response->detail)) {
            $errorDetail = $response->detail;
            if (isset($response->type)) {
                $errorDetail .= ' (Tipo: ' . $response->type . ')';
            }
        }
        
        return $errorDetail;
    }
}