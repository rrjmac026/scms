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
                        <!-- Student Selection -->
                        {{-- Replace the Student Selection div with this enhanced searchable version --}}

                        <!-- Student Selection with Search -->
                        <div>
                            <x-input-label for="student_id" value="{{ __('Student') }}" />
                            
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
                                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
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
                                            >
                                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $student->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $student->student_number }}
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
                            <x-input-label for="counselor_id" value="{{ __('Counselor') }}" />
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="auto_assign" id="auto_assign" class="rounded border-gray-300 dark:border-gray-700 text-pink-600"
                                        {{ old('auto_assign') ? 'checked' : '' }}>
                                    <label for="auto_assign" class="text-sm text-gray-600 dark:text-gray-400">Auto-assign counselor based on student's grade level</label>
                                </div>
                                
                                <select name="counselor_id" id="counselor_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500">
                                    <option value="">Select Counselor</option>
                                    @foreach($counselors as $counselor)
                                        <option value="{{ $counselor->id }}" {{ old('counselor_id') == $counselor->id ? 'selected' : '' }}>
                                            {{ $counselor->user->name }} - {{ $counselor->specialization }}
                                        </option>
                                    @endforeach
                                </select>
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

                        <!-- Time Slot Selection with Visual Cards -->
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
                            
                            <!-- Time selection error message -->
                            <div id="time-error" class="hidden mt-2 text-sm text-red-600 dark:text-red-400">
                                <i class="fas fa-exclamation-circle"></i>
                                <span id="time-error-message"></span>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" value="{{ __('Status') }}" />
                            <select name="status" id="status" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500" required>
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
                            <x-input-label for="concern" value="{{ __('Concern/Notes') }}" />
                            <textarea id="concern" name="concern" rows="4" 
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500" 
                                required 
                                maxlength="500">{{ old('concern') }}</textarea>
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

            const bookedSlots = @json($bookedSlots ?? []);

            // Character counter
            concernTextarea.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });

            // Initialize character count
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
                    updateTimeSlots(selectedDate);
                } catch (error) {
                    console.error('Date validation error:', error);
                    showTimeError('Invalid date selected');
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

                    // Remove selected class from all buttons
                    timeSlotButtons.forEach(btn => btn.classList.remove('selected'));

                    // Add selected class to clicked button
                    this.classList.add('selected');

                    // Update hidden input
                    timeHiddenInput.value = this.dataset.time;
                    
                    hideTimeError();
                });
            });

            // Form submission validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const errors = [];

                // Check if date is selected
                if (!dateInput.value) {
                    errors.push('Please select a date');
                    isValid = false;
                }

                // Check if time is selected
                if (!timeHiddenInput.value) {
                    errors.push('Please select a time slot');
                    showTimeError('Please select a time slot');
                    isValid = false;
                }

                // Check if student is selected
                const studentSelect = document.getElementById('student_id');
                if (!studentSelect.value) {
                    errors.push('Please select a student');
                    isValid = false;
                }

                // Check if category is selected
                const categorySelect = document.getElementById('counseling_category_id');
                if (!categorySelect.value) {
                    errors.push('Please select a counseling category');
                    isValid = false;
                }

                // Check if concern is filled
                if (!concernTextarea.value.trim()) {
                    errors.push('Please provide concern/notes');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    
                    // Show errors at the top
                    if (errors.length > 0) {
                        alert('Please fix the following errors:\n\n• ' + errors.join('\n• '));
                    }
                    
                    // Scroll to first error
                    const firstError = document.querySelector('.text-red-600, #time-error:not(.hidden)');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    
                    return false;
                }

                // Disable submit button and show loading state
                submitBtn.disabled = true;
                submitBtnText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
            });

            function updateTimeSlots(selectedDate) {
                console.log('Selected date:', selectedDate);
                console.log('Booked slots:', bookedSlots);
                
                let hasAvailableSlots = false;

                timeSlotButtons.forEach(button => {
                    const time = button.dataset.time;
                    
                    const isBooked = bookedSlots.some(slot => {
                        const slotMatches = slot.preferred_date === selectedDate && slot.preferred_time === time;
                        if (slotMatches) {
                            console.log('Found booked slot:', slot);
                        }
                        return slotMatches;
                    });

                    // Reset classes
                    button.classList.remove('available', 'booked', 'selected');
                    button.disabled = false;

                    if (isBooked) {
                        button.classList.add('booked');
                        button.disabled = true;
                        console.log('Marking as booked:', time);
                    } else {
                        button.classList.add('available');
                        hasAvailableSlots = true;
                    }
                });

                // Clear selection
                timeHiddenInput.value = '';

                // Show message if no slots available
                if (!hasAvailableSlots) {
                    showTimeError('No available time slots for this date. Please choose another date.');
                } else {
                    hideTimeError();
                }
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

            // Restore old time selection if validation fails
            @if(old('preferred_time'))
                const oldTime = "{{ old('preferred_time') }}";
                const oldDate = "{{ old('preferred_date') }}";
                
                if (oldDate) {
                    setTimeout(() => {
                        updateTimeSlots(oldDate);
                        
                        // Select the old time
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
                if (!e.target.closest('.relative') && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            });
        });
    </script>

    <script>
        // Add this at the end of your existing scripts
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