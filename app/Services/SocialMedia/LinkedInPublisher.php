<?php

namespace App\Services\SocialMedia;

use App\Models\News;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class LinkedInPublisher implements SocialMediaPublisher
{
    protected $accessToken;
    protected $companyId;
    protected $configured = false;
    protected $baseUrl = 'https://api.linkedin.com/v2';
    protected $maxTextLength = 3000;
    
    public function __construct()
    {
        $this->initialize();
    }
    
    protected function initialize()
    {
        $this->accessToken = config('services.linkedin.access_token');
        $this->companyId = config('services.linkedin.company_id');
        
        if (!empty($this->accessToken) && !empty($this->companyId)) {
            $this->configured = true;
        } else {
            Log::warning('LinkedIn API no está configurada correctamente.');
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
                'message' => 'LinkedIn API no está configurada.'
            ];
        }
        
        try {
            $content = $this->formatContent($article);
            
            // Estructura básica para una publicación en LinkedIn
            $postData = [
                'author' => "urn:li:organization:{$this->companyId}",
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $content['text']
                        ],
                        'shareMediaCategory' => 'NONE',
                    ],
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
                ]
            ];
            
            // Si hay un enlace, lo añadimos como media
            if (!empty($content['link'])) {
                $postData['specificContent']['com.linkedin.ugc.ShareContent']['shareMediaCategory'] = 'ARTICLE';
                $postData['specificContent']['com.linkedin.ugc.ShareContent']['media'] = [
                    [
                        'status' => 'READY',
                        'description' => [
                            'text' => Str::limit(strip_tags($article->content), 200)
                        ],
                        'originalUrl' => $content['link'],
                        'title' => [
                            'text' => $article->title
                        ]
                    ]
                ];
                
                // Si hay imagen, la añadimos como thumbnail
                if (!empty($content['media'])) {
                    // LinkedIn requiere subir la imagen primero para obtener un asset ID
                    $imageAssetId = $this->uploadImage($content['media'][0]);
                    
                    if ($imageAssetId) {
                        $postData['specificContent']['com.linkedin.ugc.ShareContent']['media'][0]['thumbnails'] = [
                            [
                                'imageSpecificContent' => [
                                    'com.linkedin.common.ImageContent' => [
                                        'id' => $imageAssetId
                                    ]
                                ]
                            ]
                        ];
                    }
                }
            }
            
            // Enviar la solicitud para crear el post
            $response = Http::withToken($this->accessToken)
                ->withHeaders([
                    'X-Restli-Protocol-Version' => '2.0.0',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/ugcPosts", $postData);
            
            if ($response->successful()) {
                $responseData = $response->json();
                $postId = $responseData['id'] ?? null;
                
                if ($postId) {
                    // El formato del post ID es algo como "urn:li:share:1234567890"
                    // Extraemos el número real para crear la URL
                    $idParts = explode(':', $postId);
                    $postNumber = end($idParts);
                    $postUrl = "https://www.linkedin.com/feed/update/{$postId}";
                    
                    return [
                        'success' => true,
                        'post_id' => $postId,
                        'post_url' => $postUrl,
                        'message' => null
                    ];
                }
            }
            
            return [
                'success' => false,
                'post_id' => null,
                'post_url' => null,
                'message' => "Error al publicar en LinkedIn: " . ($response->body() ?? 'Sin respuesta')
            ];
        } catch (\Exception $e) {
            Log::error('Error al publicar en LinkedIn: ' . $e->getMessage(), [
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
        // LinkedIn permite más texto que Twitter pero menos que Facebook
        $title = $article->title;
        $excerpt = $article->excerpt ?? Str::limit(strip_tags($article->content), 200);
        $url = route('news.show', $article->slug);
        
        // Construir texto del post
        $text = "{$title}\n\n{$excerpt}\n\n";
        
        // Añadir hashtags relevantes basados en categorías o tags
        if ($article->categories->isNotEmpty()) {
            $hashtags = $article->categories->map(function ($category) {
                return '#' . Str::camel($category->name);
            })->implode(' ');
            
            if (strlen($text . $hashtags) <= $this->maxTextLength) {
                $text .= $hashtags;
            }
        }
        
        // Preparar imágenes
        $media = [];
        if ($article->featured_image) {
            $media[] = $this->getImagePath($article->featured_image);
        }
        
        return [
            'text' => $text,
            'media' => $media,
            'link' => $url
        ];
    }
    
    protected function uploadImage($imagePath)
    {
        try {
            // Primero obtenemos un URL de registro para subir la imagen
            $registerUploadResponse = Http::withToken($this->accessToken)
                ->withHeaders([
                    'X-Restli-Protocol-Version' => '2.0.0',
                ])
                ->post("{$this->baseUrl}/assets?action=registerUpload", [
                    'registerUploadRequest' => [
                        'recipes' => [
                            'com.linkedin.ugc.ImageUploadMediaRecipe'
                        ],
                        'owner' => "urn:li:organization:{$this->companyId}",
                        'serviceRelationships' => [
                            [
                                'relationshipType' => 'OWNER',
                                'identifier' => 'urn:li:userGeneratedContent'
                            ]
                        ]
                    ]
                ]);
            
            if (!$registerUploadResponse->successful()) {
                throw new \Exception("Error al registrar la carga: " . $registerUploadResponse->body());
            }
            
            $uploadData = $registerUploadResponse->json();
            $uploadUrl = $uploadData['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'] ?? null;
            $assetId = $uploadData['value']['asset'] ?? null;
            
            if (!$uploadUrl || !$assetId) {
                throw new \Exception("No se pudo obtener la URL de carga o el ID del recurso");
            }
            
            // Preparar el archivo
            $imageContent = $this->getImageContent($imagePath);
            
            // Subir la imagen usando la URL proporcionada
            $uploadResponse = Http::withToken($this->accessToken)
                ->withHeaders([
                    'Content-Type' => $this->getImageMimeType($imagePath),
                ])
                ->put($uploadUrl, $imageContent);
            
            if (!$uploadResponse->successful()) {
                throw new \Exception("Error al subir la imagen: " . $uploadResponse->body());
            }
            
            return $assetId;
        } catch (\Exception $e) {
            Log::error("Error al subir imagen a LinkedIn: " . $e->getMessage(), [
                'mediaPath' => $imagePath,
                'trace' => $e->getTraceAsString()
            ]);
            
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
    
    protected function getImageContent($imagePath)
    {
        // Si es una URL, obtenemos el contenido
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return file_get_contents($imagePath);
        }
        
        // Para archivos locales
        if (file_exists($imagePath)) {
            return file_get_contents($imagePath);
        }
        
        throw new \Exception("No se pudo obtener el contenido de la imagen: {$imagePath}");
    }
    
    protected function getImageMimeType($imagePath)
    {
        // Si es una URL, intentamos deducir el tipo MIME de la extensión
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            switch (strtolower($extension)) {
                case 'jpg':
                case 'jpeg':
                    return 'image/jpeg';
                case 'png':
                    return 'image/png';
                case 'gif':
                    return 'image/gif';
                default:
                    return 'image/jpeg'; // Por defecto
            }
        }
        
        // Para archivos locales
        if (file_exists($imagePath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $imagePath);
            finfo_close($finfo);
            return $mime;
        }
        
        return 'image/jpeg'; // Por defecto
    }
}