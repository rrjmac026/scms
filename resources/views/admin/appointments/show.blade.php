<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Appointment Details') }}
            </h2>
            <div class="flex gap-2">
                <x-secondary-button onclick="history.back()">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </x-secondary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Appointment Overview -->
                <div class="col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('Appointment Overview') }}
                        </h3>
                        
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-8">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $appointment->preferred_date->format('F d, Y') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Time</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($appointment->preferred_time)->format('h:i A') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' :
                                           ($appointment->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' :
                                           ($appointment->status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' :
                                           'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300')) }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Concern/Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $appointment->concern ?? 'No concerns noted.' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Participants Info -->
                <div class="space-y-6">
                    <!-- Student Info -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Student Information</h4>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $appointment->student->user->first_name }} {{ $appointment->student->user->last_name }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Student Number: {{ $appointment->student->student_number }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Counselor Info -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Counselor Information</h4>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $appointment->counselor->user->first_name }} {{ $appointment->counselor->user->last_name }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Specialization: {{ $appointment->counselor->specialization ?? 'General Counseling' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
