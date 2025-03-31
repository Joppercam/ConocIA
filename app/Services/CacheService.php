<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Tiempo de caché predeterminado (1 hora en segundos)
     */
    protected const DEFAULT_CACHE_TIME = 3600;
    
    /**
     * Tiempo de caché largo (24 horas en segundos)
     */
    protected const LONG_CACHE_TIME = 86400;
    
    /**
     * Prefijo de clave de caché para el espacio de nombres de la aplicación
     */
    protected const CACHE_PREFIX = 'app_';
    
    /**
     * Obtener o almacenar datos en caché con gestión de claves estandarizada
     *
     * @param string $key Clave base del caché
     * @param array $params Parámetros adicionales para la clave
     * @param \Closure $callback Función para obtener datos si no están en caché
     * @param int|null $seconds Tiempo de expiración en segundos
     * @return mixed
     */
    public static function remember($key, $params = [], \Closure $callback, $seconds = null)
    {
        $fullKey = self::buildKey($key, $params);
        $cacheTime = $seconds ?? self::DEFAULT_CACHE_TIME;
        
        return Cache::remember($fullKey, $cacheTime, $callback);
    }
    
    /**
     * Olvidar (invalidar) una entrada de caché
     *
     * @param string $key Clave base del caché
     * @param array $params Parámetros adicionales para la clave
     * @return bool
     */
    public static function forget($key, $params = [])
    {
        $fullKey = self::buildKey($key, $params);
        return Cache::forget($fullKey);
    }
    
    /**
     * Construir una clave de caché estandarizada
     *
     * @param string $key Clave base
     * @param array $params Parámetros adicionales
     * @return string
     */
    protected static function buildKey($key, $params = [])
    {
        // Agregar la versión de la aplicación a la clave para invalidar automáticamente
        // el caché cuando se actualiza la aplicación
        $params['app_version'] = config('app.version', '1.0');
        
        // Convertir parámetros a string y crear hash para evitar claves demasiado largas
        $paramsStr = !empty($params) ? '_' . md5(json_encode($params)) : '';
        
        return self::CACHE_PREFIX . $key . $paramsStr;
    }
    
    /**
     * Obtener claves de caché específicas para el módulo de investigación
     *
     * @param string $key Nombre base de la clave
     * @param array $params Parámetros adicionales
     * @return string
     */
    public static function forResearch($key, $params = [])
    {
        return self::buildKey('research_' . $key, $params);
    }
    
    /**
     * Obtener claves de caché específicas para el módulo de noticias
     *
     * @param string $key Nombre base de la clave
     * @param array $params Parámetros adicionales
     * @return string
     */
    public static function forNews($key, $params = [])
    {
        return self::buildKey('news_' . $key, $params);
    }
    
    /**
     * Limpiar todo el caché de la aplicación
     *
     * @return bool
     */
    public static function clear()
    {
        return Cache::flush();
    }
    
    /**
     * Reiniciar el caché para un modelo específico
     *
     * @param string $modelName Nombre del modelo (ej. 'research', 'news')
     * @return void
     */
    public static function resetModelCache($modelName)
    {
        // Aquí podríamos implementar una lógica más sofisticada para
        // limpiar solo las claves relacionadas con un modelo específico
        // Si el sistema de caché lo permite (Redis, Memcached, etc.)
        
        // Por ahora, simplemente limpiamos todo el caché
        self::clear();
    }
}