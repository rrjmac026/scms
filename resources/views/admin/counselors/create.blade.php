<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add New Counselor') }}
            </h2>
        </div>
    </x-slot>

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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.counselors.store') }}" method="POST" class="space-y-8" novalidate>
                        @csrf
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            Fields marked with <span class="text-red-500">*</span> are required
                        </p>
                        
                        <!-- User Account Information -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">User Account Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- First Name -->
                                <div>
                                    <x-input-label for="first_name">
                                        {{ __('First Name') }} <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input id="first_name" name="first_name" type="text" 
                                        class="mt-1 block w-full" :value="old('first_name')" required autofocus />
                                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                                </div>

                                <!-- Middle Name -->
                                <div>
                                    <x-input-label for="middle_name">
                                        {{ __('Middle Name') }}
                                    </x-input-label>
                                    <x-text-input id="middle_name" name="middle_name" type="text" 
                                        class="mt-1 block w-full" :value="old('middle_name')" />
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Optional
                                    </p>
                                    <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
                                </div>

                                <!-- Last Name -->
                                <div>
                                    <x-input-label for="last_name">
                                        {{ __('Last Name') }} <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input id="last_name" name="last_name" type="text" 
                                        class="mt-1 block w-full" :value="old('last_name')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input-label for="email">
                                        {{ __('Email') }} <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input id="email" name="email" type="email" 
                                        class="mt-1 block w-full" :value="old('email')" required 
                                        placeholder="counselor@lccdo.edu.ph" />
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Must be a valid @lccdo.edu.ph email address
                                    </p>
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <!-- Password -->
                                <div>
                                    <x-input-label for="password">
                                        {{ __('Password') }} <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input id="password" name="password" type="password" 
                                        class="mt-1 block w-full" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <x-input-label for="password_confirmation">
                                        {{ __('Confirm Password') }} <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" 
                                        class="mt-1 block w-full" required />
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Professional Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Employee Number -->
                                <div>
                                    <x-input-label for="employee_number">
                                        {{ __('Employee Number') }} <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input id="employee_number" name="employee_number" type="text" 
                                        class="mt-1 block w-full" :value="old('employee_number')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('employee_number')" />
                                </div>

                                <!-- Assigned Grade Level -->
                                <div>
                                    <x-input-label for="assigned_grade_level">
                                        {{ __('Assigned Grade Level') }} <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <select name="assigned_grade_level" id="assigned_grade_level" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-pink-500 focus:border-pink-500">
                                        <option value="">Select Grade Level</option>
                                        <option value="11" {{ old('assigned_grade_level') == '11' ? 'selected' : '' }}>Grade 11</option>
                                        <option value="12" {{ old('assigned_grade_level') == '12' ? 'selected' : '' }}>Grade 12</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('assigned_grade_level')" />
                                </div>

                                <!-- Availability Schedule -->
                                <div class="md:col-span-2">
                                    <x-input-label for="availability_schedule">
                                        {{ __('Availability Schedule') }} <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <div class="mt-2 space-y-2">
                                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                            <div class="flex items-center">
                                                <input type="checkbox" name="availability_schedule[]" value="{{ $day }}"
                                                    {{ in_array($day, old('availability_schedule', [])) ? 'checked' : '' }}
                                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-pink-600 focus:ring-pink-500" required>
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $day }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('availability_schedule')" />
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6">
                            <x-secondary-button type="button" onclick="history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-primary-button>
                                {{ __('Create Counselor') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>