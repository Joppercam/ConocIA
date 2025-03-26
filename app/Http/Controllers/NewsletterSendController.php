<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Models\News;
use App\Models\Research;
use App\Models\Column;
use App\Mail\NewsletterMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class NewsletterSendController extends Controller
{
    public function form()
    {
        $subscribersCount = Newsletter::where('is_active', true)->count();
        return view('admin.newsletter.send', compact('subscribersCount'));
    }
    
    public function send(Request $request)
    {
        dd($request);
        $request->validate([
            'subject' => 'required|string|max:100',
            'news_count' => 'required|integer|min:1|max:10',
            'include_research' => 'boolean',
            'include_columns' => 'boolean',
            'research_count' => 'integer|min:0|max:5',
            'columns_count' => 'integer|min:0|max:5',
        ]);
        
        // Obtener suscriptores activos
        $subscribers = Newsletter::where('is_active', true)->get();
        
        if ($subscribers->isEmpty()) {
            return back()->with('error', 'No hay suscriptores activos.');
        }
        
        // Obtener las últimas noticias
        $news = News::published()->latest()->take($request->news_count)->get();
        
        // Obtener noticias destacadas
        $featuredNews = News::published()->featured()->latest()->take(1)->get();
        
        // Obtener investigaciones recientes si se solicitaron
        $researches = collect();
        if ($request->include_research) {
            $researchCount = $request->research_count ?? 2;
            $researches = Research::published()->latest()->take($researchCount)->get();
        }
        
        // Obtener columnas recientes si se solicitaron
        $columns = collect();
        if ($request->include_columns) {
            $columnsCount = $request->columns_count ?? 2;
            $columns = Column::published()->latest()->take($columnsCount)->get();
        }
        
        // Si no hay contenido para enviar
        if ($news->isEmpty() && $researches->isEmpty() && $columns->isEmpty()) {
            return back()->with('error', 'No hay contenido disponible para enviar.');
        }
        
        // Enviar el newsletter a cada suscriptor
        $sentCount = 0;
        $errorCount = 0;
        
        foreach ($subscribers as $subscriber) {
            try {
                // Generar token si no existe
                if (!$subscriber->token) {
                    $subscriber->token = Str::random(60);
                    $subscriber->save();
                }
                
                Mail::to($subscriber->email)
                    ->send(new NewsletterMail(
                        $news,
                        $request->subject,
                        $subscriber->token,
                        $featuredNews,
                        $researches,
                        $columns,
                        $subscriber
                    ));
                $sentCount++;
            } catch (\Exception $e) {
                // Registrar error
                Log::error("Error enviando newsletter a {$subscriber->email}: " . $e->getMessage());
                $errorCount++;
            }
        }
        
        $message = "Newsletter enviado a {$sentCount} suscriptores.";
        if ($errorCount > 0) {
            $message .= " Hubo {$errorCount} errores durante el envío.";
        }
        
        return back()->with('success', $message);
    }
}