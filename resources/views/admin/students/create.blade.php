<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add New Student') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.students.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- User Information -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Personal Information</h3>
                                
                                <!-- First Name -->
                                <div>
                                    <x-input-label for="first_name" :value="__('First Name')" />
                                    <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" 
                                        :value="old('first_name')" required autofocus />
                                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                                </div>

                                <!-- Middle Name -->
                                <div>
                                    <x-input-label for="middle_name" :value="__('Middle Name')" />
                                    <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" 
                                        :value="old('middle_name')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
                                </div>

                                <!-- Last Name -->
                                <div>
                                    <x-input-label for="last_name" :value="__('Last Name')" />
                                    <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" 
                                        :value="old('last_name')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
                                        :value="old('email')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <!-- Password -->
                                <div>
                                    <x-input-label for="password" :value="__('Password')" />
                                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" 
                                        class="mt-1 block w-full" required />
                                </div>
                            </div>

                            <!-- Student Information -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Academic Information</h3>
                                
                                <!-- Student Number -->
                                <div>
                                    <x-input-label for="student_number" :value="__('Student Number')" />
                                    <x-text-input id="student_number" name="student_number" type="text" 
                                        class="mt-1 block w-full" :value="old('student_number')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('student_number')" />
                                </div>

                                <!-- Course -->
                                <div>
                                    <x-input-label for="course" :value="__('Course')" />
                                    <x-text-input id="course" name="course" type="text" 
                                        class="mt-1 block w-full" :value="old('course')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('course')" />
                                </div>

                                <!-- Year Level -->
                                <div>
                                    <x-input-label for="year_level" :value="__('Year Level')" />
                                    <select id="year_level" name="year_level" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                        <option value="">Select Year Level</option>
                                        <option value="1st Year">1st Year</option>
                                        <option value="2nd Year">2nd Year</option>
                                        <option value="3rd Year">3rd Year</option>
                                        <option value="4th Year">4th Year</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('year_level')" />
                                </div>

                                <!-- Special Needs -->
                                <div>
                                    <x-input-label for="special_needs" :value="__('Special Needs/Considerations')" />
                                    <textarea id="special_needs" name="special_needs" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                        rows="3">{{ old('special_needs') }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('special_needs')" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <x-secondary-button onclick="history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Create Student') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
