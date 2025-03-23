<?php

use Illuminate\Support\Str;

if (!function_exists('getImageUrl')) {
    function getImageUrl($path, $folder = 'default', $size = 'medium') {
        if (empty($path)) {
            return asset("storage/images/defaults/{$folder}-default-{$size}.jpg");
        }
        
        // Si la ruta ya comienza con 'storage/', solo usamos asset()
        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }
        
        // De lo contrario, construimos la ruta completa
        return asset('storage/' . $path);
    }
}