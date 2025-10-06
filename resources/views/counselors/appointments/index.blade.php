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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-xl font-bold mb-4">My Appointments</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($appointments as $appointment)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $appointment->preferred_date->format('M d, Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $appointment->formatted_time }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $appointment->student->user->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $appointment->category->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                bg-{{ $appointment->status_color }}-100 text-{{ $appointment->status_color }}-800">
                                                {{ $appointment->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="flex flex-wrap gap-2">
                                                {{-- View --}}
                                                <a href="{{ route('counselor.appointments.show', $appointment) }}" 
                                                   class="text-blue-600 hover:text-blue-900 hover:underline">
                                                    View
                                                </a>

                                                {{-- Accept --}}
                                                @if($appointment->status === 'approved')
                                                    <form action="{{ route('counselor.appointments.accept', $appointment) }}" 
                                                          method="POST" 
                                                          class="inline"
                                                          onsubmit="return confirm('Are you sure you want to accept this appointment?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-green-600 hover:text-green-900 hover:underline">
                                                            Accept
                                                        </button>
                                                    </form>

                                                    {{-- Reject --}}
                                                    <button type="button" 
                                                            onclick="openRejectModal({{ $appointment->id }})" 
                                                            class="text-red-600 hover:text-red-900 hover:underline cursor-pointer">
                                                        Reject
                                                    </button>
                                                @endif

                                                {{-- Complete --}}
                                                @if(in_array($appointment->status, ['approved', 'accepted']))
                                                    <form action="{{ route('counselor.appointments.complete', $appointment) }}" 
                                                        method="POST" 
                                                        class="inline" 
                                                        onsubmit="return confirm('Mark this appointment as completed?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-purple-600 hover:text-purple-900 hover:underline">
                                                            Complete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
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