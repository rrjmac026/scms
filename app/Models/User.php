<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use PragmaRX\Google2FA\Google2FA;


class User extends Authenticatable implements MustVerifyEmail
{
    use
    // HasApiTokens, 
    HasFactory, 
    // HasProfilePhoto, 
    Notifiable,
    TwoFactorAuthenticatable;

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

    public function verifyTwoFactorCode(string $code): bool
    {
        if (!$this->two_factor_secret) {
            return false;
        }

        $google2fa = new Google2FA();

        return $google2fa->verifyKey(
            decrypt($this->two_factor_secret),
            $code
        );
    }

    /**
     * Verify a recovery code (optional)
     */
    public function verifyRecoveryCode(string $code): bool
    {
        if (!$this->two_factor_recovery_codes) {
            return false;
        }

        $codes = json_decode(decrypt($this->two_factor_recovery_codes), true);

        if (in_array($code, $codes)) {
            // Remove the used code
            $codes = array_diff($codes, [$code]);
            $this->two_factor_recovery_codes = encrypt(json_encode(array_values($codes)));
            $this->save();
            return true;
        }

        return false;
    }
}
