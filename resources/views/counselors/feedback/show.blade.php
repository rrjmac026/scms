<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Feedback Details') }}
            </h2>
            <x-secondary-button onclick="history.back()">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Feedback Overview -->
                <div class="md:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Feedback Overview
                        </h3>

                        <!-- Overall Rating -->
                        @if($feedback->rating)
                            <div class="mb-6">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Overall Rating</div>
                                <div class="flex items-center">
                                    <span class="text-3xl font-bold text-gray-900 dark:text-gray-100 mr-3">
                                        {{ number_format($feedback->rating, 1) }}/5
                                    </span>
                                    <div class="flex text-2xl text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Detailed Ratings -->
                        <div class="space-y-4 mb-6">
                            @php $questions = config('counseling.feedback_questions'); @endphp

                            @for($i = 1; $i <= 12; $i++)
                                @php
                                    $key = "q$i";
                                    $questionText = $questions[$i-1] ?? "Question $i";
                                @endphp

                                @if(!empty($feedback->$key))
                                    <div>
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ $questionText }}
                                        </div>

                                        @if($i <= 10)
                                            <div class="flex items-center">
                                                <div class="flex text-yellow-400">
                                                    @for($j = 1; $j <= 5; $j++)
                                                        <i class="fas fa-star {{ $j <= $feedback->$key ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                                    ({{ $feedback->$key }}/5)
                                                </span>
                                            </div>
                                        @else
                                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-gray-700 dark:text-gray-300">
                                                {{ $feedback->$key }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endfor
                        </div>

                        <!-- Likes -->
                        @if($feedback->likes)
                            <div class="mt-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">What did you like about this counseling session?</h4>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-gray-700 dark:text-gray-300">
                                    {{ $feedback->likes }}
                                </div>
                            </div>
                        @endif

                        <!-- Comments -->
                        @if($feedback->comments)
                            <div class="mt-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">Comments / Suggestions to improve session</h4>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-gray-700 dark:text-gray-300">
                                    {{ $feedback->comments }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Student Info -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Student Information</h4>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $feedback->student->user->first_name }} {{ $feedback->student->user->last_name }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Student Number</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $feedback->student->student_number }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Session Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $feedback->counselingSession->started_at->format('F j, Y') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
