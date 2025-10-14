<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Session Details') }}
            </h2>
            <div class="flex gap-2">
                <x-secondary-button onclick="history.back()">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </x-secondary-button>
                @if($counselingSession->status !== 'completed')
                    <a href="{{ route('admin.counseling-sessions.edit', $counselingSession) }}" 
                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>{{ __('Edit Session') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Session Info -->
                <div class="md:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                    Session Information
                                </h3>
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-8">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                        <dd class="mt-1">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $counselingSession->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($counselingSession->status === 'ongoing' ? 'bg-blue-100 text-blue-800' : 
                                                   'bg-green-100 text-green-800') }}">
                                                {{ ucfirst($counselingSession->status) }}
                                            </span>
                                        </dd>
                                    </div>
                                    
                                    @if($counselingSession->started_at)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Started At</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $counselingSession->started_at->format('F d, Y g:i A') }}
                                            </dd>
                                        </div>
                                    @endif

                                    @if($counselingSession->ended_at)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Ended At</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $counselingSession->ended_at->format('F d, Y g:i A') }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $counselingSession->formatted_duration }} minutes
                                            </dd>
                                        </div>
                                    @endif

                                    <div class="md:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Primary Concern</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $counselingSession->concern }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $counselingSession->category ? $counselingSession->category->name : 'N/A' }}
                                        </dd>
                                    </div>

                                    <div class="md:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Session Notes</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $counselingSession->notes ?? 'No notes recorded.' }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Info -->
                <div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Student Information
                            </h3>
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                                        <i class="fas fa-user-graduate text-pink-500"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ $counselingSession->student->user->name }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $counselingSession->student->student_number }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
