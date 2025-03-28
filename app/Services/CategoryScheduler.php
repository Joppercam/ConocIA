<?php

namespace App\Services;

class CategoryScheduler
{
    /**
     * Lista de todas las categorías disponibles
     */
    protected static $allCategories = [
        // Categorías técnicas
        'inteligencia-artificial',
        'machine-learning',
        'deep-learning',
        'nlp',
        'computer-vision',
        'robotica',
        'computacion-cuantica',
        
        // Categorías empresariales
        'openai',
        'google-ai',
        'microsoft-ai',
        'meta-ai',
        'amazon-ai',
        'anthropic',
        'startups-de-ia',
        
        // Categorías de aplicación
        'ia-generativa',
        'automatizacion',
        'ia-en-salud',
        'ia-en-finanzas',
        'ia-en-educacion',
        
        // Categorías sociales/impacto
        'etica-de-la-ia',
        'regulacion-de-ia',
        'impacto-laboral',
        'privacidad-y-seguridad',
        
        // Categorías generales (para compatibilidad)
        'tecnologia',
        'investigacion',
        'ciberseguridad',
        'innovacion',
        'etica',
    ];
    
    /**
     * Obtiene las categorías que deben procesarse en la hora actual
     * 
     * @param int $hour Hora actual (0-23)
     * @param string $date Fecha actual en formato Y-m-d
     * @return array Categorías a procesar
     */
    public static function getCategoriesToProcess(int $hour, string $date): array
    {
        // Ya realizamos esta verificación en el comando principal,
        // aquí solo nos enfocamos en seleccionar las categorías
        
        // Calculamos el índice relativo dentro del horario de operación (0-14)
        $hourIndex = $hour - 8;
        
        // Usamos la fecha como semilla para variar las categorías día a día
        $dateSeed = crc32($date);
        
        // Total de categorías y rotaciones por día
        $totalCategories = count(self::$allCategories);
        $rotationsPerDay = 15; // 8 AM a 10 PM
        
        // Calculamos el desplazamiento inicial para hoy
        $startOffset = $dateSeed % $totalCategories;
        
        // Calculamos qué dos categorías procesar en esta hora
        $category1Index = ($startOffset + ($hourIndex * 2)) % $totalCategories;
        $category2Index = ($startOffset + ($hourIndex * 2) + 1) % $totalCategories;
        
        return [
            self::$allCategories[$category1Index],
            self::$allCategories[$category2Index]
        ];
    }
}