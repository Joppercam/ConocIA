<?php

namespace App\Http\ViewComposers;

use App\Models\TikTokScript;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class TikTokComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Cachear el resultado por 5 minutos para mejorar el rendimiento
        $pendingScriptsCount = Cache::remember('tiktok_pending_scripts_count', 300, function () {
            // Verificar si la tabla existe antes de consultar
            try {
                return TikTokScript::where('status', 'pending_review')->count();
            } catch (\Exception $e) {
                // Si hay un error (tabla no existe aún), devolver 0
                return 0;
            }
        });
        
        $view->with('pendingTikTokScriptsCount', $pendingScriptsCount);
    }
}