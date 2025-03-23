<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class NewsApiController extends Controller
{
    public function index()
    {
        // Obtener las categorías desde la base de datos
        $categories = Category::all();
        
        // Obtener la lista de categorías disponibles desde el comando
        $availableCategories = $this->getAvailableCategories();
        
        return view('admin.api.index', compact('categories', 'availableCategories'));
    }
    
    public function execute(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'count' => 'required|integer|min:1|max:10',
            'language' => 'required|string|in:es,en'
        ]);
        
        // Asegurarse de que la base de datos esté configurada para colas
        if (config('queue.default') == 'sync') {
            return back()->with('error', 'Las colas deben estar configuradas para database o redis. Por favor revisa la configuración QUEUE_CONNECTION.');
        }
        
        try {
            // Ejecutar el comando como un job en segundo plano
            \Illuminate\Support\Facades\Bus::dispatch(
                new \App\Jobs\ProcessApiCommand(
                    $request->category, 
                    $request->count, 
                    $request->language
                )
            );
            
            // Registrar la actividad
            Log::info('Comando de API de noticias puesto en cola', [
                'category' => $request->category,
                'count' => $request->count,
                'language' => $request->language,
                'user_id' => auth()->id()
            ]);
            
            return back()->with('success', 'El comando de API de noticias se ha puesto en cola. Las noticias se procesarán en segundo plano.');
        } catch (\Exception $e) {
            Log::error('Error al encolar comando de API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    private function getAvailableCategories()
    {
        // Esta función obtiene las categorías disponibles directamente del comando
        $output = [];
        Artisan::call('news:fetch', [], $output);
        
        // Extraer las categorías del output
        $categories = [];
        $currentGroup = null;
        
        foreach ($output as $line) {
            if (strpos($line, 'Categorías ') === 0) {
                $currentGroup = trim($line, ':');
                $categories[$currentGroup] = [];
            } elseif (strpos($line, ' - ') === 0 && $currentGroup) {
                $categoryInfo = trim(substr($line, 3));
                if (preg_match('/^([a-z0-9-]+) \((.+)\)$/', $categoryInfo, $matches)) {
                    $categories[$currentGroup][] = [
                        'slug' => $matches[1],
                        'name' => $matches[2]
                    ];
                }
            }
        }
        
        return $categories;
    }
}