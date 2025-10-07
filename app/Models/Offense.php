<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offense extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'counselor_id',
        'counseling_session_id',
        'offense',
        'remarks',
        'date',
        'status',
        'resolved',
        'solution',
    ];

    protected $casts = [
        'resolved' => 'boolean',
        'date' => 'date',
    ];

    /**
     * The student who committed the offense.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function counselingSession()
    {
        return $this->belongsTo(CounselingSession::class);
    }
}
