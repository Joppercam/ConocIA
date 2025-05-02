<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Podcast;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class PodcastController extends Controller
{
    /**
     * Muestra la lista de podcasts en el panel de administración
     */
    public function index()
    {
        $podcasts = Podcast::with('news')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
                    
        return view('admin.podcasts.index', compact('podcasts'));
    }
    
    /**
     * Muestra el formulario para crear un nuevo podcast manualmente
     */
    public function create()
    {
        // Obtener noticias que aún no tienen podcast
        $news = News::whereDoesntHave('podcast')
                ->orderBy('created_at', 'desc')
                ->get();
                
        return view('admin.podcasts.create', compact('news'));
    }
    
    /**
     * Almacena un nuevo podcast
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'news_id' => 'required|exists:news,id',
            'voice' => 'required|string',
        ]);
        
        $news = News::findOrFail($validated['news_id']);
        
        // Preparar el contenido
        $content = strip_tags($news->content);
        $introduction = "A continuación escuchará la noticia titulada: {$news->title}. ";
        $ending = " Esta ha sido una noticia de nuestro portal. Gracias por escuchar.";
        $fullContent = $introduction . $content . $ending;
        
        // Convertir a audio
        try {
            $apiKey = config('services.openai.api_key');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/audio/speech', [
                'model' => 'tts-1',
                'input' => $fullContent,
                'voice' => $validated['voice'],
                'response_format' => 'mp3',
            ]);
            
            if ($response->successful()) {
                // Crear nombre de archivo único
                $fileName = 'podcasts/' . date('Y-m-d') . '/' . Str::slug($news->id) . '-' . Str::random(8) . '.mp3';
                
                // Guardar el archivo de audio
                Storage::put('public/' . $fileName, $response->body());
                
                // Crear el podcast
                $podcast = Podcast::create([
                    'news_id' => $news->id,
                    'title' => $news->title,
                    'audio_path' => $fileName,
                    'duration' => 0, // La duración se calculará después
                    'published_at' => now(),
                ]);
                
                // Limpiar caché
                $this->clearPodcastCache();
                
                return redirect()->route('admin.podcasts.index')
                        ->with('success', 'Podcast creado con éxito');
            } else {
                return back()->withErrors(['api_error' => 'Error en la API de OpenAI: ' . $response->body()])
                        ->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al generar el podcast: ' . $e->getMessage()])
                    ->withInput();
        }
    }
    
    /**
     * Muestra un podcast específico
     */
    public function show(Podcast $podcast)
    {
        return view('admin.podcasts.show', compact('podcast'));
    }
    
    /**
     * Elimina un podcast
     */
    public function destroy(Podcast $podcast)
    {
        try {
            // Eliminar el archivo de audio
            if (Storage::exists('public/' . $podcast->audio_path)) {
                Storage::delete('public/' . $podcast->audio_path);
            }
            
            // Eliminar el registro
            $podcast->delete();
            
            // Limpiar caché
            $this->clearPodcastCache();
            
            return redirect()->route('admin.podcasts.index')
                    ->with('success', 'Podcast eliminado con éxito');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el podcast: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Ejecuta la generación de podcasts manualmente
     */
    public function generatePodcasts()
    {
        try {
            // Ejecutar el comando
            \Artisan::call('podcasts:generate');
            
            // Obtener la salida del comando
            $output = \Artisan::output();
            
            // Limpiar caché
            $this->clearPodcastCache();
            
            return back()->with('success', 'Proceso de generación de podcasts ejecutado con éxito')
                    ->with('command_output', $output);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al ejecutar el proceso: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Limpia la caché relacionada con podcasts
     */
    private function clearPodcastCache()
    {
        Cache::forget('podcasts.recent');
        Cache::forget('podcasts.popular');
        Cache::forget('podcasts.feed');
    }
}