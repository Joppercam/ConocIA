<?php

namespace App\Services\Video;

interface VideoServiceInterface
{
    /**
     * Buscar videos por términos de búsqueda
     *
     * @param array $keywords
     * @param int $limit
     * @return array
     */
    public function search(array $keywords, int $limit = 5): array;
    
    /**
     * Obtener videos populares
     *
     * @param int $limit
     * @return array
     */
    public function getPopular(int $limit = 5): array;
    
    /**
     * Obtener información detallada de un video por su ID
     *
     * @param string $videoId
     * @return array|null
     */
    public function getVideoInfo(string $videoId): ?array;
}
