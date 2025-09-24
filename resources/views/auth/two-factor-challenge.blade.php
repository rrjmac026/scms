<x-guest-layout>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #FF92C2 0%, #FFB3D1 50%, #FFC4E1 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 146, 194, 0.2);
        }
        .input-focus:focus {
            border-color: #FF92C2 !important;
            box-shadow: 0 0 0 3px rgba(255, 146, 194, 0.1) !important;
        }
        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .pink-gradient-btn {
            background: linear-gradient(135deg, #FF92C2 0%, #FF6BA8 100%);
            box-shadow: 0 4px 15px rgba(255, 146, 194, 0.3);
            transition: all 0.2s ease;
        }
        .pink-gradient-btn:hover {
            box-shadow: 0 6px 25px rgba(255, 146, 194, 0.4);
            transform: translateY(-1px);
        }
        .recovery-link:hover {
            color: #FF92C2 !important;
        }
    </style>



        <!-- Header Section -->
        <div class="text-center mb-8">
        </div>
            <!-- Instruction Text -->
            <div class="mb-8 text-center">
                <p class="text-gray-700 text-sm leading-relaxed">
                    {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
                </p>
            </div>

            <!-- 2FA Code Form -->
            <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="code" :value="__('Authentication Code')" class="text-gray-700 font-medium mb-3" />
                    
                    <div class="relative">
                        <x-text-input id="code"
                                     type="text"
                                     inputmode="numeric"
                                     name="code"
                                     maxlength="6"
                                     placeholder="000000"
                                     autofocus
                                     autocomplete="one-time-code"
                                     class="input-focus block w-full px-4 py-4 text-center text-lg font-mono tracking-widest border-gray-300 rounded-xl focus:border-pink-500 focus:ring-pink-500 transition-all duration-200 shadow-sm"
                                     :value="old('code')" />
                        
                        <!-- Input decoration -->
                        <div class="absolute inset-0 rounded-xl border-2 border-transparent bg-gradient-to-r from-pink-300 via-pink-200 to-pink-300 opacity-0 -z-10 transition-opacity duration-200 blur-sm" 
                             id="input-glow"></div>
                    </div>
                    
                    <x-input-error :messages="$errors->get('code')" class="mt-3 bg-red-50 px-3 py-2 rounded-lg border border-red-200" />
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="pink-gradient-btn w-full flex justify-center items-center py-4 px-6 border border-transparent text-base font-medium rounded-xl text-white transform active:scale-95 transition-all duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('Verify Code') }}
                    </button>
                </div>
            </form>


    <script>
        // Add focus glow effect
        const codeInput = document.getElementById('code');
        const inputGlow = document.getElementById('input-glow');
        
        codeInput.addEventListener('focus', () => {
            inputGlow.style.opacity = '0.3';
        });
        
        codeInput.addEventListener('blur', () => {
            inputGlow.style.opacity = '0';
        });

        // Auto-format the input as user types
        codeInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            if (value.length > 6) value = value.substring(0, 6);
            e.target.value = value;
        });
    </script>
</x-guest-layout>