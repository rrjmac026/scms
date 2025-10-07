<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Counseling History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($sessions->isEmpty())
                        <div class="text-center py-8">
                            <div class="mb-4">
                                <i class="fas fa-history text-6xl text-gray-300 dark:text-gray-600"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                No counseling sessions yet
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400">
                                Your counseling session history will appear here
                            </p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($sessions as $session)
                                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm border border-gray-200 dark:border-gray-600 hover:border-pink-200 dark:hover:border-pink-800 transition-all duration-200">
                                    <div class="p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                                                    <i class="fas fa-user-tie text-pink-500"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $session->counselor->user->name }}
                                                    </h4>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $session->started_at ? $session->started_at->format('F j, Y') : 'Not started yet' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="{{ route('student.counseling-history.show', $session) }}" 
                                               class="px-4 py-2 bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-300 rounded-lg hover:bg-pink-100 dark:hover:bg-pink-900/50 transition-colors duration-200">
                                                View Details
                                            </a>

                                            @if($session->status === 'completed')
                                                @if(!$session->feedback)
                                                    <a href="{{ route('student.feedback.create', $session) }}" 
                                                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200">
                                                        <i class="fas fa-comment-dots mr-1"></i> Give Feedback
                                                    </a>
                                                @else
                                                    <span class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium flex items-center">
                                                        <i class="fas fa-check-circle mr-1"></i> Feedback Submitted
                                                    </span>
                                                @endif
                                            @endif

                                        </div>
                                        <div class="mt-4">
                                            <p class="text-gray-600 dark:text-gray-300">
                                                {{ Str::limit($session->notes, 150) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $sessions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
