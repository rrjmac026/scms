<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Update Session') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.counseling-sessions.update', $counselingSession) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Counselor Selection -->
                        <div>
                            <x-input-label for="counselor_id" :value="__('Select Counselor')" />
                            <select id="counselor_id" name="counselor_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800">
                                <option value="">Choose a counselor</option>
                                @foreach($counselors as $counselor)
                                    <option value="{{ $counselor->id }}" {{ $counselingSession->counselor_id == $counselor->id ? 'selected' : '' }}>
                                        {{ $counselor->user->name }} - {{ $counselor->specialization }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('counselor_id')" class="mt-2" />
                        </div>

                        <!-- Student Selection -->
                        <div>
                            <x-input-label for="student_id" :value="__('Select Student')" />
                            <select id="student_id" name="student_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800">
                                <option value="">Choose a student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ $counselingSession->student_id == $student->id ? 'selected' : '' }}>
                                        {{ $student->user->name }} - {{ $student->student_number }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                        </div>

                        <!-- Session Status -->
                        <div>
                            <x-input-label for="status" :value="__('Session Status')" />
                            <select id="status" name="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800">
                                <option value="pending" {{ $counselingSession->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="ongoing" {{ $counselingSession->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ $counselingSession->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        <!-- Updated Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Session Notes')" />
                            <textarea id="notes" name="notes" rows="6"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800">{{ old('notes', $counselingSession->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
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
    </div>
</x-app-layout>
