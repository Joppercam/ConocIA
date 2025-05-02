<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Podcast;
use Illuminate\Http\Request;

class SpotifyController extends Controller
{
    /**
     * Muestra el dashboard de integración con Spotify
     */
    public function dashboard()
    {
        // Obtener todos los podcasts
        $podcasts = Podcast::orderBy('created_at', 'desc')->get();
        
        return view('admin.spotify.dashboard', compact('podcasts'));
    }
    
    /**
     * Compartir un podcast en Spotify
     */
    public function share(Podcast $podcast)
    {
        // Preparamos los enlaces para compartir
        $spotifyShareUrl = 'https://open.spotify.com/show/conocia?si=podcast_' . $podcast->id;
        
        return view('admin.spotify.share', compact('podcast', 'spotifyShareUrl'));
    }
}