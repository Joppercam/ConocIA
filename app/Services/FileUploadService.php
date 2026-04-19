<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileUploadService
{
    /**
     * Subir y optimizar una imagen
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param int $width
     * @param int $height
     * @param bool $keepAspectRatio
     * @return string
     */
    public function uploadImage(
        UploadedFile $file, 
        string $directory, 
        int $width = 1200, 
        int $height = 800, 
        bool $keepAspectRatio = true
    ): string {
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();

        $image = Image::make($file);

        if ($keepAspectRatio) {
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        } else {
            $image->fit($width, $height);
        }

        $imageData = $image->encode(null, 80);
        $disk = env('CLOUDFLARE_R2_KEY') ? 'custom_public' : 'public';
        Storage::disk($disk)->put("{$directory}/{$filename}", (string) $imageData, 'public');

        return "{$directory}/{$filename}";
    }
    
    /**
     * Subir un archivo PDF
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string
     */
    public function uploadPdf(UploadedFile $file, string $directory): string
    {
        // Crear un nombre único para el archivo
        $filename = Str::random(20) . '.pdf';
        
        // Guardar el archivo
        $path = $file->storeAs("public/{$directory}", $filename);
        
        // Devolver ruta relativa
        return "{$directory}/{$filename}";
    }
    
    /**
     * Eliminar un archivo
     *
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $path): bool
    {
        if (Storage::exists("public/{$path}")) {
            return Storage::delete("public/{$path}");
        }
        
        return false;
    }
}
