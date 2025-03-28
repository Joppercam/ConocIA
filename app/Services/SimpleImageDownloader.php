<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Utils;

class SimpleImageDownloader
{
    /**
     * Cliente HTTP para descargar imágenes
     */
    protected $httpClient;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => 5,          // Timeout corto para evitar bloqueos
            'connect_timeout' => 3,  // Conexión rápida
            'verify' => false,       // Considera cambiar esto en producción
        ]);
    }

    /**
     * Descarga una imagen desde una URL y la guarda localmente
     *
     * @param string $imageUrl URL de la imagen a descargar
     * @param string $categorySlug Slug de la categoría para organizar las imágenes
     * @return string|null Ruta local de la imagen guardada o null si falla
     */
    public function download($imageUrl, $categorySlug)
    {
        if (empty($imageUrl)) {
            Log::info('URL de imagen vacía');
            return null;
        }
        
        try {
            // Cabeceras comunes que simulan un navegador web para evitar restricciones
            $browserHeaders = [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
                'Cache-Control' => 'no-cache',
                'Pragma' => 'no-cache',
            ];

            // Crear un nombre de archivo seguro
            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            $extension = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? $extension : 'jpg';
            $fileName = Str::random(20) . '.' . $extension;
            
            // Definir rutas
            $directory = 'news/' . $categorySlug;
            $fullPath = $directory . '/' . $fileName;
            
            // Asegurar que el directorio existe
            if (!Storage::disk('custom_public')->exists($directory)) {
                Storage::disk('custom_public')->makeDirectory($directory, 0755, true);
                Log::info('Directorio creado: ' . $directory);
            }
            
            // Intentar descargar la imagen usando Guzzle con manejo de errores
            Log::info('Descargando imagen: ' . $imageUrl);
            
            // Configurar referer basado en la URL de la imagen
            $referer = parse_url($imageUrl, PHP_URL_SCHEME) . '://' . parse_url($imageUrl, PHP_URL_HOST) . '/';
            
            $response = $this->httpClient->get($imageUrl, [
                'headers' => $browserHeaders,
                'referer' => $referer,
            ]);
            
            if ($response->getStatusCode() != 200) {
                Log::warning('La respuesta HTTP no fue exitosa: ' . $response->getStatusCode());
                return null;
            }
            
            $imageContent = $response->getBody()->getContents();
            
            // Verificar si el contenido es una imagen válida
            if (strlen($imageContent) < 100) {  // Verificación simple de tamaño mínimo
                Log::warning('El contenido descargado es demasiado pequeño para ser una imagen válida');
                return null;
            }
            
            // Verificar si es HTML en lugar de una imagen
            if (strpos($imageContent, '<!DOCTYPE html>') !== false || 
                strpos($imageContent, '<html') !== false) {
                Log::warning('El contenido descargado es HTML, no una imagen: ' . $imageUrl);
                return null;
            }
            
            // Guardar imagen usando Storage con el disco personalizado
            $success = Storage::disk('custom_public')->put($fullPath, $imageContent);
            
            if (!$success) {
                Log::error('No se pudo guardar la imagen en Storage');
                return null;
            }
            
            Log::info('Imagen guardada correctamente en: ' . $fullPath);
            
            // Devolver ruta relativa para usar en HTML
            return 'storage/' . $fullPath;
            
        } catch (RequestException $e) {
            Log::error('Error al realizar la petición HTTP: ' . $e->getMessage());
            
            // Intentar método alternativo si es un error de cliente (como 403)
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            if ($statusCode >= 400 && $statusCode < 500) {
                Log::info("Error $statusCode - Intentando método alternativo para: $imageUrl");
                return $this->downloadWithFallbackMethod($imageUrl, $categorySlug);
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error al descargar imagen: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Descarga múltiples imágenes en paralelo con mejor manejo de errores 403
     *
     * @param array $imageData Array asociativo [url => [categorySlug => slug, newsId => id]]
     * @return array Array asociativo [url => path] con las rutas de las imágenes descargadas
     */
    public function downloadMultiple($imageData)
    {
        $promises = [];
        $results = [];
        
        // Cabeceras comunes que simulan un navegador web para evitar restricciones
        $browserHeaders = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
        ];
        
        // Crear las promesas para cada imagen
        foreach ($imageData as $url => $data) {
            $categorySlug = $data['categorySlug'];
            
            // Preparamos el directorio de antemano
            $directory = 'news/' . $categorySlug;
            if (!Storage::disk('custom_public')->exists($directory)) {
                Storage::disk('custom_public')->makeDirectory($directory, 0755, true);
                Log::info('Directorio creado: ' . $directory);
            }
            
            // Configurar referer basado en la URL de la imagen
            $referer = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST) . '/';
            
            // Crear la promesa para esta imagen con cabeceras de navegador
            $promises[$url] = $this->httpClient->getAsync($url, [
                'headers' => $browserHeaders,
                'timeout' => 10, // Aumentamos un poco el timeout para sitios más lentos
                'connect_timeout' => 5,
                'verify' => false,
                'referer' => $referer,
            ]);
        }
        
        // Esperamos a que todas las promesas se completen
        try {
            $responses = Utils::settle($promises)->wait();
            
            foreach ($responses as $url => $response) {
                $data = $imageData[$url];
                $categorySlug = $data['categorySlug'];
                $newsId = $data['newsId'];
                
                try {
                    if ($response['state'] === 'fulfilled') {
                        // La petición fue exitosa
                        $imageContent = $response['value']->getBody()->getContents();
                        
                        // Procesamos la imagen exitosa
                        $result = $this->processImageContent($imageContent, $url, $categorySlug);
                        $results[$url] = $result;
                        
                        if ($result) {
                            Log::info("Imagen descargada exitosamente para noticia #$newsId: $url");
                        } else {
                            Log::warning("Imagen descargada pero no válida para noticia #$newsId: $url");
                        }
                    } else {
                        // La petición falló
                        $reason = $response['reason'];
                        
                        // Identificar el tipo de error para logging detallado
                        if ($reason instanceof RequestException) {
                            $statusCode = $reason->getResponse() ? $reason->getResponse()->getStatusCode() : 'sin respuesta';
                            Log::error("Error HTTP $statusCode al descargar imagen para noticia #$newsId: $url");
                            
                            // Si es un error 403, intentamos estrategias alternativas
                            if ($statusCode == 403) {
                                Log::info("Acceso denegado (403) - Intentando estrategia alternativa para: $url");
                                
                                // Intentar solo una vez más con un método alternativo (por ejemplo, usando file_get_contents)
                                $altResult = $this->downloadWithFallbackMethod($url, $categorySlug);
                                $results[$url] = $altResult;
                                
                                if ($altResult) {
                                    Log::info("Éxito con método alternativo para noticia #$newsId: $url");
                                } else {
                                    Log::warning("Fallo también con método alternativo para noticia #$newsId: $url");
                                }
                            } else {
                                $results[$url] = null;
                            }
                        } else {
                            Log::error("Error desconocido al descargar imagen para noticia #$newsId: $url - " . $reason->getMessage());
                            $results[$url] = null;
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Excepción procesando imagen para noticia #$newsId: $url - " . $e->getMessage());
                    $results[$url] = null;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error general en descargas múltiples: ' . $e->getMessage());
        }
        
        return $results;
    }

    /**
     * Procesa el contenido de una imagen descargada
     * 
     * @param string $imageContent Contenido binario de la imagen
     * @param string $url URL original de la imagen (para logging y diagnóstico)
     * @param string $categorySlug Slug de la categoría
     * @return string|null Ruta de la imagen guardada o null si falla
     */
    private function processImageContent($imageContent, $url, $categorySlug)
    {
        // Verificación simple del contenido
        if (strlen($imageContent) < 100) {
            Log::warning('El contenido descargado es demasiado pequeño para ser una imagen válida: ' . $url);
            return null;
        }
        
        // Verificar firma de formatos comunes de imagen (opcional pero recomendado)
        $signatures = [
            "\xFF\xD8\xFF" => 'jpg',            // JPEG
            "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A" => 'png', // PNG
            "GIF" => 'gif',                     // GIF
            "RIFF" => 'webp',                   // WEBP
        ];
        
        $validSignature = false;
        foreach ($signatures as $sig => $format) {
            if (substr($imageContent, 0, strlen($sig)) === $sig) {
                $validSignature = true;
                break;
            }
        }
        
        if (!$validSignature) {
            // Verificación más permisiva
            if (strpos($imageContent, '<!DOCTYPE html>') !== false || 
                strpos($imageContent, '<html') !== false) {
                Log::warning('El contenido descargado es HTML, no una imagen: ' . $url);
                return null;
            }
        }
        
        // Crear nombre de archivo seguro
        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        $extension = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? $extension : 'jpg';
        $fileName = Str::random(20) . '.' . $extension;
        
        // Definir rutas
        $directory = 'news/' . $categorySlug;
        $fullPath = $directory . '/' . $fileName;
        
        // Guardar la imagen usando el disco personalizado
        $success = Storage::disk('custom_public')->put($fullPath, $imageContent);
        
        if ($success) {
            return 'storage/' . $fullPath;
        } else {
            Log::error('Error al guardar imagen: ' . $url);
            return null;
        }
    }

    /**
     * Método alternativo de descarga para cuando Guzzle falla
     * 
     * @param string $url URL de la imagen
     * @param string $categorySlug Slug de la categoría
     * @return string|null Ruta de la imagen guardada o null si falla
     */
    private function downloadWithFallbackMethod($url, $categorySlug)
    {
        try {
            // Configurar el contexto para simular un navegador
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => [
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                        'Accept-Language: es-ES,es;q=0.9,en;q=0.8',
                    ],
                    'timeout' => 10,
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);
            
            // Intentar descargar con file_get_contents
            $imageContent = @file_get_contents($url, false, $context);
            
            if ($imageContent === false) {
                Log::warning('Método alternativo: file_get_contents falló para: ' . $url);
                return null;
            }
            
            // Procesar el contenido descargado
            return $this->processImageContent($imageContent, $url, $categorySlug);
            
        } catch (\Exception $e) {
            Log::error('Error en método alternativo de descarga: ' . $e->getMessage());
            return null;
        }
    }
}