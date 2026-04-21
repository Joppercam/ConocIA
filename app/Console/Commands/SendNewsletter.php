<?php

namespace App\Console\Commands;

use App\Models\Newsletter;
use App\Models\News;
use App\Models\Research;
use App\Models\ConocIaPaper;
use App\Models\Startup;
use App\Mail\NewsletterMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SendNewsletter extends Command
{
    protected $signature = 'newsletter:send
                            {--subject= : Asunto del newsletter}
                            {--news=4 : Cantidad de noticias a incluir}
                            {--dry-run : Simular el envío sin enviar emails}';

    protected $description = 'Envía el newsletter con las últimas noticias a los suscriptores activos';

    public function handle()
    {
        $dayName = now()->locale('es')->dayName;
        $date    = now()->locale('es')->isoFormat('D [de] MMMM');
        $subject = $this->option('subject') ?: "ConocIA | {$dayName} {$date}";
        $newsCount = (int) $this->option('news');
        $dryRun    = $this->option('dry-run');

        if ($newsCount < 1 || $newsCount > 10) {
            $this->error('La cantidad de noticias debe estar entre 1 y 10');
            return 1;
        }

        $subscribers = Newsletter::where('is_active', true)->whereNotNull('verified_at')->get();

        if ($subscribers->isEmpty()) {
            $this->error('No hay suscriptores activos.');
            return 1;
        }

        $this->info("Se enviarán emails a {$subscribers->count()} suscriptores.");

        $featuredNews = News::published()->featured()->latest()->take(1)->get();
        $this->info("Noticias destacadas: {$featuredNews->count()}");

        $news = News::published()
            ->when($featuredNews->isNotEmpty(), fn($q) => $q->where('id', '!=', $featuredNews->first()->id))
            ->latest()
            ->take($newsCount)
            ->get();
        $this->info("Noticias recientes: {$news->count()}");

        $papers = ConocIaPaper::published()->latest('published_at')->take(2)->get();
        $this->info("Papers: {$papers->count()}");

        $startup = Startup::active()->where('featured', true)->latest()->first()
            ?? Startup::active()->latest()->first();
        $this->info("Startup: " . ($startup ? $startup->name : 'ninguna'));

        $research = Research::published()->latest()->take(1)->get();
        $this->info("Investigaciones: {$research->count()}");

        if ($news->isEmpty() && $featuredNews->isEmpty()) {
            $this->error('No hay contenido disponible para enviar.');
            return 1;
        }

        $sentCount  = 0;
        $errorCount = 0;

        $this->output->progressStart($subscribers->count());

        foreach ($subscribers as $subscriber) {
            try {
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
                            $papers,
                            $startup,
                            $research,
                            $subscriber
                        ));
                }

                $sentCount++;
            } catch (\Exception $e) {
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
