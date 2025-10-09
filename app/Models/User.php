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
use Illuminate\Support\Facades\Storage;

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
        'status',
        'contact_number',
        'address',
        'profile_photo_path',
        'google_id',
        'google_token',
        'google_token_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'google_token' => 'array',
        'google_token_expires_at' => 'datetime',
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

    /**
     * Get the user's full name
     */
    public function getNameAttribute(): string
    {
        $middle = $this->middle_name ? " {$this->middle_name}" : '';
        return "{$this->first_name}{$middle} {$this->last_name}";
    }

    /**
     * Get the user's profile photo URL
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo_path 
            ? Storage::url($this->profile_photo_path)
            : $this->defaultProfilePhotoUrl();
    }

    /**
     * Get the default profile photo URL
     */
    public function defaultProfilePhotoUrl(): string
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Delete the user's profile photo
     */
    public function deleteProfilePhoto(): void
    {
        if ($this->profile_photo_path) {
            Storage::disk('public')->delete($this->profile_photo_path);
            $this->update(['profile_photo_path' => null]);
        }
    }

    /**
     * Update the user's profile photo
     */
    public function updateProfilePhoto($photo): void
    {
        // Delete old photo if exists
        $this->deleteProfilePhoto();
        
        // Store new photo
        $path = $photo->store('profile-photos', 'public');
        
        // Update user record
        $this->update(['profile_photo_path' => $path]);
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

        if (in_array($code, [$code])) {
            // Remove the used code
            $codes = array_diff($codes, [$code]);
            $this->two_factor_recovery_codes = encrypt(json_encode(array_values($codes)));
            $this->save();
            return true;
        }

        return false;
    }
}