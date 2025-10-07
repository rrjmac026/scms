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
         return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Your Counseling Appointment was Rejected')
            ->view('emails.appointments.rejected', ['appointment' => $this->appointment]);
    }
}

