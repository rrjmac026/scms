{{-- layouts/sidebar-admin.blade.php --}}
<nav class="flex flex-col h-full bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div class="px-6 py-8">
        <div class="flex items-center space-x-4">
            <div class="relative">
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
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white truncate">Admin Panel</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->name }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="flex-1 px-4 pb-6 overflow-y-auto">
        <!-- Overview Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-2 px-3 mb-4">
                <div class="w-1 h-4 bg-gradient-to-b from-pink-400 to-pink-500 rounded-full" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);"></div>
                <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Overview') }}
                </h2>
            </div>
            
            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                class="group relative flex items-center w-full px-4 py-3 mb-1 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('admin.dashboard') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                style="{{ request()->routeIs('admin.dashboard') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : 'bg-pink-50 dark:bg-pink-900/20 group-hover:bg-pink-100 dark:group-hover:bg-pink-900/30' }}">
                    <i class="fas fa-chart-pie text-lg {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-pink-500' }}" style="{{ request()->routeIs('admin.dashboard') ? '' : 'color: #FF92C2;' }}"></i>
                </div>
                <span class="flex-1">{{ __('Dashboard') }}</span>
                @if(request()->routeIs('admin.dashboard'))
                    <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                @else
                    <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                @endif
            </x-nav-link>
        </div>

        <!-- Management Section -->
        <div class="mb-8">
            <div class="flex items-center space-x-2 px-3 mb-4">
                <div class="w-1 h-4 bg-gradient-to-b from-pink-400 to-pink-500 rounded-full" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);"></div>
                <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Management') }}
                </h2>
            </div>
            
            <div class="space-y-1">
                <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')"
                    class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('admin.users.*') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                    style="{{ request()->routeIs('admin.users.*') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-white/20' : 'bg-blue-50 dark:bg-blue-900/20 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30' }}">
                        <i class="fas fa-users text-lg {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-blue-500' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Manage Users') }}</span>
                    @if(request()->routeIs('admin.users.*'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>

                <x-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.*')"
                    class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('admin.students.*') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                    style="{{ request()->routeIs('admin.students.*') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('admin.students.*') ? 'bg-white/20' : 'bg-emerald-50 dark:bg-emerald-900/20 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/30' }}">
                        <i class="fas fa-user-graduate text-lg {{ request()->routeIs('admin.students.*') ? 'text-white' : 'text-emerald-500' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Students') }}</span>
                    @if(request()->routeIs('admin.students.*'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>

                <x-nav-link :href="route('admin.counselors.index')" :active="request()->routeIs('admin.counselors.*')"
                    class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('admin.counselors.*') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                    style="{{ request()->routeIs('admin.counselors.*') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('admin.counselors.*') ? 'bg-white/20' : 'bg-amber-50 dark:bg-amber-900/20 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/30' }}">
                        <i class="fas fa-user-tie text-lg {{ request()->routeIs('admin.counselors.*') ? 'text-white' : 'text-amber-500' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Counselors') }}</span>
                    @if(request()->routeIs('admin.counselors.*'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>

                {{-- ✅ Fixed Calendar View - More specific route check --}}
                <x-nav-link :href="route('admin.appointments.calendar')" :active="request()->routeIs('admin.appointments.calendar')"
                    class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('admin.appointments.calendar') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                    style="{{ request()->routeIs('admin.appointments.calendar') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('admin.appointments.calendar') ? 'bg-white/20' : 'bg-indigo-50 dark:bg-indigo-900/20 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/30' }}">
                        <i class="fas fa-calendar-check text-lg {{ request()->routeIs('admin.appointments.calendar') ? 'text-white' : 'text-indigo-500' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Calendar View') }}</span>
                    @if(request()->routeIs('admin.appointments.calendar'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>

                {{-- ✅ Fixed Appointments - Exclude calendar route --}}
                <x-nav-link :href="route('admin.appointments.index')" 
                    :active="request()->routeIs('admin.appointments.*') && !request()->routeIs('admin.appointments.calendar')"
                    class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ (request()->routeIs('admin.appointments.*') && !request()->routeIs('admin.appointments.calendar')) ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                    style="{{ (request()->routeIs('admin.appointments.*') && !request()->routeIs('admin.appointments.calendar')) ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ (request()->routeIs('admin.appointments.*') && !request()->routeIs('admin.appointments.calendar')) ? 'bg-white/20' : 'bg-rose-50 dark:bg-rose-900/20 group-hover:bg-rose-100 dark:group-hover:bg-rose-900/30' }}">
                        <i class="fas fa-calendar-alt text-lg {{ (request()->routeIs('admin.appointments.*') && !request()->routeIs('admin.appointments.calendar')) ? 'text-white' : 'text-rose-500' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Appointments') }}</span>
                    @if(request()->routeIs('admin.appointments.*') && !request()->routeIs('admin.appointments.calendar'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>

                <x-nav-link :href="route('admin.feedback.index')" :active="request()->routeIs('admin.feedback.*')"
                    class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('admin.feedback.*') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                    style="{{ request()->routeIs('admin.feedback.*') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('admin.feedback.*') ? 'bg-white/20' : 'bg-purple-50 dark:bg-purple-900/20 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/30' }}">
                        <i class="fas fa-comment-dots text-lg {{ request()->routeIs('admin.feedback.*') ? 'text-white' : 'text-purple-500' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Feedback') }}</span>
                    @if(request()->routeIs('admin.feedback.*'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>


                <x-nav-link :href="route('admin.counseling-categories.index')" :active="request()->routeIs('admin.counseling-categories.*')"
                    class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('admin.counseling-categories.*') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                    style="{{ request()->routeIs('admin.counseling-categories.*') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('admin.counseling-categories.*') ? 'bg-white/20' : 'bg-teal-50 dark:bg-teal-900/20 group-hover:bg-teal-100 dark:group-hover:bg-teal-900/30' }}">
                        <i class="fas fa-tags text-lg {{ request()->routeIs('admin.counseling-categories.*') ? 'text-white' : 'text-teal-500' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Counseling Categories') }}</span>
                    @if(request()->routeIs('admin.counseling-categories.*'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>

                <x-nav-link :href="route('admin.counseling-sessions.index')" 
                        :active="request()->routeIs('admin.counseling-sessions.*')"
                        class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('admin.counseling-sessions.*') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                        style="{{ request()->routeIs('admin.counseling-sessions.*') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('admin.counseling-sessions.*') ? 'bg-white/20' : 'bg-pink-50 dark:bg-pink-900/20 group-hover:bg-pink-100 dark:group-hover:bg-pink-900/30' }}">
                        <i class="fas fa-comments text-lg {{ request()->routeIs('admin.counseling-sessions.*') ? 'text-white' : 'text-pink-500' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Counseling Sessions') }}</span>
                    @if(request()->routeIs('admin.counseling-sessions.*'))
                        <div class="w-2 h-2 bg-white rounded-full opacity-75"></div>
                    @else
                        <i class="fas fa-chevron-right text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                    @endif
                </x-nav-link>

                <!-- Add Reports Link Here -->
                <x-nav-link :href="route('admin.reports.index')" 
                        :active="request()->routeIs('admin.reports.*')"
                        class="group relative flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 ease-out {{ request()->routeIs('admin.reports.*') ? 'text-white shadow-lg transform translate-x-1' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:translate-x-1' }}"
                        style="{{ request()->routeIs('admin.reports.*') ? 'background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);' : '' }}">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-white/20' : 'bg-cyan-50 dark:bg-cyan-900/20 group-hover:bg-cyan-100 dark:group-hover:bg-cyan-900/30' }}">
                        <i class="fas fa-chart-bar text-lg {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-cyan-500' }}"></i>
                    </div>
                    <span class="flex-1">{{ __('Reports & Analytics') }}</span>
                    @if(request()->routeIs('admin.reports.*'))
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
                        <i class="fas fa-crown text-white text-lg"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white font-semibold text-sm truncate">Administrator</p>
                    <p class="text-white/75 text-xs">Full system access</p>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>
</nav>