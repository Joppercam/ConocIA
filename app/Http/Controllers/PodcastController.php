<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PodcastController extends Controller
{
    /**
     * Muestra la lista de podcasts
     */
    public function index()
    {
        // Usar caché para mejorar rendimiento
        $podcasts = Cache::remember('podcasts.recent', 60*60, function () {
            return Podcast::with('news')
                    ->orderBy('published_at', 'desc')
                    ->paginate(10);
        });
        
        return view('podcasts.index', compact('podcasts'));
    }
    
    /**
     * Muestra un podcast específico
     */
    public function show(Podcast $podcast)
    {
        return view('podcasts.show', compact('podcast'));
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
}