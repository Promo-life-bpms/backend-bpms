<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use PhpParser\Node\Expr\Cast\String_;

class TestN extends Notification
{
    use Queueable;

    protected $password;
    protected $email;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(String $password, String $email)
    {
        $this->password = $password;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Bienvenido al BPMS')
            ->line('Has sido registrado correctamente')
            ->line('Tu password es: ' . $this->password)
            ->line('tu correo es :' . $this->email)
            ->action('Acceder', url('https://dev-bpms.promolife.online/api'))
            ->line('¡Gracias!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
