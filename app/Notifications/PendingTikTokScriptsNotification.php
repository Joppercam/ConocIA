<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class PendingTikTokScriptsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Número total de guiones pendientes
     *
     * @var int
     */
    protected $totalCount;

    /**
     * Lista de guiones pendientes (limitada)
     *
     * @var Collection
     */
    protected $pendingScripts;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $totalCount, Collection $pendingScripts)
    {
        $this->totalCount = $totalCount;
        $this->pendingScripts = $pendingScripts;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("[$this->totalCount] Guiones de TikTok pendientes de revisión")
            ->greeting("Hola {$notifiable->name},")
            ->line("Hay {$this->totalCount} guiones de TikTok pendientes de revisión en el sistema.")
            ->action('Ver en el panel', route('admin.tiktok.index'))
            ->line('Aquí están los más recientes:');
            
        // Agregar los guiones recientes al mensaje
        foreach ($this->pendingScripts as $script) {
            $title = $script->article->title ?? 'Artículo sin título';
            $message->line("• $title");
        }
        
        if ($this->totalCount > $this->pendingScripts->count()) {
            $message->line("Y " . ($this->totalCount - $this->pendingScripts->count()) . " más...");
        }
        
        $message->salutation('Gracias por tu atención');
        
        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'total_count' => $this->totalCount,
            'message' => "Hay {$this->totalCount} guiones de TikTok pendientes de revisión.",
            'action_url' => route('admin.tiktok.index')
        ];
    }
}