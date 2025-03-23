<?php

namespace App;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Obtiene la URL de la imagen de la noticia o una imagen predeterminada
     * 
     * @param string|null $imageName Nombre de la imagen
     * @param string $size Tamaño deseado: 'small', 'medium', o 'large'
     * @return string URL de la imagen
     */
    public static function getNewsImage($imageName, $size = 'medium') 
    {
        // Si la imagen existe en el almacenamiento, devuelve su URL
        if ($imageName && Storage::disk('public')->exists('images/news/' . $imageName)) {
            return Storage::url('images/news/' . $imageName);
        }
        
        // Si no, devuelve la imagen predeterminada según el tamaño
        return Storage::url('images/default/news-default-' . $size . '.jpg');
    }

    /**
     * Obtiene la URL del avatar de un usuario o una imagen predeterminada
     * 
     * @param string|null $avatarName Nombre del archivo de avatar
     * @param string $size Tamaño deseado: 'small', 'medium', o 'large'
     * @return string URL del avatar
     */
    public static function getAvatarImage($avatarName, $size = 'small') 
    {
        // Ruta base para avatares
        $basePath = 'images/avatars/' . $size . '/';
        
        // Si se proporciona un nombre de avatar y el archivo existe
        if ($avatarName && Storage::disk('public')->exists($basePath . $avatarName)) {
            return Storage::url($basePath . $avatarName);
        }
        
        // Si no existe en el tamaño específico, intenta en la carpeta original
        if ($avatarName && Storage::disk('public')->exists('images/avatars/original/' . $avatarName)) {
            return Storage::url('images/avatars/original/' . $avatarName);
        }
        
        // Si no hay avatar o no existe, devuelve el avatar predeterminado
        return asset('storage/images/defaults/avatar-default.jpg');
    }

    /**
     * Método genérico para obtener URL de imagen con manejo de diferentes tipos
     * 
     * @param string|null $imageName Nombre del archivo de imagen
     * @param string $type Tipo de imagen (avatars, news, etc)
     * @param string $size Tamaño de la imagen (small, medium, large)
     * @return string URL de la imagen
     */
    public static function getImageUrl($imageName, $type = 'general', $size = 'medium')
    {
        // Según el tipo, llamar al método específico
        switch ($type) {
            case 'avatars':
                return self::getAvatarImage($imageName, $size);
            
            case 'news':
                return self::getNewsImage($imageName, $size);
            
            // Caso genérico para otros tipos de imágenes
            default:
                // Ruta base para el tipo especificado
                $basePath = 'images/' . $type . '/' . $size . '/';
                
                // Si se proporciona un nombre de imagen y el archivo existe
                if ($imageName && Storage::disk('public')->exists($basePath . $imageName)) {
                    return Storage::url($basePath . $imageName);
                }
                
                // Intenta en la carpeta original
                if ($imageName && Storage::disk('public')->exists('images/' . $type . '/original/' . $imageName)) {
                    return Storage::url('images/' . $type . '/original/' . $imageName);
                }
                
                // Imagen predeterminada para el tipo especificado o una genérica
                $defaultPath = 'images/defaults/' . $type . '-default.jpg';
                if (Storage::disk('public')->exists($defaultPath)) {
                    return Storage::url($defaultPath);
                }
                
                // Fallback final: imagen genérica predeterminada
                return asset('storage/images/defaults/image-default.jpg');
        }
    }
}