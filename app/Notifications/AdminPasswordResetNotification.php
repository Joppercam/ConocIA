<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class AdminPasswordResetNotification extends Notification
{
    /**
     * El token para restablecer la contraseña
     *
     * @var string
     */
    public $token;

    /**
     * Crear una nueva instancia de notificación
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Obtener los canales de notificación
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Obtener la representación por correo de la notificación
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $resetUrl = url(route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject(Lang::get('Solicitud de Restablecimiento de Contraseña'))
            ->greeting('Hola ' . $notifiable->name)
            ->line(Lang::get('Has recibido este correo porque se solicitó un restablecimiento de contraseña para tu cuenta de administración.'))
            ->action(Lang::get('Restablecer Contraseña'), $resetUrl)
            ->line(Lang::get('Este enlace expirará en :count minutos.', ['count' => config('auth.passwords.users.expire')]))
            ->line(Lang::get('Si no solicitaste un restablecimiento de contraseña, ignora este mensaje.'))
            ->salutation('Saludos, Equipo Administrativo');
    }
}