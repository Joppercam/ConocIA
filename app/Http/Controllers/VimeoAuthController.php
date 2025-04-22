<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Vimeo\Vimeo;

class VimeoAuthController extends Controller
{
    protected $vimeo;

    public function __construct()
    {
        $this->vimeo = new Vimeo(
            config('services.vimeo.client_id'),
            config('services.vimeo.client_secret'),
            config('services.vimeo.access_token')
        );
    }

    public function redirect()
    {
        $callback_url = url('/vimeo/callback');
        $scopes = ['public', 'private'];
        $state = csrf_token();
        
        $url = $this->vimeo->buildAuthorizationEndpoint($callback_url, $scopes, $state);
        return redirect($url);
    }

    public function callback(Request $request)
    {
        if ($request->input('state') !== csrf_token()) {
            return redirect('/')->with('error', 'Estado inválido');
        }

        try {
            $response = $this->vimeo->accessToken($request->input('code'), url('/vimeo/callback'));
            
            if (isset($response['body']['access_token'])) {
                // Guardar el token en la base de datos o en la sesión
                session(['vimeo_token' => $response['body']['access_token']]);
                
                return redirect('/dashboard')->with('success', 'Autenticación con Vimeo completada');
            }
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Error en la autenticación: ' . $e->getMessage());
        }
        
        return redirect('/')->with('error', 'Error en la autenticación');
    }
}