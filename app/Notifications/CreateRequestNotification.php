<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CreateRequestNotification extends Notification
{
    use Queueable;

    protected $concept, $center, $total;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(String $concept, String $center,  String $total )
    {
        $this->concept = $concept;
        $this->center = $center;
        $this->total = $total;
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
            ->line( new HtmlString('<h2>Caja Chica - Nueva Solicitud</h2>'))
            ->line( new HtmlString('<h5>Tienes una solicitud de gasto por aprobar</h5>'))
            ->line( new HtmlString('<br />'))
            ->line( new HtmlString('<p><b>Concepto: </b></b>' . $this->concept ))
            ->line( new HtmlString('<p><b>Centro de costos: </b></b>' . $this->center ))
            ->line( new HtmlString('<p><b>Total: </b></b>' . $this->total ));
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
