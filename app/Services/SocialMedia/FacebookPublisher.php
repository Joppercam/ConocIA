<?php

namespace App\Services\SocialMedia;

use App\Models\News;
use Facebook\Facebook;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class FacebookPublisher implements SocialMediaPublisher
{
    protected $fb;
    protected $pageId;
    protected $configured = false;
    protected $maxTextLength = 5000; // Facebook permite textos más largos
    
    public function __construct()
    {
        $this->initialize();
    }
    
    protected function initialize()
    {
        $appId = config('services.facebook.app_id');
        $appSecret = config('services.facebook.app_secret');
        $accessToken = config('services.facebook.access_token');
        $this->pageId = config('services.facebook.page_id');
        
        if (!empty($appId) && !empty($appSecret) && !empty($accessToken) && !empty($this->pageId)) {
            try {
                $this->fb = new Facebook([
                    'app_id' => $appId,
                    'app_secret' => $appSecret,
                    'default_graph_version' => 'v16.0',
                    'default_access_token' => $accessToken
                ]);
                $this->configured = true;
            } catch (\Exception $e) {
                Log::error('Error al inicializar Facebook API: ' . $e->getMessage());
            }
        } else {
            Log::warning('Facebook API no está configurada correctamente.');
        }
    }
    
    public function isConfigured(): bool
    {
        return $this->configured;
    }
    
    public function publish(News $article): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'post_id' => null,
                'post_url' => null,
                'message' => 'Facebook API no está configurada.'
            ];
        }
        
        try {
            $content = $this->formatContent($article);
            
            $postData = [
                'message' => $content['text']
            ];
            
            // Si hay un enlace, lo añadimos
            if (!empty($content['link'])) {
                $postData['link'] = $content['link'];
            }
            
            // Si hay imágenes, prepararlas
            if (!empty($content['media'])) {
                // Para Facebook, podemos publicar una imagen con un enlace
                // o múltiples imágenes sin enlace
                if (count($content['media']) == 1 && !empty($content['link'])) {
                    // Una imagen con enlace
                    $postData['source'] = $this->fb->fileToUpload($content['media'][0]);
                } else {
                    // Múltiples imágenes sin enlace (crea un álbum)
                    unset($postData['link']); // Quitar el enlace para permitir múltiples fotos
                    
                    foreach ($content['media'] as $index => $mediaPath) {
                        $postData["source{$index}"] = $this->fb->fileToUpload($mediaPath);
                    }
                }
            }
            
            $response = $this->fb->post('/' . $this->pageId . '/feed', $postData);
            $graphNode = $response->getGraphNode();
            
            if (isset($graphNode['id'])) {
                $postId = $graphNode['id'];
                $postUrl = "https://facebook.com/{$postId}";
                
                return [
                    'success' => true,
                    'post_id' => $postId,
                    'post_url' => $postUrl,
                    'message' => null
                ];
            } else {
                return [
                    'success' => false,
                    'post_id' => null,
                    'post_url' => null,
                    'message' => 'Error desconocido al publicar en Facebook'
                ];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            Log::error('Error de respuesta de Facebook: ' . $e->getMessage(), [
                'news_id' => $article->id,
                'code' => $e->getCode()
            ]);
            
            return [
                'success' => false,
                'post_id' => null,
                'post_url' => null,
                'message' => 'Error de API: ' . $e->getMessage()
            ];
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            Log::error('Error del SDK de Facebook: ' . $e->getMessage(), [
                'news_id' => $article->id
            ]);
            
            return [
                'success' => false,
                'post_id' => null,
                'post_url' => null,
                'message' => 'Error de SDK: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('Error general al publicar en Facebook: ' . $e->getMessage(), [
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
        // Para Facebook podemos incluir más texto y detalles
        $title = $article->title;
        $excerpt = $article->excerpt ?? Str::limit(strip_tags($article->content), 200);
        $url = route('news.show', $article->slug);
        
        // Construir texto del post
        $text = "{$title}\n\n{$excerpt}\n\nLee más: {$url}";
        
        // Añadir hashtags relevantes basados en categorías o tags
        if ($article->categories->isNotEmpty()) {
            $hashtags = $article->categories->map(function ($category) {
                return '#' . Str::camel($category->name);
            })->implode(' ');
            
            if (strlen($text . "\n\n" . $hashtags) <= $this->maxTextLength) {
                $text .= "\n\n" . $hashtags;
            }
        }
        
        // Preparar imágenes
        $media = [];
        if ($article->featured_image) {
            $media[] = $this->getImagePath($article->featured_image);
        }
        
        // En Facebook es mejor incluir el enlace explícitamente para que genere la vista previa
        return [
            'text' => $text,
            'media' => $media,
            'link' => $url
        ];
    }
    
    protected function getImagePath($imagePath)
    {
        // Si es una URL completa, usarla directamente
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            // Para Facebook necesitamos el archivo real, no la URL
            $tempFile = tempnam(sys_get_temp_dir(), 'fb_');
            file_put_contents($tempFile, file_get_contents($imagePath));
            return $tempFile;
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
}