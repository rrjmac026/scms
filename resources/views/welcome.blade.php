<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'SCMS') }}</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        
        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            
            @keyframes fadeInUp {
                0% { 
                    opacity: 0; 
                    transform: translateY(30px); 
                }
                100% { 
                    opacity: 1; 
                    transform: translateY(0); 
                }
            }
            
            @keyframes slideInLeft {
                0% { 
                    opacity: 0; 
                    transform: translateX(-30px); 
                }
                100% { 
                    opacity: 1; 
                    transform: translateX(0); 
                }
            }
            
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.8; }
            }
            
            @keyframes logoGlow {
                0%, 100% { 
                    box-shadow: 0 0 20px rgba(236, 72, 153, 0.3),
                                0 0 40px rgba(249, 115, 22, 0.2);
                }
                50% { 
                    box-shadow: 0 0 30px rgba(236, 72, 153, 0.5),
                                0 0 60px rgba(249, 115, 22, 0.3);
                }
            }
            
            @keyframes shimmer {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(100%); }
            }
            
            .float-animation {
                animation: float 3s ease-in-out infinite;
            }
            
            .fade-in-up {
                animation: fadeInUp 0.8s ease-out forwards;
            }
            
            .slide-in-left {
                animation: slideInLeft 0.8s ease-out forwards;
            }
            
            .pulse-animation {
                animation: pulse 2s ease-in-out infinite;
            }
            
            .logo-glow {
                animation: logoGlow 3s ease-in-out infinite;
            }
            
            .glassmorphism {
                background: rgba(255, 255, 255, 0.25);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.18);
            }
            
            .dark .glassmorphism {
                background: rgba(0, 0, 0, 0.25);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.18);
            }
            
            .gradient-text {
                background: linear-gradient(135deg, #ec4899, #f97316);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            .hero-bg {
                background: radial-gradient(ellipse at top, rgba(236, 72, 153, 0.1) 0%, transparent 50%),
                           radial-gradient(ellipse at bottom, rgba(249, 115, 22, 0.1) 0%, transparent 50%);
            }
            
            .dark .hero-bg {
                background: radial-gradient(ellipse at top, rgba(236, 72, 153, 0.2) 0%, transparent 50%),
                           radial-gradient(ellipse at bottom, rgba(249, 115, 22, 0.2) 0%, transparent 50%);
            }
            
            .logo-container {
                position: relative;
                overflow: hidden;
            }
            
            .logo-container::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
                animation: shimmer 3s ease-in-out infinite;
                animation-delay: 2s;
            }
            
            .logo-bg {
                background: linear-gradient(135deg, #ffffffff 0%, #ffffffff 100%);
                position: relative;
                overflow: hidden;
            }
            
            .logo-bg::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 50%, rgba(0,0,0,0.1) 100%);
                border-radius: inherit;
            }
            
            .btn-gradient {
                position: relative;
                overflow: hidden;
            }
            
            .btn-gradient::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s;
            }
            
            .btn-gradient:hover::before {
                left: 100%;
            }
        </style>
        
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
                        if (localStorage.getItem('darkMode') === 'true') {
                            document.documentElement.classList.add('dark');
                        }
                    }
                });
            });
        </script>
    </head>
    <body class="antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-300"
          x-data
          x-init="$store.darkMode.init()"
          :class="{ 'dark': $store.darkMode.on }">
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 glassmorphism shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center slide-in-left">
                        <div class="relative logo-container">
                            <!-- Enhanced logo container with actual logo -->
                            <div class="h-12 w-12 logo-bg rounded-2xl flex items-center justify-center shadow-xl transform hover:scale-110 transition-all duration-300 logo-glow">
                                <img src="assets/app_logo.PNG" 
                                     alt="SCMS Logo" 
                                     class="h-8 w-8 object-contain relative z-10" />
                            </div>
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full pulse-animation shadow-lg"></div>
                        </div>
                        <div class="ml-4">
                            <span class="text-2xl font-black text-gray-800 dark:text-gray-100 tracking-tight">SCMS</span>
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-medium tracking-wide">Student Counseling Management</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 fade-in-up">
                        <!-- Dark Mode Toggle -->
                        <button @click="$store.darkMode.toggle()" 
                                class="p-2 rounded-xl bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-200 hover:scale-110">
                            <i class="fas fa-moon dark:hidden text-lg"></i>
                            <i class="fas fa-sun hidden dark:inline text-lg"></i>
                        </button>
                        
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" 
                                   class="btn-gradient group relative px-6 py-2.5 bg-gradient-to-r from-pink-400 via-pink-500 to-orange-400 text-white rounded-xl font-semibold transition-all duration-300 hover:shadow-xl hover:shadow-pink-200 dark:hover:shadow-pink-900/50 transform hover:scale-105">
                                    <span class="relative z-10 flex items-center">
                                        <i class="fas fa-tachometer-alt mr-2"></i>
                                        Dashboard
                                    </span>
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="btn-gradient group relative px-6 py-2.5 bg-gradient-to-r from-pink-400 via-pink-500 to-orange-400 text-white rounded-xl font-semibold transition-all duration-300 hover:shadow-xl hover:shadow-pink-200 dark:hover:shadow-pink-900/50 transform hover:scale-105">
                                    <span class="relative z-10 flex items-center">
                                        <i class="fas fa-sign-in-alt mr-2"></i>
                                        Login
                                    </span>
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative min-h-screen flex items-center justify-center pt-16 hero-bg">
            <!-- Enhanced Floating Background Elements -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-20 left-10 w-20 h-20 bg-pink-300/20 rounded-full float-animation backdrop-blur-sm"></div>
                <div class="absolute top-40 right-20 w-32 h-32 bg-orange-300/20 rounded-full float-animation backdrop-blur-sm" style="animation-delay: 1s;"></div>
                <div class="absolute bottom-32 left-20 w-16 h-16 bg-pink-400/20 rounded-full float-animation backdrop-blur-sm" style="animation-delay: 2s;"></div>
                <div class="absolute bottom-20 right-32 w-24 h-24 bg-orange-400/20 rounded-full float-animation backdrop-blur-sm" style="animation-delay: 0.5s;"></div>
                
                <!-- Additional decorative elements -->
                <div class="absolute top-1/2 left-5 w-2 h-2 bg-pink-500 rounded-full opacity-60 float-animation" style="animation-delay: 3s;"></div>
                <div class="absolute top-1/3 right-10 w-3 h-3 bg-orange-500 rounded-full opacity-60 float-animation" style="animation-delay: 1.5s;"></div>
                <div class="absolute bottom-1/3 left-1/4 w-1 h-1 bg-pink-400 rounded-full opacity-80 float-animation" style="animation-delay: 2.5s;"></div>
            </div>
            
            <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <!-- Enhanced Logo Display in Hero -->
                    <div class="fade-in-up mb-8">
                        <div class="inline-flex items-center justify-center">
                            <div class="relative logo-container">
                                <div class="h-20 w-20 logo-bg rounded-3xl flex items-center justify-center shadow-2xl logo-glow">
                                    <img src="assets/app_logo.PNG" 
                                         alt="SCMS Logo" 
                                         class="h-14 w-14 object-contain relative z-10" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Main Heading -->
                    <div class="fade-in-up">
                        <h1 class="text-5xl md:text-7xl font-black tracking-tight text-gray-900 dark:text-white mb-6">
                            Welcome to 
                            <span class="gradient-text block mt-2">SCMS</span>
                        </h1>
                    </div>
                    
                    <!-- Enhanced Subtitle -->
                    <div class="fade-in-up" style="animation-delay: 0.2s;">
                        <p class="mt-6 text-xl leading-relaxed text-gray-600 dark:text-gray-300 max-w-3xl mx-auto font-medium">
                            Your comprehensive platform for student counseling management. Connect with professional counselors, schedule appointments, and get the mental health support you deserve.
                        </p>
                    </div>
                    
                    <!-- Enhanced Feature Pills -->
                    <div class="fade-in-up flex flex-wrap justify-center gap-3 mt-8" style="animation-delay: 0.4s;">
                        <span class="px-4 py-2 bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 rounded-full text-sm font-semibold border border-pink-200 dark:border-pink-800 hover:scale-105 transition-transform duration-200">
                            <i class="fas fa-calendar-check mr-2"></i>Easy Scheduling
                        </span>
                        <span class="px-4 py-2 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded-full text-sm font-semibold border border-orange-200 dark:border-orange-800 hover:scale-105 transition-transform duration-200">
                            <i class="fas fa-user-friends mr-2"></i>Professional Counselors
                        </span>
                        <span class="px-4 py-2 bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 rounded-full text-sm font-semibold border border-pink-200 dark:border-pink-800 hover:scale-105 transition-transform duration-200">
                            <i class="fas fa-shield-alt mr-2"></i>Secure & Private
                        </span>
                        <span class="px-4 py-2 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded-full text-sm font-semibold border border-orange-200 dark:border-orange-800 hover:scale-105 transition-transform duration-200">
                            <i class="fas fa-mobile-alt mr-2"></i>Mobile Friendly
                        </span>
                    </div>
                    
                    <!-- Enhanced CTA Buttons -->
                    <div class="fade-in-up mt-12 flex flex-col sm:flex-row items-center justify-center gap-4" style="animation-delay: 0.6s;">
                        <a href="#" 
                           class="btn-gradient group relative w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-pink-400 via-pink-500 to-orange-400 text-white rounded-2xl font-bold text-lg transition-all duration-300 hover:shadow-2xl hover:shadow-pink-200 dark:hover:shadow-pink-900/50 transform hover:scale-105">
                            <span class="relative z-10 flex items-center justify-center">
                                <i class="fas fa-rocket mr-3"></i>
                                Get Started Today
                            </span>
                        </a>
                        <a href="{{ route('login') }}" 
                           class="group w-full sm:w-auto px-8 py-4 text-gray-700 dark:text-gray-300 hover:text-pink-500 dark:hover:text-pink-400 transition-all duration-200 font-semibold text-lg flex items-center justify-center border-2 border-gray-200 dark:border-gray-700 rounded-2xl hover:border-pink-300 dark:hover:border-pink-600">
                            <i class="fas fa-play-circle mr-3 text-xl"></i>
                            Learn more 
                            <i class="fas fa-arrow-right ml-3 transform group-hover:translate-x-1 transition-transform duration-200"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <!-- Enhanced Features Preview Section -->
        <div class="py-20 bg-white dark:bg-gray-800 transition-colors duration-300">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Why Choose <span class="gradient-text">SCMS</span>?
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300">Experience counseling management like never before</p>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="group bg-gradient-to-br from-pink-50 to-orange-50 dark:from-pink-900/20 dark:to-orange-900/20 p-8 rounded-3xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-pink-100 dark:border-pink-800/30">
                        <div class="w-16 h-16 bg-gradient-to-br from-pink-400 to-pink-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <i class="fas fa-calendar-alt text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Smart Scheduling</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">Book appointments with ease using our intelligent scheduling system that finds the perfect time for you and your counselor.</p>
                    </div>
                    
                    <div class="group bg-gradient-to-br from-orange-50 to-pink-50 dark:from-orange-900/20 dark:to-pink-900/20 p-8 rounded-3xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-orange-100 dark:border-orange-800/30">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-pink-400 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <i class="fas fa-comments text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Secure Communication</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">Connect with counselors through our encrypted messaging system, ensuring your privacy and confidentiality at all times.</p>
                    </div>
                    
                    <div class="group bg-gradient-to-br from-pink-50 to-orange-50 dark:from-pink-900/20 dark:to-orange-900/20 p-8 rounded-3xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-pink-100 dark:border-pink-800/30">
                        <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-orange-400 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Progress Tracking</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">Monitor your mental health journey with detailed progress reports and personalized insights from your counseling sessions.</p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Enhanced JavaScript functionality
            document.addEventListener('DOMContentLoaded', function() {
                // Intersection Observer for animations
                const observerOptions = {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                };
                
                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('fade-in-up');
                        }
                    });
                }, observerOptions);
                
                // Observe feature cards
                document.querySelectorAll('.group').forEach(card => {
                    observer.observe(card);
                });
                
                // Enhanced logo interaction
                const logoContainers = document.querySelectorAll('.logo-container');
                logoContainers.forEach(container => {
                    container.addEventListener('mouseenter', function() {
                        this.querySelector('img').style.transform = 'scale(1.1) rotate(5deg)';
                    });
                    
                    container.addEventListener('mouseleave', function() {
                        this.querySelector('img').style.transform = 'scale(1) rotate(0deg)';
                    });
                });
            });
        </script>
    </body>
</html>