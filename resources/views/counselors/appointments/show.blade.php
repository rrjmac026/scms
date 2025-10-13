{{-- resources/views/counselors/appointments/show.blade.php --}}
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
                <!-- Student Info - Enhanced (Left side - 2 columns) -->
                <div class="col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Student Information
                        </h3>
                        
                        <!-- Student Header -->
                        <div class="flex items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="h-16 w-16 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                                <i class="fas fa-user-graduate text-2xl text-pink-500"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ $appointment->student->user->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $appointment->student->student_number }}
                                </div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $appointment->student->user->email }}
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <i class="fas fa-graduation-cap mr-2 text-blue-500"></i>
                                Academic Information
                            </h4>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @if($appointment->student->lrn)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">LRN</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $appointment->student->lrn }}
                                    </dd>
                                </div>
                                @endif
                                
                                @if($appointment->student->grade_level)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Grade Level</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $appointment->student->grade_level }}
                                    </dd>
                                </div>
                                @endif

                                @if($appointment->student->strand)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Strand</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $appointment->student->strand }}
                                    </dd>
                                </div>
                                @endif

                                @if($appointment->student->special_needs)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Special Needs</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 rounded text-xs">
                                            {{ $appointment->student->special_needs }}
                                        </span>
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Personal Information -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <i class="fas fa-id-card mr-2 text-green-500"></i>
                                Personal Information
                            </h4>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @if($appointment->student->birthdate)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Birthdate</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ \Carbon\Carbon::parse($appointment->student->birthdate)->format('F d, Y') }}
                                        <span class="text-xs text-gray-500">({{ \Carbon\Carbon::parse($appointment->student->birthdate)->age }} years old)</span>
                                    </dd>
                                </div>
                                @endif

                                @if($appointment->student->gender)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Gender</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ ucfirst($appointment->student->gender) }}
                                    </dd>
                                </div>
                                @endif

                                @if($appointment->student->contact_number)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Contact Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        <i class="fas fa-phone text-xs mr-1"></i>{{ $appointment->student->contact_number }}
                                    </dd>
                                </div>
                                @endif

                                @if($appointment->student->civil_status)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Civil Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ ucfirst($appointment->student->civil_status) }}
                                    </dd>
                                </div>
                                @endif

                                @if($appointment->student->nationality)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Nationality</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $appointment->student->nationality }}
                                    </dd>
                                </div>
                                @endif

                                @if($appointment->student->religion)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Religion</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $appointment->student->religion }}
                                    </dd>
                                </div>
                                @endif

                                @if($appointment->student->address)
                                <div class="md:col-span-2">
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $appointment->student->address }}
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Parent/Guardian Information -->
                        @if($appointment->student->father_name || $appointment->student->mother_name || $appointment->student->guardian_name)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <i class="fas fa-users mr-2 text-purple-500"></i>
                                Parent/Guardian Information
                            </h4>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Father Info -->
                                @if($appointment->student->father_name)
                                <div class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        <i class="fas fa-male mr-1"></i>Father
                                    </dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                                        <div class="font-medium">{{ $appointment->student->father_name }}</div>
                                        @if($appointment->student->father_contact)
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            <i class="fas fa-phone mr-1"></i>{{ $appointment->student->father_contact }}
                                        </div>
                                        @endif
                                        @if($appointment->student->father_occupation)
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            <i class="fas fa-briefcase mr-1"></i>{{ $appointment->student->father_occupation }}
                                        </div>
                                        @endif
                                    </dd>
                                </div>
                                @endif

                                <!-- Mother Info -->
                                @if($appointment->student->mother_name)
                                <div class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        <i class="fas fa-female mr-1"></i>Mother
                                    </dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                                        <div class="font-medium">{{ $appointment->student->mother_name }}</div>
                                        @if($appointment->student->mother_contact)
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            <i class="fas fa-phone mr-1"></i>{{ $appointment->student->mother_contact }}
                                        </div>
                                        @endif
                                        @if($appointment->student->mother_occupation)
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            <i class="fas fa-briefcase mr-1"></i>{{ $appointment->student->mother_occupation }}
                                        </div>
                                        @endif
                                    </dd>
                                </div>
                                @endif

                                <!-- Guardian Info -->
                                @if($appointment->student->guardian_name)
                                <div class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg {{ ($appointment->student->father_name && $appointment->student->mother_name) ? 'md:col-span-2' : '' }}">
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        <i class="fas fa-user-shield mr-1"></i>Guardian
                                    </dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                                        <div class="font-medium">{{ $appointment->student->guardian_name }}</div>
                                        @if($appointment->student->guardian_relationship)
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            <i class="fas fa-heart mr-1"></i>{{ ucfirst($appointment->student->guardian_relationship) }}
                                        </div>
                                        @endif
                                        @if($appointment->student->guardian_contact)
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            <i class="fas fa-phone mr-1"></i>{{ $appointment->student->guardian_contact }}
                                        </div>
                                        @endif
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Appointment Details (Right side - 1 column) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Appointment Details
                        </h3>
                        
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($appointment->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                           ($appointment->status === 'accepted' ? 'bg-blue-100 text-blue-800' :
                                           ($appointment->status === 'completed' ? 'bg-purple-100 text-purple-800' :
                                           'bg-red-100 text-red-800'))) }}">
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
                                    <i class="fas fa-calendar mr-1 text-gray-400"></i>
                                    {{ $appointment->preferred_date->format('F d, Y') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Time</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    <i class="fas fa-clock mr-1 text-gray-400"></i>
                                    {{ \Carbon\Carbon::parse($appointment->preferred_time)->format('h:i A') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reason for Appointment</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                                    {{ $appointment->concern }}
                                </dd>
                            </div>

                            @if($appointment->status === 'rejected' && $appointment->rejection_reason)
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                    <dt class="text-sm font-medium text-red-600 dark:text-red-400 flex items-center">
                                        <i class="fas fa-ban mr-2"></i> Rejection Reason
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                                        "{{ $appointment->rejection_reason }}"
                                    </dd>
                                </div>
                            @endif
                        </dl>

                        @if($appointment->status === 'pending')
                            <div class="mt-6 flex flex-col gap-3">
                                <button type="button" 
                                        onclick="openApproveModal()"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <i class="fas fa-check mr-2"></i>
                                    {{ __('Approve') }}
                                </button>

                                <button type="button"
                                        onclick="openRejectModal()"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <i class="fas fa-times mr-2"></i>
                                    {{ __('Reject') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Approve Appointment</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Are you sure you want to approve this appointment?</p>
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
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Reject Appointment</h3>
            <form action="{{ route('counselor.appointments.update-status', $appointment) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for Rejection</label>
                    <textarea name="rejection_reason" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
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