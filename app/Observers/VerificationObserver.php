<?php

namespace App\Observers;

use App\Models\Verification;
use App\Services\VerificadorCacheService;

class VerificationObserver
{
    protected $cacheService;
    
    public function __construct(VerificadorCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    
    /**
     * Handle the Verification "created" event.
     */
    public function created(Verification $verification): void
    {
        // Invalidar listas y estadísticas cuando se crea una nueva verificación
        $this->cacheService->invalidateVerificationsList();
        $this->cacheService->invalidateFeaturedVerifications();
        $this->cacheService->invalidateStats();
    }

    /**
     * Handle the Verification "updated" event.
     */
    public function updated(Verification $verification): void
    {
        // Invalidar caché específico de esta verificación
        $this->cacheService->invalidateVerification($verification->id);
        
        // Si el veredicto cambió, invalidar listas y estadísticas
        if ($verification->isDirty('verdict')) {
            $this->cacheService->invalidateVerificationsList();
            $this->cacheService->invalidateFeaturedVerifications();
            $this->cacheService->invalidateStats();
        }
    }

    /**
     * Handle the Verification "deleted" event.
     */
    public function deleted(Verification $verification): void
    {
        // Invalidar todo el caché relacionado
        $this->cacheService->invalidateVerification($verification->id);
        $this->cacheService->invalidateVerificationsList();
        $this->cacheService->invalidateFeaturedVerifications();
        $this->cacheService->invalidateStats();
    }
}