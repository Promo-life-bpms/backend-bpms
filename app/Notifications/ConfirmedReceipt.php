<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmedReceipt extends Notification
{
    use Queueable;

    public $emisor;
    public $receptor;
    public $idPurchase;
    public function __construct($emisor, $receptor, $idPurchase)
    {
        $this->emisor = $emisor;
        $this->receptor = $receptor;
        $this->idPurchase = $idPurchase;
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
                    ->markdown('mail.cajachica.ConfirmationPurchaseRequest',[
                        'emisor'=>$this->emisor,
                        'receptor'=>$this->receptor,
                        'idPurchase'=>$this->idPurchase,
                        
        ])
        ->subject('ENTREGA DE EFECTIVO')
        ->from('adminportales@promolife.com.mx', 'BPMS PL - BH');
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
