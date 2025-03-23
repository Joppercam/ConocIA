<?php

namespace App\Http\Controllers\Admin;

use App\Models\Newsletter;
use App\Http\Controllers\Controller;
use App\Mail\NewsletterMail;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class NewsletterAdminController extends Controller
{
    public function index()
    {
        $subscribers = Newsletter::latest()->paginate(15);
        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();
        return back()->with('success', 'Suscriptor eliminado correctamente');
    }
    
    public function toggleActive(Newsletter $newsletter)
    {
        $newsletter->update(['is_active' => !$newsletter->is_active]);
        $status = $newsletter->is_active ? 'activado' : 'desactivado';
        return back()->with('success', "Suscriptor {$status} correctamente");
    }

    public function showSendForm()
    {
        $subscribersCount = Newsletter::where('is_active', true)->count();
        return view('admin.newsletter.send', compact('subscribersCount'));
    }
    
    public function sendNewsletter(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:100',
            'news_count' => 'required|integer|min:1|max:10',
        ]);
        
        // Obtener suscriptores activos
        $subscribers = Newsletter::where('is_active', true)->get();
        
        if ($subscribers->isEmpty()) {
            return back()->with('error', 'No hay suscriptores activos.');
        }
        
        // Obtener las últimas noticias
        $news = News::latest()->take($request->news_count)->get();
        
        if ($news->isEmpty()) {
            return back()->with('error', 'No hay noticias disponibles para enviar.');
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
                    ->send(new NewsletterMail($news, $request->subject, $subscriber->token));
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