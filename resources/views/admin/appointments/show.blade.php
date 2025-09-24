<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-2xl text-pink-500"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('Appointment Details') }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Appointment #{{ $appointment->id }}
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <x-secondary-button onclick="history.back()" class="bg-white">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </x-secondary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Appointment Overview -->
                <div class="col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="border-b border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            <i class="fas fa-info-circle mr-2 text-pink-500"></i>{{ __('Appointment Overview') }}
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">
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
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                        <div class="border-b border-gray-200 dark:border-gray-700 p-4">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                <i class="fas fa-user-graduate mr-2 text-blue-500"></i>Student Information
                            </h4>
                        </div>
                        <div class="p-6">
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
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                        <div class="border-b border-gray-200 dark:border-gray-700 p-4">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                <i class="fas fa-user-tie mr-2 text-amber-500"></i>Counselor Information
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $appointment->counselor?->user?->first_name ?? 'N/A' }}
                                        {{ $appointment->counselor?->user?->last_name ?? '' }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $appointment->counselor?->specialization ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!$appointment->counselor && $appointment->status === 'pending')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                        <form action="{{ route('admin.appointments.assign', $appointment) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Assign Counselor
                                </label>
                                <select name="counselor_id" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500">
                                    <option value="">-- Auto Assign --</option>
                                    @foreach($availableCounselors as $counselor)
                                        <option value="{{ $counselor->id }}">
                                            {{ $counselor->user->first_name }} {{ $counselor->user->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-150">
                                <i class="fas fa-user-plus mr-2"></i>Assign Counselor
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
