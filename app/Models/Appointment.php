<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'counselor_id',
        'counseling_category_id',
        'counseling_session_id',
        'preferred_date',
        'preferred_time',
        'proposed_date',
        'proposed_time',
        'status',
        'concern',
        'reschedule_reason',
        'student_reschedule_response',
        'rejection_reason',
        'google_event_id',
    ];

    protected $casts = [
        'preferred_date' => 'date:Y-m-d',
        'proposed_date' => 'date:Y-m-d',
    ];

    // ============================================
    // ACCESSOR METHODS
    // ============================================
    
    public function getFormattedTimeAttribute()
    {
        return Carbon::createFromFormat('H:i:s', $this->preferred_time)->format('h:i A');
    }

    public function getPreferredDateTimeAttribute()
    {
        return Carbon::createFromFormat(
            'Y-m-d H:i',
            $this->preferred_date->format('Y-m-d') . ' ' . $this->preferred_time
        );
    }

    public function getFormattedDateTimeAttribute(): string
    {
        return $this->preferred_date->format('M d, Y') . ' at ' . 
               Carbon::parse($this->preferred_time)->format('g:i A');
    }

    public function getFormattedProposedDateTimeAttribute(): ?string
    {
        if (!$this->proposed_date || !$this->proposed_time) {
            return null;
        }

        return $this->proposed_date->format('M d, Y') . ' at ' . 
               Carbon::parse($this->proposed_time)->format('g:i A');
    }


    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'primary',
            'accepted' => 'purple',
            'completed' => 'success',
            'rejected', 'declined' => 'danger',
            'cancelled_by_student', 'cancelled_by_counselor' => 'secondary',
            'reschedule_requested_by_counselor', 'reschedule_requested_by_student' => 'info',
            'reschedule_declined' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved by Admin',
            'accepted' => 'Accepted by Counselor',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
            'declined' => 'Declined by Admin',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    // ============================================
    // RELATIONSHIPS
    // ============================================
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor()
    {
        return $this->belongsTo(Counselor::class);
    }

    public function counselingSession()
    {
        return $this->hasOne(CounselingSession::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }
    
    public function category() 
    {
        return $this->belongsTo(CounselingCategory::class, 'counseling_category_id');
    }

    // ============================================
    // SCOPES
    // ============================================
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('counselor_id')->where('status', 'pending');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('preferred_date', '>=', now()->toDateString())
                    ->whereIn('status', ['pending', 'approved']);
    }

    // ============================================
    // HELPER METHODS
    // ============================================
    
    /**
     * Check if appointment can be cancelled
     */
    public function canBeCancelled(): bool
    {
        if (!in_array($this->status, ['pending', 'approved'])) {
            return false;
        }

        $appointmentDateTime = Carbon::parse($this->preferred_date->format('Y-m-d') . ' ' . $this->preferred_time);
        
        return $appointmentDateTime->isAfter(now()->addDay());
    }

    /**
     * Check if appointment needs reschedule response
     */
    public function needsRescheduleResponse(): bool
    {
        return in_array($this->status, [
            'reschedule_requested_by_counselor',
            'reschedule_requested_by_student'
        ]);
    }

    /**
     * Check if student needs to respond
     */
    public function studentNeedsToRespond(): bool
    {
        return $this->status === 'reschedule_requested_by_counselor';
    }

    /**
     * Check if counselor needs to respond
     */
    public function counselorNeedsToRespond(): bool
    {
        return $this->status === 'reschedule_requested_by_student';
    }
}