<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Services\SocialMedia\TwitterPublisher;
use App\Services\SocialMedia\FacebookPublisher;
use App\Services\SocialMedia\LinkedInPublisher;
use App\Models\SocialMediaPost;
use Illuminate\Support\Facades\Log;

class PublishNewsToSocialMedia extends Command
{
    protected $signature = 'news:publish-social {--network=all} {--limit=5}';
    protected $description = 'Publica noticias recientes en redes sociales';

    protected $publishers = [];

    public function __construct(
        TwitterPublisher $twitterPublisher,
        FacebookPublisher $facebookPublisher,
        LinkedInPublisher $linkedInPublisher
    ) {
        parent::__construct();
        
        $this->publishers = [
            'twitter' => $twitterPublisher,
            'facebook' => $facebookPublisher,
            'linkedin' => $linkedInPublisher
        ];
    }

    public function handle()
    {
        $network = $this->option('network');
        $limit = $this->option('limit');

        $this->info("Iniciando publicación de noticias en redes sociales...");
        
        // Obtener noticias que no han sido publicadas aún
        $news = $this->getUnpublishedNews($network, $limit);
        
        if ($news->isEmpty()) {
            $this->info("No hay noticias pendientes para publicar.");
            return 0;
        }
        
        $this->info("Se encontraron {$news->count()} noticias para publicar.");
        
        foreach ($news as $article) {
            $this->publishArticle($article, $network);
        }
        
        $this->info("Proceso completado.");
        return 0;
    }
    
    protected function getUnpublishedNews($network, $limit)
    {
        $query = News::where('status', 'published')
            ->where('created_at', '>=', now()->subDays(2)) // Noticias de los últimos 2 días
            ->orderBy('created_at', 'desc');
            
        if ($network !== 'all') {
            // Solo traer noticias que no se han publicado en la red específica
            $query->whereDoesntHave('socialPosts', function ($q) use ($network) {
                $q->where('network', $network);
            });
        } else {
            // Traer noticias que no se han publicado en al menos una de las redes
            $query->where(function ($q) {
                foreach (array_keys($this->publishers) as $net) {
                    $q->orWhereDoesntHave('socialPosts', function ($subQ) use ($net) {
                        $subQ->where('network', $net);
                    });
                }
            });
        }
        
        return $query->limit($limit)->get();
    }
    
    protected function publishArticle($article, $targetNetwork)
    {
        $networks = $targetNetwork === 'all' ? array_keys($this->publishers) : [$targetNetwork];
        
        foreach ($networks as $network) {
            // Verificar si ya fue publicado en esta red
            if ($article->socialPosts->where('network', $network)->isNotEmpty()) {
                $this->info("La noticia '{$article->title}' ya fue publicada en $network.");
                continue;
            }
            
            try {
                $this->info("Publicando '{$article->title}' en $network...");
                
                $publisher = $this->publishers[$network];
                $result = $publisher->publish($article);
                
                if ($result['success']) {
                    // Guardar registro de la publicación
                    SocialMediaPost::create([
                        'news_id' => $article->id,
                        'network' => $network,
                        'post_id' => $result['post_id'],
                        'post_url' => $result['post_url'] ?? null,
                        'published_at' => now(),
                    ]);
                    
                    $this->info("Publicación exitosa en $network.");
                } else {
                    $this->error("Error al publicar en $network: {$result['message']}");
                    Log::error("Error publicando noticia ID {$article->id} en $network", [
                        'error' => $result['message'],
                        'news_id' => $article->id
                    ]);
                }
            } catch (\Exception $e) {
                $this->error("Excepción al publicar en $network: {$e->getMessage()}");
                Log::error("Excepción publicando noticia ID {$article->id} en $network", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'news_id' => $article->id
                ]);
            }
        }
    }
}