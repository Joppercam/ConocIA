<?php

namespace App\Console\Commands;

use App\Models\TikTokScript;
use App\Services\TikTokNewsSelector;
use App\Services\TikTokScriptGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateTikTokScripts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tiktok:generate-scripts {--count=3 : Número de guiones a generar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera guiones de TikTok para las noticias más relevantes';

    /**
     * @var TikTokNewsSelector
     */
    protected $newsSelector;

    /**
     * @var TikTokScriptGenerator
     */
    protected $scriptGenerator;

    /**
     * Create a new command instance.
     */
    public function __construct(
        TikTokNewsSelector $newsSelector,
        TikTokScriptGenerator $scriptGenerator
    ) {
        parent::__construct();
        $this->newsSelector = $newsSelector;
        $this->scriptGenerator = $scriptGenerator;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        
        $this->info("Generando $count guiones de TikTok...");
        
        // Obtener noticias recomendadas
        $recommendedNews = $this->newsSelector->getRecommendedNews($count);
        
        if ($recommendedNews->isEmpty()) {
            $this->warn('No se encontraron noticias recomendadas.');
            return;
        }
        
        $this->info("Se encontraron " . $recommendedNews->count() . " noticias candidatas.");
        
        $bar = $this->output->createProgressBar($recommendedNews->count());
        $bar->start();
        
        $generatedCount = 0;
        $errorCount = 0;
        
        foreach ($recommendedNews as $news) {
            try {
                // Verificar si ya existe un guión para esta noticia
                $existingScript = TikTokScript::where('news_id', $news->id)
                    ->whereIn('status', ['draft', 'pending_review', 'approved', 'published'])
                    ->first();
                    
                if ($existingScript) {
                    $this->line(" <comment>La noticia '{$news->title}' ya tiene un guión.</comment>");
                    continue;
                }
                
                // Generar guión
                $script = $this->scriptGenerator->generateScript($news);
                
                if ($script) {
                    $generatedCount++;
                }
                
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Error generando guión TikTok para noticia #{$news->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->line('');
        
        $this->info("Proceso completado: $generatedCount guiones generados, $errorCount errores.");
    }
}