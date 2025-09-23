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
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-6">
                                <!-- Student Selection -->
                                <div>
                                    <x-input-label for="student_id" :value="__('Select Student')" />
                                    <select id="student_id" name="student_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
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

                                <!-- Counselor Selection -->
                                <div>
                                    <x-input-label for="counselor_id" :value="__('Select Counselor')" />
                                    <select id="counselor_id" name="counselor_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                        @foreach($counselors as $counselor)
                                            <option value="{{ $counselor->id }}" 
                                                {{ old('counselor_id', $appointment->counselor_id) == $counselor->id ? 'selected' : '' }}>
                                                {{ $counselor->user->first_name }} {{ $counselor->user->last_name }}
                                                ({{ $counselor->specialization ?? 'General Counseling' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('counselor_id')" />
                                </div>
                            </div>

                            <div class="space-y-6">
                                <!-- Date and Time -->
                                <div>
                                    <x-input-label for="preferred_date" :value="__('Preferred Date')" />
                                    <x-text-input id="preferred_date" name="preferred_date" type="date" 
                                        class="mt-1 block w-full" 
                                        :value="old('preferred_date', $appointment->preferred_date->format('Y-m-d'))" 
                                        required />
                                    <x-input-error class="mt-2" :messages="$errors->get('preferred_date')" />
                                </div>

                                <div>
                                    <x-input-label for="preferred_time" :value="__('Preferred Time')" />
                                    <x-text-input id="preferred_time" name="preferred_time" type="time" 
                                        class="mt-1 block w-full" 
                                        :value="old('preferred_time', \Carbon\Carbon::parse($appointment->preferred_time)->format('H:i'))" 
                                        required />
                                    <x-input-error class="mt-2" :messages="$errors->get('preferred_time')" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <x-input-label for="status" :value="__('Status')" />
                                    <select id="status" name="status" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                        @foreach(['pending', 'approved', 'completed', 'cancelled'] as $status)
                                            <option value="{{ $status }}" 
                                                {{ old('status', $appointment->status) == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                                </div>
                            </div>

                            <!-- Concern/Notes -->
                            <div class="md:col-span-2">
                                <x-input-label for="concern" :value="__('Concern/Notes')" />
                                <textarea id="concern" name="concern" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                >{{ old('concern', $appointment->concern) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('concern')" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <x-secondary-button onclick="history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Update Appointment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
