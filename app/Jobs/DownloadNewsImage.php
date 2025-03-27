<?php

namespace App\Jobs;

use App\Models\News;
use App\Services\SimpleImageDownloader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DownloadNewsImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de intentos para el job
     */
    public $tries = 3;
    
    /**
     * Tiempo de espera entre reintentos en segundos
     */
    public $backoff = 5;
    
    /**
     * La URL de la imagen a descargar
     */
    protected $imageUrl;
    
    /**
     * El ID de la noticia
     */
    protected $newsId;
    
    /**
     * La categoría (slug) para organizar directorios
     */
    protected $categorySlug;

    /**
     * Create a new job instance.
     *
     * @param string $imageUrl
     * @param int $newsId
     * @param string $categorySlug
     * @return void
     */
    public function __construct($imageUrl, $newsId, $categorySlug)
    {
        $this->imageUrl = $imageUrl;
        $this->newsId = $newsId;
        $this->categorySlug = $categorySlug;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SimpleImageDownloader $downloader)
    {
        Log::info('Iniciando descarga asíncrona de imagen', [
            'news_id' => $this->newsId,
            'image_url' => $this->imageUrl
        ]);
        
        try {
            // Buscar la noticia
            $news = News::find($this->newsId);
            
            if (!$news) {
                Log::error('Noticia no encontrada para actualizar imagen', [
                    'news_id' => $this->newsId
                ]);
                return;
            }
            
            // Descargar la imagen
            $localPath = $downloader->download($this->imageUrl, $this->categorySlug);
            
            if ($localPath) {
                // Actualizar la noticia con la ruta de la imagen
                $news->update(['image' => $localPath]);
                
                Log::info('Imagen descargada y actualizada correctamente', [
                    'news_id' => $this->newsId,
                    'image_path' => $localPath
                ]);
            } else {
                Log::warning('No se pudo descargar la imagen', [
                    'news_id' => $this->newsId,
                    'image_url' => $this->imageUrl
                ]);
                
                // No hacemos actualización, mantenemos la imagen predeterminada
            }
        } catch (\Exception $e) {
            Log::error('Error en job de descarga de imagen', [
                'news_id' => $this->newsId,
                'image_url' => $this->imageUrl,
                'error' => $e->getMessage()
            ]);
            
            // Relanzar la excepción para que Laravel gestione los reintentos
            throw $e;
        }
    }
}