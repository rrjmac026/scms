<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create User') }}
        </h2>
    </x-slot>
    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6" x-data="{ role: '{{ old('role', 'student') }}' }">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                            <!-- Role -->
                            <div>
                                <x-input-label for="role" :value="__('Role')" />
                                <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm"
                                    x-model="role" required>
                                    <option value="student">Student</option>
                                    <option value="counselor">Counselor</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('role')" />
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
                                <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                            </div>
                        </div>

                        <!-- Student Fields -->
                        <div x-show="role === 'student'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="student_number" :value="__('Student Number')" />
                                <x-text-input id="student_number" name="student_number" type="text" class="mt-1 block w-full" 
                                    :value="old('student_number')" />
                                <x-input-error class="mt-2" :messages="$errors->get('student_number')" />
                            </div>

                            <!-- Strand -->
                            <div>
                                <x-input-label for="strand" :value="__('Strand')" />
                                <select id="strand" name="strand" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm">
                                    <option value="">Select Strand</option>
                                    <option value="STEM" {{ old('strand') == 'STEM' ? 'selected' : '' }}>STEM</option>
                                    <option value="ABM" {{ old('strand') == 'ABM' ? 'selected' : '' }}>ABM</option>
                                    <option value="HUMSS" {{ old('strand') == 'HUMSS' ? 'selected' : '' }}>HUMSS</option>
                                    <option value="GAS" {{ old('strand') == 'GAS' ? 'selected' : '' }}>GAS</option>
                                    <option value="TVL" {{ old('strand') == 'TVL' ? 'selected' : '' }}>TVL</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('strand')" />
                            </div>

                            <div>
                                <x-input-label for="grade_level" :value="__('Grade Level')" />
                                <select id="grade_level" name="grade_level" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm">
                                    <option value="">Select Grade Level</option>
                                    <option value="11" {{ old('grade_level') == '11' ? 'selected' : '' }}>Grade 11</option>
                                    <option value="12" {{ old('grade_level') == '12' ? 'selected' : '' }}>Grade 12</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('grade_level')" />
                            </div>

                            <div>
                                <x-input-label for="special_needs" :value="__('Special Needs')" />
                                <textarea id="special_needs" name="special_needs" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm">{{ old('special_needs') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('special_needs')" />
                            </div>
                        </div>

                        <!-- Counselor Fields -->
                        <div x-show="role === 'counselor'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Employee Number -->
                            <div>
                                <x-input-label for="employee_number" :value="__('Employee Number')" />
                                <x-text-input id="employee_number" name="employee_number" type="text" 
                                    class="mt-1 block w-full" :value="old('employee_number')" />
                                <x-input-error class="mt-2" :messages="$errors->get('employee_number')" />
                            </div>

                            <!-- Specialization -->
                            <div>
                                <x-input-label for="specialization" :value="__('Specialization')" />
                                <x-text-input id="specialization" name="specialization" type="text" 
                                    class="mt-1 block w-full" :value="old('specialization')" />
                                <x-input-error class="mt-2" :messages="$errors->get('specialization')" />
                            </div>

                            <!-- Gender -->
                            <div>
                                <x-input-label for="gender" :value="__('Gender')" />
                                <select id="gender" name="gender" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
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
                            <div class="md:col-span-2">
                                <x-input-label for="bio" :value="__('Short Bio')" />
                                <textarea id="bio" name="bio" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm">{{ old('bio') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                            </div>

                        </div>


                        <div class="flex items-center justify-end gap-4 mt-6">
                            <x-secondary-button type="button" onclick="history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button type="submit">
                                {{ __('Create User') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>