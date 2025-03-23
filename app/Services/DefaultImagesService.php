<?php

namespace App\Services;

class DefaultImagesService
{
    /**
     * Get default image for a specific category
     *
     * @param string|int $categoryId
     * @return string
     */
    public function getDefaultImageForCategory($categoryId)
    {
        $mapping = [
            // ID => ruta de imagen predeterminada
            'Inteligencia Artificial' => 'images/defaults/ai.jpg',
            'Tecnología' => 'images/defaults/tech.jpg',
            'Investigación' => 'images/defaults/research.jpg',
            'Robótica' => 'images/defaults/robotics.jpg',
            'Ciberseguridad' => 'images/defaults/cybersecurity.jpg',
            'Innovación' => 'images/defaults/innovation.jpg',
            'Ética' => 'images/defaults/ethics.jpg',
        ];
        
        // Por nombre de categoría
        if (isset($mapping[$categoryId])) {
            return asset($mapping[$categoryId]);
        }
        
        // Por ID de categoría (si es numérico)
        foreach (\App\Models\Category::all() as $category) {
            if ($category->id == $categoryId && isset($mapping[$category->name])) {
                return asset($mapping[$category->name]);
            }
        }
        
        // Imagen genérica por defecto si no hay coincidencia
        return asset('images/defaults/generic-tech.jpg');
    }
    
    /**
     * Verificar si una URL de imagen es válida
     *
     * @param string|null $url
     * @return bool
     */
    public function isValidImageUrl($url)
    {
        if (empty($url)) {
            return false;
        }
        
        $headers = @get_headers($url);
        
        // Verificar si la URL es accesible y es una imagen
        if ($headers && strpos($headers[0], '200') !== false) {
            foreach ($headers as $header) {
                if (strpos(strtolower($header), 'content-type: image/') !== false) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Obtener URL de imagen válida o una imagen predeterminada
     *
     * @param string|null $imageUrl
     * @param string|int $categoryId
     * @return string
     */
    public function getValidImageUrl($imageUrl, $categoryId)
    {
        if ($this->isValidImageUrl($imageUrl)) {
            return $imageUrl;
        }
        
        return $this->getDefaultImageForCategory($categoryId);
    }
}