{{-- layouts/sidebar-student.blade.php --}}
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
                <h1 class="text-xl font-bold text-gray-900 dark:text-white truncate">Student Portal</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Your Counseling Hub</p>
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
                <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')"
                    class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('student.dashboard') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                    style="{{ request()->routeIs('student.dashboard') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('student.dashboard') ? 'bg-white/20' : 'bg-pink-50 dark:bg-pink-900/20 group-hover:bg-pink-100 dark:group-hover:bg-pink-900/30' }}">
                        <i class="fas fa-home text-lg {{ request()->routeIs('student.dashboard') ? 'text-white' : 'text-pink-500' }}" style="{{ request()->routeIs('student.dashboard') ? '' : 'color: #FF92C2;' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Dashboard') }}</span>
                    @if(request()->routeIs('student.dashboard'))
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
                <x-nav-link :href="route('student.appointments.index')" :active="request()->routeIs('student.appointments.index')"
                    class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('student.appointments.index') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                    style="{{ request()->routeIs('student.appointments.index') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('student.appointments.index') ? 'bg-white/20' : 'bg-pink-50 dark:bg-pink-900/20 group-hover:bg-pink-100 dark:group-hover:bg-pink-900/30' }}">
                        <i class="fas fa-calendar-alt text-lg {{ request()->routeIs('student.appointments.index') ? 'text-white' : 'text-pink-500' }}" style="{{ request()->routeIs('student.appointments.index') ? '' : 'color: #FF92C2;' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('My Appointments') }}</span>
                </x-nav-link>
                
                <x-nav-link :href="route('student.appointments.create')" :active="request()->routeIs('student.appointments.create')"
                    class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('student.appointments.create') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                    style="{{ request()->routeIs('student.appointments.create') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('student.appointments.create') ? 'bg-white/20' : 'bg-pink-50 dark:bg-pink-900/20 group-hover:bg-pink-100 dark:group-hover:bg-pink-900/30' }}">
                        <i class="fas fa-calendar-plus text-lg {{ request()->routeIs('student.appointments.create') ? 'text-white' : 'text-pink-500' }}" style="{{ request()->routeIs('student.appointments.create') ? '' : 'color: #FF92C2;' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Book Appointment') }}</span>
                    @if(request()->routeIs('student.appointments.create'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>
            </div>
        </div>

        <!-- Counseling History Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-2 px-3 mb-4">
                <div class="w-1 h-4 bg-gradient-to-b from-pink-400 to-pink-500 rounded-full"
                    style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);"></div>
                <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('History') }}
                </h2>
            </div>

            <div class="space-y-1">
                <x-nav-link :href="route('student.counseling-history.index')" 
                            :active="request()->routeIs('student.counseling-history.index')"
                            class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('student.counseling-history') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                            style="{{ request()->routeIs('student.counseling-history') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('student.counseling-history') ? 'bg-white/20' : 'bg-pink-50 dark:bg-pink-900/20 group-hover:bg-pink-100 dark:group-hover:bg-pink-900/30' }}">
                        <i class="fas fa-history text-lg {{ request()->routeIs('student.counseling-history') ? 'text-white' : 'text-pink-500' }}" style="{{ request()->routeIs('student.counseling-history') ? '' : 'color: #FF92C2;' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Counseling History') }}</span>
                    @if(request()->routeIs('student.counseling-history'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>
            </div>
        </div>

        <!-- Feedback Section -->
        <div class="mb-8">
    <div class="flex items-center space-x-2 px-3 mb-4">
        <div class="w-1 h-4 bg-gradient-to-b from-pink-400 to-pink-500 rounded-full" 
             style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);"></div>
        <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            {{ __('Feedback') }}
        </h2>
    </div>
    
        <div class="space-y-1">
            <x-nav-link :href="route('student.feedback.index')" 
                :active="request()->routeIs('student.feedback.*')"
                class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('student.feedback.*') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                style="{{ request()->routeIs('student.feedback.*') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                
                <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg 
                    {{ request()->routeIs('student.feedback.*') ? 'bg-white/20' : 'bg-pink-50 dark:bg-pink-900/20 group-hover:bg-pink-100 dark:group-hover:bg-pink-900/30' }}">
                    <i class="fas fa-comment-dots text-lg 
                        {{ request()->routeIs('student.feedback.*') ? 'text-white' : 'text-pink-500' }}" 
                        style="{{ request()->routeIs('student.feedback.*') ? '' : 'color: #FF92C2;' }}"></i>
                </div>

                <span class="flex-1">{{ __('My Feedback') }}</span>
                
                @if(request()->routeIs('student.feedback.*'))
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
                        <i class="fas fa-user-graduate text-white text-lg"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white font-semibold text-sm truncate">Student Portal</p>
                    <p class="text-white/75 text-xs">Active Session</p>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>
</nav>