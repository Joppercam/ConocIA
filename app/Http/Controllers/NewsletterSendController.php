<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterSendController extends Controller
{
    public function form()
    {
        $subscribersCount = Newsletter::where('is_active', true)->count();
        return view('admin.newsletter.send', compact('subscribersCount'));
    }
    
    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:100',
            'news_count' => 'required|integer|min:1|max:10',
        ]);
        
        // Obtener suscriptores activos
        $subscribers = Newsletter::where('is_active', true)->get();
        
        // Obtener las últimas noticias
        $news = News::latest()->take($request->news_count)->get();
        
        if ($news->isEmpty()) {
            return back()->with('error', 'No hay noticias disponibles para enviar');
        }
        
        // Enviar el newsletter a cada suscriptor
        $sentCount = 0;
        foreach ($subscribers as $subscriber) {
            try {
                Mail::to($subscriber->email)
                    ->send(new NewsletterMail($news, $subscriber->token, $request->subject));
                $sentCount++;
            } catch (\Exception $e) {
                // Registro del error en los logs
                \Log::error("Error enviando newsletter a {$subscriber->email}: " . $e->getMessage());
            }
        }
        
        return back()->with('success', "Newsletter enviado correctamente a {$sentCount} suscriptores");
    }
}