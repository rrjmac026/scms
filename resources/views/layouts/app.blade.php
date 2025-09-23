<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('darkMode', {
                    on: false,
                    toggle() { this.on = !this.on }
                })

                Alpine.store('sidebar', {
                    isOpen: window.innerWidth >= 1024,
                    toggle() { 
                        this.isOpen = !this.isOpen;
                        // Optionally persist the state
                        localStorage.setItem('sidebarOpen', this.isOpen);
                    },
                    init() {
                        // Initialize from localStorage or default based on screen width
                        const stored = localStorage.getItem('sidebarOpen');
                        this.isOpen = stored ? JSON.parse(stored) : window.innerWidth >= 1024;
                        
                        // Update isOpen state when window is resized
                        window.addEventListener('resize', () => {
                            if (window.innerWidth >= 1024) {
                                this.isOpen = true;
                            }
                        });
                    }
                })
            })
        </script>

        <style>
            [x-cloak] { display: none !important; }

            /* Ensure proper box-sizing */
            *, *::before, *::after {
                box-sizing: border-box;
            }

            /* Prevent horizontal overflow */
            html, body {
                overflow-x: hidden;
                max-width: 100vw;
            }

            /* Responsive font sizes */
            @media (max-width: 640px) {
                html { font-size: 14px; }
            }

            @media (min-width: 641px) and (max-width: 768px) {
                html { font-size: 15px; }
            }

            @media (min-width: 769px) {
                html { font-size: 16px; }
            }
        </style>
    </head>
    <body class="font-sans antialiased"
          x-data
          :class="{ 'dark': $store.darkMode.on }">
        <div class="min-h-screen flex flex-col w-full bg-gray-100 dark:bg-gray-900">
            <!-- Navigation -->
            @include('layouts.navigation')

            <!-- Page Wrapper -->
            <div class="flex flex-1 relative">
                <!-- Sidebar -->
                @include('layouts.sidebar')

                <!-- Main Content -->
                <div class="flex-1 pt-16 transition-all duration-300 ease-in-out"
                     :class="{ 
                         'lg:pl-72': $store.sidebar.isOpen,
                         'lg:pl-0': !$store.sidebar.isOpen
                     }">
                    <!-- Page Heading -->
                    @isset($header)
                        <header class="bg-white dark:bg-gray-800 shadow-sm">
                            <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <main class="py-6 px-4 sm:px-6 lg:px-8">
                        @hasSection('content')
                            @yield('content')
                        @else
                            {{ $slot ?? '' }}
                        @endif
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
