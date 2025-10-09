<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Change Password for') }} {{ $user->first_name }} {{ $user->last_name }}
            </h2>
            <x-secondary-button onclick="history.back()">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6">
                    <form action="{{ route('admin.users.update-password', $user) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- User Info Display -->
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-lg">
                                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $user->email }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">
                                        Role: <span class="font-medium">{{ ucfirst($user->role) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Password Fields -->
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="password" :value="__('New Password')" />
                                <x-text-input id="password" name="password" type="password" 
                                    class="mt-1 block w-full" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Minimum 8 characters
                                </p>
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                                <x-text-input id="password_confirmation" name="password_confirmation" 
                                    type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Warning Message -->
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                        The user will need to use this new password for their next login.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-4">
                            <x-secondary-button type="button" onclick="history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                <i class="fas fa-key mr-2"></i>{{ __('Update Password') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>