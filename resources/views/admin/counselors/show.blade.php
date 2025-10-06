<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Counselor Details') }}
            </h2>
            <div class="flex gap-2">
                <x-secondary-button onclick="history.back()">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </x-secondary-button>
                <a href="{{ route('admin.counselors.edit', $counselor) }}" 
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
                            <div class="w-24 h-24 rounded-full bg-red-100 dark:bg-red-800 mx-auto mb-4 flex items-center justify-center">
                                <span class="text-2xl font-bold text-red-600 dark:text-red-200">
                                    {{ substr($counselor->user->first_name, 0, 1) }}
                                </span>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $counselor->user->first_name }} {{ $counselor->user->last_name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $counselor->employee_number }}
                            </p>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $counselor->user->email }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Specialization</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $counselor->specialization ?? 'General Counseling' }}
                                    </dd>
                                </div>

                                <!-- ✅ Added Assigned Grade Level Section -->
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned Grade Level</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $counselor->assigned_grade_level ? 'Grade ' . $counselor->assigned_grade_level : 'Unassigned' }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Availability</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if(is_array($counselor->availability_schedule) && count($counselor->availability_schedule) > 0)
                                        <ul class="list-disc list-inside">
                                            @foreach($counselor->availability_schedule as $day)
                                                <li>{{ ucfirst($day) }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>No schedule available</p>
                                    @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Statistics and Recent Activity -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sessions</h4>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ $counselor->sessions ? $counselor->sessions->count() : 0 }}
                            </p>
                        </div>
                        <!-- Add more statistics cards as needed -->
                    </div>

                    <!-- Recent Sessions -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Recent Sessions</h3>
                            <div class="space-y-4">
                                @forelse($counselor->counselingSessions ? $counselor->counselingSessions->take(5) : collect() as $session)
                                    <div class="border-l-4 border-red-500 pl-4">
                                        <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                            <div>
                                                {{ $session->started_at 
                                                    ? $session->started_at->format('M d, Y') 
                                                    : ($session->created_at->format('M d, Y') ?? 'N/A') }}
                                            </div>

                                            {{-- Status Badge --}}
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if($session->status === 'completed')
                                                    bg-green-100 text-green-800
                                                @elseif($session->status === 'ongoing')
                                                    bg-blue-100 text-blue-800
                                                @else
                                                    bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($session->status ?? 'pending') }}
                                            </span>
                                        </div>

                                        {{-- Session Notes --}}
                                        <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ Str::limit($session->notes ?? 'No notes available', 100) }}
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400">No sessions recorded yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
