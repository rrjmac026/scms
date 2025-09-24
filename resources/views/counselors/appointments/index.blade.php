<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manage Appointments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($appointments->isEmpty())
                        <div class="text-center py-8">
                            <div class="mb-4">
                                <i class="fas fa-calendar text-6xl text-gray-300 dark:text-gray-600"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                No appointments yet
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400">
                                You don't have any appointments scheduled at the moment.
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date & Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($appointments as $appointment)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                                                        <i class="fas fa-user-graduate text-pink-500"></i>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {{ $appointment->student->user->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $appointment->student->student_number }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $appointment->preferred_date->format('M d, Y') }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $appointment->preferred_time }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-500' : 
                                                       ($appointment->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-500' : 
                                                       'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-500') }}">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium">
                                                <div class="flex items-center gap-3">
                                                    <a href="{{ route('counselor.appointments.show', $appointment) }}" 
                                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($appointment->status === 'pending')
                                                        <form action="{{ route('counselor.appointments.update-status', $appointment) }}" 
                                                              method="POST" 
                                                              class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="approved">
                                                            <button type="submit" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
