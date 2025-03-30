<?php
// app/Http/Middleware/CanonicalUrls.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CanonicalUrls
{
    /**
     * Manejar la solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener la respuesta
        $response = $next($request);
        
        // Solo procesar respuestas HTML
        if (!$this->isHtmlResponse($response)) {
            return $response;
        }
        
        // Verificar si hay parámetros de URL innecesarios que deberían redirigirse
        if ($this->shouldRedirect($request)) {
            $canonicalUrl = $this->getCanonicalUrl($request);
            return redirect($canonicalUrl, 301);
        }
        
        return $response;
    }
    
    /**
     * Verificar si la respuesta es HTML
     *
     * @param  mixed  $response
     * @return bool
     */
    protected function isHtmlResponse($response): bool
    {
        if (!method_exists($response, 'header')) {
            return false;
        }
        
        $contentType = $response->header('Content-Type');
        return $contentType && Str::contains($contentType, 'text/html');
    }
    
    /**
     * Determinar si la solicitud debería ser redirigida
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldRedirect(Request $request): bool
    {
        // Redirigir URLs con mayúsculas a minúsculas
        if (strtolower($request->path()) !== $request->path()) {
            return true;
        }
        
        // Eliminar parámetros de seguimiento conocidos
        $trackingParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'fbclid', 'gclid'];
        foreach ($trackingParams as $param) {
            if ($request->has($param)) {
                return true;
            }
        }
        
        // Redirigir si hay un slash al final innecesario o faltante según la configuración
        $shouldHaveTrailingSlash = config('app.trailing_slash', false);
        $hasTrailingSlash = Str::endsWith($request->getRequestUri(), '/');
        
        if ($request->path() !== '/' && $shouldHaveTrailingSlash !== $hasTrailingSlash) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Obtener la URL canónica para la solicitud
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getCanonicalUrl(Request $request): string
    {
        // Convertir la ruta a minúsculas
        $path = strtolower($request->path());
        
        // Agregar o quitar el slash final según la configuración
        $shouldHaveTrailingSlash = config('app.trailing_slash', false);
        if ($path !== '/') {
            if ($shouldHaveTrailingSlash && !Str::endsWith($path, '/')) {
                $path .= '/';
            } elseif (!$shouldHaveTrailingSlash && Str::endsWith($path, '/')) {
                $path = rtrim($path, '/');
            }
        }
        
        // Conservar solo los parámetros de consulta necesarios
        $query = collect($request->query())
            ->reject(function ($value, $key) {
                $trackingParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'fbclid', 'gclid'];
                return in_array($key, $trackingParams);
            })
            ->all();
        
        // Construir la URL canónica
        $url = url($path);
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }
        
        return $url;
    }
}