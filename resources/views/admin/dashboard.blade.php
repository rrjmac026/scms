<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ now()->format('F j, Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Banner -->
            <div class="rounded-2xl shadow-lg mb-8 p-6 text-white" style="background: linear-gradient(to right, #FF92C2, #FF6BAD);">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold mb-2">Welcome back, Admin!</h3>
                        <p class="text-pink-100">Here's what's happening in your counseling center today.</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-chart-line text-3xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                <!-- Total Users Card -->
                <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                                <i class="fas fa-users text-xl text-white"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Total Users</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalUsers }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm">
                                <div class="flex items-center bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2 py-1 rounded-full">
                                    <i class="fas fa-arrow-up text-xs mr-1"></i>
                                    <span class="font-medium">12%</span>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">vs last month</span>
                        </div>
                    </div>
                </div>

                <!-- Active Students -->
                <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg">
                                <i class="fas fa-user-graduate text-xl text-white"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Active Students</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $activeStudents }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm">
                                <div class="flex items-center bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2 py-1 rounded-full">
                                    <i class="fas fa-arrow-up text-xs mr-1"></i>
                                    <span class="font-medium">8%</span>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">vs last month</span>
                        </div>
                    </div>
                </div>

                <!-- Counseling Sessions -->
                <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg">
                                <i class="fas fa-comments text-xl text-white"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Total Sessions</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalSessions }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm">
                                <div class="flex items-center bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2 py-1 rounded-full">
                                    <i class="fas fa-arrow-up text-xs mr-1"></i>
                                    <span class="font-medium">15%</span>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">vs last month</span>
                        </div>
                    </div>
                </div>

                <!-- Pending Appointments -->
                <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl shadow-lg">
                                <i class="fas fa-calendar-alt text-xl text-white"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Pending Appointments</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $pendingAppointments }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm">
                                <div class="flex items-center bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-2 py-1 rounded-full">
                                    <i class="fas fa-arrow-down text-xs mr-1"></i>
                                    <span class="font-medium">5%</span>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">vs last month</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Tables Section -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
                <!-- Recent Activity -->
                <div class="xl:col-span-1 bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Activity</h3>
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        </div>
                        <div class="space-y-4">
                            @foreach(range(1, 5) as $index)
                            <div class="flex items-start gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-user-plus text-white text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">New student registration</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Student ID: ST{{ 1000 + $index }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ rand(1, 5) }} hours ago</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center">
                                View all activities
                                <i class="fas fa-arrow-right ml-2 text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="xl:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Quick Actions</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a href="{{ route('admin.users.create') }}" 
                               class="group flex items-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border-2 border-transparent hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">
                                <div class="p-3 bg-blue-600 rounded-lg mr-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-user-plus text-white"></i>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white block">Add New User</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Create student or counselor account</span>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.appointments.calendar') }}" 
                               class="group flex items-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl border-2 border-transparent hover:border-purple-200 dark:hover:border-purple-800 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">
                                <div class="p-3 bg-purple-600 rounded-lg mr-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-calendar-plus text-white"></i>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white block">View Calendar</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Manage appointments & schedules</span>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.counseling-sessions.index') }}" 
                               class="group flex items-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border-2 border-transparent hover:border-green-200 dark:hover:border-green-800 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">
                                <div class="p-3 bg-green-600 rounded-lg mr-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-comments text-white"></i>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white block">View Sessions</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Monitor counseling progress</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Sessions Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl border border-gray-100 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Counseling Sessions</h3>
                        <div class="flex items-center space-x-2">
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg px-3 py-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Last 30 days</span>
                            </div>
                            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Counselor</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($recentSessions as $session)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                                    <span class="text-white font-semibold text-sm">
                                                        {{ strtoupper(substr($session->student->user->first_name, 0, 1)) }}
                                                        {{ strtoupper(substr($session->student->user->last_name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                        {{ $session->student->user->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $session->student->student_number ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $session->counselor->user->name ?? 'Unassigned' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $session->counselor->specialization ?? 'Counselor' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $session->created_at->format('M j, Y') }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $session->created_at->format('g:i A') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                                Completed
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.counseling-sessions.show', $session->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">View</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No recent sessions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing <span class="font-semibold">1</span> to <span class="font-semibold">5</span> of <span class="font-semibold">97</span> results
                            </p>
                            <div class="flex space-x-1">
                                <button class="px-3 py-1 text-sm text-gray-500 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">Previous</button>
                                <button class="px-3 py-1 text-sm text-white bg-blue-600 border border-blue-600 rounded-md hover:bg-blue-700">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
</x-app-layout>