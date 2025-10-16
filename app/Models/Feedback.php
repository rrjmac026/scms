<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    
    protected $table = 'feedbacks';

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

    /**
     * Calculate average rating from detailed questions (q1-q12)
     * Returns the average or null if no ratings exist
     */
    public function getDetailedAverageAttribute()
    {
        $ratings = [];

        for ($i = 1; $i <= 12; $i++) {
            $key = "q{$i}";
            if (!empty($this->$key) && is_numeric($this->$key)) {
                $ratings[] = $this->$key;
            }
        }

        return count($ratings) ? round(array_sum($ratings) / count($ratings), 2) : null;
    }

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