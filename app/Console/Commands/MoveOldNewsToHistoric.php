<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\NewsHistoric;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MoveOldNewsToHistoric extends Command
{
    protected $signature = 'news:archive {--days=4 : Days to keep news active} {--batch=100 : Batch size for processing}';
    protected $description = 'Move news older than specified days to historic table';

    public function handle()
    {
        // Configuración
        $days = $this->option('days');
        $batchSize = $this->option('batch');
        $cutoffDate = Carbon::now()->subDays($days);
        
        // Contar noticias a mover
        $totalNewsToMove = News::where('created_at', '<', $cutoffDate)->count();
        
        if ($totalNewsToMove === 0) {
            $this->info("No news found older than {$days} days.");
            return 0;
        }
        
        $this->info("Found {$totalNewsToMove} news articles to move to historic table.");
        $bar = $this->output->createProgressBar($totalNewsToMove);
        $bar->start();
        
        $processedCount = 0;
        $errorCount = 0;
        
        // Procesar en lotes para manejar grandes volúmenes
        News::where('created_at', '<', $cutoffDate)
            ->chunkById($batchSize, function ($newsChunk) use (&$processedCount, &$errorCount, $bar) {
                DB::beginTransaction();
                
                try {
                    foreach ($newsChunk as $news) {
                        // Verificar si ya existe en histórico
                        $exists = NewsHistoric::where('original_id', $news->id)->exists();
                        
                        if (!$exists) {
                            // Crear el registro histórico completo
                            NewsHistoric::create([
                                'title' => $news->title,
                                'slug' => $news->slug . '-archive-' . uniqid(), // Garantiza unicidad
                                'excerpt' => $news->excerpt,
                                'content' => $news->content,
                                'summary' => $news->summary,
                                'image' => $news->image,
                                'image_caption' => $news->image_caption,
                                'category_id' => $news->category_id,
                                'author_id' => $news->author_id,
                                'views' => $news->views,
                                'status' => $news->status,
                                'tags' => $news->tags,
                                'featured' => $news->featured,
                                'source' => $news->source,
                                'source_url' => $news->source_url,
                                'published_at' => $news->published_at,
                                'reading_time' => $news->reading_time,
                                'original_id' => $news->id,
                                'created_at' => $news->created_at,
                                'updated_at' => $news->updated_at
                            ]);
                            
                            // El Observer se encargará de mantener relaciones
                            
                            // Eliminar la noticia original
                            $news->delete();
                            
                            $processedCount++;
                        }
                        
                        $bar->advance();
                    }
                    
                    DB::commit();
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    $errorCount++;
                    Log::error("Error en news:archive batch: " . $e->getMessage());
                    $this->error("Error en batch: " . $e->getMessage());
                }
            });
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("Archive process completed:");
        $this->info("- {$processedCount} news moved to historic table");
        
        if ($errorCount > 0) {
            $this->error("- {$errorCount} batches had errors (check logs)");
            return 1;
        }
        
        return 0;
    }
}