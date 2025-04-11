<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Verification;
use Carbon\Carbon;

class VerificadorCacheService
{
    /**
     * Tiempo de caché para diferentes tipos de datos
     */
    const CACHE_TIMES = [
        'verificacion_individual' => 6, // horas
        'lista_verificaciones' => 1, // horas
        'verificaciones_destacadas' => 2, // horas
        'estadisticas' => 3, // horas
    ];
    
    /**
     * Prefijos para las claves de caché
     */
    const CACHE_PREFIXES = [
        'verificacion' => 'verificador_show_',
        'lista' => 'verificador_index_',
        'destacadas' => 'verificador_featured_',
        'stats' => 'verificador_stats_',
    ];
    
    /**
     * Generar o recuperar una verificación en caché
     *
     * @param int $id ID de la verificación
     * @param \Closure $callback Función a ejecutar si no está en caché
     * @return mixed Resultado de la verificación
     */
    public function getVerification($id, \Closure $callback)
    {
        $cacheKey = self::CACHE_PREFIXES['verificacion'] . $id;
        return Cache::remember(
            $cacheKey,
            now()->addHours(self::CACHE_TIMES['verificacion_individual']),
            $callback
        );
    }
    
    /**
     * Generar o recuperar una lista de verificaciones en caché
     *
     * @param array $params Parámetros de filtrado
     * @param \Closure $callback Función a ejecutar si no está en caché
     * @return mixed Resultado de la lista
     */
    public function getVerificationsList($params, \Closure $callback)
    {
        $cacheKey = self::CACHE_PREFIXES['lista'] . md5(json_encode($params));
        return Cache::remember(
            $cacheKey,
            now()->addHours(self::CACHE_TIMES['lista_verificaciones']),
            $callback
        );
    }
    
    /**
     * Generar o recuperar verificaciones destacadas en caché
     *
     * @param \Closure $callback Función a ejecutar si no está en caché
     * @return mixed Resultado de verificaciones destacadas
     */
    public function getFeaturedVerifications(\Closure $callback)
    {
        $cacheKey = self::CACHE_PREFIXES['destacadas'] . date('Y-m-d');
        return Cache::remember(
            $cacheKey,
            now()->addHours(self::CACHE_TIMES['verificaciones_destacadas']),
            $callback
        );
    }
    
    /**
     * Generar o recuperar estadísticas en caché
     *
     * @param \Closure $callback Función a ejecutar si no está en caché
     * @return mixed Resultado de las estadísticas
     */
    public function getStats(\Closure $callback)
    {
        $cacheKey = self::CACHE_PREFIXES['stats'] . date('Y-m-d');
        return Cache::remember(
            $cacheKey,
            now()->addHours(self::CACHE_TIMES['estadisticas']),
            $callback
        );
    }
    
    /**
     * Invalidar caché de una verificación específica
     *
     * @param int $id ID de la verificación
     * @return bool Resultado de la invalidación
     */
    public function invalidateVerification($id)
    {
        return Cache::forget(self::CACHE_PREFIXES['verificacion'] . $id);
    }
    
    /**
     * Invalidar caché de listas de verificaciones
     *
     * @return bool Resultado de la invalidación
     */
    public function invalidateVerificationsList()
    {
        // Obtener todas las claves que comienzan con el prefijo
        $keys = $this->getKeysByPrefix(self::CACHE_PREFIXES['lista']);
        
        // Invalidar cada clave
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        
        return true;
    }
    
    /**
     * Invalidar caché de verificaciones destacadas
     *
     * @return bool Resultado de la invalidación
     */
    public function invalidateFeaturedVerifications()
    {
        return Cache::forget(self::CACHE_PREFIXES['destacadas'] . date('Y-m-d'));
    }
    
    /**
     * Invalidar caché de estadísticas
     *
     * @return bool Resultado de la invalidación
     */
    public function invalidateStats()
    {
        return Cache::forget(self::CACHE_PREFIXES['stats'] . date('Y-m-d'));
    }
    
    /**
     * Ejecutar limpieza programada de caché
     *
     * @return array Estadísticas de limpieza
     */
    public function runScheduledCacheCleaning()
    {
        $stats = [
            'verificaciones_eliminadas' => 0,
            'listas_eliminadas' => 0,
            'destacadas_eliminadas' => 0,
            'stats_eliminadas' => 0
        ];
        
        // Eliminar caché de verificaciones poco vistas (excepto las más recientes)
        $lowViewVerifications = Verification::where('views_count', '<', 10)
            ->where('created_at', '<', Carbon::now()->subDays(7))
            ->limit(50)
            ->get();
        
        foreach ($lowViewVerifications as $verification) {
            if ($this->invalidateVerification($verification->id)) {
                $stats['verificaciones_eliminadas']++;
            }
        }
        
        // Eliminar caché de listas antiguas (más de 1 día)
        $listaKeys = $this->getKeysByPrefix(self::CACHE_PREFIXES['lista']);
        
        foreach ($listaKeys as $key) {
            $ttl = Cache::getTimeToLive($key);
            if ($ttl && $ttl < 60 * 60 * 24) { // Menos de 24 horas restantes
                Cache::forget($key);
                $stats['listas_eliminadas']++;
            }
        }
        
        // Eliminar caché de destacadas y stats de días anteriores
        $oldDate = Carbon::yesterday()->format('Y-m-d');
        if (Cache::forget(self::CACHE_PREFIXES['destacadas'] . $oldDate)) {
            $stats['destacadas_eliminadas']++;
        }
        
        if (Cache::forget(self::CACHE_PREFIXES['stats'] . $oldDate)) {
            $stats['stats_eliminadas']++;
        }
        
        return $stats;
    }
    
    /**
     * Obtener claves de caché por prefijo
     *
     * @param string $prefix Prefijo a buscar
     * @return array Lista de claves
     */
    protected function getKeysByPrefix($prefix)
    {
        // Nota: Este método depende del driver de caché que estés usando
        // Para Redis puedes usar el comando KEYS, para otros drivers necesitarás
        // implementar una solución específica
        
        // Ejemplo para Redis:
        if (config('cache.default') === 'redis') {
            $redis = Cache::getRedis();
            $keys = $redis->keys(config('cache.prefix') . ':' . $prefix . '*');
            
            // Eliminar el prefijo de Redis para obtener las claves de Laravel
            return array_map(function ($key) {
                return str_replace(config('cache.prefix') . ':', '', $key);
            }, $keys);
        }
        
        // Para otros drivers, podrías implementar un registro de claves
        // o devolver un array vacío si no es posible obtener las claves
        return [];
    }
}