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
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                                {{ $student->user->first_name }} {{ $student->user->middle_name }} {{ $student->user->last_name }}
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
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">LRN</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->lrn ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Strand</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($student->strand === 'STEM') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($student->strand === 'ABM') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($student->strand === 'HUMSS') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                            @elseif($student->strand === 'GAS') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($student->strand === 'TVL') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                            @endif">
                                            {{ $student->strand ?? 'Not specified' }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Grade Level</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->grade_level ?? 'Not specified' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Details Section -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Personal Information</h3>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Birthdate</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $student->birthdate ? \Carbon\Carbon::parse($student->birthdate)->format('F d, Y') : 'N/A' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Gender</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->gender ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->contact_number ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Civil Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->civil_status ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nationality</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->nationality ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Religion</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->religion ?? 'N/A' }}</dd>
                                </div>
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->address ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Parent/Guardian Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Parent/Guardian Information</h3>
                            
                            <div class="space-y-6">
                                <!-- Father's Info -->
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Father's Information</h4>
                                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->father_name ?? 'N/A' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->father_contact ?? 'N/A' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Occupation</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->father_occupation ?? 'N/A' }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Mother's Info -->
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Mother's Information</h4>
                                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->mother_name ?? 'N/A' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->mother_contact ?? 'N/A' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Occupation</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->mother_occupation ?? 'N/A' }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Guardian's Info -->
                                @if($student->guardian_name)
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Guardian's Information</h4>
                                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->guardian_name }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->guardian_contact ?? 'N/A' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Relationship</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->guardian_relationship ?? 'N/A' }}</dd>
                                        </div>
                                    </dl>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Counseling History -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Counseling History</h3>
                            <div class="space-y-4">
                                @forelse($student->counselingSessions as $session)
                                    <div class="border-l-4 border-blue-500 pl-4 py-2">
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