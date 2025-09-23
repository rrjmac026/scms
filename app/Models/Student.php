<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_number',
        'course',
        'year_level',
        'special_needs',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function behaviorIncidents()
    {
        return $this->hasMany(BehaviorIncident::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function counselingSessions()
    {
        return $this->hasMany(CounselingSession::class);
    }

}
