<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Por favor inicia sesión primero.');
        }
        
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return $next($request);
        }
        
        return redirect()->route('home')->with('error', 'No tienes permisos de administrador.');
    }
}
