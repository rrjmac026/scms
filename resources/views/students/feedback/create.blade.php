<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Submit Session Feedback') }}
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
                    <form action="{{ route('student.feedback.store', $session) }}" method="POST">
                        @csrf
                        
                        <!-- Session Info -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Session Information</h4>
                            <div class="text-gray-900 dark:text-gray-100">
                                <p>Date: {{ $session->started_at->format('F j, Y') }}</p>
                                <p>Counselor: {{ $session->counselor->user->name }}</p>
                            </div>
                        </div>
                        
                        <!-- Questions -->
                        <div class="space-y-6 mb-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Counseling Session Evaluation</h3>
                            
                            @for($i = 1; $i <= 10; $i++)
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Question {{ $i }}: {{ config('counseling.feedback_questions')[$i-1] ?? "Rate your experience" }}
                                    </label>
                                    <div class="flex space-x-4">
                                        @for($rating = 1; $rating <= 5; $rating++)
                                            <label class="flex items-center">
                                                <input type="radio" name="q{{ $i }}" value="{{ $rating }}" 
                                                       class="text-pink-500 focus:ring-pink-500" required>
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $rating }}</span>
                                            </label>
                                        @endfor
                                    </div>
                                    @error("q$i")
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endfor

                            <!-- Overall Rating -->
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Overall Rating
                                </label>
                                <div class="flex space-x-4">
                                    @for($rating = 1; $rating <= 5; $rating++)
                                        <label class="flex items-center">
                                            <input type="radio" name="rating" value="{{ $rating }}" 
                                                   class="text-pink-500 focus:ring-pink-500">
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $rating }}</span>
                                        </label>
                                    @endfor
                                </div>
                            </div>

                            <!-- Comments -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Additional Comments
                                </label>
                                <textarea name="comments" rows="4" 
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-pink-500 focus:ring-pink-500"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button type="submit">
                                {{ __('Submit Feedback') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
