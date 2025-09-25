<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

    class Feedback extends Model
    {
        use HasFactory;

        protected $fillable = [
        'student_id',
        'counselor_id',
        'counseling_session_id',
        'rating',
        'comments',
        'likes',
        'q1','q2','q3','q4','q5','q6','q7','q8','q9','q10',
        'q11','q12',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function counselingSession()
    {
        return $this->belongsTo(CounselingSession::class);
    }
}
