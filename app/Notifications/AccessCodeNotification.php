<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AccessCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $code;
    protected object $user;
    /**
     * Create a new notification instance.
     */
    public function __construct(object $user, string $code)
    {
        $this->code = $code;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu código de acceso temporal')
            ->greeting('¡Hola!')
            ->line('Este es el código temporal que debes introducir para acceder a NextToPlay: ')
            ->line(new HtmlString('<div id="code" style="padding:10px;text-align:center;font-size:22px;font-weight:700;background-color:#f0f2f5;border-radius:5px;margin-bottom:10px">'.$this->code.'</div>'))
            ->salutation('¡Gracias por usar la aplicación!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
