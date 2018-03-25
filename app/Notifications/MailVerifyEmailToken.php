<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailVerifyEmailToken extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
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
                    ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                    ->subject('Potrditev email naslova')
                    ->greeting('Pozdravljeni!')
                    ->line('Veseli smo, da ste se odločili za uporabo aplikacije Trubadur.')
                    ->line('Za uspešno dokončanje registracije, sledite povezavi:')
                    ->action('Potrdi email naslov', url('register/verify', $this->token))
                    ->line('Če se ne želite registrirati, lahko sporočilo ignorirate.')
                    ->salutation('Lep pozdrav, ekipa Trubadur');
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
