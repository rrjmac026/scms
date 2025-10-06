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
        'gender',
        'birth_date',
        'bio',
        'availability_schedule',
        'assigned_grade_level',
    ];

    protected $casts = [
        'availability_schedule' => 'array',
        'birth_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function counselingSessions()
    {
        return $this->hasMany(CounselingSession::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function counselingCategories()
    {
        return $this->hasMany(CounselingCategory::class);
    }
    public function category()
    {
        return $this->belongsTo(CounselingCategory::class, 'counseling_category_id');
    }
}
