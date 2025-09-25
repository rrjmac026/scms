<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Counselor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index(Request $request)
    {
        $appointments = Appointment::with(['student.user', 'counselor.user', 'category'])
            ->latest()
            ->paginate(10);

        $appointments->getCollection()->transform(function ($appointment) {
            if ($appointment->status === 'pending') {
                $appointment->availableCounselors = $this->getAvailableCounselors(
                    $appointment->preferred_date
                );
            } else {
                $appointment->availableCounselors = collect();
            }
            return $appointment;
        });

        return view('admin.appointments.index', compact('appointments'));
    }

    /**
     * Display the specified appointment and list available counselors.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['student.user', 'counselor.user', 'counselingSession', 'category']);

        $availableCounselors = $this->getAvailableCounselors(
            $appointment->preferred_date
        );

        return view('admin.appointments.show', compact('appointment', 'availableCounselors'));
    }

    /**
     * Assign a counselor to an appointment.
     */
    public function assignCounselor(Request $request, Appointment $appointment)
    {
        try {
            // Check if appointment is still pending
            if ($appointment->status !== 'pending') {
                return back()->with('error', 'Only pending appointments can be assigned.');
            }

            if ($appointment->counselor_id) {
                return back()->with('error', 'Appointment already has an assigned counselor.');
            }

            $availableCounselors = $this->getAvailableCounselors($appointment->preferred_date);
            
            // Auto-assign if no specific counselor selected
            if (empty($request->counselor_id)) {
                $counselor = $availableCounselors->first();
                if (!$counselor) {
                    return back()->with('error', 'No available counselors found for this date.');
                }
                $counselorId = $counselor->id;
            } else {
                $counselorId = $request->counselor_id;
                if (!$availableCounselors->contains('id', $counselorId)) {
                    return back()->with('error', 'Selected counselor is not available.');
                }
            }

            $appointment->update(['counselor_id' => $counselorId]);

            return back()->with('success', 'Counselor assigned successfully. Waiting for counselor approval.');

        } catch (\Exception $e) {
            \Log::error('Error assigning counselor: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while assigning the counselor. Please try again.');
        }
    }

    /**
     * Get all available counselors for a given date (ignoring time).
     */
    protected function getAvailableCounselors($date) 
    {
        try {
            $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));

            return Counselor::with('user')
                ->whereNotNull('availability_schedule')
                ->get()
                ->filter(function ($counselor) use ($dayOfWeek, $date) {
                    try {
                        $schedule = is_string($counselor->availability_schedule) 
                            ? json_decode($counselor->availability_schedule, true) 
                            : $counselor->availability_schedule;

                        if (!$schedule) {
                            \Log::warning("Invalid schedule format for counselor {$counselor->id}");
                            return false;
                        }

                        // Handle both formats of availability schedule
                        $availableDays = isset($schedule['days']) 
                            ? array_map('strtolower', $schedule['days'])  
                            : array_map('strtolower', (array)$schedule); 

                        $isAvailable = in_array($dayOfWeek, $availableDays);

                        if (!$isAvailable) {
                            return false;
                        }

                        return !$counselor->appointments()
                            ->whereDate('preferred_date', $date)
                            ->whereIn('status', ['approved', 'completed'])
                            ->exists();

                    } catch (\Exception $e) {
                        \Log::error("Error processing counselor {$counselor->id}: " . $e->getMessage());
                        return false;
                    }
                })
                ->values();

        } catch (\Exception $e) {
            \Log::error('Error getting available counselors: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function calendar()
    {
        $appointments = Appointment::with(['student.user', 'counselor.user', 'category'])
            ->whereIn('status', ['pending', 'approved', 'completed'])
            ->get()
            ->map(function ($appointment) {
                $counselorName = $appointment->counselor 
                    ? $appointment->counselor->user->name 
                    : 'Unassigned';
                
                // ✅ Fix: Combine date and time properly
                $startDateTime = $appointment->preferred_date;
                if ($appointment->preferred_time) {
                    // Ensure we have both date and time
                    $date = $appointment->preferred_date instanceof \Carbon\Carbon
                        ? $appointment->preferred_date->format('Y-m-d')
                        : $appointment->preferred_date;
                    
                    $time = substr($appointment->preferred_time, 0, 5); // Get HH:MM only
                    $startDateTime = $date . 'T' . $time . ':00'; // ISO format for calendar
                }
                
                return [
                    'id'          => $appointment->id,
                    'title'       => $appointment->student->user->name . ' (' . ucfirst($appointment->status) . ')',
                    'start'       => $startDateTime, // ✅ Now includes both date and time
                    'color'       => match ($appointment->status) {
                        'pending'   => '#fbbf24', // yellow
                        'approved'  => '#3b82f6', // blue
                        'completed' => '#10b981', // green
                        default     => '#6b7280', // gray
                    },
                    'extendedProps' => [
                        'student'     => $appointment->student->user->name,
                        'counselor'   => $counselorName,
                        'category'    => $appointment->category->name ?? 'General', // ✅ Safe fallback
                        'status'      => $appointment->status,
                        'description' => Str::limit($appointment->concern ?? '', 50) // ✅ Use 'concern' field
                    ]
                ];
            });

        return view('admin.calendar.index', [
            'appointments' => $appointments,
        ]);
    }
}

