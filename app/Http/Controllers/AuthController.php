<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de inicio de sesión.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    /**
     * Procesar inicio de sesión.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        $remember = $request->boolean('remember');
        
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Verificar si el usuario está activo
            if (!Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'email' => 'Esta cuenta está desactivada. Contacta al administrador.',
                ]);
            }
            
            return redirect()->intended('/');
        }
        
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }
    
    /**
     * Mostrar formulario de registro.
     */
    public function showRegistrationForm()
    {
        $intended = session('url.intended', '');
        $fromCourse = str_contains($intended, '/cursos/');
        return view('auth.register', compact('fromCourse', 'intended'));
    }
    
    /**
     * Procesar registro de usuario.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Generar username único a partir del email
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $validated['email'])[0]));
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i++;
        }

        // Obtener el rol de usuario normal
        $userRole = Role::where('slug', 'user')->first();

        if (!$userRole) {
            return back()->withErrors(['error' => 'Error al registrar usuario. Contacta al administrador.']);
        }

        $user = User::create([
            'name'     => $validated['name'],
            'username' => $username,
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id'  => $userRole->id,
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended('/');
    }
    
    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
