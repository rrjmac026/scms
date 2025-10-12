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
     * Sync appointment to Google Calendar depending on status
     */
    public function sync(Appointment $appointment)
    {
        Log::info("=== SYNC STARTED for appointment {$appointment->id} ===");
        Log::info("Appointment status: {$appointment->status}");
        
        try {
            // Decide which calendar to sync to
            $calendarOwner = $this->getCalendarOwner($appointment);
            
            if (!$calendarOwner) {
                Log::warning("No Google Calendar user found for appointment {$appointment->id}");
                return;
            }
            
            Log::info("Calendar owner found: User ID {$calendarOwner->id}, Role: {$calendarOwner->role}");
            Log::info("Has google_token: " . ($calendarOwner->google_token ? 'YES' : 'NO'));

            switch ($appointment->status) {
                case 'pending':
                case 'approved':
                case 'accepted':
                    Log::info("Calling createOrUpdateEvent for appointment {$appointment->id}");
                    $this->createOrUpdateEvent($calendarOwner, $appointment);
                    break;

                case 'cancelled':
                case 'cancelled_by_student':
                case 'rejected':
                case 'completed':
                    Log::info("Calling deleteEvent for appointment {$appointment->id}");
                    $this->deleteEvent($calendarOwner, $appointment);
                    break;
                    
                default:
                    Log::info("Status {$appointment->status} doesn't require sync");
            }
            
            Log::info("=== SYNC COMPLETED for appointment {$appointment->id} ===");
            
        } catch (\Exception $e) {
            Log::error("=== SYNC FAILED for appointment {$appointment->id} ===");
            Log::error("Error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    protected function createOrUpdateEvent(User $calendarOwner, Appointment $appointment)
    {
        Log::info("--- createOrUpdateEvent started ---");
        
        try {
            // Load relationships if not already loaded
            if (!$appointment->relationLoaded('student')) {
                Log::info("Loading student relationship");
                $appointment->load('student.user');
            }
            
            if (!$appointment->relationLoaded('category')) {
                Log::info("Loading category relationship");
                $appointment->load('category');
            }
            
            if (!$appointment->relationLoaded('counselor')) {
                Log::info("Loading counselor relationship");
                $appointment->load('counselor.user');
            }
            
            Log::info("Building event data...");
            $eventData = $this->buildEventData($appointment);
            Log::info("Event data built successfully", ['summary' => $eventData['summary']]);

            if ($appointment->google_event_id) {
                Log::info("Updating existing Google event: {$appointment->google_event_id}");
                $this->gcal->updateEvent($calendarOwner, $appointment->google_event_id, $eventData);
                Log::info("Event updated successfully");
            } else {
                Log::info("Creating new Google Calendar event");
                $event = $this->gcal->createEvent($calendarOwner, $eventData);
                
                $eventId = $event->getId();
                Log::info("Event created with ID: {$eventId}");
                
                // Update the appointment
                $updated = $appointment->update(['google_event_id' => $eventId]);
                Log::info("Appointment update result: " . ($updated ? 'SUCCESS' : 'FAILED'));
                
                // Refresh and verify
                $appointment->refresh();
                Log::info("After refresh - google_event_id: " . ($appointment->google_event_id ?? 'NULL'));
            }
            
            Log::info("--- createOrUpdateEvent completed ---");
            
        } catch (\Exception $e) {
            Log::error("--- createOrUpdateEvent FAILED ---");
            Log::error("Error in createOrUpdateEvent: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    protected function deleteEvent(User $calendarOwner, Appointment $appointment)
    {
        Log::info("--- deleteEvent started ---");
        
        if ($appointment->google_event_id) {
            try {
                Log::info("Deleting Google event: {$appointment->google_event_id}");
                $this->gcal->deleteEvent($calendarOwner, $appointment->google_event_id);
                $appointment->update(['google_event_id' => null]);
                Log::info("Event deleted successfully");
            } catch (\Exception $e) {
                Log::error("Failed to delete event: " . $e->getMessage());
                throw $e;
            }
        } else {
            Log::info("No google_event_id to delete");
        }
        
        Log::info("--- deleteEvent completed ---");
    }

    /**
     * Build Google Calendar event data array from Appointment
     */
    protected function buildEventData(Appointment $appointment)
    {
        Log::info("Building event data - checking relationships...");
        
        // Check if relationships are loaded
        Log::info("Student exists: " . ($appointment->student ? 'YES' : 'NO'));
        Log::info("Category exists: " . ($appointment->category ? 'YES' : 'NO'));
        Log::info("Counselor exists: " . ($appointment->counselor ? 'YES' : 'NO'));
        
        // FIX: Convert Carbon date to string first
        $dateString = $appointment->preferred_date instanceof \Carbon\Carbon
            ? $appointment->preferred_date->format('Y-m-d')
            : $appointment->preferred_date;
        
        $start = Carbon::parse($dateString . ' ' . $appointment->preferred_time);
        $end = (clone $start)->addHour();
        
        Log::info("Start time: {$start->toRfc3339String()}");
        Log::info("End time: {$end->toRfc3339String()}");

        // FIX: Use null-safe operators properly
        $studentName = $appointment->student?->user?->name ?? 'Student';
        $counselorName = $appointment->counselor?->user?->name ?? 'Counselor';
        $categoryName = $appointment->category?->name ?? 'General';
        $concern = $appointment->concern ?? 'No concern specified';
        
        Log::info("Student name: {$studentName}");
        Log::info("Counselor name: {$counselorName}");
        Log::info("Category name: {$categoryName}");

        $eventData = [
            'summary' => "Counseling Session with {$studentName}",
            'description' => "Category: {$categoryName}\nCounselor: {$counselorName}\nConcern: {$concern}",
            'start' => ['dateTime' => $start->toRfc3339String(), 'timeZone' => 'Asia/Manila'],
            'end'   => ['dateTime' => $end->toRfc3339String(), 'timeZone' => 'Asia/Manila'],
        ];
        
        Log::info("Event data structure created");
        
        return $eventData;
    }

    /**
     * Determine who owns the Google Calendar
     */
    protected function getCalendarOwner(Appointment $appointment): ?User
    {
        Log::info("Getting calendar owner...");
        
        // Load necessary relationships
        if (!$appointment->relationLoaded('counselor')) {
            $appointment->load('counselor.user');
        }
        if (!$appointment->relationLoaded('student')) {
            $appointment->load('student.user');
        }
        
        // Priority 1: Use student's calendar if they have connected Google
        if ($appointment->student && $appointment->student->user) {
            Log::info("Student user exists, checking google_token...");
            
            if ($appointment->student->user->google_token) {
                Log::info("Using student's calendar (User ID: {$appointment->student->user->id})");
                return $appointment->student->user;
            } else {
                Log::info("Student has no google_token");
            }
        }
        
        // Priority 2: Prefer counselor if connected
        if ($appointment->counselor && $appointment->counselor->user) {
            Log::info("Counselor user exists, checking google_token...");
            
            if ($appointment->counselor->user->google_token) {
                Log::info("Using counselor's calendar (User ID: {$appointment->counselor->user->id})");
                return $appointment->counselor->user;
            } else {
                Log::info("Counselor has no google_token");
            }
        } else {
            Log::info("Appointment has no counselor assigned");
        }

        // Priority 3: Fallback to admin
        Log::info("Looking for admin with google_token...");
        $admin = User::where('role', 'admin')
            ->whereNotNull('google_token')
            ->first();
            
        if ($admin) {
            Log::info("Using admin's calendar (User ID: {$admin->id})");
            return $admin;
        } else {
            Log::warning("No admin with google_token found!");
            return null;
        }
    }
}