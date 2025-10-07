<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounselingCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',            
        'description',     
        'counselor_id',    
        'status',          
        'admin_feedback',  
    ];


    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function appointments()
    {
        return $this->hasMany(\App\Models\Appointment::class);
    }
}
