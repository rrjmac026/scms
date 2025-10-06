<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Appointment;

class AppointmentAccepted extends Notification
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
        $date = $this->appointment->preferred_date->format('F d, Y');
        $time = \Carbon\Carbon::parse($this->appointment->preferred_time)->format('h:i A');
        $counselorName = $this->appointment->counselor->user->name;

        return (new MailMessage)
            ->subject('Your Counseling Appointment has been Accepted')
            ->greeting('Hello ' . $this->appointment->student->user->first_name . ',')
            ->line('Your counseling appointment has been accepted by ' . $counselorName . '.')
            ->line('Appointment Details:')
            ->line("Date: {$date}")
            ->line("Time: {$time}")
            ->line('Location: Guidance Office')
            ->action('View Appointment Details', route('student.appointments.show', $this->appointment))
            ->line('Please arrive 5 minutes before your scheduled time.');
    }
}
