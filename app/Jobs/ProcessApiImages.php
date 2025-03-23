<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SimpleImageDownloader;
use Illuminate\Support\Facades\Log;

class ProcessApiImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $newsId;
    protected $imageUrl;

    public function __construct($newsId, $imageUrl)
    {
        $this->newsId = $newsId;
        $this->imageUrl = $imageUrl;
    }

    public function handle()
    {
        // Aquí ponemos lógica de descarga con manejo de errores
        try {
            $downloader = new SimpleImageDownloader();
            $localPath = $downloader->download($this->imageUrl);
            
            // Actualizar la noticia con la ruta local de la imagen
            $news = \App\Models\News::find($this->newsId);
            if ($news) {
                $news->update(['image' => $localPath]);
            }
        } catch (\Exception $e) {
            // Log del error
            Log::error("Error descargando imagen: " . $e->getMessage(), [
                'news_id' => $this->newsId,
                'url' => $this->imageUrl
            ]);
        }
    }
}