<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class CategoryHelper
{
    /**
     * Obtiene el estilo CSS para una categoría
     *
     * @param mixed $category Objeto de categoría
     * @return string Estilo CSS para la categoría
     */
    public static function getCategoryStyle($category)
    {
        if (!$category || !isset($category->color)) {
            return 'background-color: var(--primary-color);';
        }
        
        return 'background-color: ' . $category->color . ';';
    }
    
    /**
     * Obtiene el icono para una categoría
     *
     * @param mixed $category Objeto de categoría
     * @return string Clase CSS del icono para la categoría
     */
    public static function getCategoryIcon($category)
    {
        if (!$category || !isset($category->icon)) {
            return 'fa-tag';
        }
        
        return $category->icon;
    }
    
    /**
     * Obtiene la URL para una categoría
     *
     * @param mixed $category Objeto de categoría
     * @return string URL de la categoría
     */
    public static function getCategoryUrl($category)
    {
        if (!$category || !isset($category->slug)) {
            return '#';
        }
        
        return route('news.category', $category->slug);
    }
    
    /**
     * Genera HTML de un badge de categoría con estilo y enlace
     *
     * @param mixed $category Objeto de categoría
     * @param bool $showIcon Mostrar icono junto al nombre
     * @param array $attributes Atributos HTML adicionales
     * @return string HTML del badge de categoría
     */
    public static function getCategoryBadge($category, $showIcon = true, $attributes = [])
    {
        if (!$category) {
            return '';
        }
        
        $url = self::getCategoryUrl($category);
        $style = self::getCategoryStyle($category);
        
        // Preparar atributos
        $attributesStr = '';
        foreach ($attributes as $key => $value) {
            $attributesStr .= " {$key}=\"{$value}\"";
        }
        
        // Preparar icono si se solicita
        $icon = $showIcon ? '<i class="fas ' . self::getCategoryIcon($category) . ' me-1"></i>' : '';
        
        // Sanear el nombre para evitar problemas con comillas
        $safeName = htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8');
        
        return "<a href=\"{$url}\" class=\"category-badge\" style=\"{$style}\"{$attributesStr}>{$icon}{$safeName}</a>";
    }
    
    /**
     * Obtiene un array con las categorías más utilizadas
     *
     * @param int $limit Número máximo de categorías a devolver
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPopularCategories($limit = 10)
    {
        return \App\Models\Category::withCount(['news' => function($query) {
                $query->where('status', 'published');
            }])
            ->having('news_count', '>', 0)
            ->orderBy('news_count', 'desc')
            ->take($limit)
            ->get();
    }
    
    /**
     * Genera un menú de navegación por categorías
     *
     * @param array $categories Categorías a incluir en el menú
     * @param string $activeSlug Slug de la categoría activa
     * @return string HTML del menú de categorías
     */
    public static function getCategoryMenu($categories, $activeSlug = null)
    {
        $html = '<ul class="category-menu">';
        
        foreach ($categories as $category) {
            $isActive = $activeSlug && $category->slug === $activeSlug;
            $activeClass = $isActive ? ' class="active"' : '';
            $icon = self::getCategoryIcon($category);
            $url = self::getCategoryUrl($category);
            $safeName = htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8');
            
            $html .= "<li{$activeClass}>";
            $html .= "<a href=\"{$url}\">";
            $html .= "<i class=\"fas {$icon} me-2\"></i>{$safeName}";
            
            // Mostrar contador si está disponible
            if (isset($category->news_count)) {
                $html .= " <span class=\"category-count\">{$category->news_count}</span>";
            }
            
            $html .= "</a></li>";
        }
        
        $html .= '</ul>';
        
        return $html;
    }
}