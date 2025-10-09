<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Appointment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Student Information Section -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Student Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Student -->
                                <div>
                                    <x-input-label for="student_id" :value="__('Student')" />
                                    <select id="student_id" name="student_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm"
                                        required>
                                        <option value="">Select Student</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" 
                                                {{ old('student_id', $appointment->student_id) == $student->id ? 'selected' : '' }}>
                                                {{ $student->user->first_name }} {{ $student->user->last_name }} 
                                                ({{ $student->student_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('student_id')" />
                                </div>

                                <!-- Counseling Category -->
                                <div>
                                    <x-input-label for="counseling_category_id" :value="__('Counseling Category')" />
                                    <select id="counseling_category_id" name="counseling_category_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm"
                                        required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('counseling_category_id', $appointment->counseling_category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('counseling_category_id')" />
                                </div>

                                <!-- Concern -->
                                <div class="md:col-span-2">
                                    <x-input-label for="concern" :value="__('Concern/Reason')" />
                                    <textarea id="concern" name="concern" rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm"
                                        required>{{ old('concern', $appointment->concern) }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('concern')" />
                                </div>
                            </div>
                        </div>

                        <!-- Appointment Details Section -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Appointment Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Counselor -->
                                <div>
                                    <x-input-label for="counselor_id" :value="__('Assigned Counselor')" />
                                    <select id="counselor_id" name="counselor_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm"
                                        required>
                                        <option value="">Select Counselor</option>
                                        @foreach($counselors as $counselor)
                                            <option value="{{ $counselor->id }}" 
                                                {{ old('counselor_id', $appointment->counselor_id) == $counselor->id ? 'selected' : '' }}>
                                                {{ $counselor->user->first_name }} {{ $counselor->user->last_name }}
                                                @if($counselor->specialization)
                                                    - {{ $counselor->specialization }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('counselor_id')" />
                                </div>

                                <!-- Preferred Date -->
                                <div>
                                    <x-input-label for="preferred_date" :value="__('Preferred Date')" />
                                    <x-text-input id="preferred_date" name="preferred_date" type="date" 
                                        class="mt-1 block w-full" 
                                        :value="old('preferred_date', $appointment->preferred_date instanceof \Carbon\Carbon ? $appointment->preferred_date->format('Y-m-d') : $appointment->preferred_date)" 
                                        required 
                                        min="{{ date('Y-m-d') }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('preferred_date')" />
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Appointments cannot be scheduled on weekends
                                    </p>
                                </div>

                                <!-- Preferred Time -->
                                <div>
                                    <x-input-label for="preferred_time" :value="__('Preferred Time')" />
                                    <x-text-input id="preferred_time" name="preferred_time" type="time" 
                                        class="mt-1 block w-full" 
                                        :value="old('preferred_time', $appointment->preferred_time)" 
                                        required />
                                    <x-input-error class="mt-2" :messages="$errors->get('preferred_time')" />
                                </div>
                            </div>
                        </div>

                        <!-- Current Status Info -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                        Current Status
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                        <p>
                                            This appointment is currently: 
                                            <span class="font-semibold uppercase">{{ str_replace('_', ' ', $appointment->status) }}</span>
                                        </p>
                                        @if($appointment->google_event_id)
                                            <p class="mt-1">
                                                <svg class="inline h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                Synced with Google Calendar
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-4 mt-6">
                            <x-secondary-button type="button" onclick="history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button type="submit">
                                {{ __('Update Appointment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Prevent selecting weekends
        document.getElementById('preferred_date').addEventListener('change', function(e) {
            const date = new Date(e.target.value);
            const day = date.getDay();
            
            if (day === 0 || day === 6) {
                alert('Appointments cannot be scheduled on weekends. Please select a weekday.');
                e.target.value = '';
            }
        });
    </script>
    @endpush
</x-app-layout>