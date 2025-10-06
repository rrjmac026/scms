<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Appointment Management') }}
            </h2>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="h-10 w-10 rounded-xl bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-pink-500"></i>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-gray-900 rounded-full"></div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Appointments</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $appointments->total() }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.appointments.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-pink-400 to-pink-500 text-white rounded-xl hover:from-pink-500 hover:to-pink-600 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-plus mr-2"></i>{{ __('New Appointment') }}
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Flash Messages -->
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 dark:bg-red-900/50 dark:border-red-500 dark:text-red-300 px-4 py-3 rounded-xl relative" role="alert">
                <span class="block sm:inline"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 dark:bg-green-900/50 dark:border-green-500 dark:text-green-300 px-4 py-3 rounded-xl relative" role="alert">
                <span class="block sm:inline"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        <i class="fas fa-filter mr-2 text-pink-500"></i>Filter Appointments
                    </h3>
                </div>
                <div class="p-4">
                    <form method="GET" action="{{ route('admin.appointments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                            <input type="text" 
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="Search by name or email...">
                        </div>
                        
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-pink-500 focus:border-pink-500">
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="declined" {{ request('status') == 'declined' ? 'selected' : '' }}>Declined</option>
                            </select>
                        </div>

                        <div class="space-y-1 flex flex-col justify-end sm:col-span-2 lg:col-span-1">
                            <button type="submit" class="w-full px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-filter mr-2"></i>{{ __('Apply Filters') }}
                            </button>
                        </div>

                        <div class="space-y-1 flex flex-col justify-end sm:col-span-2 lg:col-span-1">
                            <a href="{{ route('admin.appointments.index') }}" class="w-full px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors text-center">
                                <i class="fas fa-redo mr-2"></i>{{ __('Reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="block lg:hidden space-y-4 mb-6">
                @forelse($appointments as $appointment)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $appointment->student->user->first_name }} {{ $appointment->student->user->last_name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $appointment->student->student_number }}
                                </div>
                            </div>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800 ...' :
                                    ($appointment->status === 'approved' ? 'bg-blue-100 text-blue-800 ...' :
                                    ($appointment->status === 'declined' ? 'bg-red-100 text-red-800 ...' :
                                    ($appointment->status === 'completed' ? 'bg-emerald-100 text-emerald-800 ...' :
                                    'bg-gray-100 text-gray-800 ...'))) }}">
                                {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
                            </span>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Date:</span>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $appointment->preferred_date->format('M d, Y') }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Time:</span>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($appointment->preferred_time)->format('h:i A') }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Counselor:</span>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $appointment->counselor?->user?->first_name ?? 'Unassigned' }}
                                    {{ $appointment->counselor?->user?->last_name ?? '' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Category:</span>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $appointment->category?->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('admin.appointments.show', $appointment) }}" 
                                   class="inline-flex items-center justify-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm transition-colors">
                                    <i class="fas fa-eye mr-2"></i> View Details
                                </a>

                                @if($appointment->status === 'pending')
                                    <form action="{{ route('admin.appointments.approve', $appointment) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm transition-colors whitespace-nowrap"
                                                title="Approve and auto-assign counselor">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                        <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No appointments found</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden lg:block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Counselor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($appointments as $appointment)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $appointment->preferred_date->format('M d, Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($appointment->preferred_time)->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $appointment->student->user->first_name }} {{ $appointment->student->user->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $appointment->student->student_number }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $appointment->counselor?->user?->first_name ?? 'Unassigned' }}
                                                {{ $appointment->counselor?->user?->last_name ?? '' }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $appointment->counselor?->specialization ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $appointment->category?->name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' :
                                                   ($appointment->status === 'approved' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' :
                                                   ($appointment->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' :
                                                   ($appointment->status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' :
                                                   'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300'))) }}">
                                                {{ str_replace('_', ' ', ucfirst($appointment->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-3">
                                                @if($appointment->status === 'pending')
                                                    <!-- Approve Form -->
                                                    <form action="{{ route('admin.appointments.approve', $appointment) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm transition-colors whitespace-nowrap"
                                                                title="Approve and auto-assign counselor">
                                                            <i class="fas fa-check mr-1"></i> Approve
                                                        </button>
                                                    </form>
                                                @endif
                                                <!-- View Details -->
                                                <a href="{{ route('admin.appointments.show', $appointment) }}" 
                                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <!-- Edit -->
                                                <a href="{{ route('admin.appointments.edit', $appointment) }}" 
                                                   class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 transition-colors"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- Delete -->
                                                <form action="{{ route('admin.appointments.destroy', $appointment) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                                                <p class="text-gray-500 dark:text-gray-400">No appointments found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>