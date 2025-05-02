<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PodcastController extends Controller
{
    public function index()
    {
        $podcasts = Cache::remember('podcasts.recent', 60*60, function () {
            return Podcast::with('news')
                    ->orderBy('published_at', 'desc')
                    ->paginate(10);
        });
        
        return view('podcasts.index', compact('podcasts'));
    }
    
    public function show(Podcast $podcast)
    {
        // Cargar las noticias relacionadas
        $podcast->load('news');
        
        // Funciones auxiliares para la vista
        $getCategoryIcon = function($category) {
            $icons = [
                'Política' => 'fa-landmark',
                'Economía' => 'fa-chart-line',
                'Tecnología' => 'fa-microchip',
                'Salud' => 'fa-heartbeat',
                'Ciencia' => 'fa-flask',
                'Deportes' => 'fa-futbol',
                'Cultura' => 'fa-theater-masks',
                'Internacional' => 'fa-globe-americas',
            ];
            
            return $icons[$category->name] ?? 'fa-tag';
        };
        
        $getCategoryStyle = function($category) {
            $styles = [
                'Política' => 'background-color: #007bff;',
                'Economía' => 'background-color: #28a745;',
                'Tecnología' => 'background-color: #6610f2;',
                'Salud' => 'background-color: #dc3545;',
                'Ciencia' => 'background-color: #17a2b8;',
                'Deportes' => 'background-color: #fd7e14;',
                'Cultura' => 'background-color: #e83e8c;',
                'Internacional' => 'background-color: #20c997;',
            ];
            
            return $styles[$category->name] ?? 'background-color: #6c757d;';
        };
        
        return view('podcasts.show', compact('podcast', 'getCategoryIcon', 'getCategoryStyle'));
    }
    
    /**
     * Registra la reproducción de un podcast e incrementa su contador
     */
    public function play(Podcast $podcast)
    {
        $podcast->incrementPlayCount();
        
        // Limpiar caché relacionada
        Cache::forget('podcasts.popular');
        
        return response()->json([
            'success' => true,
            'play_count' => $podcast->play_count
        ]);
    }
    
    /**
     * Devuelve los 5 podcasts más reproducidos
     */
    public function popular()
    {
        $popularPodcasts = Cache::remember('podcasts.popular', 60*60, function () {
            return Podcast::orderBy('play_count', 'desc')
                    ->take(5)
                    ->get();
        });
        
        return response()->json($popularPodcasts);
    }
    
    /**
     * Genera feed RSS para podcasts
     */
    public function feed()
    {
        $podcasts = Cache::remember('podcasts.feed', 60*60, function () {
            return Podcast::with('news')
                    ->orderBy('published_at', 'desc')
                    ->take(50)
                    ->get();
        });
        
        return response()->view('podcasts.feed', compact('podcasts'))
                ->header('Content-Type', 'application/xml');
    }


    /**
     * Registra una reproducción del podcast
     *
     * @param  \App\Models\Podcast  $podcast
     * @return \Illuminate\Http\Response
     */
    public function registerPlay(Podcast $podcast)
    {
        // Incrementar el contador de reproducciones
        $podcast->increment('play_count');
        
        return response()->json([
            'success' => true,
            'play_count' => $podcast->play_count
        ]);
    }


    /**
     * Almacena un nuevo podcast de resumen diario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'voice' => 'required|string',
        ]);
        
        // Preparar el contenido del resumen diario
        $introduction = "A continuación escuchará el resumen de noticias del día " . now()->format('d \d\e F \d\e Y') . ". ";
        
        // Aquí podrías obtener un resumen de las noticias recientes
        // Por ejemplo:
        $recentNews = News::where('created_at', '>=', now()->subDay())
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
        
        $content = "";
        foreach ($recentNews as $index => $news) {
            $content .= "Noticia " . ($index + 1) . ": " . $news->title . ". " . strip_tags($news->summary ?: substr($news->content, 0, 150)) . ". ";
        }
        
        $ending = " Este ha sido el resumen diario de noticias. Gracias por escuchar.";
        $fullContent = $introduction . $content . $ending;
        
        // Convertir a audio usando OpenAI
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
                $fileName = 'podcasts/' . date('Y-m-d') . '/resumen-diario-' . Str::random(8) . '.mp3';
                
                // Guardar el archivo de audio
                Storage::put('public/' . $fileName, $response->body());
                
                // Crear el podcast
                $podcast = Podcast::create([
                    'news_id' => null, // Ya no asociamos a una noticia específica
                    'title' => $validated['title'],
                    'audio_path' => $fileName,
                    'duration' => 0, // La duración se calculará después
                    'published_at' => now(),
                    'voice' => $validated['voice'],
                    'is_daily_summary' => true
                ]);
                
                // Limpiar caché
                $this->clearPodcastCache();
                
                return redirect()->route('admin.podcasts.index')
                        ->with('success', 'Resumen diario creado con éxito');
            } else {
                return back()->withErrors(['api_error' => 'Error en la API de OpenAI: ' . $response->body()])
                        ->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al generar el resumen diario: ' . $e->getMessage()])
                    ->withInput();
        }
    }


}