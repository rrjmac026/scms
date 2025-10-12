{{-- resources/views/students/appointments/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Book Appointment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('student.appointments.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Preferred Date -->
                        <div>
                            <x-input-label for="preferred_date" :value="__('Preferred Date')" />
                            <x-text-input id="preferred_date" name="preferred_date" type="date" 
                                class="mt-1 block w-full" required min="{{ date('Y-m-d') }}" />
                            <x-input-error :messages="$errors->get('preferred_date')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label for="counseling_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Counseling Category
                            </label>
                            <select name="counseling_category_id" id="counseling_category_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Time Slot Selection with Visual Cards -->
                        <div>
                            <x-input-label for="preferred_time" :value="__('Preferred Time')" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-3">Select a date first to see available time slots</p>
                            
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

                        <!-- Concern/Reason -->
                        <div>
                            <x-input-label for="concern" :value="__('Reason for Appointment')" />
                            <textarea id="concern" name="concern" rows="4" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800"
                                placeholder="Please briefly describe your reason for seeking counseling..."></textarea>
                            <x-input-error :messages="$errors->get('concern')" class="mt-2" />
                        </div>

                        <div class="flex justify-end gap-4">
                            <x-secondary-button type="button" onclick="history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Book Appointment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
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
            const dateInput = document.getElementById('preferred_date');
            const timeHiddenInput = document.getElementById('preferred_time');
            const timeSlotButtons = document.querySelectorAll('.time-slot');

            const bookedSlots = @json($bookedSlots);

            // Handle date change
            dateInput.addEventListener('change', function () {
                const selectedDate = new Date(this.value);
                const day = selectedDate.getDay();

                if (day === 0 || day === 6) {
                    alert('Appointments can only be booked on weekdays (Monday to Friday).');
                    this.value = '';
                    return;
                }

                updateTimeSlots(this.value);
            });

            // Handle time slot selection
            timeSlotButtons.forEach(button => {
                button.addEventListener('click', function () {
                    if (this.classList.contains('booked') || this.disabled) {
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

            function updateTimeSlots(selectedDate) {
                console.log('Selected date:', selectedDate);
                console.log('Booked slots:', bookedSlots);
                
                timeSlotButtons.forEach(button => {
                    const time = button.dataset.time; // e.g., "14:00"
                    
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
                    }
                });

                // Clear selection
                timeHiddenInput.value = '';
            }
        });
    </script>
</x-app-layout>