<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Student Details') }}
            </h2>
            <div class="flex gap-2">
                <x-secondary-button onclick="history.back()">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </x-secondary-button>
                <a href="{{ route('admin.students.edit', $student) }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                    <i class="fas fa-edit mr-2"></i>{{ __('Edit') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center mb-6">
                            <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 mx-auto mb-4 flex items-center justify-center">
                                <span class="text-2xl font-bold text-gray-600 dark:text-gray-300">
                                    {{ substr($student->user->first_name, 0, 1) }}
                                </span>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $student->user->first_name }} {{ $student->user->last_name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $student->student_number }}
                            </p>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Course</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->course ?? 'Not specified' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Year Level</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->year_level ?? 'Not specified' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Academic History -->
                <div class="md:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Counseling History</h3>
                            <div class="space-y-4">
                                @forelse($student->counselingSessions as $session)
                                    <div class="border-l-4 border-blue-500 pl-4">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $session->date->format('M d, Y') }}
                                        </div>
                                        <div class="text-gray-900 dark:text-gray-100">
                                            {{ $session->notes }}
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400">No counseling sessions recorded.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Special Needs Section -->
                    @if($student->special_needs)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Special Considerations</h3>
                                <p class="text-gray-700 dark:text-gray-300">
                                    {{ $student->special_needs }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
