{{-- layouts/sidebar-counselor.blade.php --}}
<nav class="flex flex-col h-full bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div class="px-6 py-8">
        <div class="flex items-center space-x-4">
            <div class="relative">
                    <img 
                        src="{{ auth()->user()->profile_photo_url }}" 
                        alt="{{ auth()->user()->name }}" 
                        class="w-12 h-12 rounded-2xl object-cover shadow-lg cursor-pointer"
                        onclick="document.getElementById('profilePhotoInput').click();"
                    />

                    <form id="profilePhotoForm" action="{{ route('profile.update-photo') }}" method="POST" enctype="multipart/form-data" class="hidden">
                        @csrf
                        <input type="file" name="profile_photo" id="profilePhotoInput" accept="image/*"
                            onchange="document.getElementById('profilePhotoForm').submit();">
                    </form>
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-gray-900 rounded-full"></div>
                </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white truncate">Counselor Portal</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->counselor->user->name ?? auth()->user()->name }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="flex-1 px-4 pb-6 overflow-y-auto">
        <!-- Dashboard Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-2 px-3 mb-4">
                <div class="w-1 h-4 bg-gradient-to-b from-pink-400 to-pink-500 rounded-full" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);"></div>
                <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Overview') }}
                </h2>
            </div>
            
            <div class="space-y-1">
                <x-nav-link :href="route('counselor.dashboard')" :active="request()->routeIs('counselor.dashboard')"
                    class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('counselor.dashboard') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                    style="{{ request()->routeIs('counselor.dashboard') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('counselor.dashboard') ? 'bg-white/20' : 'bg-pink-50 dark:bg-pink-900/20 group-hover:bg-pink-100 dark:group-hover:bg-pink-900/30' }}">
                        <i class="fas fa-home text-lg {{ request()->routeIs('counselor.dashboard') ? 'text-white' : 'text-pink-500' }}" style="{{ request()->routeIs('counselor.dashboard') ? '' : 'color: #FF92C2;' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Dashboard') }}</span>
                    @if(request()->routeIs('counselor.dashboard'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>
            </div>
        </div>

        <!-- Appointments Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-2 px-3 mb-4">
                <div class="w-1 h-4 bg-gradient-to-b from-pink-400 to-pink-500 rounded-full" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);"></div>
                <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Appointments') }}
                </h2>
            </div>
            
            <div class="space-y-1">
                <x-nav-link :href="route('counselor.appointments.index')" :active="request()->routeIs('counselor.appointments.*')"
                    class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('counselor.appointments.*') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                    style="{{ request()->routeIs('counselor.appointments.*') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('counselor.appointments.*') ? 'bg-white/20' : 'bg-pink-50 dark:bg-pink-900/20 group-hover:bg-pink-100 dark:group-hover:bg-pink-900/30' }}">
                        <i class="fas fa-calendar-check text-lg {{ request()->routeIs('counselor.appointments.*') ? 'text-white' : 'text-pink-500' }}" style="{{ request()->routeIs('counselor.appointments.*') ? '' : 'color: #FF92C2;' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Appointments') }}</span>
                    @if(request()->routeIs('counselor.appointments.*'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>
            </div>
        </div>

        <!-- Counselor Calendar Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-2 px-3 mb-4">
                <div class="w-1 h-4 bg-gradient-to-b from-pink-400 to-pink-500 rounded-full" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);"></div>
                <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Calendar') }}
                </h2>
            </div>

            <div class="space-y-1">
                <x-nav-link :href="route('counselor.calendar')" :active="request()->routeIs('counselor.calendar')"
                    class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('counselor.calendar') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                    style="{{ request()->routeIs('counselor.calendar') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('counselor.calendar') ? 'bg-white/20' : 'bg-pink-50 dark:bg-pink-900/20 group-hover:bg-pink-100 dark:group-hover:bg-pink-900/30' }}">
                        <i class="fas fa-calendar-alt text-lg {{ request()->routeIs('counselor.calendar') ? 'text-white' : 'text-pink-500' }}" style="{{ request()->routeIs('counselor.calendar') ? '' : 'color: #FF92C2;' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Calendar') }}</span>
                    @if(request()->routeIs('counselor.calendar'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>
            </div>
        </div>


        <!-- Offenses Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-2 px-3 mb-4">
                <div class="w-1 h-4 bg-gradient-to-b from-pink-400 to-pink-500 rounded-full" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);"></div>
                <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Offenses') }}
                </h2>
            </div>
        </div>

        <!-- Sessions Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-2 px-3 mb-4">
                <div class="w-1 h-4 bg-gradient-to-b from-pink-400 to-pink-500 rounded-full"
                    style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);"></div>
                <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Counseling Sessions') }}
                </h2>
            </div>

            <div class="space-y-1">
                <x-nav-link :href="route('counselor.counseling-sessions.index')" 
                            :active="request()->routeIs('counselor.sessions')"
                            class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('counselor.sessions') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                            style="{{ request()->routeIs('counselor.sessions') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('counselor.sessions') ? 'bg-white/20' : 'bg-pink-50 dark:bg-pink-900/20 group-hover:bg-pink-100 dark:group-hover:bg-pink-900/30' }}">
                        <i class="fas fa-comments text-lg {{ request()->routeIs('counselor.sessions') ? 'text-white' : 'text-pink-500' }}" style="{{ request()->routeIs('counselor.sessions') ? '' : 'color: #FF92C2;' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Counseling Sessions') }}</span>
                    @if(request()->routeIs('counselor.sessions'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-2 px-3 mb-4">
                <div class="w-1 h-4 bg-gradient-to-b from-pink-400 to-pink-500 rounded-full"
                    style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);"></div>
                <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Categories') }}
                </h2>
            </div>

            <div class="space-y-1">
                <x-nav-link :href="route('counselor.counseling-categories.index')" 
                            :active="request()->routeIs('counselor.counseling-categories.*')"
                            class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('counselor.counseling-categories.*') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                            style="{{ request()->routeIs('counselor.counseling-categories.*') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('counselor.counseling-categories.*') ? 'bg-white/20' : 'bg-pink-50 dark:bg-pink-900/20 group-hover:bg-pink-100 dark:group-hover:bg-pink-900/30' }}">
                        <i class="fas fa-tags text-lg {{ request()->routeIs('counselor.counseling-categories.*') ? 'text-white' : 'text-pink-500' }}" style="{{ request()->routeIs('counselor.counseling-categories.*') ? '' : 'color: #FF92C2;' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Counseling Categories') }}</span>
                    @if(request()->routeIs('counselor.counseling-categories.*'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>
            </div>
        </div>
    </div>

    <!-- Footer Status Card -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="relative overflow-hidden rounded-2xl p-4 shadow-lg" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);">
            <div class="absolute top-0 right-0 w-32 h-32 -mt-16 -mr-16 bg-white opacity-5 rounded-full"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 -mb-12 -ml-12 bg-white opacity-5 rounded-full"></div>
            <div class="relative flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-user-tie text-white text-lg"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white font-semibold text-sm truncate">Counselor Portal</p>
                    <p class="text-white/75 text-xs">Active Session</p>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>
</nav>