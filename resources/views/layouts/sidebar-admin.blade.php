<!-- resources/views/layouts/sidebar.blade.php -->
<nav class="flex flex-col h-full bg-white dark:bg-gray-800">
    <!-- Header -->
    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-shield text-white text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Admin Panel</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">System Administration</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="flex-1 p-6 space-y-2 overflow-y-auto">
        <!-- Overview -->
        <div class="mb-6">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 px-4">
                {{ __('Overview') }}
            </h3>
            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-in-out {{ request()->routeIs('admin.dashboard') ? 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:translate-x-1' }}">
                <i class="fas fa-chart-pie w-5 h-5 mr-3"></i>
                <span>{{ __('Dashboard') }}</span>
            </x-nav-link>
        </div>
</nav>
