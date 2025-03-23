<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SetupDefaultImagesController extends Controller
{
    /**
     * Asegura que existan las imágenes predeterminadas en el sistema
     */
    public function ensureDefaultImagesExist()
    {
        $this->createDefaultAvatar();
        
        return redirect()->back()->with('success', 'Imágenes predeterminadas verificadas y creadas si era necesario.');
    }
    
    /**
     * Crea el avatar predeterminado si no existe
     */
    private function createDefaultAvatar()
    {
        $defaultAvatarPath = 'public/images/defaults/avatar-default.jpg';
        
        // Verifica si el directorio existe, si no, créalo
        $directory = 'public/images/defaults';
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        
        // Si el avatar predeterminado no existe, créalo
        if (!Storage::exists($defaultAvatarPath)) {
            // Crear un avatar simple usando Intervention Image
            $img = Image::canvas(200, 200, '#3490dc'); // Color azul de Laravel
            
            // Añadir iniciales o un icono
            $img->text('U', 100, 100, function($font) {
                $font->file(public_path('fonts/OpenSans-Bold.ttf')); // Asegúrate de que esta fuente exista
                $font->size(120);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('center');
            });
            
            // Si no tienes la fuente o prefieres un enfoque más simple:
            // $img = Image::canvas(200, 200, '#3490dc')->circle(160, 100, 100, function ($draw) {
            //     $draw->background('#ffffff');
            // });
            
            // Guarda la imagen
            $img->save(storage_path('app/'.$defaultAvatarPath));
            
            return true;
        }
        
        return false;
    }
}