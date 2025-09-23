<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BehaviorIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'counselor_id',
        'incident_type',
        'description',
        'severity',
        'date_reported',
        'action_taken',
    ];

    protected $casts = [
        'date_reported' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }
}
