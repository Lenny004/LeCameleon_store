<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $broker = config('auth.defaults.passwords', 'users');
        $expire = (int) config("auth.passwords.{$broker}.expire", 60);

        return (new MailMessage)
            ->subject('Restablece tu contraseña — Le Cameleon')
            ->greeting('Hola,')
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta.')
            ->action('Restablecer contraseña', route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]))
            ->line("Este enlace vence en {$expire} minutos.")
            ->line('Si no solicitaste este cambio, puedes ignorar este correo.');
    }
}
