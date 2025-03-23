<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email'
        ]);

        $token = Str::random(60);
        
        Newsletter::create([
            'email' => $request->email,
            'token' => $token
        ]);

        // Aquí podrías enviar un correo de confirmación
        // Mail::to($request->email)->send(new ConfirmNewsletter($token));

        return back()->with('newsletter_success', '¡Gracias por suscribirte a nuestro newsletter!');
    }

    public function unsubscribe($token)
    {
        $subscriber = Newsletter::where('token', $token)->first();
        
        if (!$subscriber) {
            return redirect()->route('home')->with('error', 'Enlace inválido');
        }
        
        $subscriber->update(['is_active' => false]);
        
        return redirect()->route('home')->with('success', 'Te has dado de baja correctamente');
    }
    
    // Métodos para el panel admin
    public function index()
    {
        $subscribers = Newsletter::paginate(15);
        return view('admin.newsletter.index', compact('subscribers'));
    }
    
    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();
        return redirect()->route('admin.newsletter.index')->with('success', 'Suscriptor eliminado');
    }
}