<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Appointment;

class CounselorAppointmentAssigned extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
         return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('New Counseling Appointment Assigned')
            ->view('emails.appointments.counselor-assigned', ['appointment' => $this->appointment]);
    }
}

