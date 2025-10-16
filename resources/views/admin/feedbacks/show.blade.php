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

                        <!-- Detailed Questions Average (from q1-q12) -->
                        @if($detailedAvg)
                            <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Average from Detailed Questions</div>
                                <div class="flex items-center">
                                    <span class="text-2xl font-bold text-gray-900 dark:text-gray-100 mr-3">
                                        {{ number_format($detailedAvg, 2) }}/5
                                    </span>
                                    <div class="flex text-xl text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= round($detailedAvg) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Calculated from {{ $answeredQuestionsCount }} answered questions</p>
                            </div>
                        @endif

                        <!-- Detailed Ratings (q1–q12) -->
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Detailed Ratings</h4>
                        <div class="space-y-4 mb-6">
                            @php $questions = config('counseling.feedback_questions'); @endphp

                            @for($i = 1; $i <= 12; $i++)
                                @php
                                    $key = "q$i";
                                    $questionText = $questions[$i-1] ?? "Question $i";
                                    $rating = $feedback->$key;
                                @endphp

                                @if(!empty($rating) && is_numeric($rating))
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            {{ $i }}. {{ $questionText }}
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="flex text-yellow-400 text-lg">
                                                    @for($j = 1; $j <= 5; $j++)
                                                        <i class="fas fa-star {{ $j <= $rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="ml-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ number_format($rating, 1) }}/5
                                                </span>
                                            </div>
                                            
                                            <!-- Rating percentage bar -->
                                            <div class="flex items-center ml-4">
                                                <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ ($rating / 5) * 100 }}%"></div>
                                                </div>
                                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                                                    {{ number_format(($rating / 5) * 100, 0) }}%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endfor

                            @if($answeredQuestionsCount === 0)
                                <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                    No detailed ratings provided
                                </div>
                            @endif
                        </div>


                        <!-- Likes -->
                        @if($feedback->likes)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">
                                    <i class="fas fa-thumbs-up text-green-500 mr-2"></i>What did you like about this counseling session?
                                </h4>
                                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-gray-700 dark:text-gray-300">
                                    {{ $feedback->likes }}
                                </div>
                            </div>
                        @endif


                        <!-- Comments -->
                        @if($feedback->comments)
                            <div class="mt-6">
                                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">
                                    <i class="fas fa-comment-dots text-blue-500 mr-2"></i>Comments / Suggestions to improve session
                                </h4>
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-gray-700 dark:text-gray-300">
                                    {{ $feedback->comments }}
                                </div>
                            </div>
                        @endif

                        <!-- Timestamp -->
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-clock mr-1"></i>
                                Submitted on {{ $feedback->created_at->format('F d, Y \a\t h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Session Details -->
                <div class="space-y-6">
                    <!-- Student Info -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                <i class="fas fa-user-graduate mr-2 text-indigo-500"></i>Student Information
                            </h4>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $feedback->student->user->first_name }} {{ $feedback->student->user->last_name }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Student Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $feedback->student->student_number }}
                                    </dd>
                                </div>
                                @if($feedback->student->user->email)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $feedback->student->user->email }}
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Counselor Info -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                <i class="fas fa-user-tie mr-2 text-purple-500"></i>Counselor Information
                            </h4>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $feedback->counselor->user->first_name }} {{ $feedback->counselor->user->last_name }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Specialization</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $feedback->counselor->specialization ?? 'General Counseling' }}
                                    </dd>
                                </div>
                                @if($feedback->counselor->user->email)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $feedback->counselor->user->email }}
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-white">
                            <h4 class="text-lg font-medium mb-4">
                                <i class="fas fa-chart-line mr-2"></i>Rating Summary
                            </h4>
                            <div class="space-y-3">
                                @if($feedback->rating)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm opacity-90">Overall</span>
                                    <span class="font-bold text-lg">{{ number_format($feedback->rating, 1) }}/5</span>
                                </div>
                                @endif
                                @if($detailedAvg)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm opacity-90">Detailed Avg</span>
                                    <span class="font-bold text-lg">{{ number_format($detailedAvg, 2) }}/5</span>
                                </div>
                                @endif
                                <div class="flex justify-between items-center">
                                    <span class="text-sm opacity-90">Questions Answered</span>
                                    <span class="font-bold text-lg">{{ $answeredQuestionsCount }}/12</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>