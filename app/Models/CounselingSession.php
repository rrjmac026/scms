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
        'category_id',
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
        if ($this->duration) {
            $hours = floor($this->duration / 60);
            $minutes = $this->duration % 60;

            return sprintf('%02dh %02dm', $hours, $minutes);
        }

        if ($this->started_at && $this->ended_at) {
            $diffInSeconds = $this->ended_at->diffInSeconds($this->started_at);
            return gmdate('H:i:s', $diffInSeconds);
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
