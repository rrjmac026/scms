{{-- resources/views/admin/appointments/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                    <i class="fas fa-calendar-plus text-2xl text-pink-500"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('Create Appointment') }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Schedule a new counseling appointment
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
                <form action="{{ route('admin.appointments.store') }}" method="POST" class="p-6" id="appointmentForm">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Selection with Search -->
                        <div>
                            <x-input-label for="student_id">
                                {{ __('Student') }} <span class="text-red-500">*</span>
                            </x-input-label>
                            <div class="relative mt-1">
                                <!-- Search Input -->
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="student_search" 
                                        placeholder="Search student by name or number..."
                                        class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500 pl-10"
                                        autocomplete="off"
                                    />
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    
                                    <!-- Clear button -->
                                    <button 
                                        type="button" 
                                        id="clear_search" 
                                        class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <!-- Hidden select for form submission -->
                                <select name="student_id" id="student_id" class="hidden" required>
                                    <option value="">Select Student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" 
                                            data-grade="{{ $student->grade_level ?? 'N/A' }}"
                                            {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->user->name }} ({{ $student->student_number }})
                                        </option>
                                    @endforeach
                                </select>
                                
                                <!-- Dropdown list -->
                                <div 
                                    id="student_dropdown" 
                                    class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg max-h-64 overflow-y-auto"
                                >
                                    <div id="student_list" class="py-1">
                                        @foreach($students as $student)
                                            <div 
                                                class="student-option px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors"
                                                data-id="{{ $student->id }}"
                                                data-name="{{ strtolower($student->user->name) }}"
                                                data-number="{{ strtolower($student->student_number) }}"
                                                data-grade="{{ $student->grade_level ?? 'N/A' }}"
                                            >
                                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $student->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $student->student_number }} - Grade {{ $student->grade_level ?? 'N/A' }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- No results message -->
                                    <div id="no_results" class="hidden px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-search mb-2 text-2xl"></i>
                                        <p>No students found</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Selected student display -->
                            <div id="selected_student" class="hidden mt-2 p-3 bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-800 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100" id="selected_name"></div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400" id="selected_number"></div>
                                    </div>
                                    <button 
                                        type="button" 
                                        id="remove_student"
                                        class="text-pink-500 hover:text-pink-700 dark:hover:text-pink-400"
                                        title="Remove selection"
                                    >
                                        <i class="fas fa-times-circle text-xl"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                        </div>

                        <!-- Counselor Selection -->
                        <div>
                            <x-input-label for="counselor_id" value="" />
                                {{ __('Counselor') }} <span class="text-red-500">*</span>
                            </xi-input-label>
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
                                        <option value="{{ $counselor->id }}" {{ old('counselor_id') == $counselor->id ? 'selected' : '' }}>
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

                        <!-- Date -->
                        <div>
                            <x-input-label for="preferred_date" value="{{ __('Date') }}" />
                            <x-text-input id="preferred_date" type="date" name="preferred_date" class="mt-1 block w-full"
                                :value="old('preferred_date')" required min="{{ date('Y-m-d') }}" />
                            <x-input-error :messages="$errors->get('preferred_date')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="counseling_category_id" value="{{ __('Category') }}" />
                            <select name="counseling_category_id" id="counseling_category_id"
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('counseling_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('counseling_category_id')" class="mt-2" />
                        </div>

                        <!-- Time Slot Selection -->
                        <div class="md:col-span-2">
                            <x-input-label for="preferred_time" :value="__('Preferred Time')" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-3">
                                <i class="fas fa-info-circle"></i>
                                Select a date first to see available time slots
                            </p>
                            
                            <!-- Hidden input to store selected time -->
                            <input type="hidden" id="preferred_time" name="preferred_time" required>
                            
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
                            <select name="status" id="status"
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500"
                                required disabled>
                                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-info-circle"></i>
                                Approved appointments will be added to Google Calendar (if connected)
                            </p>
                        </div>

                        <!-- Concern/Notes -->
                        <div class="md:col-span-2">
                            <x-input-label for="concern" value="{{ __('Reason for Appointment') }}" />
                            <textarea id="concern" name="concern" rows="4" 
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500" 
                                required 
                                maxlength="500"
                                placeholder="Please briefly describe the reason for this appointment...">{{ old('concern') }}</textarea>
                            <div class="flex justify-between mt-1">
                                <x-input-error :messages="$errors->get('concern')" />
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    <span id="char-count">0</span>/500
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
                            <span id="submitBtnText">{{ __('Create Appointment') }}</span>
                        </x-primary-button>
                    </div>
                </form>
            </div>
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

        /* Available slots - Lighter green */
        .time-slot.available {
            border-color: #22c55e;
            background-color: #f0fdf4;
        }

        .dark .time-slot.available {
            background-color: #052e16;
            border-color: #22c55e;
        }

        .time-slot.available:hover {
            background-color: #dcfce7;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .dark .time-slot.available:hover {
            background-color: #14532d;
        }

        /* Booked slots - Red */
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

        /* Selected slot - Bold Blue */
        .time-slot.selected {
            border-color: #3b82f6;
            background-color: #ede9fe;
            border-width: 3px;
        }

        .dark .time-slot.selected {
            background-color: #3b82f6;
            border-color: #1e3a8a;
            border-width: 3px;
        }

        .time-slot.selected .time {
            color: #3b82f6;
            font-weight: 700;
        }

        .dark .time-slot.selected .time {
            color: #e9d5ff;
            font-weight: 700;
        }

        .time-slot.selected .status::after {
            content: '(Selected)';
            color: #1e3a8a;
            font-weight: 600;
        }

        .dark .time-slot.selected .status::after {
            color: #c4b5fd;
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
    const counselorSelect = document.getElementById('counselor_id');
    const studentSelect = document.getElementById('student_id');
    const autoAssignCheckbox = document.getElementById('auto_assign');

    // Parse booked slots with counselor_id
    const bookedSlots = {!! json_encode($bookedSlots ?? []) !!};

    // Get all counselors data for auto-assignment
    const counselors = @json($counselors->map(function($c) {
        return [
            'id' => $c->id,
            'grade_level' => $c->assigned_grade_level,
            'name' => $c->user->name
        ];
    }));

    const students = @json($students->map(function($s) {
        return [
            'id' => $s->id,
            'grade_level' => $s->grade_level
        ];
    }));

    // Character counter
    concernTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Initialize character count
    charCount.textContent = concernTextarea.value.length;

    // Get the current selected or auto-assigned counselor
    function getCurrentCounselorId() {
        if (autoAssignCheckbox.checked) {
            // Get student's grade level
            const selectedStudentId = parseInt(studentSelect.value);
            const student = students.find(s => s.id === selectedStudentId);
            
            if (student && student.grade_level) {
                // Find counselor for this grade level
                const counselor = counselors.find(c => c.grade_level === student.grade_level);
                return counselor ? counselor.id : null;
            }
            return null;
        } else {
            return counselorSelect.value ? parseInt(counselorSelect.value) : null;
        }
    }

    // Update time slots when date OR counselor changes
    function refreshTimeSlots() {
        const selectedDate = dateInput.value;
        const counselorId = getCurrentCounselorId();
        
        if (selectedDate && counselorId) {
            updateTimeSlots(selectedDate, counselorId);
        } else if (selectedDate && !counselorId) {
            // Date selected but no counselor - disable all slots
            disableAllTimeSlots();
        }
    }

    // Handle date change
    dateInput.addEventListener('change', function () {
        const selectedDate = new Date(this.value);
        const day = selectedDate.getDay();

        if (day === 0 || day === 6) {
            alert('Appointments can only be booked on weekdays (Monday to Friday).');
            this.value = '';
            disableAllTimeSlots();
            return;
        }

        refreshTimeSlots();
    });

    // Handle counselor selection change
    counselorSelect.addEventListener('change', refreshTimeSlots);

    // Handle student selection change (for auto-assign)
    studentSelect.addEventListener('change', function() {
        if (autoAssignCheckbox.checked) {
            refreshTimeSlots();
        }
    });

    // Handle auto-assign checkbox
    autoAssignCheckbox.addEventListener('change', function() {
        counselorSelect.disabled = this.checked;
        if (this.checked) {
            counselorSelect.value = '';
        }
        refreshTimeSlots();
    });

    // Handle time slot selection
    timeSlotButtons.forEach(button => {
        button.addEventListener('click', function () {
            if (this.classList.contains('booked') || this.disabled) {
                return;
            }

            if (!dateInput.value) {
                alert('Please select a date first');
                return;
            }

            const counselorId = getCurrentCounselorId();
            if (!counselorId) {
                alert('Please select a counselor or enable auto-assign first');
                return;
            }

            // Remove selected class from all buttons
            timeSlotButtons.forEach(btn => btn.classList.remove('selected'));

            // Add selected class to clicked button
            this.classList.add('selected');

            // Update hidden input
            timeHiddenInput.value = this.dataset.time;
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
            isValid = false;
        }

        if (!studentSelect.value) {
            errors.push('Please select a student');
            isValid = false;
        }

        if (!autoAssignCheckbox.checked && !counselorSelect.value) {
            errors.push('Please select a counselor or enable auto-assign');
            isValid = false;
        }

        const categorySelect = document.getElementById('counseling_category_id');
        if (!categorySelect.value) {
            errors.push('Please select a counseling category');
            isValid = false;
        }

        if (!concernTextarea.value.trim()) {
            errors.push('Please provide a reason for the appointment');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n• ' + errors.join('\n• '));
            return false;
        }

        submitBtn.disabled = true;
        submitBtnText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
    });

    function updateTimeSlots(selectedDate, counselorId) {
        timeSlotButtons.forEach(button => {
            const time = button.dataset.time;
            
            // Check if THIS COUNSELOR has this time slot booked
            const isBooked = bookedSlots.some(slot => {
                return slot.preferred_date === selectedDate &&
                       slot.preferred_time === time &&
                       parseInt(slot.counselor_id) === parseInt(counselorId);
            });

            // Reset all classes first
            button.classList.remove('available', 'booked', 'selected');
            button.disabled = false;

            // Ensure we have a status element
            const statusEl = button.querySelector('.status');
            if (statusEl) {
                statusEl.innerHTML = ''; // clear previous status text
            }
            button.removeAttribute('title');

            if (isBooked) {
                button.classList.add('booked');
                button.disabled = true;

                // Find the exact booked slot object so we can show details
                const slotObj = bookedSlots.find(slot => {
                    return slot.preferred_date === selectedDate &&
                           slot.preferred_time === time &&
                           (String(slot.counselor_id) === String(counselorId));
                });

                if (slotObj && statusEl) {
                    // Use the consistent data structure from controller
                    const studentName = slotObj.student_name || 'Student';
                    const counselorName = slotObj.counselor_name || 'Counselor';
                    const studentNumber = slotObj.student_number || '';

                    // Display student information
                    const displayText = studentNumber ? 
                        `${studentName} (${studentNumber})` : 
                        studentName;
                    
                    statusEl.innerHTML = `<span class="text-xs text-red-600 dark:text-red-300">${displayText}</span>`;
                    button.title = `Booked: ${studentName} with ${counselorName}`;
                } else if (statusEl) {
                    // Generic booked label if details not available
                    statusEl.textContent = '(Booked)';
                    button.title = 'Booked';
                }
            } else {
                button.classList.add('available');
                if (statusEl) {
                    statusEl.textContent = '';
                }
                button.title = 'Available';
            }
        });

        // Clear any previous selection
        timeHiddenInput.value = '';
    }

    function disableAllTimeSlots() {
        timeSlotButtons.forEach(button => {
            button.classList.remove('available', 'booked', 'selected');
            button.disabled = true;
        });
        timeHiddenInput.value = '';
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

// Student search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('student_search');
    const clearSearchBtn = document.getElementById('clear_search');
    const dropdown = document.getElementById('student_dropdown');
    const studentList = document.getElementById('student_list');
    const noResults = document.getElementById('no_results');
    const studentOptions = document.querySelectorAll('.student-option');
    const selectedStudent = document.getElementById('selected_student');
    const selectedName = document.getElementById('selected_name');
    const selectedNumber = document.getElementById('selected_number');
    const removeStudentBtn = document.getElementById('remove_student');
    const hiddenSelect = document.getElementById('student_id');

    // Show dropdown when focusing on search input
    searchInput.addEventListener('focus', () => {
        dropdown.classList.remove('hidden');
    });

    // Handle search input
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        let hasResults = false;

        // Toggle clear button
        clearSearchBtn.classList.toggle('hidden', !searchTerm);

        studentOptions.forEach(option => {
            const name = option.dataset.name;
            const number = option.dataset.number;
            
            if (name.includes(searchTerm) || number.includes(searchTerm)) {
                option.classList.remove('hidden');
                hasResults = true;
            } else {
                option.classList.add('hidden');
            }
        });

        // Toggle no results message
        noResults.classList.toggle('hidden', hasResults);
    });

    // Clear search
    clearSearchBtn.addEventListener('click', () => {
        searchInput.value = '';
        clearSearchBtn.classList.add('hidden');
        studentOptions.forEach(option => option.classList.remove('hidden'));
        noResults.classList.add('hidden');
        searchInput.focus();
    });

    // Handle student selection
    studentOptions.forEach(option => {
        option.addEventListener('click', () => {
            const id = option.dataset.id;
            const name = option.querySelector('.font-medium').textContent.trim();
            const number = option.querySelector('.text-sm').textContent.trim();

            // Update hidden select
            hiddenSelect.value = id;

            // Update display
            selectedName.textContent = name;
            selectedNumber.textContent = number;
            selectedStudent.classList.remove('hidden');
            
            // Clear and hide search
            searchInput.value = '';
            dropdown.classList.add('hidden');
        });
    });

    // Remove selected student
    removeStudentBtn.addEventListener('click', () => {
        hiddenSelect.value = '';
        selectedStudent.classList.add('hidden');
        searchInput.value = '';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#student_search') && 
            !e.target.closest('#student_dropdown') && 
            !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
        }
    });

    // Restore selected student if validation fails
    @if(old('student_id'))
        const selectedOption = hiddenSelect.options[hiddenSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const studentText = selectedOption.textContent.trim();
            const namePart = studentText.split('(')[0].trim();
            const numberPart = studentText.match(/\(([^)]+)\)/)?.[1] || '';
            
            selectedName.textContent = namePart;
            selectedNumber.textContent = numberPart;
            selectedStudent.classList.remove('hidden');
        }
    @endif
});

