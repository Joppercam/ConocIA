<?php

namespace App\Console\Commands;

use App\Models\Newsletter;
use App\Models\News;
use App\Models\Research;
use App\Models\Column;
use App\Models\Category;
use App\Mail\NewsletterMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SendNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:send 
                            {--subject= : Asunto del newsletter}
                            {--news=5 : Cantidad de noticias a incluir}
                            {--research=2 : Cantidad de investigaciones a incluir}
                            {--columns=2 : Cantidad de columnas a incluir}
                            {--include-research : Incluir investigaciones recientes}
                            {--include-columns : Incluir columnas recientes}
                            {--category=* : IDs de categorías para filtrar suscriptores}
                            {--dry-run : Simular el envío sin enviar emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía el newsletter con las últimas noticias a los suscriptores activos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Obtener opciones
        $subject = $this->option('subject') ?: 'Boletín de Noticias - ' . now()->format('d/m/Y');
        $newsCount = (int) $this->option('news');
        $includeResearch = $this->option('include-research');
        $includeColumns = $this->option('include-columns');
        $researchCount = (int) $this->option('research');
        $columnsCount = (int) $this->option('columns');
        $categoryIds = $this->option('category');
        $dryRun = $this->option('dry-run');

        // Validar parámetros
        if ($newsCount < 1 || $newsCount > 10) {
            $this->error('La cantidad de noticias debe estar entre 1 y 10');
            return 1;
        }

        if ($researchCount < 0 || $researchCount > 5) {
            $this->error('La cantidad de investigaciones debe estar entre 0 y 5');
            return 1;
        }

        if ($columnsCount < 0 || $columnsCount > 5) {
            $this->error('La cantidad de columnas debe estar entre 0 y 5');
            return 1;
        }

        // Verificar categorías si se especificaron
        if (!empty($categoryIds)) {
            $validCategories = Category::whereIn('id', $categoryIds)->count();
            
            if ($validCategories !== count($categoryIds)) {
                $this->error('Una o más categorías especificadas no existen');
                return 1;
            }
            
            $this->info('Filtrando por ' . $validCategories . ' categorías');
        }

        // Obtener suscriptores activos
        $query = Newsletter::where('is_active', true);
        
        // Filtrar por categorías si se especificaron
        if (!empty($categoryIds)) {
            $query->whereHas('categories', function($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });
        }
        
        $subscribers = $query->get();
        
        if ($subscribers->isEmpty()) {
            $this->error('No hay suscriptores activos' . 
                         (!empty($categoryIds) ? ' para las categorías seleccionadas' : '') . '.');
            return 1;
        }

        $this->info("Se enviarán emails a {$subscribers->count()} suscriptores.");
        
        // Obtener las últimas noticias
        $news = News::published()->latest()->take($newsCount)->get();
        $this->info("Noticias encontradas: {$news->count()}");
        
        // Obtener noticias destacadas
        $featuredNews = News::published()->featured()->latest()->take(1)->get();
        $this->info("Noticias destacadas: {$featuredNews->count()}");
        
        // Obtener investigaciones recientes si se solicitaron
        $researches = collect();
        if ($includeResearch) {
            $researches = Research::published()->latest()->take($researchCount)->get();
            $this->info("Investigaciones encontradas: {$researches->count()}");
        }
        
        // Obtener columnas recientes si se solicitaron
        $columns = collect();
        if ($includeColumns) {
            $columns = Column::published()->latest()->take($columnsCount)->get();
            $this->info("Columnas encontradas: {$columns->count()}");
        }
        
        // Si no hay contenido para enviar
        if ($news->isEmpty() && $researches->isEmpty() && $columns->isEmpty()) {
            $this->error('No hay contenido disponible para enviar.');
            return 1;
        }
        
        // Enviar el newsletter a cada suscriptor
        $sentCount = 0;
        $errorCount = 0;
        
        $this->output->progressStart($subscribers->count());
        
        foreach ($subscribers as $subscriber) {
            try {
                // Generar token si no existe
                if (!$subscriber->token) {
                    $subscriber->token = Str::random(60);
                    $subscriber->save();
                }
                
                if (!$dryRun) {
                    Mail::to($subscriber->email)
                        ->send(new NewsletterMail(
                            $news,
                            $subject,
                            $subscriber->token,
                            $featuredNews,
                            $researches,
                            $columns,
                            $subscriber
                        ));
                }
                
                $sentCount++;
            } catch (\Exception $e) {
                // Registrar error
                Log::error("Error enviando newsletter a {$subscriber->email}: " . $e->getMessage());
                $this->error("Error enviando a {$subscriber->email}: " . $e->getMessage());
                $errorCount++;
            }
            
            $this->output->progressAdvance();
        }
        
        $this->output->progressFinish();
        
        if ($dryRun) {
            $this->info("SIMULACIÓN: Se enviaría el newsletter a {$sentCount} suscriptores.");
        } else {
            $this->info("Newsletter enviado a {$sentCount} suscriptores.");
        }
        
        if ($errorCount > 0) {
            $this->warn("Hubo {$errorCount} errores durante el envío.");
        }
        
        return 0;
    }
}