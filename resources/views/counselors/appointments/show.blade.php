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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Appointment Info -->
                <div class="col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Appointment Details
                        </h3>
                        
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-8">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($appointment->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                           'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $appointment->preferred_date->format('F d, Y') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Time</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $appointment->preferred_time }}
                                </dd>
                            </div>

                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Student's Concern</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                                    {{ $appointment->concern }}
                                </dd>
                            </div>
                        </dl>

                        @if($appointment->status === 'pending')
                            <div class="mt-6 flex gap-4">
                                <form action="{{ route('counselor.appointments.update-status', $appointment) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <x-primary-button>
                                        <i class="fas fa-check mr-2"></i>
                                        {{ __('Approve Appointment') }}
                                    </x-primary-button>
                                </form>

                                <form action="{{ route('counselor.appointments.update-status', $appointment) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <x-danger-button>
                                        <i class="fas fa-times mr-2"></i>
                                        {{ __('Reject Appointment') }}
                                    </x-danger-button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Student Info -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Student Information
                        </h3>
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                                <i class="fas fa-user-graduate text-pink-500"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ $appointment->student->user->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $appointment->student->student_number }}
                                </div>
                            </div>
                        </div>
                        <dl class="grid grid-cols-1 gap-4 mt-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Course</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $appointment->student->course }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Year Level</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $appointment->student->year_level }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
