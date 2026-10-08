<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $expire = (int) config('auth.verification.expire', 60);
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes($expire),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
        );

        return (new MailMessage)
            ->subject('Verifica tu correo — Le Cameleon')
            ->greeting('Hola,')
            ->line('Confirma tu correo electrónico para continuar en Le Cameleon.')
            ->action('Verificar correo', $verificationUrl)
            ->line("Este enlace vence en {$expire} minutos.");
    }
}
