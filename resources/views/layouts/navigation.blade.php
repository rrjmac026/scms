<nav class="fixed top-0 left-0 right-0 z-50 border-b border-pink-200/50 dark:border-pink-800/50 shadow-lg bg-gradient-to-r from-pink-500 via-pink-400 to-pink-500 dark:from-pink-900 dark:via-pink-800 dark:to-pink-900"
     style="background: linear-gradient(90deg, #FF92C2 0%, #ff9ec9 50%, #FF92C2 100%);"
     x-data="{ isScrolled: false }"
     @scroll.window="isScrolled = (window.pageYOffset > 10)"
     :class="{ 'backdrop-blur-md bg-pink-400/90 dark:bg-pink-900/90': isScrolled }">
    <div class="mx-auto px-4 sm:px-6">
        <div class="flex justify-between h-16">
            <!-- Left Side -->
            <div class="flex items-center gap-4">
                <!-- Toggle Button -->
                <button @click="$store.sidebar.toggle()" 
                    class="p-3 rounded-xl bg-gradient-to-br from-pink-400 to-pink-500 hover:from-pink-500 hover:to-pink-600 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 group"
                    style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);">
                    <i class="fas text-white transition-transform duration-200 group-hover:rotate-12" :class="$store.sidebar.isOpen ? 'fa-bars-staggered' : 'fa-bars'"></i>
                </button>

                <!-- Brand -->
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-gradient-to-br from-pink-400 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300" 
                         style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);">
                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <div>
                        <span class="text-2xl font-black tracking-tight text-gray-800 dark:text-gray-200">
                            SCMS
                        </span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Student Counseling Management</p>
                    </div>
                </div>
            </div>

            <!-- Right Side - Profile Menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                    class="flex items-center gap-4 px-4 py-2 rounded-xl transition-all duration-300 bg-white/10 hover:bg-white/20 group">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300">
                        <span class="text-sm font-bold text-white">
                            {{ substr(Auth::user()->first_name, 0, 1) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-pink-600 transition-colors duration-300">
                                {{ Auth::user()->first_name }}
                            </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Administrator</p>
                        </div>
                        <i class="fas fa-chevron-down text-sm opacity-75 group-hover:text-pink-500 transform group-hover:rotate-180 transition-all duration-300" style="color: #FF92C2;"></i>
                    </div>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-3 w-64 rounded-2xl shadow-2xl py-3 border bg-white dark:bg-gray-800 border-pink-200 dark:border-pink-700/50 backdrop-blur-sm"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 246, 250, 0.95) 100%);">
                    
                    <!-- Profile Header -->
                    <div class="px-5 py-3 border-b border-pink-100 dark:border-pink-800/30">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-pink-400 to-pink-500 flex items-center justify-center shadow-md" 
                                 style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);">
                                <span class="text-xs font-bold text-white">
                                    {{ substr(Auth::user()->first_name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                                <p class="text-xs text-pink-500">System Administrator</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Menu Items -->
                    <div class="py-2">
                        <!-- Profile -->
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center px-5 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-pink-50 hover:to-pink-100 dark:hover:from-pink-900/20 dark:hover:to-pink-800/20 hover:text-pink-600 transition-all duration-200 group">
                            <div class="flex items-center justify-center w-8 h-8 rounded-xl bg-pink-100 dark:bg-pink-900/30 mr-3 group-hover:bg-pink-200 dark:group-hover:bg-pink-800/50 transition-colors duration-200">
                                <i class="fas fa-user text-pink-500 text-sm"></i>
                            </div>
                            <span>My Profile</span>
                            <i class="fas fa-arrow-right text-xs opacity-0 group-hover:opacity-100 ml-auto transform translate-x-[-5px] group-hover:translate-x-0 transition-all duration-200"></i>
                        </a>
                        
                        <!-- Dark Mode Toggle -->
                        <button @click="$store.darkMode.toggle()"
                                class="flex items-center w-full px-5 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-pink-50 hover:to-pink-100 dark:hover:from-pink-900/20 dark:hover:to-pink-800/20 hover:text-pink-600 transition-all duration-200 group">
                            <div class="flex items-center justify-center w-8 h-8 rounded-xl bg-pink-100 dark:bg-pink-900/30 mr-3 group-hover:bg-pink-200 dark:group-hover:bg-pink-800/50 transition-colors duration-200">
                                <template x-if="$store.darkMode.on">
                                    <i class="fas fa-sun text-amber-400 text-sm"></i>
                                </template>
                                <template x-if="!$store.darkMode.on">
                                    <i class="fas fa-moon text-pink-500 text-sm"></i>
                                </template>
                            </div>
                            <span x-text="$store.darkMode.on ? 'Light Mode' : 'Dark Mode'"></span>
                            <i class="fas fa-arrow-right text-xs opacity-0 group-hover:opacity-100 ml-auto transform translate-x-[-5px] group-hover:translate-x-0 transition-all duration-200"></i>
                        </button>

                        <!-- Divider -->
                        <div class="mx-5 my-2 border-t border-pink-100 dark:border-pink-800/30"></div>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center w-full px-5 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-red-50 hover:to-pink-50 dark:hover:from-red-900/20 dark:hover:to-pink-900/20 hover:text-red-600 transition-all duration-200 group">
                                <div class="flex items-center justify-center w-8 h-8 rounded-xl bg-red-100 dark:bg-red-900/30 mr-3 group-hover:bg-red-200 dark:group-hover:bg-red-800/50 transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt text-red-500 text-sm"></i>
                                </div>
                                <span>Sign Out</span>
                                <i class="fas fa-arrow-right text-xs opacity-0 group-hover:opacity-100 ml-auto transform translate-x-[-5px] group-hover:translate-x-0 transition-all duration-200"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- Updated Main Sidebar Container --}}
<div>
    <div class="h-full">
        <!-- Backdrop for mobile -->
        <div x-show="!$store.sidebar.isOpen && window.innerWidth < 1024" 
             x-cloak
             @click="$store.sidebar.toggle()"
             class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden">
        </div>

        <!-- Sidebar with improved transitions -->
        <aside x-show="$store.sidebar.isOpen" 
               x-cloak
               :class="{
                   'fixed': window.innerWidth < 1024,
                   'absolute lg:fixed': window.innerWidth >= 1024
               }"
               class="top-16 left-0 h-[calc(100vh-4rem)] w-72 max-w-[85vw] sm:max-w-72 bg-white dark:bg-gray-800 border-r border-pink-200 dark:border-pink-700/30 shadow-xl z-40 transform transition-transform duration-300 ease-in-out"
               x-transition:enter="transform transition-transform duration-300 ease-in-out"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transform transition-transform duration-300 ease-in-out"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full">
            
            <!-- Sidebar Content with improved scrolling -->
            <div class="flex flex-col h-full">
                <div class="flex-1 overflow-y-auto overflow-x-hidden">
                    @if(auth()->user()->role === 'student')
                        @include('layouts.sidebar-student')
                    @elseif(auth()->user()->role === 'counselor')
                        @include('layouts.sidebar-counselor')
                    @elseif(auth()->user()->role === 'admin')
                        @include('layouts.sidebar-admin')
                    @endif
                </div>
            </div>
        </aside>

        <!-- Mobile sidebar toggle button -->
        <button @click="$store.sidebar.toggle()"
                class="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-lg bg-white dark:bg-gray-800 shadow-md hover:bg-pink-50 dark:hover:bg-pink-900/20 hover:text-pink-500 transition-all duration-200 border border-pink-200 dark:border-pink-700/30 group">
            <i class="fas transition-colors duration-200 group-hover:text-pink-500" :class="$store.sidebar.isOpen ? 'fa-times' : 'fa-bars'" style="color: #FF92C2;"></i>
        </button>
    </div>
</div>