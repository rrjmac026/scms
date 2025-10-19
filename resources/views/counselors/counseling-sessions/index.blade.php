<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Counseling Sessions') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('counselor.counseling-sessions.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Date From -->
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-calendar-alt mr-1"></i> From Date
                                </label>
                                <input 
                                    type="date" 
                                    id="date_from" 
                                    name="date_from"
                                    value="{{ request('date_from') }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800"
                                >
                            </div>

                            <!-- Date To -->
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-calendar-check mr-1"></i> To Date
                                </label>
                                <input 
                                    type="date" 
                                    id="date_to" 
                                    name="date_to"
                                    value="{{ request('date_to') }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800"
                                >
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-filter mr-1"></i> Status
                                </label>
                                <select 
                                    id="status" 
                                    name="status"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800"
                                >
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="flex items-end gap-2">
                                <button 
                                    type="submit"
                                    class="flex-1 px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2"
                                >
                                    <i class="fas fa-search"></i>
                                    <span>Filter</span>
                                </button>
                                <a 
                                    href="{{ route('counselor.counseling-sessions.index') }}"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg shadow-sm transition-colors duration-200"
                                >
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Active Filters Display -->
                        @if(request('date_from') || request('date_to') || request('status'))
                            <div class="flex items-center gap-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-info-circle mr-1"></i> Active Filters:
                                </span>
                                <div class="flex flex-wrap gap-2">
                                    @if(request('date_from'))
                                        <span class="px-3 py-1 bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-400 text-xs rounded-full">
                                            From: {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                                        </span>
                                    @endif
                                    @if(request('date_to'))
                                        <span class="px-3 py-1 bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-400 text-xs rounded-full">
                                            To: {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                                        </span>
                                    @endif
                                    @if(request('status'))
                                        <span class="px-3 py-1 bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-400 text-xs rounded-full">
                                            Status: {{ ucfirst(request('status')) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Sessions Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($sessions->isEmpty())
                        <div class="text-center py-8">
                            <div class="mb-4">
                                <i class="fas fa-comments text-6xl text-gray-300 dark:text-gray-600"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                No sessions found
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400">
                                @if(request()->hasAny(['date_from', 'date_to', 'status']))
                                    Try adjusting your filters to see more results
                                @else
                                    Start recording your counseling sessions
                                @endif
                            </p>
                        </div>
                    @else
                        <!-- Results Count -->
                        <div class="mb-4 flex items-center justify-between">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Showing <span class="font-semibold">{{ $sessions->firstItem() }}</span> to 
                                <span class="font-semibold">{{ $sessions->lastItem() }}</span> of 
                                <span class="font-semibold">{{ $sessions->total() }}</span> sessions
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Duration</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($sessions as $session)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                                                        <i class="fas fa-user-graduate text-pink-500"></i>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {{ $session->student->user->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $session->student->student_number }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($session->started_at)
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                                        {{ $session->started_at->format('M d, Y') }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $session->started_at->format('h:i A') }}
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Not started</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $session->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-500' : 
                                                       ($session->status === 'ongoing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-500' : 
                                                       'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-500') }}">
                                                    {{ ucfirst($session->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($session->duration)
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                                        <i class="fas fa-clock mr-1 text-gray-400"></i>
                                                        {{ $session->formatted_duration ?? gmdate('H:i:s', $session->duration) }}
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center gap-3">
                                                    <a href="{{ route('counselor.counseling-sessions.show', $session) }}" 
                                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                                       title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('counselor.counseling-sessions.edit', $session) }}" 
                                                       class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300"
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $sessions->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>