// Auto-assign functionality
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

        // Student search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('student_search');
            const clearSearchBtn = document.getElementById('clear_search');
            const dropdown = document.getElementById('student_dropdown');
            const studentList = document.getElementById('student_list');
            const noResults = document.getElementById('no_results');
            const studentOptions = document.querySelectorAll('.student-option');
            const selectedStudent = document.getElementById('selected_student');
            const selectedName = document.getElementById('selected_name');
            const selectedNumber = document.getElementById('selected_number');
            const removeStudentBtn = document.getElementById('remove_student');
            const hiddenSelect = document.getElementById('student_id');

            // Show dropdown when focusing on search input
            searchInput.addEventListener('focus', () => {
                dropdown.classList.remove('hidden');
            });

            // Handle search input
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                let hasResults = false;

                // Toggle clear button
                clearSearchBtn.classList.toggle('hidden', !searchTerm);

                studentOptions.forEach(option => {
                    const name = option.dataset.name;
                    const number = option.dataset.number;
                    
                    if (name.includes(searchTerm) || number.includes(searchTerm)) {
                        option.classList.remove('hidden');
                        hasResults = true;
                    } else {
                        option.classList.add('hidden');
                    }
                });

                // Toggle no results message
                noResults.classList.toggle('hidden', hasResults);
            });

            // Clear search
            clearSearchBtn.addEventListener('click', () => {
                searchInput.value = '';
                clearSearchBtn.classList.add('hidden');
                studentOptions.forEach(option => option.classList.remove('hidden'));
                noResults.classList.add('hidden');
                searchInput.focus();
            });

            // Handle student selection
            studentOptions.forEach(option => {
                option.addEventListener('click', () => {
                    const id = option.dataset.id;
                    const name = option.querySelector('.font-medium').textContent.trim();
                    const number = option.querySelector('.text-sm').textContent.trim();

                    // Update hidden select
                    hiddenSelect.value = id;

                    // Update display
                    selectedName.textContent = name;
                    selectedNumber.textContent = number;
                    selectedStudent.classList.remove('hidden');
                    
                    // Clear and hide search
                    searchInput.value = '';
                    dropdown.classList.add('hidden');
                });
            });

            // Remove selected student
            removeStudentBtn.addEventListener('click', () => {
                hiddenSelect.value = '';
                selectedStudent.classList.add('hidden');
                searchInput.value = '';
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#student_search') && 
                    !e.target.closest('#student_dropdown') && 
                    !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            });

            // Restore selected student if validation fails
            @if(old('student_id'))
                const selectedOption = hiddenSelect.options[hiddenSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const studentText = selectedOption.textContent.trim();
                    const namePart = studentText.split('(')[0].trim();
                    const numberPart = studentText.match(/\(([^)]+)\)/)?.[1] || '';
                    
                    selectedName.textContent = namePart;
                    selectedNumber.textContent = numberPart;
                    selectedStudent.classList.remove('hidden');
                }
            @endif
        });

        // Auto-assign functionality
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