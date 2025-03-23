<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SimpleImageDownloader
{
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
            // Verificar si la URL es accesible
            $headers = @get_headers($imageUrl);
            if (!$headers || strpos($headers[0], '200') === false) {
                Log::warning('La URL de imagen no es accesible: ' . $imageUrl);
                return null;
            }
            
            // Crear un nombre de archivo seguro
            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            $extension = $extension ?: 'jpg'; // Default a jpg si no hay extensión
            $fileName = Str::random(20) . '.' . $extension;
            
            // Definir rutas
            $directory = 'news/' . $categorySlug;
            $fullPath = $directory . '/' . $fileName;
            $absolutePath = storage_path('app/public/' . $directory);
            
            // Método 1: Usar Storage de Laravel
            Log::info('Verificando si el directorio existe usando Storage: ' . $directory);
            if (!Storage::disk('public')->exists($directory)) {
                Log::info('Intentando crear directorio usando Storage');
                Storage::disk('public')->makeDirectory($directory, 0755, true);
            }
            
            // Método 2: Si Storage falla, intentar crear directamente
            if (!is_dir($absolutePath)) {
                Log::info('Storage falló, intentando crear directorio usando mkdir: ' . $absolutePath);
                if (!file_exists($absolutePath)) {
                    mkdir($absolutePath, 0755, true);
                }
            }
            
            // Verificar si el directorio existe después de intentar crearlo
            if (!is_dir($absolutePath)) {
                Log::error('No se pudo crear el directorio: ' . $absolutePath);
                
                // Intentar crear solo la carpeta de categoría sin subcarpetas
                $basicPath = storage_path('app/public/news');
                if (!is_dir($basicPath)) {
                    mkdir($basicPath, 0755, true);
                    Log::info('Creando directorio básico: ' . $basicPath);
                }
                
                // Intento simplificado final
                $categoryPath = $basicPath . '/' . $categorySlug;
                if (!is_dir($categoryPath)) {
                    mkdir($categoryPath, 0755, true);
                    Log::info('Creando directorio de categoría: ' . $categoryPath);
                }
            }
            
            // Verificar permisos de escritura
            if (is_dir($absolutePath) && !is_writable($absolutePath)) {
                Log::error('El directorio existe pero no se puede escribir: ' . $absolutePath);
                return null;
            }
            
            // Descargar imagen
            Log::info('Descargando imagen: ' . $imageUrl);
            $imageContent = @file_get_contents($imageUrl);
            if ($imageContent === false) {
                Log::error('No se pudo obtener el contenido de la imagen: ' . $imageUrl);
                return null;
            }
            
            // Guardar imagen usando Storage
            Log::info('Guardando imagen usando Storage en: ' . $fullPath);
            $success = Storage::disk('public')->put($fullPath, $imageContent);
            if (!$success) {
                Log::error('Storage::put falló. Intentando método alternativo.');
                
                // Método alternativo: escribir directamente al archivo
                $directPath = storage_path('app/public/' . $fullPath);
                Log::info('Guardando directamente en: ' . $directPath);
                $success = file_put_contents($directPath, $imageContent);
                
                if ($success === false) {
                    Log::error('No se pudo guardar la imagen usando ningún método');
                    return null;
                }
            }
            
            Log::info('Imagen guardada correctamente en: ' . $fullPath);
            
            // Devolver ruta relativa para usar en HTML
            return 'storage/' . $fullPath;
            
        } catch (\Exception $e) {
            Log::error('Error al descargar imagen: ' . $e->getMessage());
            Log::error('Traza: ' . $e->getTraceAsString());
            return null;
        }
    }
}