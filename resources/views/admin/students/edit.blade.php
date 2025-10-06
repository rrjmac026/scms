<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Student') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.students.update', $student) }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')
                        
                        <!-- User ID (Hidden) -->
                        <input type="hidden" name="user_id" value="{{ $student->user_id }}">

                        <!-- Academic Information -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Academic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Student Number -->
                                <div>
                                    <x-input-label for="student_number" :value="__('Student Number')" />
                                    <x-text-input id="student_number" name="student_number" type="text" 
                                        class="mt-1 block w-full" :value="old('student_number', $student->student_number)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('student_number')" />
                                </div>

                                <!-- LRN -->
                                <div>
                                    <x-input-label for="lrn" :value="__('LRN (Learner Reference Number)')" />
                                    <x-text-input id="lrn" name="lrn" type="text" 
                                        class="mt-1 block w-full" :value="old('lrn', $student->lrn)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('lrn')" />
                                </div>

                                <!-- Strand -->
                                <div>
                                    <x-input-label for="strand" :value="__('Strand')" />
                                    <select id="strand" name="strand" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                        <option value="">Select Strand</option>
                                        <option value="STEM" {{ old('strand', $student->strand) === 'STEM' ? 'selected' : '' }}>STEM</option>
                                        <option value="ABM" {{ old('strand', $student->strand) === 'ABM' ? 'selected' : '' }}>ABM</option>
                                        <option value="HUMSS" {{ old('strand', $student->strand) === 'HUMSS' ? 'selected' : '' }}>HUMSS</option>
                                        <option value="GAS" {{ old('strand', $student->strand) === 'GAS' ? 'selected' : '' }}>GAS</option>
                                        <option value="TVL" {{ old('strand', $student->strand) === 'TVL' ? 'selected' : '' }}>TVL</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('strand')" />
                                </div>

                                <!-- Grade Level -->
                                <div>
                                    <x-input-label for="grade_level" :value="__('Grade Level')" />
                                    <select id="grade_level" name="grade_level" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                        <option value="">Select Grade Level</option>
                                        <option value="11" {{ old('grade_level', $student->grade_level) === 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                                        <option value="12" {{ old('grade_level', $student->grade_level) === 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('grade_level')" />
                                </div>

                                <!-- Special Needs -->
                                <div class="md:col-span-2">
                                    <x-input-label for="special_needs" :value="__('Special Needs/Considerations')" />
                                    <textarea id="special_needs" name="special_needs" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        rows="3">{{ old('special_needs', $student->special_needs) }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('special_needs')" />
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Birthdate -->
                                <div>
                                    <x-input-label for="birthdate" :value="__('Birthdate')" />
                                    <x-text-input id="birthdate" name="birthdate" type="date" 
                                        class="mt-1 block w-full" :value="old('birthdate', $student->birthdate)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('birthdate')" />
                                </div>

                                <!-- Gender -->
                                <div>
                                    <x-input-label for="gender" :value="__('Gender')" />
                                    <select id="gender" name="gender" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ old('gender', $student->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender', $student->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                                </div>

                                <!-- Address -->
                                <div class="md:col-span-2">
                                    <x-input-label for="address" :value="__('Address')" />
                                    <textarea id="address" name="address" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        rows="2">{{ old('address', $student->address) }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>

                                <!-- Contact Number -->
                                <div>
                                    <x-input-label for="contact_number" :value="__('Contact Number')" />
                                    <x-text-input id="contact_number" name="contact_number" type="text" 
                                        class="mt-1 block w-full" :value="old('contact_number', $student->contact_number)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('contact_number')" />
                                </div>

                                <!-- Civil Status -->
                                <div>
                                    <x-input-label for="civil_status" :value="__('Civil Status')" />
                                    <select id="civil_status" name="civil_status" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                        <option value="">Select Civil Status</option>
                                        <option value="Single" {{ old('civil_status', $student->civil_status) === 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Married" {{ old('civil_status', $student->civil_status) === 'Married' ? 'selected' : '' }}>Married</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('civil_status')" />
                                </div>

                                <!-- Nationality -->
                                <div>
                                    <x-input-label for="nationality" :value="__('Nationality')" />
                                    <x-text-input id="nationality" name="nationality" type="text" 
                                        class="mt-1 block w-full" :value="old('nationality', $student->nationality)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('nationality')" />
                                </div>

                                <!-- Religion -->
                                <div>
                                    <x-input-label for="religion" :value="__('Religion')" />
                                    <x-text-input id="religion" name="religion" type="text" 
                                        class="mt-1 block w-full" :value="old('religion', $student->religion)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('religion')" />
                                </div>
                            </div>
                        </div>

                        <!-- Father's Information -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Father's Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Father Name -->
                                <div>
                                    <x-input-label for="father_name" :value="__('Full Name')" />
                                    <x-text-input id="father_name" name="father_name" type="text" 
                                        class="mt-1 block w-full" :value="old('father_name', $student->father_name)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('father_name')" />
                                </div>

                                <!-- Father Contact -->
                                <div>
                                    <x-input-label for="father_contact" :value="__('Contact Number')" />
                                    <x-text-input id="father_contact" name="father_contact" type="text" 
                                        class="mt-1 block w-full" :value="old('father_contact', $student->father_contact)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('father_contact')" />
                                </div>

                                <!-- Father Occupation -->
                                <div>
                                    <x-input-label for="father_occupation" :value="__('Occupation')" />
                                    <x-text-input id="father_occupation" name="father_occupation" type="text" 
                                        class="mt-1 block w-full" :value="old('father_occupation', $student->father_occupation)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('father_occupation')" />
                                </div>
                            </div>
                        </div>

                        <!-- Mother's Information -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Mother's Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Mother Name -->
                                <div>
                                    <x-input-label for="mother_name" :value="__('Full Name')" />
                                    <x-text-input id="mother_name" name="mother_name" type="text" 
                                        class="mt-1 block w-full" :value="old('mother_name', $student->mother_name)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('mother_name')" />
                                </div>

                                <!-- Mother Contact -->
                                <div>
                                    <x-input-label for="mother_contact" :value="__('Contact Number')" />
                                    <x-text-input id="mother_contact" name="mother_contact" type="text" 
                                        class="mt-1 block w-full" :value="old('mother_contact', $student->mother_contact)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('mother_contact')" />
                                </div>

                                <!-- Mother Occupation -->
                                <div>
                                    <x-input-label for="mother_occupation" :value="__('Occupation')" />
                                    <x-text-input id="mother_occupation" name="mother_occupation" type="text" 
                                        class="mt-1 block w-full" :value="old('mother_occupation', $student->mother_occupation)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('mother_occupation')" />
                                </div>
                            </div>
                        </div>

                        <!-- Guardian's Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Guardian's Information (if applicable)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Guardian Name -->
                                <div>
                                    <x-input-label for="guardian_name" :value="__('Full Name')" />
                                    <x-text-input id="guardian_name" name="guardian_name" type="text" 
                                        class="mt-1 block w-full" :value="old('guardian_name', $student->guardian_name)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('guardian_name')" />
                                </div>

                                <!-- Guardian Contact -->
                                <div>
                                    <x-input-label for="guardian_contact" :value="__('Contact Number')" />
                                    <x-text-input id="guardian_contact" name="guardian_contact" type="text" 
                                        class="mt-1 block w-full" :value="old('guardian_contact', $student->guardian_contact)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('guardian_contact')" />
                                </div>

                                <!-- Guardian Relationship -->
                                <div>
                                    <x-input-label for="guardian_relationship" :value="__('Relationship')" />
                                    <x-text-input id="guardian_relationship" name="guardian_relationship" type="text" 
                                        class="mt-1 block w-full" :value="old('guardian_relationship', $student->guardian_relationship)" 
                                        placeholder="e.g., Aunt, Uncle, Grandparent" />
                                    <x-input-error class="mt-2" :messages="$errors->get('guardian_relationship')" />
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6">
                            <x-secondary-button type="button" onclick="history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Update Student') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>