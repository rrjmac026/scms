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