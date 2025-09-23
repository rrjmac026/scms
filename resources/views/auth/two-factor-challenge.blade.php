<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
    </div>

    <form method="POST" action="{{ route('two-factor.verify') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Authentication Code')" />
            <x-text-input id="code"
                         type="text"
                         inputmode="numeric"
                         name="code"
                         autofocus
                         autocomplete="one-time-code"
                         class="mt-1 block w-full"
                         :value="old('code')" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ml-4">
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-4 text-center">
        <span class="text-sm text-gray-600 dark:text-gray-400">
            {{ __("Don't have access to your authenticator app?") }}
        </span>

        <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-4">
            @csrf
            <input type="hidden" name="recovery_code" value="1">
            <button type="submit" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline">
                {{ __('Use a recovery code') }}
            </button>
        </form>
    </div>
</x-guest-layout>