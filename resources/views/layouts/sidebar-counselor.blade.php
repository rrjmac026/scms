<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <nav class="bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 p-4 w-64 min-h-screen">
        <div class="p-4 space-y-2">
            <!-- Dashboard Section -->
            <div class="mb-4">
                <x-nav-link :href="route('counselor.dashboard')" :active="request()->routeIs('counselor.dashboard')"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('counselor.dashboard') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-home mr-3 text-lg"></i>
                    <span>{{ __('Dashboard') }}</span>
                </x-nav-link>
            </div>

            <!-- Main Navigation -->
            <div class="space-y-1">
                <x-nav-link :href="route('counselor.appointments')" :active="request()->routeIs('counselor.appointments')"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('counselor.appointments') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-calendar-check mr-3 text-lg"></i>
                    <span>{{ __('Appointments') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('counselor.sessions')" :active="request()->routeIs('counselor.sessions')"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('counselor.sessions') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-comments mr-3 text-lg"></i>
                    <span>{{ __('Sessions') }}</span>
                </x-nav-link>
            </div>
        </div>
    </nav>
</div>
