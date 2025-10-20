{{-- resources/views/admin/appointments/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                    <i class="fas fa-edit text-2xl text-pink-500"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('Edit Appointment') }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Update appointment details
                    </p>
                </div>
            </div>
            <x-secondary-button onclick="history.back()" class="bg-white">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Error Alert -->
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-sm text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            

            <!-- Validation Errors Summary -->
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">
                                Please fix the following errors:
                            </h3>
                            <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST" class="p-6" id="appointmentForm">
                    @csrf
                    @method('PUT')
   

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Selection -->
                        <div>
                            <x-input-label for="student_id" value="{{ __('Student') }}" />
                            
                            <select name="student_id" id="student_id" 
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500"
                                required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id', $appointment->student_id) == $student->id ? 'selected' : '' }}>
                                        {{ $student->user->name }} ({{ $student->student_number }})
                                    </option>
                                @endforeach
                            </select>
                            
                            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                        </div>

                        <!-- Counselor Selection -->
                        <div>
                            <x-input-label for="counselor_id" value="{{ __('Counselor') }}" />
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="auto_assign" id="auto_assign" value="1" 
                                        class="rounded border-gray-300 dark:border-gray-700 text-pink-600"
                                        {{ old('auto_assign') ? 'checked' : '' }}>
                                    <label for="auto_assign" class="text-sm text-gray-600 dark:text-gray-400">
                                        Auto-assign counselor based on student's grade level
                                    </label>
                                </div>
                                
                                <select name="counselor_id" id="counselor_id" 
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500"
                                    {{ old('auto_assign') ? 'disabled' : '' }}>
                                    <option value="">Select Counselor (or use auto-assign)</option>
                                    @foreach($counselors as $counselor)
                                        <option value="{{ $counselor->id }}" {{ old('counselor_id', $appointment->counselor_id) == $counselor->id ? 'selected' : '' }}>
                                            {{ $counselor->user->name }} - {{ $counselor->specialization }}
                                            @if($counselor->assigned_grade_level)
                                                (Grade {{ $counselor->assigned_grade_level }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-info-circle"></i>
                                    Check auto-assign to automatically select counselor based on student's grade level
                                </p>
                            </div>
                            <x-input-error :messages="$errors->get('counselor_id')" class="mt-2" />
                        </div>
             <!-- Time selection error message -->
<div id="time-error" class="hidden mt-2 text-sm text-red-600 dark:text-red-400">
    <i class="fas fa-exclamation-circle"></i>
    <span id="time-error-message"></span>
</div>
                        <!-- Date -->
                        <div>
                            <x-input-label for="preferred_date" value="{{ __('Date') }}" />
                            <x-text-input id="preferred_date" type="date" name="preferred_date" class="mt-1 block w-full"
                                :value="old('preferred_date', $appointment->preferred_date instanceof \Carbon\Carbon ? $appointment->preferred_date->format('Y-m-d') : $appointment->preferred_date)" 
                                required min="{{ date('Y-m-d') }}" />
                            <x-input-error :messages="$errors->get('preferred_date')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="counseling_category_id" value="{{ __('Category') }}" />
                            <select name="counseling_category_id" id="counseling_category_id"
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('counseling_category_id', $appointment->counseling_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('counseling_category_id')" class="mt-2" />
                        </div>

                        <!-- Time Slot Selection with Visual Cards -->
                        <div class="md:col-span-2">
                            <x-input-label for="preferred_time" :value="__('Preferred Time')" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-3">
                                <i class="fas fa-info-circle"></i>
                                Select a date first to see available time slots
                            </p>
                            
                            <!-- Hidden input to store selected time -->
                            <input type="hidden" id="preferred_time" name="preferred_time" value="{{ old('preferred_time', $appointment->preferred_time) }}" required>
                            
                            <!-- Time slot grid -->
                            <div id="time-slots-container" class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-2">
                                <button type="button" class="time-slot" data-time="09:00" disabled>
                                    <span class="time">9:00 AM</span>
                                    <span class="status"></span>
                                </button>
                                <button type="button" class="time-slot" data-time="10:00" disabled>
                                    <span class="time">10:00 AM</span>
                                    <span class="status"></span>
                                </button>
                                <button type="button" class="time-slot" data-time="11:00" disabled>
                                    <span class="time">11:00 AM</span>
                                    <span class="status"></span>
                                </button>
                                <button type="button" class="time-slot" data-time="13:00" disabled>
                                    <span class="time">1:00 PM</span>
                                    <span class="status"></span>
                                </button>
                                <button type="button" class="time-slot" data-time="14:00" disabled>
                                    <span class="time">2:00 PM</span>
                                    <span class="status"></span>
                                </button>
                                <button type="button" class="time-slot" data-time="15:00" disabled>
                                    <span class="time">3:00 PM</span>
                                    <span class="status"></span>
                                </button>
                                <button type="button" class="time-slot" data-time="16:00" disabled>
                                    <span class="time">4:00 PM</span>
                                    <span class="status"></span>
                                </button>
                            </div>
                            
                            <!-- Legend -->
                            <div class="flex gap-4 mt-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded bg-green-500"></div>
                                    <span class="text-gray-600 dark:text-gray-400">Available</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded bg-red-500"></div>
                                    <span class="text-gray-600 dark:text-gray-400">Booked</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded bg-blue-500"></div>
                                    <span class="text-gray-600 dark:text-gray-400">Selected</span>
                                </div>
                            </div>
                            
                            <x-input-error :messages="$errors->get('preferred_time')" class="mt-2" />
                            
                          
                        </div>

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" value="{{ __('Status') }}" />
                            <select name="status" id="status" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500" required>
                                <option value="pending" {{ old('status', $appointment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status', $appointment->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="accepted" {{ old('status', $appointment->status) == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="declined" {{ old('status', $appointment->status) == 'declined' ? 'selected' : '' }}>Declined</option>
                                <option value="rejected" {{ old('status', $appointment->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-info-circle"></i>
                                Approved appointments will be synced to Google Calendar (if connected)
                            </p>
                        </div>

                        <!-- Current Status Display -->
                        <!-- <div>
                            <x-input-label value="{{ __('Current Status') }}" />
                            <div class="mt-1 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-info-circle text-blue-500"></i>
                                    <div>
                                        <div class="text-sm font-semibold text-blue-800 dark:text-blue-200 uppercase">
                                            {{ str_replace('_', ' ', $appointment->status) }}
                                        </div>
                                        @if($appointment->google_event_id)
                                            <div class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                                                <i class="fas fa-check-circle"></i> Synced with Google Calendar
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <!-- Concern/Notes -->
                        <div class="md:col-span-2">
                            <x-input-label for="concern" value="{{ __('Reason for Appointments') }}" />
                            <textarea id="concern" name="concern" rows="4" 
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500" 
                                required 
                                maxlength="500">{{ old('concern', $appointment->concern) }}</textarea>
                            <div class="flex justify-between mt-1">
                                <x-input-error :messages="$errors->get('concern')" />
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    <span id="char-count">{{ strlen($appointment->concern ?? '') }}</span>/500
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-4">
                        <x-secondary-button type="button" onclick="history.back()">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-primary-button id="submitBtn">
                            <i class="fas fa-save mr-2"></i>
                            <span id="submitBtnText">{{ __('Update Appointment') }}</span>
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Status Action Buttons Section -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        <i class="fas fa-tasks mr-2 text-pink-500"></i>Status Actions
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        @if($appointment->status === 'pending')
                            <!-- Approve Button -->
                            <form action="{{ route('admin.appointments.approve', $appointment) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm transition-colors whitespace-nowrap"
                                        title="Approve and auto-assign counselor">
                                    <i class="fas fa-check mr-2"></i> Approve
                                </button>
                            </form>

                            <!-- Decline Button (for pending) -->
                            <form action="{{ route('admin.appointments.decline', $appointment) }}" method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to decline this pending appointment?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm transition-colors whitespace-nowrap"
                                        title="Decline pending appointment">
                                    <i class="fas fa-times-circle mr-2"></i> Decline
                                </button>
                            </form>
                        @endif

                        @if($appointment->status === 'approved')
                            <!-- Decline Button (for approved) -->
                            <form action="{{ route('admin.appointments.decline', $appointment) }}" method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to decline this approved appointment?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm transition-colors whitespace-nowrap"
                                        title="Decline approved appointment">
                                    <i class="fas fa-times-circle mr-2"></i> Decline
                                </button>
                            </form>
                        @endif

                        @if($appointment->status === 'accepted')
                            <!-- Reject Button (only for accepted) -->
                            <form action="{{ route('admin.appointments.reject', $appointment) }}" method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to reject this accepted appointment?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors whitespace-nowrap"
                                        title="Reject accepted appointment">
                                    <i class="fas fa-ban mr-2"></i> Reject
                                </button>
                            </form>
                        @endif

                        @if(!in_array($appointment->status, ['pending', 'approved', 'accepted']))
                            <div class="text-sm text-gray-500 dark:text-gray-400 py-2">
                                No status actions available for {{ ucfirst($appointment->status) }} appointments.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Delete Button Section -->
            @if ($appointment->status !== 'approved')
                <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <form action="{{ route('admin.appointments.destroy', $appointment->id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this appointment? This action cannot be undone.')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors">
                                <i class="fas fa-trash mr-2"></i>{{ __('Delete Appointment') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .time-slot {
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            background-color: white;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
        }

        .dark .time-slot {
            background-color: #1f2937;
            border-color: #374151;
        }

        .time-slot .time {
            font-weight: 600;
            color: #374151;
        }

        .dark .time-slot .time {
            color: #d1d5db;
        }

        .time-slot .status {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .time-slot:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Available slots */
        .time-slot.available {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        .dark .time-slot.available {
            background-color: #064e3b;
            border-color: #10b981;
        }

        .time-slot.available:hover {
            background-color: #dcfce7;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .dark .time-slot.available:hover {
            background-color: #065f46;
        }

        /* Booked slots */
        .time-slot.booked {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .dark .time-slot.booked {
            background-color: #7f1d1d;
            border-color: #ef4444;
        }

        .time-slot.booked .status::after {
            content: '(Booked)';
        }

        /* Selected slot */
        .time-slot.selected {
            border-color: #3b82f6;
            background-color: #dbeafe;
        }

        .dark .time-slot.selected {
            background-color: #1e3a8a;
            border-color: #3b82f6;
        }

        .time-slot.selected .status::after {
            content: '(Selected)';
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('appointmentForm');
            const dateInput = document.getElementById('preferred_date');
            const timeHiddenInput = document.getElementById('preferred_time');
            const timeSlotButtons = document.querySelectorAll('.time-slot');
            const concernTextarea = document.getElementById('concern');
            const charCount = document.getElementById('char-count');
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            const timeError = document.getElementById('time-error');
            const timeErrorMessage = document.getElementById('time-error-message');
            const counselorSelect = document.getElementById('counselor_id');

            const bookedSlots = @json($bookedSlots ?? []);
            const currentAppointmentId = {{ $appointment->id }};
            const currentCounselorId = {{ $appointment->counselor_id ?? 'null' }};

            console.log('Current appointment ID:', currentAppointmentId);
            console.log('Current counselor ID:', currentCounselorId);
            console.log('Booked slots:', bookedSlots);

            // Character counter
            concernTextarea.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });

            charCount.textContent = concernTextarea.value.length;

            // Handle date change
            dateInput.addEventListener('change', function () {
                try {
                    const selectedDate = this.value;
                    if (!selectedDate) {
                        showTimeError('Please select a date');
                        return;
                    }

                    const dateObj = new Date(selectedDate + 'T00:00:00');
                    const day = dateObj.getDay();

                    if (day === 0 || day === 6) {
                        showTimeError('Appointments can only be booked on weekdays (Monday to Friday)');
                        this.value = '';
                        disableAllTimeSlots();
                        return;
                    }

                    hideTimeError();
                    const counselorId = counselorSelect.value ? parseInt(counselorSelect.value) : null;
                    if (counselorId) {
                        updateTimeSlots(selectedDate, counselorId);
                    } else {
                        showTimeError('Please select a counselor first');
                    }
                } catch (error) {
                    console.error('Date validation error:', error);
                    showTimeError('Invalid date selected');
                }
            });

            // Handle counselor change
            counselorSelect.addEventListener('change', function() {
                const selectedDate = dateInput.value;
                const counselorId = this.value ? parseInt(this.value) : null;
                
                if (selectedDate && counselorId) {
                    updateTimeSlots(selectedDate, counselorId);
                } else if (selectedDate && !counselorId) {
                    disableAllTimeSlots();
                    showTimeError('Please select a counselor');
                }
            });

            // Handle time slot selection
            timeSlotButtons.forEach(button => {
                button.addEventListener('click', function () {
                    if (this.classList.contains('booked') || this.disabled) {
                        return;
                    }

                    if (!dateInput.value) {
                        showTimeError('Please select a date first');
                        return;
                    }

                    if (!counselorSelect.value) {
                        showTimeError('Please select a counselor first');
                        return;
                    }

                    timeSlotButtons.forEach(btn => btn.classList.remove('selected'));
                    this.classList.add('selected');
                    timeHiddenInput.value = this.dataset.time;
                    hideTimeError();
                });
            });

            // Form submission validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const errors = [];

                if (!dateInput.value) {
                    errors.push('Please select a date');
                    isValid = false;
                }

                if (!timeHiddenInput.value) {
                    errors.push('Please select a time slot');
                    showTimeError('Please select a time slot');
                    isValid = false;
                }

                const studentSelect = document.getElementById('student_id');
                if (!studentSelect.value) {
                    errors.push('Please select a student');
                    isValid = false;
                }

                if (!counselorSelect.value) {
                    errors.push('Please select a counselor');
                    isValid = false;
                }

                const categorySelect = document.getElementById('counseling_category_id');
                if (!categorySelect.value) {
                    errors.push('Please select a counseling category');
                    isValid = false;
                }

                if (!concernTextarea.value.trim()) {
                    errors.push('Please provide concern/notes');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    
                    if (errors.length > 0) {
                        alert('Please fix the following errors:\n\n• ' + errors.join('\n• '));
                    }
                    
                    const firstError = document.querySelector('.text-red-600, #time-error:not(.hidden)');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    
                    return false;
                }

                submitBtn.disabled = true;
                submitBtnText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
            });

            function updateTimeSlots(selectedDate, counselorId) {
                console.log('=== UPDATE TIME SLOTS ===');
                console.log('Selected date:', selectedDate);
                console.log('Selected counselor:', counselorId);
                console.log('Booked slots:', bookedSlots);
                
                let hasAvailableSlots = false;

                timeSlotButtons.forEach(button => {
                    const time = button.dataset.time;
                    
                    console.log(`Checking slot: ${selectedDate} ${time} for counselor ${counselorId}`);
                    
                    // Check if THIS COUNSELOR has this time slot booked (excluding current appointment)
                    const isBooked = bookedSlots.some(slot => {
                        const dateMatches = slot.preferred_date === selectedDate;
                        const timeMatches = slot.preferred_time === time;
                        const counselorMatches = parseInt(slot.counselor_id) === parseInt(counselorId);
                        const matches = dateMatches && timeMatches && counselorMatches;
                        
                        if (matches) {
                            console.log(`✓ BOOKED: ${selectedDate} ${time} for counselor ${counselorId}`, slot);
                        }
                        
                        return matches;
                    });

                    // Reset classes
                    button.classList.remove('available', 'booked', 'selected');
                    button.disabled = false;

                    if (isBooked) {
                        button.classList.add('booked');
                        button.disabled = true;
                        console.log(`→ Marking as BOOKED: ${time}`);
                    } else {
                        button.classList.add('available');
                        hasAvailableSlots = true;
                        console.log(`→ Marking as AVAILABLE: ${time}`);
                    }
                });

                if (!hasAvailableSlots) {
                    showTimeError('No available time slots for this counselor on this date. Please choose another date or counselor.');
                } else {
                    hideTimeError();
                }
                
                console.log('========================');
            }

            function disableAllTimeSlots() {
                timeSlotButtons.forEach(button => {
                    button.classList.remove('available', 'booked', 'selected');
                    button.disabled = true;
                });
                timeHiddenInput.value = '';
            }

            function showTimeError(message) {
                timeErrorMessage.textContent = message;
                timeError.classList.remove('hidden');
            }

            function hideTimeError() {
                timeError.classList.add('hidden');
            }

            // Initialize time slots on page load with current date and counselor
            const currentDate = dateInput.value;
            const initialCounselorId = counselorSelect.value ? parseInt(counselorSelect.value) : null;
            
            if (currentDate && initialCounselorId) {
                setTimeout(() => {
                    updateTimeSlots(currentDate, initialCounselorId);
                    
                    const currentTime = timeHiddenInput.value;
                    if (currentTime) {
                        timeSlotButtons.forEach(btn => {
                            if (btn.dataset.time === currentTime) {
                                btn.classList.add('selected');
                            }
                        });
                    }
                }, 100);
            }

            // Restore old time selection if validation fails
            @if(old('preferred_time'))
                const oldTime = "{{ old('preferred_time') }}";
                const oldDate = "{{ old('preferred_date') }}";
                const oldCounselorId = "{{ old('counselor_id') }}";
                
                if (oldDate && oldCounselorId) {
                    setTimeout(() => {
                        updateTimeSlots(oldDate, parseInt(oldCounselorId));
                        
                        timeSlotButtons.forEach(btn => {
                            if (btn.dataset.time === oldTime) {
                                btn.classList.add('selected');
                                timeHiddenInput.value = oldTime;
                            }
                        });
                    }, 100);
                }
            @endif
        });
    </script>

    <script>
        // Auto-assign checkbox functionality
        document.addEventListener('DOMContentLoaded', function() {
            const autoAssignCheckbox = document.getElementById('auto_assign');
            const counselorSelect = document.getElementById('counselor_id');

            autoAssignCheckbox.addEventListener('change', function() {
                counselorSelect.disabled = this.checked;
                if (this.checked) {
                    counselorSelect.value = '';
                }
            });

            // Initial state
            counselorSelect.disabled = autoAssignCheckbox.checked;
        });
    </script>
</x-app-layout>