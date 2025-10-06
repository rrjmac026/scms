<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Appointment Details') }}
            </h2>
            <x-secondary-button onclick="history.back()">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Appointment Info -->
                <div class="col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Appointment Details
                        </h3>
                        
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-8">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($appointment->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                           'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $appointment->category?->name ?? 'N/A' }}
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $appointment->preferred_date->format('F d, Y') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Time</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $appointment->preferred_time }}
                                </dd>
                            </div>

                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Student's Concern</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                                    {{ $appointment->concern }}
                                </dd>
                            </div>
                        </dl>

                        @if($appointment->status === 'pending')
                            <div class="mt-6 flex gap-4">
                                <button type="button" 
                                        onclick="openApproveModal()"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <i class="fas fa-check mr-2"></i>
                                    {{ __('Approve Appointment') }}
                                </button>

                                <button type="button"
                                        onclick="openRejectModal()"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <i class="fas fa-times mr-2"></i>
                                    {{ __('Reject Appointment') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Student Info -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Student Information
                        </h3>
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                                <i class="fas fa-user-graduate text-pink-500"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ $appointment->student->user->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $appointment->student->student_number }}
                                </div>
                            </div>
                        </div>
                        <dl class="grid grid-cols-1 gap-4 mt-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Course</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $appointment->student->course }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Year Level</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $appointment->student->year_level }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Approve Appointment</h3>
            <p class="text-sm text-gray-600 mb-4">Are you sure you want to approve this appointment?</p>
            <form action="{{ route('counselor.appointments.update-status', $appointment) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="approved">
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeApproveModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Approve
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Appointment</h3>
            <form action="{{ route('counselor.appointments.update-status', $appointment) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection</label>
                    <textarea name="rejection_reason" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
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

    @push('scripts')
    <script>
    function openApproveModal() {
        document.getElementById('approveModal').classList.remove('hidden');
    }

    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
    }

    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const approveModal = document.getElementById('approveModal');
        const rejectModal = document.getElementById('rejectModal');
        
        if (event.target === approveModal) {
            closeApproveModal();
        }
        if (event.target === rejectModal) {
            closeRejectModal();
        }
    }

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeApproveModal();
            closeRejectModal();
        }
    });
    </script>
    @endpush
</x-app-layout>