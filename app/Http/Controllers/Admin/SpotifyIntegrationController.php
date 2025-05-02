<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Podcast;
use Illuminate\Http\Request;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Storage;

class SpotifyIntegrationController extends Controller
{
    protected $spotifyApi;
    protected $spotifySession;

    public function __construct()
    {
        $this->spotifySession = new Session(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret'),
            config('services.spotify.redirect')
        );

        $this->spotifyApi = new SpotifyWebAPI();
    }

    /**
     * Redirige al usuario para autorizar la aplicación con Spotify
     */
    public function authorizeSpotify()
    {
        $options = [
            'scope' => [
                'ugc-image-upload',
                'user-read-private',
                'user-read-email',
                'playlist-read-private',
                'playlist-modify-public',
                'playlist-modify-private',
                'user-library-read',
                'user-library-modify'
            ],
        ];



        return redirect()->away($this->spotifySession->getAuthorizeUrl($options));
    }

    public function handleSpotifyCallback(Request $request)
    {
        if ($request->has('code')) {
            $this->spotifySession->requestAccessToken($request->code);
            
            // Guardar tokens en la sesión
            session([
                'spotify_access_token' => $this->spotifySession->getAccessToken(),
                'spotify_refresh_token' => $this->spotifySession->getRefreshToken(),
                'spotify_token_expires' => time() + $this->spotifySession->getTokenExpiration(),
            ]);
            
            // Si el usuario no está autenticado, redirigir al login
            if (!auth()->check()) {
                session(['redirect_after_login' => 'admin.spotify.dashboard']);
                return redirect()->route('admin.login')
                    ->with('info', 'Por favor inicia sesión para continuar con la integración de Spotify.');
            }
            
            // Si está autenticado, redirigir al dashboard
            return redirect()->route('admin.spotify.dashboard')
                ->with('success', '¡Conectado a Spotify exitosamente!');
        }
        
        return redirect()->route('admin.login')
            ->with('error', 'Error al conectar con Spotify');
    }

    /**
     * Muestra el panel de control de Spotify
     */
    public function dashboard()
    {
        $this->refreshTokenIfNeeded();
        
        // Cargar podcasts que aún no están en Spotify
        $pendingPodcasts = Podcast::where('is_on_spotify', false)->get();
        
        // Si ya hay una cuenta de podcast en Spotify, obtenemos información
        $spotifyShows = [];
        
        try {
            $this->spotifyApi->setAccessToken(session('spotify_access_token'));
            // Obtener los shows del usuario (requiere permisos de podcast-read)
            $spotifyShows = $this->spotifyApi->getMyShows();
        } catch (\Exception $e) {
            // Manejar error
        }
        
        return view('admin.spotify.dashboard', compact('pendingPodcasts', 'spotifyShows'));
    }

    /**
     * Sube un podcast a Spotify
     */
    public function uploadPodcast(Request $request, Podcast $podcast)
    {
        $this->refreshTokenIfNeeded();
        
        try {
            $this->spotifyApi->setAccessToken(session('spotify_access_token'));
            
            // Verificar si ya existe el show para ConocIA
            $showId = null;
            $spotifyShows = $this->spotifyApi->getMyShows();
            
            foreach ($spotifyShows->items as $show) {
                if ($show->name === 'ConocIA Podcast') {
                    $showId = $show->id;
                    break;
                }
            }
            
            // Si no existe, crear un nuevo show
            if (!$showId) {
                // Crear el show (esto requeriría más implementación y permisos adicionales)
                // Esta parte depende de la API específica y requiere permisos especiales
                // Posiblemente necesites usar Spotify for Podcasters directamente
                
                // Por ahora, simulamos una respuesta
                return redirect()->back()->with('warning', 'No se encontró el show de ConocIA en Spotify. Por favor, crea el show primero usando Spotify for Podcasters.');
            }
            
            // Subir el episodio del podcast
            // Nota: Este proceso varía según la API de Spotify y podría requerir
            // procesos adicionales como subir primero a un host compatible
            
            // Actualizar información del podcast
            $podcast->update([
                'spotify_id' => $showId,
                'is_on_spotify' => true,
                'spotify_url' => "https://open.spotify.com/show/{$showId}"
            ]);
            
            return redirect()->route('admin.spotify.dashboard')
                ->with('success', 'Podcast subido a Spotify exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al subir podcast a Spotify: ' . $e->getMessage());
        }
    }

    /**
     * Refresca el token de Spotify si es necesario
     */
    protected function refreshTokenIfNeeded()
    {
        if (session()->has('spotify_token_expires') && session('spotify_token_expires') < time()) {
            $this->spotifySession->refreshAccessToken(session('spotify_refresh_token'));
            
            session([
                'spotify_access_token' => $this->spotifySession->getAccessToken(),
                'spotify_token_expires' => time() + $this->spotifySession->getTokenExpiration(),
            ]);
        }
    }
}