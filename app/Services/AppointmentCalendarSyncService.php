<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AppointmentCalendarSyncService
{
    protected $gcal;

    public function __construct(GoogleCalendarService $gcal)
    {
        $this->gcal = $gcal;
    }

    /**
     * Sync appointment to ALL relevant Google Calendars
     * (Student + Counselor + Admin)
     */
    public function sync(Appointment $appointment)
    {
        Log::info("=== MULTI-CALENDAR SYNC STARTED for appointment {$appointment->id} ===");
        Log::info("Appointment status: {$appointment->status}");
        
        try {
            // Get all users who should have this event
            $calendarOwners = $this->getAllCalendarOwners($appointment);
            
            if ($calendarOwners->isEmpty()) {
                Log::warning("No Google Calendar users found for appointment {$appointment->id}");
                return;
            }
            
            Log::info("Found {$calendarOwners->count()} calendar owner(s)");

            switch ($appointment->status) {
                case 'pending':
                case 'approved':
                case 'completed':  // ← Keep completed appointments in calendar
                    Log::info("Creating/updating events in all calendars");
                    $this->syncToAllCalendars($calendarOwners, $appointment);
                    break;

                case 'cancelled':
                case 'cancelled_by_student':
                case 'rejected':
                case 'completed':
                    Log::info("Deleting events from all calendars");
                    $this->deleteFromAllCalendars($calendarOwners, $appointment);
                    break;
                    
                default:
                    Log::info("Status {$appointment->status} doesn't require sync");
            }
            
            Log::info("=== MULTI-CALENDAR SYNC COMPLETED for appointment {$appointment->id} ===");
            
        } catch (\Exception $e) {
            Log::error("=== MULTI-CALENDAR SYNC FAILED for appointment {$appointment->id} ===");
            Log::error("Error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Sync appointment to all relevant calendars
     */
    protected function syncToAllCalendars($calendarOwners, Appointment $appointment)
    {
        // Load relationships
        $appointment->load(['student.user', 'counselor.user', 'category']);
        
        $eventData = $this->buildEventData($appointment);
        $googleEventIds = $this->getGoogleEventIds($appointment);
        
        foreach ($calendarOwners as $owner) {
            try {
                Log::info("Syncing to {$owner->role}'s calendar (User ID: {$owner->id})");
                
                // Check if this user already has an event ID
                $existingEventId = $googleEventIds->get($owner->id);
                
                if ($existingEventId) {
                    // Try to update existing event
                    try {
                        $this->gcal->updateEvent($owner, $existingEventId, $eventData);
                        Log::info("✅ Updated event for {$owner->role} (ID: {$existingEventId})");
                    } catch (\Exception $e) {
                        Log::warning("Failed to update, creating new event instead");
                        $event = $this->gcal->createEvent($owner, $eventData);
                        $this->storeGoogleEventId($appointment, $owner->id, $event->getId());
                        Log::info("✅ Created new event for {$owner->role} (ID: {$event->getId()})");
                    }
                } else {
                    // Create new event
                    $event = $this->gcal->createEvent($owner, $eventData);
                    $this->storeGoogleEventId($appointment, $owner->id, $event->getId());
                    Log::info("✅ Created event for {$owner->role} (ID: {$event->getId()})");
                }
                
            } catch (\Exception $e) {
                Log::error("Failed to sync to {$owner->role}'s calendar: " . $e->getMessage());
                // Continue with other calendars even if one fails
            }
        }
    }

    /**
     * Delete appointment from all calendars
     */
    protected function deleteFromAllCalendars($calendarOwners, Appointment $appointment)
    {
        $googleEventIds = $this->getGoogleEventIds($appointment);
        
        if ($googleEventIds->isEmpty()) {
            Log::info("No google_event_ids to delete");
            return;
        }
        
        foreach ($calendarOwners as $owner) {
            try {
                $eventId = $googleEventIds->get($owner->id);
                
                if ($eventId) {
                    Log::info("Deleting event from {$owner->role}'s calendar (ID: {$eventId})");
                    $this->gcal->deleteEvent($owner, $eventId);
                    Log::info("✅ Deleted event from {$owner->role}'s calendar");
                }
            } catch (\Exception $e) {
                Log::error("Failed to delete from {$owner->role}'s calendar: " . $e->getMessage());
                // Continue with other calendars
            }
        }
        
        // Clear all stored event IDs
        $appointment->update(['google_event_id' => null]);
    }

    /**
     * Build Google Calendar event data array from Appointment
     */
    protected function buildEventData(Appointment $appointment)
    {
        $dateString = $appointment->preferred_date instanceof \Carbon\Carbon
            ? $appointment->preferred_date->format('Y-m-d')
            : $appointment->preferred_date;
        
        $start = Carbon::parse($dateString . ' ' . $appointment->preferred_time);
        $end = (clone $start)->addHour();

        $studentName = $appointment->student?->user?->name ?? 'Student';
        $counselorName = $appointment->counselor?->user?->name ?? 'Counselor';
        $categoryName = $appointment->category?->name ?? 'General';
        $concern = $appointment->concern ?? 'No concern specified';

        return [
            'summary' => "Counseling Session with {$studentName}",
            'description' => "Category: {$categoryName}\nCounselor: {$counselorName}\nConcern: {$concern}\nStatus: " . ucfirst($appointment->status),
            'start' => ['dateTime' => $start->toRfc3339String(), 'timeZone' => 'Asia/Manila'],
            'end'   => ['dateTime' => $end->toRfc3339String(), 'timeZone' => 'Asia/Manila'],
        ];
    }

    /**
     * Get all users who should see this appointment in their calendar
     * Returns collection of User models
     */
    protected function getAllCalendarOwners(Appointment $appointment)
    {
        $owners = collect();
        
        // Load relationships
        $appointment->load(['counselor.user', 'student.user']);
        
        // 1. Student (if they have Google Calendar)
        if ($appointment->student?->user?->google_token) {
            $owners->push($appointment->student->user);
            Log::info("✅ Student has Google Calendar");
        }
        
        // 2. Counselor (if assigned and has Google Calendar)
        if ($appointment->counselor?->user?->google_token) {
            $owners->push($appointment->counselor->user);
            Log::info("✅ Counselor has Google Calendar");
        }
        
        // 3. Admin (any admin with Google Calendar)
        $admin = User::where('role', 'admin')
            ->whereNotNull('google_token')
            ->first();
            
        if ($admin) {
            $owners->push($admin);
            Log::info("✅ Admin has Google Calendar");
        }
        
        return $owners->unique('id');
    }

    /**
     * Get stored Google Event IDs from JSON field
     * Returns collection: [user_id => event_id]
     */
    protected function getGoogleEventIds(Appointment $appointment)
    {
        if (!$appointment->google_event_id) {
            return collect();
        }
        
        // Try to decode as JSON (new format: {"1": "event_id_1", "2": "event_id_2"})
        if (is_string($appointment->google_event_id)) {
            $decoded = json_decode($appointment->google_event_id, true);
            if (is_array($decoded)) {
                return collect($decoded);
            }
            
            // Old format: single event ID string - assume it's admin's
            $admin = User::where('role', 'admin')
                ->whereNotNull('google_token')
                ->first();
            
            if ($admin) {
                return collect([$admin->id => $appointment->google_event_id]);
            }
        }
        
        return collect();
    }

    /**
     * Store Google Event ID for a specific user
     * Format: {"user_id": "event_id", ...}
     */
    protected function storeGoogleEventId(Appointment $appointment, $userId, $eventId)
    {
        $eventIds = $this->getGoogleEventIds($appointment);
        $eventIds->put($userId, $eventId);
        
        $appointment->update([
            'google_event_id' => json_encode($eventIds->toArray())
        ]);
        
        Log::info("Stored event ID for user {$userId}: {$eventId}");
    }
}