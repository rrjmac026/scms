<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('User Details') }}
            </h2>
            <div class="flex gap-2">
                <x-secondary-button onclick="history.back()">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </x-secondary-button>
                <a href="{{ route('admin.users.edit', $user) }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-edit mr-2"></i>{{ __('Edit') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- User Profile Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl">
                    <div class="p-6">
                        <div class="text-center mb-6">
                            <div class="w-24 h-24 mx-auto mb-4">
                                <div class="w-full h-full rounded-full bg-gradient-to-r from-pink-500 to-rose-500 flex items-center justify-center text-white text-2xl font-bold">
                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                </div>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ $user->first_name }} {{ $user->last_name }}
                            </h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ 
                                $user->role === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' :
                                ($user->role === 'counselor' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' :
                                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400')
                            }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="py-3 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $user->email }}</dd>
                                </div>
                                <div class="py-3 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Joined</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</dd>
                                </div>
                                <div class="py-3 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                    <dd class="text-sm">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></div>
                                            Active
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                <!-- Role-Specific Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ ucfirst($user->role) }} Information
                            </h3>
                        </div>

                        <div class="p-6 space-y-4">
                            @if($user->role === 'counselor' && $user->counselor)
                                <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <div class="py-3 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Employee Number</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $user->counselor->employee_number }}</dd>
                                    </div>
                                    <div class="py-3 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Specialization</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $user->counselor->specialization ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="py-3 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Gender</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ ucfirst($user->counselor->gender ?? 'N/A') }}</dd>
                                    </div>
                                </dl>
                            @elseif($user->role === 'student' && $user->student)
                                <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <div class="py-3 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Student Number</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $user->student->student_number }}</dd>
                                    </div>
                                    <div class="py-3 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Course</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $user->student->course ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="py-3 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Year Level</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $user->student->year_level ?? 'N/A' }}</dd>
                                    </div>
                                </dl>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">No additional information available.</p>
                            @endif
                        </div>
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>
