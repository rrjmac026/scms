<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counselor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_number',
        'specialization',
        'availability_schedule',
    ];

    protected $casts = [
        'availability_schedule' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}
