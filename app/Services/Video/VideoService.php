<?php

namespace App\Services\Video;

use App\Models\Video;
use App\Models\VideoKeyword;
use App\Models\VideoTag;
use App\Models\VideoCategory;
use App\Models\VideoPlatform;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VideoService
{
    protected $services = [];

    public function __construct()
    {
        // Obtener plataformas activas
        $platforms = VideoPlatform::where('is_active', true)->get();
        
        foreach ($platforms as $platform) {
            $serviceClass = "App\\Services\\Video\\" . ucfirst($platform->code) . "Service";
            
            if (class_exists($serviceClass)) {
                $this->services[$platform->code] = new $serviceClass();
            }
        }
    }

    /**
     * Buscar videos en todas las plataformas
     *
     * @param array $keywords
     * @param int $limit
     * @param string|null $platform
     * @return array
     */
    public function search(array $keywords, int $limit = 5, ?string $platform = null): array
    {
        $results = [];
        $perPlatformLimit = $platform ? $limit : ceil($limit / count($this->services));
        
        foreach ($this->services as $code => $service) {
            // Si se especifica una plataforma, solo buscar en esa
            if ($platform && $platform !== $code) {
                continue;
            }
            
            $videos = $service->search($keywords, $perPlatformLimit);
            $results = array_merge($results, $videos);
        }
        
        // Ordenar por relevancia (presencia de palabras clave en el título)
        usort($results, function($a, $b) use ($keywords) {
            $scoreA = $this->calculateRelevanceScore($a, $keywords);
            $scoreB = $this->calculateRelevanceScore($b, $keywords);
            
            return $scoreB <=> $scoreA;
        });
        
        // Limitar resultados totales
        return array_slice($results, 0, $limit);
    }

    /**
     * Obtener videos populares
     *
     * @param int $limit
     * @param string|null $platform
     * @return array
     */
    public function getPopular(int $limit = 5, ?string $platform = null): array
    {
        $results = [];
        $perPlatformLimit = $platform ? $limit : ceil($limit / count($this->services));
        
        foreach ($this->services as $code => $service) {
            if ($platform && $platform !== $code) {
                continue;
            }
            
            $videos = $service->getPopular($perPlatformLimit);
            $results = array_merge($results, $videos);
        }
        
        // Ordenar por número de vistas
        usort($results, function($a, $b) {
            return $b['view_count'] <=> $a['view_count'];
        });
        
        return array_slice($results, 0, $limit);
    }

    /**
     * Obtener o crear un video en la base de datos
     *
     * @param string $platformCode
     * @param string $videoId
     * @return Video|null
     */
    public function getOrCreateVideo(string $platformCode, string $videoId): ?Video
    {
        // Primero buscar si ya existe
        $platform = VideoPlatform::where('code', $platformCode)->first();
        
        if (!$platform) {
            return null;
        }
        
        $existingVideo = Video::where('platform_id', $platform->id)
            ->where('external_id', $videoId)
            ->first();
        
        if ($existingVideo) {
            return $existingVideo;
        }
        
        // Si no existe, obtener su información y crearlo
        if (isset($this->services[$platformCode])) {
            $videoInfo = $this->services[$platformCode]->getVideoInfo($videoId);
            
            if ($videoInfo) {
                return $this->createVideoFromData($videoInfo);
            }
        }
        
        return null;
    }

    /**
     * Crear un video a partir de datos
     *
     * @param array $data
     * @return Video
     */
    public function createVideoFromData(array $data): Video
    {
        $keywords = $data['keywords'] ?? [];
        unset($data['keywords']);
        
        DB::beginTransaction();
        
        try {
            // Crear el video
            $video = Video::create($data);
            
            // Agregar palabras clave
            foreach ($keywords as $keyword) {
                VideoKeyword::create([
                    'video_id' => $video->id,
                    'keyword' => trim($keyword)
                ]);
                
                // Crear o asignar tags para las palabras clave
                // Normalizar el nombre removiendo caracteres especiales como #
                $cleanKeyword = trim(str_replace('#', '', $keyword));
                $slug = Str::slug($cleanKeyword);
                
                // Buscar por slug en lugar de name para evitar duplicados
                $tag = VideoTag::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $cleanKeyword,
                        'slug' => $slug
                    ]
                );
                
                $video->tags()->syncWithoutDetaching([$tag->id]);
            }
            
            DB::commit();
            return $video;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear video: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Buscar videos recomendados para una noticia
     *
     * @param array $keywords
     * @param int $limit
     * @return array
     */
    public function getRecommendedVideos(array $keywords, int $limit = 3): array
    {
        // Buscar videos existentes por palabras clave
        $existingVideos = Video::whereHas('keywords', function($query) use ($keywords) {
            $query->whereIn('keyword', $keywords);
        })
        ->orderBy('published_at', 'desc')
        ->limit($limit)
        ->get();
        
        // Si hay suficientes videos existentes, devolverlos
        if ($existingVideos->count() >= $limit) {
            return $existingVideos->map(function($video) use ($keywords) {
                $score = $this->calculateRelevanceScoreForModel($video, $keywords);
                return array_merge($video->toArray(), ['relevance_score' => $score]);
            })->toArray();
        }
        
        // Si no hay suficientes, buscar nuevos y combinarlos
        $neededVideos = $limit - $existingVideos->count();
        $newVideosData = $this->search($keywords, $neededVideos);
        
        $combinedVideos = $existingVideos->toArray();
        
        foreach ($newVideosData as $videoData) {
            // Verificar si ya existe este video
            $exists = Video::where('platform_id', $videoData['platform_id'])
                ->where('external_id', $videoData['external_id'])
                ->exists();
            
            if (!$exists) {
                // Crear el video
                $video = $this->createVideoFromData($videoData);
                $videoData['id'] = $video->id;
                $videoData['relevance_score'] = $this->calculateRelevanceScore($videoData, $keywords);
                $combinedVideos[] = $videoData;
            }
        }
        
        // Ordenar por relevancia
        usort($combinedVideos, function($a, $b) {
            return $b['relevance_score'] <=> $a['relevance_score'];
        });
        
        return array_slice($combinedVideos, 0, $limit);
    }

    /**
     * Calcular puntuación de relevancia para datos de video
     */
    protected function calculateRelevanceScore(array $video, array $keywords): int
    {
        $score = 0;
        $title = strtolower($video['title']);
        $description = strtolower($video['description'] ?? '');
        
        foreach ($keywords as $keyword) {
            $keyword = strtolower(trim($keyword));
            
            // Mayor peso si aparece en el título
            if (strpos($title, $keyword) !== false) {
                $score += 3;
            }
            
            // Menor peso si aparece en la descripción
            if (strpos($description, $keyword) !== false) {
                $score += 1;
            }
            
            // Verificar si está en las keywords del video
            if (isset($video['keywords']) && in_array($keyword, array_map('strtolower', $video['keywords']))) {
                $score += 2;
            }
        }
        
        return $score;
    }

    /**
     * Calcular puntuación de relevancia para modelo de video
     */
    protected function calculateRelevanceScoreForModel(Video $video, array $keywords): int
    {
        $score = 0;
        $title = strtolower($video->title);
        $description = strtolower($video->description ?? '');
        
        // Obtener palabras clave del video
        $videoKeywords = $video->keywords->pluck('keyword')->map(function($keyword) {
            return strtolower($keyword);
        })->toArray();
        
        foreach ($keywords as $keyword) {
            $keyword = strtolower(trim($keyword));
            
            // Mayor peso si aparece en el título
            if (strpos($title, $keyword) !== false) {
                $score += 3;
            }
            
            // Menor peso si aparece en la descripción
            if (strpos($description, $keyword) !== false) {
                $score += 1;
            }
            
            // Verificar si está en las keywords del video
            if (in_array($keyword, $videoKeywords)) {
                $score += 2;
            }
        }
        
        return $score;
    }
}