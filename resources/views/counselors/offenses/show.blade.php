<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Offense Details') }}
            </h2>
            <div class="flex gap-2">
                <x-secondary-button onclick="history.back()">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </x-secondary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Offense Details -->
                <div class="md:col-span-2">
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
                        <div class="p-6">
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                    Offense Information
                                </h3>
                                <dl class="grid grid-cols-1 gap-6">
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Offense</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $offense->offense }}</dd>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $offense->date->format('F d, Y') }}
                                        </dd>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                        <dd class="mt-1">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $offense->resolved ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                                {{ $offense->resolved ? 'Resolved' : 'Pending' }}
                                            </span>
                                        </dd>
                                    </div>

                                    @if($offense->remarks)
                                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Remarks</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $offense->remarks }}</dd>
                                        </div>
                                    @endif

                                    @if($offense->solution)
                                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Solution/Resolution</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $offense->solution }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>

                            @if(!$offense->resolved)
                                <div class="mt-6 flex justify-end">
                                    <form action="{{ route('counselor.offenses.resolve', $offense) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors duration-200 flex items-center gap-2">
                                            <i class="fas fa-check"></i>
                                            Mark as Resolved
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Student Information -->
                <div>
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="fas fa-user-graduate text-pink-500 mr-2"></i>
                                Student Information
                            </h3>
                            
                            <div class="text-center mb-6">
                                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center">
                                    <i class="fas fa-user-graduate text-2xl text-white"></i>
                                </div>
                                <div class="font-bold text-lg text-gray-900 dark:text-gray-100">
                                    {{ $offense->student->user->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $offense->student->student_number }}
                                </div>
                            </div>

                            @if($offense->counselingSession)
                                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Related Session</h4>
                                    <a href="{{ route('counselor.counseling-sessions.show', $offense->counselingSession) }}"
                                       class="block p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-pink-50 dark:hover:bg-pink-900/20 transition-colors duration-200">
                                        <div class="flex items-center">
                                            <i class="fas fa-comments text-pink-500 mr-3"></i>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    Counseling Session
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $offense->counselingSession->started_at?->format('M d, Y h:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
