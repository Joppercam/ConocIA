<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\News;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

class SimpleAdminController extends Controller
{
    /**
     * Mostrar el formulario de login
     */
    public function showLoginForm()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.simple_login');
    }

    /**
     * Manejar la solicitud de login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Login manual - sin usar el sistema completo de Auth
        $user = User::where('email', $credentials['email'])->first();
        
        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Login manual
            Auth::login($user);
            
            // Verificar si es admin
            if ($user->isAdmin()) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }
            
            // Si no es admin, hacer logout
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return back()->withErrors([
                'email' => 'No tienes permisos de administrador.',
            ]);
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Dashboard simplificado
     */
    public function dashboard()
    {
        // Si no es admin, redirigir al login
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('admin.login');
        }
        
        // Datos básicos para el dashboard
        $stats = [
            'total_news' => News::count(),
            'total_categories' => Category::count(),
            'total_users' => User::count(),
        ];
        
        // Noticias recientes
        $recentNews = News::orderBy('created_at', 'desc')->limit(5)->get();
        
        return view('admin.simple_dashboard', compact('stats', 'recentNews'));
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}