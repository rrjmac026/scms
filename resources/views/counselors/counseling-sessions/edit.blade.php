<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Update Session') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form Section -->
                <div class="lg:col-span-2 order-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl">
                        <div class="p-8">
                            <form action="{{ route('counselor.counseling-sessions.update', $counselingSession) }}" method="POST" class="space-y-6">
                                @csrf
                                @method('PATCH')

                                <!-- Student Selection (Read-only) -->
                                <div>
                                    <x-input-label for="student_id" :value="__('Select Student')" />
                                    <select id="student_id" name="student_id" required disabled
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800">
                                        <option value="{{ $counselingSession->student->id }}" selected>
                                            {{ $counselingSession->student->user->name }} - {{ $counselingSession->student->student_number }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Concern -->
                                <div>
                                    <x-input-label for="concern" :value="__('Primary Concern')" />
                                    <textarea id="concern" name="concern" rows="3" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800"
                                        placeholder="Describe the main concern or reason for the session...">{{ old('concern', $counselingSession->concern) }}</textarea>
                                    <x-input-error :messages="$errors->get('concern')" class="mt-2" />
                                </div>

                                <!-- Session Notes -->
                                <div>
                                    <x-input-label for="notes" :value="__('Session Notes')" />
                                    <textarea id="notes" name="notes" rows="6"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800"
                                        placeholder="Enter any initial notes or observations...">{{ old('notes', $counselingSession->notes) }}</textarea>
                                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                                </div>

                                <!-- Session Status -->
                                <div>
                                    <x-input-label for="status" :value="__('Session Status')" />
                                    <select id="status" name="status" required
                                        @disabled(!$counselingSession->started_at)
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800">
                                        <option value="pending" {{ $counselingSession->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="ongoing" {{ $counselingSession->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                        <option value="completed" {{ $counselingSession->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>

                                    @if(!$counselingSession->started_at)
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            ⚠️ You can update the status once the session has started.
                                        </p>
                                    @endif
                                </div>

                                <div class="flex justify-end gap-4">
                                    <x-secondary-button onclick="history.back()">
                                        {{ __('Cancel') }}
                                    </x-secondary-button>
                                    <x-primary-button>
                                        {{ __('Update Session') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Student Information Sidebar -->
                <div class="order-2 lg:order-3">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl sticky top-6">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                                <i class="fas fa-user-graduate text-pink-600"></i>
                                Student Information
                            </h3>
                            
                            <!-- Student Header -->
                            <div class="text-center mb-6">
                                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center shadow-lg">
                                    <i class="fas fa-user-graduate text-2xl text-white"></i>
                                </div>
                                <div class="font-bold text-lg text-gray-900 dark:text-gray-100">
                                    {{ $counselingSession->student->user->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $counselingSession->student->student_number }}
                                </div>
                            </div>

                            <!-- Tabs Navigation -->
                            <div x-data="{ activeTab: 'basic' }" class="space-y-4">
                                <!-- Tab Buttons -->
                                <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
                                    <button 
                                        @click="activeTab = 'basic'"
                                        :class="activeTab === 'basic' ? 'border-pink-500 text-pink-600 dark:text-pink-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                                        class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                                    >
                                        <i class="fas fa-id-card mr-1"></i> Basic
                                    </button>
                                    <button 
                                        @click="activeTab = 'personal'"
                                        :class="activeTab === 'personal' ? 'border-pink-500 text-pink-600 dark:text-pink-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                                        class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                                    >
                                        <i class="fas fa-user mr-1"></i> Personal
                                    </button>
                                    <button 
                                        @click="activeTab = 'family'"
                                        :class="activeTab === 'family' ? 'border-pink-500 text-pink-600 dark:text-pink-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                                        class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                                    >
                                        <i class="fas fa-users mr-1"></i> Family
                                    </button>
                                </div>

                                <!-- Tab Content -->
                                <div class="space-y-3">
                                    <!-- Basic Info Tab -->
                                    <div x-show="activeTab === 'basic'" class="space-y-3">
                                        <div class="flex items-start justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                <i class="fas fa-envelope mr-1"></i> Email
                                            </span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 text-right break-all">
                                                {{ $counselingSession->student->user->email }}
                                            </span>
                                        </div>

                                        @if($counselingSession->student->lrn)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                <i class="fas fa-barcode mr-1"></i> LRN
                                            </span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 font-mono">
                                                {{ $counselingSession->student->lrn }}
                                            </span>
                                        </div>
                                        @endif

                                        @if($counselingSession->student->grade_level)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                <i class="fas fa-graduation-cap mr-1"></i> Grade Level
                                            </span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                Grade {{ $counselingSession->student->grade_level }}
                                            </span>
                                        </div>
                                        @endif

                                        @if($counselingSession->student->strand)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                <i class="fas fa-book mr-1"></i> Strand
                                            </span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $counselingSession->student->strand }}
                                            </span>
                                        </div>
                                        @endif

                                        @if($counselingSession->student->special_needs)
                                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-l-4 border-blue-500">
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                                <i class="fas fa-info-circle mr-1"></i> Special Needs
                                            </div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $counselingSession->student->special_needs }}
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Personal Info Tab -->
                                    <div x-show="activeTab === 'personal'" class="space-y-3">
                                        @if($counselingSession->student->birthdate)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                <i class="fas fa-birthday-cake mr-1"></i> Birthdate
                                            </span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ \Carbon\Carbon::parse($counselingSession->student->birthdate)->format('F d, Y') }}
                                            </span>
                                        </div>
                                        @endif

                                        @if($counselingSession->student->gender)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                <i class="fas fa-venus-mars mr-1"></i> Gender
                                            </span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ ucfirst($counselingSession->student->gender) }}
                                            </span>
                                        </div>
                                        @endif

                                        @if($counselingSession->student->civil_status)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                <i class="fas fa-heart mr-1"></i> Civil Status
                                            </span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ ucfirst($counselingSession->student->civil_status) }}
                                            </span>
                                        </div>
                                        @endif

                                        @if($counselingSession->student->nationality)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                <i class="fas fa-flag mr-1"></i> Nationality
                                            </span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $counselingSession->student->nationality }}
                                            </span>
                                        </div>
                                        @endif

                                        @if($counselingSession->student->religion)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                <i class="fas fa-pray mr-1"></i> Religion
                                            </span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $counselingSession->student->religion }}
                                            </span>
                                        </div>
                                        @endif

                                        @if($counselingSession->student->contact_number)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                <i class="fas fa-phone mr-1"></i> Contact
                                            </span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $counselingSession->student->contact_number }}
                                            </span>
                                        </div>
                                        @endif

                                        @if($counselingSession->student->address)
                                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                                <i class="fas fa-map-marker-alt mr-1"></i> Address
                                            </div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $counselingSession->student->address }}
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Family Info Tab -->
                                    <div x-show="activeTab === 'family'" class="space-y-4">
                                        <!-- Father Info -->
                                        @if($counselingSession->student->father_name)
                                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-l-4 border-blue-500">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                <i class="fas fa-male mr-1"></i> Father
                                            </div>
                                            <div class="space-y-2 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Name:</span>
                                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $counselingSession->student->father_name }}</span>
                                                </div>
                                                @if($counselingSession->student->father_contact)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Contact:</span>
                                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $counselingSession->student->father_contact }}</span>
                                                </div>
                                                @endif
                                                @if($counselingSession->student->father_occupation)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Occupation:</span>
                                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $counselingSession->student->father_occupation }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Mother Info -->
                                        @if($counselingSession->student->mother_name)
                                        <div class="p-3 bg-pink-50 dark:bg-pink-900/20 rounded-lg border-l-4 border-pink-500">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                <i class="fas fa-female mr-1"></i> Mother
                                            </div>
                                            <div class="space-y-2 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Name:</span>
                                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $counselingSession->student->mother_name }}</span>
                                                </div>
                                                @if($counselingSession->student->mother_contact)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Contact:</span>
                                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $counselingSession->student->mother_contact }}</span>
                                                </div>
                                                @endif
                                                @if($counselingSession->student->mother_occupation)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Occupation:</span>
                                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $counselingSession->student->mother_occupation }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Guardian Info -->
                                        @if($counselingSession->student->guardian_name)
                                        <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg border-l-4 border-purple-500">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                <i class="fas fa-user-shield mr-1"></i> Guardian
                                            </div>
                                            <div class="space-y-2 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Name:</span>
                                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $counselingSession->student->guardian_name }}</span>
                                                </div>
                                                @if($counselingSession->student->guardian_contact)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Contact:</span>
                                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $counselingSession->student->guardian_contact }}</span>
                                                </div>
                                                @endif
                                                @if($counselingSession->student->guardian_relationship)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Relationship:</span>
                                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $counselingSession->student->guardian_relationship }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>