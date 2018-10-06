<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Persona;
use App\Personamaestro;

class UserResetPasswordNotification extends Notification
{
    //Places this task to a queue if its enabled
    use Queueable;

    //Token handler
    public $token;

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
       
        $persona = Persona::find($notifiable->persona_id);
        $personamaestro = Personamaestro::find($persona->personamaestro_id);
         
        return (new MailMessage)
        ->subject('Solicitud de restablecimiento de contraseña')
        ->greeting('Hola '. $personamaestro->nombres . " " . $personamaestro->apellidos . "!")
        ->line('Usted está recibiendo este correo electrónico porque recibimos una solicitud de restablecimiento de contraseña para su cuenta.')
        ->action('Restablecer contraseña', url('password/reset', $this->token))
        ->line('Si no solicitó restablecer la contraseña, no se requieren más acciones.')
        ->salutation('¡Saludos!');
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
