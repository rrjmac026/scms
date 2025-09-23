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

                        <!-- Detailed Ratings -->
                        <div class="space-y-4 mb-6">
                            @for ($i = 1; $i <= 10; $i++)
                                @php $question = "q$i"; @endphp
                                @if($feedback->$question)
                                    <div>
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Question {{ $i }}
                                        </div>
                                        <div class="flex items-center">
                                            <div class="flex text-yellow-400">
                                                @for ($j = 1; $j <= 5; $j++)
                                                    <i class="fas fa-star {{ $j <= $feedback->$question ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                                ({{ $feedback->$question }}/5)
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endfor
                        </div>

                        <!-- Comments -->
                        @if($feedback->comments)
                            <div>
                                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">Comments</h4>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <p class="text-gray-700 dark:text-gray-300">{{ $feedback->comments }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Session Details -->
                <div class="space-y-6">
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
                            </dl>
                        </div>
                    </div>

                    <!-- Counselor Info -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Counselor Information</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $feedback->counselor->user->first_name }} {{ $feedback->counselor->user->last_name }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Specialization</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $feedback->counselor->specialization ?? 'General Counseling' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
