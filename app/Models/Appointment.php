<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'counselor_id',
        'preferred_date',
        'preferred_time',
        'status',
        'concern',
    ];

    protected $casts = [
        'preferred_date' => 'date',
    ];

    public function getFormattedTimeAttribute()
    {
        return \Carbon\Carbon::createFromFormat('H:i:s', $this->preferred_time)->format('h:i A');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function counselingSession()
    {
        return $this->hasOne(CounselingSession::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }
}
