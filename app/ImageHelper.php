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
}