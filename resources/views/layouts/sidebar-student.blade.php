{{-- layouts/sidebar-student.blade.php --}}
<div>
    <div class="flex flex-col h-full bg-white dark:bg-gray-800">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('Student Portal') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Your Counseling Hub') }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-6 space-y-2 overflow-y-auto">
            <!-- Home Section -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 px-4">
                    {{ __('Home') }}
                </h3>
                <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-in-out {{ request()->routeIs('student.dashboard') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:translate-x-1' }}">
                    <i class="fas fa-home w-5 h-5 mr-3 transition-colors duration-200"></i>
                    <span>{{ __('Dashboard') }}</span>
                </x-nav-link>
            </div>

            <!-- Appointments Section -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 px-4">
                    {{ __('Appointments') }}
                </h3>
                <div class="space-y-1">
                    <x-nav-link :href="route('student.appointments.create')" :active="request()->routeIs('student.appointments.create*')"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-in-out {{ request()->routeIs('student.appointments.create*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:translate-x-1' }}">
                        <i class="fas fa-calendar-plus w-5 h-5 mr-3 transition-colors duration-200"></i>
                        <span>{{ __('Book Appointment') }}</span>
                    </x-nav-link>
                    
                    <x-nav-link :href="route('student.appointments')" :active="request()->routeIs('student.appointments') || request()->routeIs('student.appointments.index')"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-in-out {{ request()->routeIs('student.appointments') || request()->routeIs('student.appointments.index') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:translate-x-1' }}">
                        <i class="fas fa-calendar-alt w-5 h-5 mr-3 transition-colors duration-200"></i>
                        <span>{{ __('My Appointments') }}</span>
                        @php
                            $upcomingAppointments = 2; // This would come from your controller/model
                        @endphp
                        @if($upcomingAppointments > 0)
                            <span class="ml-auto bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-xs px-2 py-1 rounded-full">{{ $upcomingAppointments }}</span>
                        @endif
                    </x-nav-link>
                    
                    <x-nav-link :href="route('student.appointments.history')" :active="request()->routeIs('student.appointments.history*')"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-in-out {{ request()->routeIs('student.appointments.history*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:translate-x-1' }}">
                        <i class="fas fa-clock w-5 h-5 mr-3 transition-colors duration-200"></i>
                        <span>{{ __('Appointment History') }}</span>
                    </x-nav-link>
                </div>
            </div>

            <!-- Feedback Section -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 px-4">
                    {{ __('Feedback') }}
                </h3>
                <div class="space-y-1">
                    <x-nav-link :href="route('student.feedback')" :active="request()->routeIs('student.feedback*')"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-in-out {{ request()->routeIs('student.feedback*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:translate-x-1' }}">
                        <i class="fas fa-star w-5 h-5 mr-3 transition-colors duration-200"></i>
                        <span>{{ __('Rate Sessions') }}</span>
                    </x-nav-link>
                    
                    <x-nav-link :href="route('student.feedback.create')" :active="request()->routeIs('student.feedback.create*')"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-in-out {{ request()->routeIs('student.feedback.create*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:translate-x-1' }}">
                        <i class="fas fa-comment-alt w-5 h-5 mr-3 transition-colors duration-200"></i>
                        <span>{{ __('Send Feedback') }}</span>
                    </x-nav-link>
                </div>
            </div>

            <!-- Resources Section -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 px-4">
                    {{ __('Resources') }}
                </h3>
                <div class="space-y-1">
                    <x-nav-link :href="route('student.resources')" :active="request()->routeIs('student.resources*')"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-in-out {{ request()->routeIs('student.resources*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:translate-x-1' }}">
                        <i class="fas fa-book-open w-5 h-5 mr-3 transition-colors duration-200"></i>
                        <span>{{ __('Self-Help Resources') }}</span>
                    </x-nav-link>
                    
                    <x-nav-link :href="route('student.emergency')" :active="request()->routeIs('student.emergency*')"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-in-out {{ request()->routeIs('student.emergency*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:translate-x-1' }}">
                        <i class="fas fa-phone w-5 h-5 mr-3 transition-colors duration-200"></i>
                        <span>{{ __('Emergency Contacts') }}</span>
                    </x-nav-link>
                </div>
            </div>
        </nav>

        <!-- Quick Actions -->
        <div class="p-6 border-t border-gray-100 dark:border-gray-700">
            <div class="space-y-3">
                <x-primary-button class="w-full justify-center" onclick="location.href='{{ route('student.appointments.create') }}'">
                    <i class="fas fa-plus mr-2"></i>
                    {{ __('Book New Appointment') }}
                </x-primary-button>
                <x-secondary-button class="w-full justify-center">
                    <i class="fas fa-question-circle mr-2"></i>
                    {{ __('Need Help?') }}
                </x-secondary-button>
            </div>
        </div>
    </div>
</div>