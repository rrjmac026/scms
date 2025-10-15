<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Counselor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.counselors.update', $counselor) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Information -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Personal Information</h3>
                                
                                <!-- First Name -->
                                <div>
                                    <x-input-label for="first_name" :value="__('First Name')" />
                                    <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" 
                                        :value="old('first_name', $counselor->user->first_name)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                                </div>

                                <!-- Middle Name -->
                                <div>
                                    <x-input-label for="middle_name" :value="__('Middle Name')" />
                                    <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" 
                                        :value="old('middle_name', $counselor->user->middle_name)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Optional') }}
                                    </p>
                                </div>

                                <!-- Last Name -->
                                <div>
                                    <x-input-label for="last_name" :value="__('Last Name')" />
                                    <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" 
                                        :value="old('last_name', $counselor->user->last_name)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
                                        :value="old('email', $counselor->user->email)" required 
                                        placeholder="example@lccdo.edu.ph" />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Must be a valid @lccdo.edu.ph email address') }}
                                    </p>
                                </div>

                                <!-- Password -->
                                <div>
                                    <x-input-label for="password" :value="__('New Password')" />
                                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Leave blank to keep current password') }}
                                    </p>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" 
                                        class="mt-1 block w-full" />
                                </div>
                            </div>

                            <!-- Professional Information -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Professional Information</h3>
                                
                                <!-- Employee Number -->
                                <div>
                                    <x-input-label for="employee_number" :value="__('Employee Number')" />
                                    <x-text-input id="employee_number" name="employee_number" type="text" 
                                        class="mt-1 block w-full" :value="old('employee_number', $counselor->employee_number)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('employee_number')" />
                                </div>

                                <!-- Assigned Grade Level -->
                                <div>
                                    <x-input-label for="assigned_grade_level" :value="__('Assigned Grade Level')" />
                                    <select name="assigned_grade_level" id="assigned_grade_level"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-red-500 focus:border-red-500">
                                        <option value="">Select Grade Level</option>
                                        <option value="11" {{ old('assigned_grade_level', $counselor->assigned_grade_level ?? '') == '11' ? 'selected' : '' }}>Grade 11</option>
                                        <option value="12" {{ old('assigned_grade_level', $counselor->assigned_grade_level ?? '') == '12' ? 'selected' : '' }}>Grade 12</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('assigned_grade_level')" />
                                </div>

                                <!-- Availability Schedule -->
                                <div>
                                    <x-input-label for="availability_schedule" :value="__('Availability Schedule')" />
                                    <div class="mt-2 space-y-2">
                                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                            <div class="flex items-center">
                                                <input type="checkbox" name="availability_schedule[]" value="{{ $day }}"
                                                       {{ in_array($day, $counselor->availability_schedule ?? []) ? 'checked' : '' }}
                                                       class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-red-600 shadow-sm focus:ring-red-500">
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $day }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <x-secondary-button type="button" onclick="history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Update Counselor') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>