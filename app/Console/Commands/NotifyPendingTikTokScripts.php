<?php

namespace App\Console\Commands;

use App\Models\TikTokScript;
use App\Models\User;
use App\Notifications\PendingTikTokScriptsNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyPendingTikTokScripts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tiktok:notify-pending-scripts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica a los administradores sobre guiones de TikTok pendientes de revisión';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener guiones pendientes de revisión
        $pendingScripts = TikTokScript::with('article')
            ->where('status', 'pending_review')
            ->get();
            
        $count = $pendingScripts->count();
        
        if ($count == 0) {
            $this->info('No hay guiones pendientes de revisión.');
            return;
        }
        
        $this->info("Enviando notificación por $count guiones pendientes de revisión.");
        
        // Obtener administradores
        $admins = User::where('role', 'admin')->get();
        
        if ($admins->isEmpty()) {
            $this->warn('No se encontraron administradores para notificar.');
            return;
        }
        
        try {
            // Enviar notificación a cada administrador
            foreach ($admins as $admin) {
                $admin->notify(new PendingTikTokScriptsNotification($count, $pendingScripts->take(5)));
                $this->line("Notificación enviada a: {$admin->name} ({$admin->email})");
            }
            
            $this->info("Notificaciones enviadas correctamente a " . $admins->count() . " administradores.");
            
        } catch (\Exception $e) {
            $this->error("Error enviando notificaciones: " . $e->getMessage());
            Log::error("Error enviando notificaciones de TikTok: " . $e->getMessage());
        }
    }
}