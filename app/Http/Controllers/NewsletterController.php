<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        // Validación básica
        $request->validate([
            'email' => 'required|email'
        ]);
        
        $email = $request->input('email');
        
        // Buscar si el email ya existe en la base de datos
        $existingSubscription = Newsletter::where('email', $email)->first();
        
        if ($existingSubscription) {
            // El usuario ya está suscrito
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'info' => true,
                    'message' => 'Este correo electrónico ya está suscrito a nuestro newsletter.'
                ]);
            }
            
            return redirect()->back()->with('subscription_info', 'Este correo electrónico ya está suscrito a nuestro newsletter.');
        }
        
        try {
            // Generar token único para cancelación
            $token = Str::random(40);
            
            // Si no existe, crear nueva suscripción con los nombres correctos de columnas
            Newsletter::create([
                'email' => $email,
                'is_active' => true,      // Usar 'is_active' en lugar de 'active'
                'token' => $token
                // No se incluye 'verified_at' porque normalmente se establece cuando el usuario verifica
            ]);
            
            // Responder según tipo de solicitud
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Te has suscrito correctamente. ¡Gracias por unirte a nuestro newsletter!'
                ]);
            }
            
            return redirect()->back()->with('subscription_success', 'Te has suscrito correctamente. ¡Gracias por unirte a nuestro newsletter!');
        } catch (\Exception $e) {
            \Log::error('Error al suscribir newsletter: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hubo un problema al procesar tu solicitud. Por favor intenta nuevamente.'
                ]);
            }
            
            return redirect()->back()->with('subscription_error', 'Hubo un problema al procesar tu solicitud. Por favor intenta nuevamente.');
        }
    }

    public function unsubscribe($token)
    {
        $subscriber = Newsletter::where('token', $token)->first();
        
        if (!$subscriber) {
            return redirect()->route('home')->with('error', 'Enlace inválido');
        }
        
        // Actualizar 'is_active' en lugar de 'active'
        $subscriber->update([
            'is_active' => false
            // No se cambia 'verified_at', ya que parece que tienes un concepto diferente de verificación
        ]);
        
        return redirect()->route('home')->with('success', 'Te has dado de baja correctamente');
    }
    
    // Los otros métodos se mantienen sin cambios
}