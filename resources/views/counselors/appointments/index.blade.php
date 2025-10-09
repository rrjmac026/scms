<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Appointment Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            {{-- My Appointments Section --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl border border-pink-100 dark:border-pink-900/10">
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-6 text-gray-900 dark:text-white">My Appointments</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-pink-100 dark:divide-pink-900/10">
                            <thead>
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-pink-600 dark:text-pink-400 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-pink-600 dark:text-pink-400 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-pink-600 dark:text-pink-400 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-pink-600 dark:text-pink-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-pink-600 dark:text-pink-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-pink-50 dark:divide-pink-900/10">
                                @forelse($appointments as $appointment)
                                    <tr class="hover:bg-pink-50/50 dark:hover:bg-pink-900/10 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $appointment->preferred_date->format('M d, Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $appointment->formatted_time }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                                                    <i class="fas fa-user text-pink-500"></i>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ $appointment->student->user->name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-200">
                                                {{ $appointment->category->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $appointment->status === 'completed' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : 
                                                   ($appointment->status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200' : 
                                                   'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200') }}">
                                                {{ $appointment->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <a href="{{ route('counselor.appointments.show', $appointment) }}" 
                                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if($appointment->status === 'approved')
                                                    <form action="{{ route('counselor.appointments.accept', $appointment) }}" 
                                                          method="POST" 
                                                          class="inline"
                                                          onsubmit="return confirm('Are you sure you want to accept this appointment?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition-colors duration-200">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>

                                                    <button type="button" 
                                                            onclick="openRejectModal({{ $appointment->id }})" 
                                                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors duration-200">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif

                                                @if($appointment->status === 'accepted')
                                                    <form action="{{ route('counselor.appointments.complete', $appointment) }}" 
                                                        method="POST" 
                                                        class="inline" 
                                                        onsubmit="return confirm('Mark this appointment as completed?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-pink-600 dark:text-pink-400 hover:text-pink-800 dark:hover:text-pink-300 transition-colors duration-200">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No appointments assigned yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reject Modal (kept because it needs a reason) --}}
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center" style="z-index: 9999;">
        <div class="bg-white p-5 border w-96 shadow-lg rounded-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Appointment</h3>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection</label>
                    <textarea name="rejection_reason" rows="3" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-500"
                        placeholder="Please provide a reason..."></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeRejectModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(id) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/counselor/appointments/${id}/reject`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal on background click
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRejectModal();
            }
        });
    </script>
</x-app-layout>