<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentApproved extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['mail']; // or ['database', 'mail'] if you want
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Your appointment has been approved.')
                    ->action('View Appointment', url('/appointments/'.$this->appointment->id))
                    ->line('Thank you!');
    }
}
