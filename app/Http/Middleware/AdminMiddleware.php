<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            // El usuario no está autenticado
            return redirect()->route('login')->with('error', 'Por favor inicia sesión primero.');
        }
        
        $user = Auth::user();
        // Registra información para depuración
        Log::info('User Role ID: ' . $user->role_id);
        Log::info('isAdmin check: ' . ($user->isAdmin() ? 'true' : 'false'));
        
        if ($user->isAdmin()) {
            return $next($request);
        }
        
        return redirect()->route('home')->with('error', 'No tienes permisos de administrador.');
    }
}
