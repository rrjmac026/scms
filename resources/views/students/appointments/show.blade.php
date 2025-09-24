<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Appointment Details') }}
            </h2>
            <x-secondary-button onclick="history.back()">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Appointment Info -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Appointment Information
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</span>
                                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($appointment->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                               'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</span>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $appointment->preferred_date->format('F d, Y') }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Time</span>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $appointment->preferred_time }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Counselor Info -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Counselor Information
                                </h3>
                                <div class="mt-4 flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                                            <i class="fas fa-user-tie text-pink-500"></i>
                                        </div>
                                    </div>
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ $appointment->counselor?->user?->name ?? 'Not yet assigned' }}
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $appointment->counselor?->specialization ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Concern/Notes -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Appointment Notes
                        </h3>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $appointment->concern }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if($appointment->status === 'pending')
                        <div class="mt-6 flex justify-end">
                            <form action="{{ route('student.appointments.destroy', $appointment) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-danger-button onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                    <i class="fas fa-times mr-2"></i>
                                    {{ __('Cancel Appointment') }}
                                </x-danger-button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
