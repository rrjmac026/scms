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
         return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Your Counseling Appointment has been Approved')
            ->view('emails.appointments.approved', ['appointment' => $this->appointment]);
    }
}

