<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Counseling Category') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.counseling-categories.update', $category) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PATCH')
                        
                        <div>
                            <x-input-label for="name" :value="__('Category Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                :value="old('name', $category->name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800" 
                                required>{{ old('description', $category->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="counselor_id" :value="__('Assign to Counselor')" />
                            <select id="counselor_id" name="counselor_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800">
                                <option value="">Select a counselor</option>
                                @foreach($counselors as $counselor)
                                    <option value="{{ $counselor->id }}" 
                                        {{ old('counselor_id', $category->counselor_id) == $counselor->id ? 'selected' : '' }}>
                                        {{ $counselor->user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('counselor_id')" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800">
                                <option value="active" {{ $category->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $category->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div>
                            <x-input-label for="admin_feedback" :value="__('Admin Feedback')" />
                            <textarea id="admin_feedback" name="admin_feedback" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 dark:focus:ring-pink-800">{{ old('admin_feedback', $category->admin_feedback) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('admin_feedback')" />
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <x-secondary-button onclick="history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Update Category') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
