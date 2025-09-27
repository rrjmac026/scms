<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CounselingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'counselor_id',
        'appointment_id',
        'counseling_category_id',
        'concern',
        'notes',
        'started_at',
        'ended_at',
        'duration',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function getFormattedDurationAttribute()
    {
        // If duration is stored as total seconds (recommended)
        if ($this->duration) {
            $hours = floor($this->duration / 3600);
            $minutes = floor(($this->duration % 3600) / 60);
            $seconds = $this->duration % 60;

            return sprintf('%02dh %02dm %02ds', $hours, $minutes, $seconds);
        }

        // If started and ended timestamps exist, calculate duration dynamically
        if ($this->started_at && $this->ended_at) {
            $diffInSeconds = $this->ended_at->diffInSeconds($this->started_at);
            $hours = floor($diffInSeconds / 3600);
            $minutes = floor(($diffInSeconds % 3600) / 60);
            $seconds = $diffInSeconds % 60;

            return sprintf('%02dh %02dm %02ds', $hours, $minutes, $seconds);
        }

        return 'N/A';
    }
    
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'counseling_session_id', 'id');
    }

    // public function category()
    // {
    //     return $this->appointment->category();
    // }

    public function category()
    {
        return $this->hasOneThrough(
            CounselingCategory::class,
            Appointment::class,
            'id',                // Foreign key para sa appointments table
            'id',                // Foreign key para sa categories table
            'appointment_id',    // Local key para sa sessions table
            'counseling_category_id' // Local key para sa appointments table
        );                             //For consistency nimal dungag nasad sa logic waahahahha
    }
    
}
