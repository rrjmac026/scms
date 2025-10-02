<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Counselor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.counselors.store') }}" method="POST" class="space-y-8">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Personal Information -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">
                                    Personal Information
                                </h3>

                                <!-- First Name -->
                                <div>
                                    <x-input-label for="first_name" :value="__('First Name')" />
                                    <x-text-input id="first_name" name="first_name" type="text" 
                                        class="mt-1 block w-full" :value="old('first_name')" required autofocus />
                                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                                </div>

                                <!-- Last Name -->
                                <div>
                                    <x-input-label for="last_name" :value="__('Last Name')" />
                                    <x-text-input id="last_name" name="last_name" type="text" 
                                        class="mt-1 block w-full" :value="old('last_name')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                                </div>

                                <!-- Gender -->
                                <div>
                                    <x-input-label for="gender" :value="__('Gender')" />
                                    <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-pink-500 focus:border-pink-500">
                                        <option value="">-- Select Gender --</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                                </div>

                                <!-- Birth Date -->
                                <div>
                                    <x-input-label for="birth_date" :value="__('Birth Date')" />
                                    <x-text-input id="birth_date" name="birth_date" type="date" 
                                        class="mt-1 block w-full" :value="old('birth_date')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                                </div>

                                <!-- Bio -->
                                <div>
                                    <x-input-label for="bio" :value="__('Short Bio')" />
                                    <textarea id="bio" name="bio" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-pink-500 focus:border-pink-500"
                                    >{{ old('bio') }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" name="email" type="email" 
                                        class="mt-1 block w-full" :value="old('email')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <!-- Password -->
                                <div>
                                    <x-input-label for="password" :value="__('Password')" />
                                    <x-text-input id="password" name="password" type="password" 
                                        class="mt-1 block w-full" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" 
                                        class="mt-1 block w-full" required />
                                </div>
                            </div>

                            <!-- Professional Information -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">
                                    Professional Information
                                </h3>

                                <!-- Employee Number -->
                                <div>
                                    <x-input-label for="employee_number" :value="__('Employee Number')" />
                                    <x-text-input id="employee_number" name="employee_number" type="text" 
                                        class="mt-1 block w-full" :value="old('employee_number')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('employee_number')" />
                                </div>

                                <!-- Counseling Category -->
                                <div>
                                    <x-input-label for="counseling_category_id" :value="__('Counseling Category')" />
                                    <select id="counseling_category_id" name="counseling_category_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 
                                        dark:bg-gray-900 dark:text-gray-100 focus:ring-pink-500 focus:border-pink-500" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('counseling_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('counseling_category_id')" />
                                </div>

                                <!-- Availability Schedule -->
                                <div>
                                    <x-input-label for="availability_schedule" :value="__('Availability Schedule')" />
                                    <div class="mt-2 space-y-2">
                                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                            <div class="flex items-center">
                                                <input type="checkbox" name="availability_schedule[]" value="{{ $day }}"
                                                    {{ in_array($day, old('availability_schedule', [])) ? 'checked' : '' }}
                                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-pink-600 focus:ring-pink-500">
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $day }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('availability_schedule')" />
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-4">
                            <x-secondary-button onclick="history.back()">
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
