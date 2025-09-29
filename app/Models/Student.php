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
        'lrn',
        'strand',
        'grade_level',
        'special_needs',

        // Personal Info
        'birthdate',
        'gender',
        'address',
        'contact_number',
        'civil_status',
        'nationality',
        'religion',

        // Parent/Guardian Info
        'father_name',
        'father_contact',
        'father_occupation',
        'mother_name',
        'mother_contact',
        'mother_occupation',
        'guardian_name',
        'guardian_contact',
        'guardian_relationship',
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
