<x-guest-layout>
    <div>
        <!-- Floating background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-20 h-20 bg-pink-300/10 rounded-full animate-pulse"></div>
            <div class="absolute top-40 right-20 w-32 h-32 bg-orange-300/10 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-32 left-20 w-16 h-16 bg-pink-400/10 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-20 right-32 w-24 h-24 bg-orange-400/10 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
        </div>

        <div class="max-w-md w-full space-y-8 relative">
                <!-- Session Status -->
                <x-auth-session-status class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl text-green-700 dark:text-green-300 font-medium" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div class="group">
                        <x-input-label for="email" :value="__('Email')" class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 block" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                <i class="fas fa-envelope text-gray-400 group-focus-within:text-pink-500 transition-all duration-300"></i>
                            </div>
                            <x-text-input id="email" 
                                class="block w-full pl-12 pr-4 py-4 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border-2 border-gray-200/80 dark:border-gray-600/80 rounded-2xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-pink-200/50 dark:focus:ring-pink-800/50 focus:border-pink-400 dark:focus:border-pink-400 transition-all duration-300 font-medium shadow-lg hover:shadow-xl hover:border-pink-300 dark:hover:border-pink-500 hover:bg-white dark:hover:bg-gray-800 transform hover:scale-[1.01]" 
                                type="email" 
                                name="email" 
                                :value="old('email')" 
                                required 
                                autofocus 
                                autocomplete="username" 
                                placeholder="Enter your email address" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-500 dark:text-red-400 font-medium" />
                    </div>

                    <!-- Password -->
                    <div class="group">
                        <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 block" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                <i class="fas fa-lock text-gray-400 group-focus-within:text-pink-500 transition-all duration-300"></i>
                            </div>
                            <x-text-input id="password" 
                                class="block w-full pl-12 pr-12 py-4 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border-2 border-gray-200/80 dark:border-gray-600/80 rounded-2xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-pink-200/50 dark:focus:ring-pink-800/50 focus:border-pink-400 dark:focus:border-pink-400 transition-all duration-300 font-medium shadow-lg hover:shadow-xl hover:border-pink-300 dark:hover:border-pink-500 hover:bg-white dark:hover:bg-gray-800 transform hover:scale-[1.01]"
                                type="password"
                                name="password"
                                required 
                                autocomplete="current-password" 
                                placeholder="Enter your password" />
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center z-10 text-gray-400 hover:text-pink-500 transition-colors duration-300">
                                <i id="eyeIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-500 dark:text-red-400 font-medium" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="flex items-center group cursor-pointer">
                            <input id="remember_me" 
                                type="checkbox" 
                                class="w-5 h-5 rounded-lg border-2 border-gray-300 dark:border-gray-600 text-pink-500 bg-gray-50 dark:bg-gray-700 focus:ring-4 focus:ring-pink-200 dark:focus:ring-pink-800 focus:ring-offset-0 transition-all duration-200" 
                                name="remember">
                            <span class="ml-3 text-sm text-gray-600 dark:text-gray-400 font-medium group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors duration-200">
                                {{ __('Remember me') }}
                            </span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-pink-500 hover:text-pink-600 dark:text-pink-400 dark:hover:text-pink-300 font-semibold transition-colors duration-200 hover:underline" 
                               href="{{ route('password.request') }}">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <x-primary-button class="group relative w-full py-5 px-8 bg-gradient-to-r from-pink-500 via-pink-600 to-orange-500 hover:from-pink-600 hover:via-pink-700 hover:to-orange-600 active:from-pink-700 active:via-pink-800 active:to-orange-700 text-white font-bold text-lg rounded-2xl shadow-2xl shadow-pink-300/40 dark:shadow-pink-900/60 hover:shadow-2xl hover:shadow-pink-400/50 dark:hover:shadow-pink-900/80 transform hover:scale-[1.03] active:scale-[0.98] transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-pink-300/50 dark:focus:ring-pink-800/50 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <span class="flex items-center justify-center relative z-10">
                                <i class="fas fa-sign-in-alt mr-3 group-hover:translate-x-1 transition-transform duration-300"></i>
                                {{ __('Sign In to Dashboard') }}
                            </span>
                        </x-primary-button>
                    </div>
                </form>
        </div>
    </div>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        /* Custom focus styles */
        input:focus {
            transform: translateY(-2px) scale(1.01);
        }
        
        /* Smooth transitions for all interactive elements */
        * {
            transition-property: transform, box-shadow, background-color, border-color;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Enhanced input field animations */
        input[type="email"]:focus,
        input[type="password"]:focus {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04), 0 0 0 4px rgba(244, 114, 182, 0.1);
        }
    </style>

    <script>
        // Password toggle functionality
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
</x-guest-layout>