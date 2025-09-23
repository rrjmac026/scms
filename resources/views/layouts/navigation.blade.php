<nav class="fixed top-0 left-0 right-0 z-50 border-b border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
        <div class="mx-auto px-4 sm:px-6">
            <div class="flex justify-between h-16">
                <!-- Left Side -->
                <div class="flex items-center gap-4">
                    <!-- Toggle Button -->
                    <button @click="$store.sidebar.toggle()" 
                        class="p-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas" :class="$store.sidebar.isOpen ? 'fa-bars-staggered' : 'fa-bars'"></i>
                    </button>

                    <!-- Brand -->
                    <div class="flex items-center gap-3">
                        <x-application-logo class="h-10 w-10" />
                        <span class="text-xl font-extrabold tracking-tight text-gray-800 dark:text-gray-200">
                            SCMS<span class="text-gray-600 dark:text-gray-400"> System</span>
                        </span>
                    </div>
                </div>

                <!-- Right Side - Profile Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                        class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200">
                        <div class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                {{ substr(Auth::user()->first_name, 0, 1) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ Auth::user()->first_name }}
                            </span>
                            <i class="fas fa-chevron-down text-xs opacity-75"></i>
                        </div>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg py-2 border bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700">
                        
                        <!-- Profile -->
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        
                        <!-- Dark Mode Toggle -->
                        <button @click="$store.darkMode.toggle()"
                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                            <template x-if="$store.darkMode.on">
                                <i class="fas fa-sun mr-2 text-amber-400"></i>
                            </template>
                            <template x-if="!$store.darkMode.on">
                                <i class="fas fa-moon mr-2"></i>
                            </template>
                            <span x-text="$store.darkMode.on ? 'Light Mode' : 'Dark Mode'"></span>
                        </button>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

