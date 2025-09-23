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

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('darkMode', {
                on: localStorage.getItem('darkMode') === 'true',
                toggle() {
                    this.on = !this.on;
                    localStorage.setItem('darkMode', this.on);
                    document.documentElement.classList.toggle('dark', this.on);
                },
                init() {
                    if (this.on) document.documentElement.classList.add('dark');
                }
            });

            Alpine.store('sidebar', {
                isOpen: window.innerWidth >= 1024,
                expanded: true, // Add this line
                toggle() {
                    this.isOpen = !this.isOpen;
                    localStorage.setItem('sidebarOpen', this.isOpen);
                },
                toggleExpand() { // Add this method
                    this.expanded = !this.expanded;
                    localStorage.setItem('sidebarExpanded', this.expanded);
                },
                init() {
                    const stored = localStorage.getItem('sidebarOpen');
                    this.isOpen = stored ? JSON.parse(stored) : window.innerWidth >= 1024;
                    this.expanded = localStorage.getItem('sidebarExpanded') !== 'false'; // Add this line
                    window.addEventListener('resize', () => {
                        if (window.innerWidth >= 1024) this.isOpen = true;
                    });
                }
            });
        });
    </script>

    <style>
        /* Box-sizing */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        /* Responsive font sizes */
        html { font-size: 16px; }
        @media (max-width: 768px) {
            html { font-size: 15px; }
        }
        @media (max-width: 640px) {
            html { font-size: 14px; }
        }

        /* Prevent horizontal overflow */
        body { overflow-x: hidden; max-width: 100vw; }
    </style>
</head>

<body class="font-sans antialiased" 
      x-data 
      x-init="$store.darkMode.init()" 
      :class="{ 'dark': $store.darkMode.on }">

    <div class="min-h-screen flex flex-col w-full bg-gray-100 dark:bg-gray-900">
        <!-- Navigation -->
        @include('layouts.navigation')

        <div class="flex flex-1 relative">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="flex-1 pt-16 transition-all duration-300 ease-in-out"
                 :class="{ 'lg:pl-72': $store.sidebar.isOpen, 'lg:pl-0': !$store.sidebar.isOpen }">

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
