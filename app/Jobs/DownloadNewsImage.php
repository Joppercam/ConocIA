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

    protected $newsId;
    protected $imageUrl;
    protected $categorySlug;

    /**
     * Create a new job instance.
     */
    public function __construct($newsId, $imageUrl, $categorySlug)
    {
        $this->newsId = $newsId;
        $this->imageUrl = $imageUrl;
        $this->categorySlug = $categorySlug;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Obtener la noticia
            $news = News::find($this->newsId);
            if (!$news) {
                Log::error('Noticia no encontrada para descarga de imagen', [
                    'news_id' => $this->newsId,
                    'image_url' => $this->imageUrl
                ]);
                return;
            }

            // Instanciar el servicio de descarga de imágenes
            $imageDownloader = app(SimpleImageDownloader::class);
            
            // Descargar la imagen
            $localPath = $imageDownloader->download($this->imageUrl, $this->categorySlug);
            
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
            }
        } catch (\Exception $e) {
            Log::error('Error al descargar imagen', [
                'news_id' => $this->newsId,
                'image_url' => $this->imageUrl,
                'error' => $e->getMessage()
            ]);
        }
    }
}