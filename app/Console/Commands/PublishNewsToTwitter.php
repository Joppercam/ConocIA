<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Services\SocialMedia\TwitterPublisher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishNewsToTwitter extends Command
{
    protected $signature = 'news:publish-twitter {--limit=5 : Número máximo de noticias a publicar}';
    protected $description = 'Publica noticias en Twitter';

    protected $twitterPublisher;

    public function __construct(TwitterPublisher $twitterPublisher)
    {
        parent::__construct();
        $this->twitterPublisher = $twitterPublisher;
    }

    public function handle()
    {
        // Mostrar estadísticas de uso
        $stats = $this->twitterPublisher->getUsageStats();
        
        $this->line('=== Estadísticas de uso de Twitter para ' . $stats['current_month'] . ' ===');
        $this->line('- Publicaciones usadas: ' . $stats['posts_used'] . '/' . $stats['posts_limit']);
        $this->line('- Publicaciones restantes: ' . $stats['posts_remaining']);
        $this->line('- Porcentaje utilizado: ' . $stats['percentage_used'] . '%');
        $this->line('- Próximo reinicio: ' . $stats['next_reset_date']);
        $this->line('=============================================');

        // Verificar si se puede publicar
        if (!$stats['can_publish']) {
            $this->error('Se ha alcanzado el límite mensual de publicaciones en Twitter.');
            return 1;
        }

        // Obtener el límite desde los parámetros
        $limit = $this->option('limit');
        
        // Ajustar el límite si es mayor que las publicaciones restantes
        $limit = min($limit, $stats['posts_remaining']);
        
        // Obtener noticias no publicadas
        $news = News::where('status', 'published')
            ->where('published_at', '<=', now())
            ->where(function($query) {
                $query->whereDoesntHave('socialMediaPosts', function($q) {
                    $q->where('network', 'twitter');
                })
                ->orWhereDoesntHave('socialMediaPosts');
            })
            ->orderBy('published_at', 'desc')
            ->take($limit)
            ->get();
            
        $this->line('Iniciando publicación de noticias en Twitter...');
        
        if ($news->isEmpty()) {
            $this->info('No se encontraron noticias pendientes para publicar en Twitter.');
            return 0;
        }
        
        $this->line('Se encontraron ' . $news->count() . ' noticias para publicar.');
        
        $publishedCount = 0;
        
        foreach ($news as $article) {
            $this->line('Procesando: \'' . $article->title . '\'');
            $this->line('  Publicando en Twitter...');
            
            $result = $this->twitterPublisher->publish($article);
            
            if ($result['success']) {
                // Registrar la publicación en la tabla de social_media_posts
                $article->socialMediaPosts()->create([
                    'network' => 'twitter',
                    'post_id' => $result['post_id'],
                    'post_url' => $result['post_url'],
                    'status' => 'success',
                    'published_at' => now()
                ]);
                
                $publishedCount++;
                
                if ($result['post_id']) {
                    $this->info('  ✓ Publicado correctamente. ID: ' . $result['post_id']);
                } else {
                    $this->info('  ✓ ' . $result['message']);
                }
            } else {
                $this->error('  ✗ Error al publicar: ' . $result['message']);
                
                // Registrar el error en la tabla de social_media_posts
                $article->socialMediaPosts()->create([
                    'network' => 'twitter',
                    'status' => 'error',
                    'error_message' => $result['message']
                ]);
            }
        }
        
        $this->line('Proceso completado. Se publicaron ' . $publishedCount . ' noticias en Twitter.');
        
        return 0;
    }
}