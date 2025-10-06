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
        $date = $this->appointment->preferred_date->format('F d, Y');
        $time = \Carbon\Carbon::parse($this->appointment->preferred_time)->format('h:i A');
        $studentName = $this->appointment->student->user->name;

        return (new MailMessage)
            ->subject('New Counseling Appointment Assigned')
            ->greeting('Hello ' . $this->appointment->counselor->user->first_name . ',')
            ->line('A new counseling appointment has been assigned to you.')
            ->line('Appointment Details:')
            ->line("Student: {$studentName}")
            ->line("Date: {$date}")
            ->line("Time: {$time}")
            ->line("Category: {$this->appointment->category?->name}")
            ->line('Student Concern:')
            ->line($this->appointment->concern)
            ->action('View Appointment Details', route('counselor.appointments.show', $this->appointment))
            ->line('Please review and accept/reject the appointment.');
    }
}
