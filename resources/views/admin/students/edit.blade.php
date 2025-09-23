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
                    <form action="{{ route('admin.students.update', $student) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Student Information -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Student Information</h3>
                                
                                <!-- Student Number -->
                                <div>
                                    <x-input-label for="student_number" :value="__('Student Number')" />
                                    <x-text-input id="student_number" name="student_number" type="text" 
                                        class="mt-1 block w-full" :value="old('student_number', $student->student_number)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('student_number')" />
                                </div>

                                <!-- Course -->
                                <div>
                                    <x-input-label for="course" :value="__('Course')" />
                                    <x-text-input id="course" name="course" type="text" 
                                        class="mt-1 block w-full" :value="old('course', $student->course)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('course')" />
                                </div>

                                <!-- Year Level -->
                                <div>
                                    <x-input-label for="year_level" :value="__('Year Level')" />
                                    <select id="year_level" name="year_level" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                        <option value="">Select Year Level</option>
                                        @foreach(['1st Year', '2nd Year', '3rd Year', '4th Year'] as $year)
                                            <option value="{{ $year }}" {{ old('year_level', $student->year_level) === $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('year_level')" />
                                </div>

                                <!-- Special Needs -->
                                <div>
                                    <x-input-label for="special_needs" :value="__('Special Needs/Considerations')" />
                                    <textarea id="special_needs" name="special_needs" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                        rows="3">{{ old('special_needs', $student->special_needs) }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('special_needs')" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <x-secondary-button onclick="history.back()">
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
