<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Appointment;

class AppointmentRejected extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['mail']; // or add 'database', 'broadcast', etc.
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Your Appointment was Rejected')
                    ->line('Unfortunately, your appointment on ' . $this->appointment->preferred_date->format('M d, Y') . ' at ' . $this->appointment->preferred_time . ' was rejected.')
                    ->line('Please try to book another available slot.');
    }
}
