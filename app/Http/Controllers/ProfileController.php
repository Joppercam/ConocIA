<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\FileUploadService;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    protected $fileUploadService;
    
    /**
     * Constructor con inyección de dependencias.
     */
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->middleware('auth');
        $this->fileUploadService = $fileUploadService;
    }
    
    /**
     * Mostrar formulario para editar perfil.
     */
    public function edit()
    {
        $user = auth()->user();
        
        return view('profile.edit', compact('user'));
    }
    
    /**
     * Actualizar el perfil del usuario.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        // Validar datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'twitter' => 'nullable|string|max:255',
            'linkedin' => 'nullable|url|max:255',
            'github' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);
        
        // Verificar contraseña actual si se intenta cambiar
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
            }
        }
        
        // Actualizar datos básicos del usuario
        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->bio = $validated['bio'];
        $user->website = $validated['website'];
        $user->twitter = $validated['twitter'];
        $user->linkedin = $validated['linkedin'];
        $user->github = $validated['github'];
        
        // Actualizar contraseña si se proporciona
        if ($request->filled('new_password')) {
            $user->password = Hash::make($validated['new_password']);
        }
        
        // Subir foto de perfil si se proporciona
        if ($request->hasFile('profile_photo')) {
            // Eliminar foto anterior si existe
            if ($user->profile_photo) {
                $this->fileUploadService->deleteFile($user->profile_photo);
            }
            
            $user->profile_photo = $this->fileUploadService->uploadImage(
                $request->file('profile_photo'),
                'profiles',
                400,
                400,
                false // Sin mantener proporción para hacer un cuadrado
            );
        }
        
        $user->save();
        
        return redirect()->route('profile.edit')->with('success', 'Perfil actualizado correctamente.');
    }
    
    /**
     * Eliminar la cuenta del usuario.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);
        
        $user = auth()->user();
        
        // Eliminar foto de perfil si existe
        if ($user->profile_photo) {
            $this->fileUploadService->deleteFile($user->profile_photo);
        }
        
        auth()->logout();
        
        $user->delete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Tu cuenta ha sido eliminada correctamente.');
    }
}
