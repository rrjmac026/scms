<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Record New Behavior Incident') }}
            </h2>
            <x-secondary-button onclick="history.back()">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl">
                <form action="{{ route('admin.behavior-incidents.store') }}" method="POST" class="p-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Selection -->
                        <div>
                            <x-input-label for="student_id" :value="__('Student')" />
                            <select id="student_id" name="student_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->name }} ({{ $student->student_number }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('student_id')" />
                        </div>

                        <!-- Date -->
                        <div>
                            <x-input-label for="date" :value="__('Date of Incident')" />
                            <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('date')" />
                        </div>

                        <!-- Incident Type -->
                        <div>
                            <x-input-label for="type" :value="__('Incident Type')" />
                            <x-text-input id="type" name="type" type="text" class="mt-1 block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('type')" />
                        </div>

                        <!-- Location -->
                        <div>
                            <x-input-label for="location" :value="__('Location')" />
                            <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('location')" />
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"></textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Action Taken -->
                        <div class="md:col-span-2">
                            <x-input-label for="action_taken" :value="__('Action Taken')" />
                            <textarea id="action_taken" name="action_taken" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"></textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('action_taken')" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-4">
                        <x-secondary-button type="button" onclick="history.back()">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-primary-button>
                            {{ __('Record Incident') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
