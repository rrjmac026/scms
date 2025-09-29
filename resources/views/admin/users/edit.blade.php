<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit User') }}
            </h2>
            <x-secondary-button onclick="history.back()">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" 
                          class="space-y-6"
                          x-data="{ role: '{{ old('role', $user->role) }}' }">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Information -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Personal Information</h3>
                                
                                <div>
                                    <x-input-label for="first_name" :value="__('First Name')" />
                                    <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" 
                                        :value="old('first_name', $user->first_name)" required />
                                </div>

                                <div>
                                    <x-input-label for="middle_name" :value="__('Middle Name')" />
                                    <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" 
                                        :value="old('middle_name', $user->middle_name)" />
                                </div>

                                <div>
                                    <x-input-label for="last_name" :value="__('Last Name')" />
                                    <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" 
                                        :value="old('last_name', $user->last_name)" required />
                                </div>
                            </div>

                            <!-- Account Information -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Account Information</h3>

                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
                                        :value="old('email', $user->email)" required />
                                </div>

                                <div>
                                    <x-input-label for="role" :value="__('Role')" />
                                    <select id="role" name="role" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                        x-model="role">
                                        <option value="student">Student</option>
                                        <option value="counselor">Counselor</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>

                                <div>
                                    <x-input-label for="new_password" :value="__('New Password')" />
                                    <x-text-input id="new_password" name="password" type="password" class="mt-1 block w-full" />
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave blank to keep current password</p>
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                                </div>
                            </div>
                        </div>

                        <!-- Student Fields -->
                        <div x-show="role === 'student'" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                            <h3 class="col-span-2 text-lg font-medium text-gray-900 dark:text-gray-100">Student Information</h3>

                            <div>
                                <x-input-label for="student_number" :value="__('Student Number')" />
                                <x-text-input id="student_number" name="student_number" type="text" class="mt-1 block w-full" 
                                    :value="old('student_number', optional($user->student)->student_number)" />
                            </div>

                            <div>
                                <x-input-label for="course" :value="__('Course')" />
                                <x-text-input id="course" name="course" type="text" class="mt-1 block w-full" 
                                    :value="old('course', optional($user->student)->course)" />
                            </div>

                            <div>
                                <x-input-label for="year_level" :value="__('Year Level')" />
                                <x-text-input id="year_level" name="year_level" type="text" class="mt-1 block w-full" 
                                    :value="old('year_level', optional($user->student)->year_level)" />
                            </div>

                            <div>
                                <x-input-label for="special_needs" :value="__('Special Needs')" />
                                <textarea id="special_needs" name="special_needs" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">{{ old('special_needs', optional($user->student)->special_needs) }}</textarea>
                            </div>
                        </div>

                        <!-- Counselor Fields -->
                        <div x-show="role === 'counselor'" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                            <h3 class="col-span-2 text-lg font-medium text-gray-900 dark:text-gray-100">Counselor Information</h3>

                            <div>
                                <x-input-label for="employee_number" :value="__('Employee Number')" />
                                <x-text-input id="employee_number" name="employee_number" type="text" class="mt-1 block w-full" 
                                    :value="old('employee_number', optional($user->counselor)->employee_number)" />
                            </div>

                            <div>
                                <x-input-label for="gender" :value="__('Gender')" />
                                <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                    <option value="" disabled {{ old('gender', optional($user->counselor)->gender) ? '' : 'selected' }}>Select Gender</option>
                                    <option value="male" {{ old('gender', optional($user->counselor)->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', optional($user->counselor)->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', optional($user->counselor)->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="specialization" :value="__('Specialization')" />
                                <x-text-input id="specialization" name="specialization" type="text" class="mt-1 block w-full" 
                                    :value="old('specialization', optional($user->counselor)->specialization)" />
                            </div>

                            <div>
                                <x-input-label for="contact_number" :value="__('Contact Number')" />
                                <x-text-input id="contact_number" name="contact_number" type="text" class="mt-1 block w-full" 
                                    :value="old('contact_number', optional($user->counselor)->contact_number)" />
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-4">
                            <x-secondary-button type="button" onclick="history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Update User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
