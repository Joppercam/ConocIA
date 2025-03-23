<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

abstract class BaseAdminController extends Controller
{
    /**
     * Subir y procesar imagen
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @param array $sizes
     * @return string
     */
    protected function uploadImage($file, $folder = 'images', $sizes = ['small', 'medium', 'large'])
    {
        // Generar nombre de archivo único
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        // Ruta base para almacenar
        $basePath = "public/{$folder}";
        
        // Procesar y guardar diferentes tamaños
        foreach ($sizes as $size) {
            // Definir dimensiones
            $dimensions = $this->getImageDimensions($size);
            
            // Crear imagen con Intervention
            $image = Image::make($file)
                ->resize($dimensions['width'], $dimensions['height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode($file->getClientOriginalExtension(), 75); // Comprimir
            
            // Guardar imagen en storage
            Storage::put("{$basePath}/{$size}/{$filename}", $image);
        }
        
        return $filename;
    }
    
    /**
     * Obtener dimensiones para diferentes tamaños de imagen
     * 
     * @param string $size
     * @return array
     */
    protected function getImageDimensions($size)
    {
        $dimensions = [
            'small' => ['width' => 300, 'height' => 200],
            'medium' => ['width' => 800, 'height' => 500],
            'large' => ['width' => 1200, 'height' => 800]
        ];
        
        return $dimensions[$size] ?? $dimensions['medium'];
    }
    
    /**
     * Eliminar imágenes existentes
     * 
     * @param string $filename
     * @param string $folder
     * @param array $sizes
     */
    protected function deleteImage($filename, $folder = 'images', $sizes = ['small', 'medium', 'large'])
    {
        if (!$filename) return;
        
        $basePath = "public/{$folder}";
        
        foreach ($sizes as $size) {
            $fullPath = "{$basePath}/{$size}/{$filename}";
            
            if (Storage::exists($fullPath)) {
                Storage::delete($fullPath);
            }
        }
    }
    
    /**
     * Generar slug único
     * 
     * @param string $title
     * @param string $model
     * @param int|null $exceptId
     * @return string
     */
    protected function generateUniqueSlug($title, $model, $exceptId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;
        
        // Usar la clase del modelo para consultas dinámicas
        $query = $model::where('slug', $slug);
        
        // Excluir el ID actual en caso de actualización
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        
        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count;
            $query = $model::where('slug', $slug);
            
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }
            
            $count++;
        }
        
        return $slug;
    }

    /**
     * Método para manejar acciones en lote
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $model
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleBulkActions($request, $model)
    {
        $action = $request->input('action');
        $selectedItems = $request->input('selected', []);

        if (empty($selectedItems)) {
            return back()->with('error', 'No se seleccionaron elementos.');
        }

        switch ($action) {
            case 'delete':
                $model::whereIn('id', $selectedItems)->delete();
                $message = 'Elementos seleccionados eliminados exitosamente.';
                break;

            case 'publish':
                $model::whereIn('id', $selectedItems)->update(['is_published' => true]);
                $message = 'Elementos seleccionados publicados.';
                break;

            case 'unpublish':
                $model::whereIn('id', $selectedItems)->update(['is_published' => false]);
                $message = 'Elementos seleccionados despublicados.';
                break;

            default:
                return back()->with('error', 'Acción no válida.');
        }

        return back()->with('success', $message);
    }
}