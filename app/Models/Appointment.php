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

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function session()
    {
        return $this->hasOne(Session::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }
}
