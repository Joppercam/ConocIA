<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Video\VimeoService;
use App\Models\Video;
use App\Models\VideoKeyword;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FetchVimeoVideosCommand extends Command
{
    protected $signature = 'videos:fetch-vimeo {query?} {--limit=10}';
    protected $description = 'Busca y almacena videos de Vimeo basados en términos de búsqueda';

    protected $vimeoService;

    public function __construct(VimeoService $vimeoService)
    {
        parent::__construct();
        $this->vimeoService = $vimeoService;
    }

    public function handle()
    {
        $query = $this->argument('query') ?? 'inteligencia artificial';
        $limit = $this->option('limit');
        
        $this->info("Buscando videos en Vimeo con la consulta: {$query}");
        
        try {
            // Convertir la consulta en un array de palabras clave
            $keywords = explode(' ', $query);
            
            // Buscar videos
            $videosData = $this->vimeoService->search($keywords, $limit);
            
            if (empty($videosData)) {
                $this->warn("No se encontraron videos para la consulta: {$query}");
                return 0;
            }
            
            $this->info("Se encontraron " . count($videosData) . " videos.");
            
            // Guardar cada video en la base de datos
            $savedCount = 0;
            
            foreach ($videosData as $videoData) {
                try {
                    // Verificar si el video ya existe
                    $existingVideo = Video::where('platform_id', $videoData['platform_id'])
                        ->where('external_id', $videoData['external_id'])
                        ->first();
                    
                    if ($existingVideo) {
                        // Actualizar video existente
                        $existingVideo->update($videoData);
                        $this->line("Actualizado: {$videoData['title']}");
                        $savedCount++;
                        continue;
                    }
                    
                    // Crear nuevo video
                    $video = Video::create($videoData);
                    
                    // Guardar palabras clave si existen
                    if (isset($videoData['keywords']) && is_array($videoData['keywords'])) {
                        foreach ($videoData['keywords'] as $keyword) {
                            VideoKeyword::create([
                                'video_id' => $video->id,
                                'keyword' => $keyword
                            ]);
                        }
                    }
                    
                    $this->line("Guardado: {$videoData['title']}");
                    $savedCount++;
                    
                } catch (\Exception $e) {
                    $this->error("Error al guardar video: " . $e->getMessage());
                    Log::error("Error al guardar video de Vimeo: " . $e->getMessage());
                }
            }
            
            $this->info("Se guardaron/actualizaron {$savedCount} videos de Vimeo.");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error al obtener videos de Vimeo: " . $e->getMessage());
            Log::error("Error en comando videos:fetch-vimeo: " . $e->getMessage());
            return 1;
        }
    }
}