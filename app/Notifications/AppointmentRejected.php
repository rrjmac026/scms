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
        $date = $this->appointment->preferred_date->format('F d, Y');
        $time = \Carbon\Carbon::parse($this->appointment->preferred_time)->format('h:i A');
        $counselorName = $this->appointment->counselor->user->name;

        return (new MailMessage)
            ->subject('Your Counseling Appointment was Rejected')
            ->greeting('Hello ' . $this->appointment->student->user->first_name . ',')
            ->line('Unfortunately, your counseling appointment has been rejected by ' . $counselorName . '.')
            ->line('Appointment Details:')
            ->line("Date: {$date}")
            ->line("Time: {$time}")
            ->line('Reason for Rejection:')
            ->line($this->appointment->rejection_reason)
            ->line('Please book another appointment at your convenience.')
            ->action('Book New Appointment', route('student.appointments.create'));
    }
}
