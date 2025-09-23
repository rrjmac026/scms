<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use
    // HasApiTokens, 
    HasFactory, 
    // HasProfilePhoto, 
    Notifiable;
    // TwoFactorAuthenticatable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role',
        'contact_number',
        'address',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    // Relationships
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function counselor()
    {
        return $this->hasOne(Counselor::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'student_id');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class, 'student_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    

    public function getNameAttribute(): string
    {
        $middle = $this->middle_name ? " {$this->middle_name}" : '';
        return "{$this->first_name}{$middle} {$this->last_name}";
    }
}
