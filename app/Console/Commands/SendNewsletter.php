<?php

namespace App\Console\Commands;

use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\News;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendNewsletter extends Command
{
    protected $signature = 'newsletter:send {--count=5 : Cantidad de noticias a incluir} {--subject=Últimas noticias de ConocIA : Asunto del correo}';
    protected $description = 'Envía el newsletter con las últimas noticias a todos los suscriptores activos';

    public function handle()
    {
        $count = $this->option('count');
        $subject = $this->option('subject');
        
        $subscribers = Newsletter::where('is_active', true)->get();
        
        if ($subscribers->isEmpty()) {
            $this->error('No hay suscriptores activos.');
            return Command::FAILURE;
        }
        
        $news = News::latest()->take($count)->get();
        
        if ($news->isEmpty()) {
            $this->error('No hay noticias disponibles para enviar.');
            return Command::FAILURE;
        }
        
        $this->info("Enviando newsletter a {$subscribers->count()} suscriptores...");
        $progressBar = $this->output->createProgressBar($subscribers->count());
        $progressBar->start();
        
        $sentCount = 0;
        $errorCount = 0;
        
        foreach ($subscribers as $subscriber) {
            try {
                // Si el token es null, generar uno nuevo
                if ($subscriber->token === null) {
                    $subscriber->token = Str::random(60);
                    $subscriber->save();
                }
                
                Mail::to($subscriber->email)
                    ->send(new NewsletterMail($news, $subject, $subscriber->token));
                $sentCount++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("Error enviando a {$subscriber->email}: " . $e->getMessage());
                $errorCount++;
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Newsletter enviado a {$sentCount} suscriptores.");
        if ($errorCount > 0) {
            $this->warn("Hubo {$errorCount} errores durante el envío.");
        }
        
        return Command::SUCCESS;
    }
}