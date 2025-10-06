<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Session Details') }}
            </h2>
            <div class="flex gap-2">
                <x-secondary-button onclick="history.back()">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </x-secondary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Timer Section - Full Width on Mobile -->
                <div class="lg:col-span-2 order-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl mb-6">
                        <div class="p-8" 
                            x-data="sessionTimer(
                                {{ $counselingSession->status === 'ongoing' ? 'true' : 'false' }}, 
                                '{{ $counselingSession->started_at && $counselingSession->status === 'ongoing' ? $counselingSession->started_at->toISOString() : '' }}'
                            )"
                            x-init="initTimer()"
                        >
                            <!-- Timer Header -->
                            <div class="text-center mb-8">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                    Session Timer
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Track your counseling session time
                                </p>
                            </div>

                            <!-- Timer Display -->
                            <div class="relative mb-8">
                                <!-- Animated Background Circle -->
                                <div class="mx-auto w-64 h-64 relative">
                                    <!-- Background Circle -->
                                    <div class="absolute inset-0 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30"></div>
                                    
                                    <!-- Animated Ring -->
                                    <div class="absolute inset-4 rounded-full border-4 border-transparent"
                                         :class="isRunning ? 'border-t-blue-500 border-r-blue-400 border-b-purple-500 border-l-purple-400 animate-spin' : 'border-gray-300 dark:border-gray-600'"
                                         style="animation-duration: 3s;">
                                    </div>
                                    
                                    <!-- Timer Content -->
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <!-- Time Display -->
                                        <div class="text-4xl font-mono font-bold text-gray-900 dark:text-gray-100 mb-2"
                                             :class="isRunning ? 'animate-pulse' : ''"
                                             x-text="formatTime()">
                                            00:00:00
                                        </div>
                                        
                                        <!-- Status Indicator -->
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full"
                                                 :class="isRunning ? 'bg-green-500 animate-pulse' : 'bg-gray-400'">
                                            </div>
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                                  x-text="statusText()">
                                                Ready to Start
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Session Controls -->
                            <div class="flex justify-center gap-4 mb-6">
                                @if($counselingSession->status === 'pending')
                                    <form action="{{ route('counselor.counseling-sessions.update', $counselingSession) }}" 
                                          method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="ongoing">
                                        <button type="submit" 
                                                class="group relative px-8 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 flex items-center gap-3">
                                            <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-emerald-700 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                            <i class="fas fa-play relative z-10"></i>
                                            <span class="relative z-10">Start Session</span>
                                        </button>
                                    </form>
                                @elseif($counselingSession->status === 'ongoing')
                                    <form action="{{ route('counselor.counseling-sessions.update', $counselingSession) }}" 
                                          method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" 
                                                class="group relative px-8 py-4 bg-gradient-to-r from-red-500 to-rose-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 flex items-center gap-3">
                                            <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-rose-700 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                            <i class="fas fa-stop relative z-10"></i>
                                            <span class="relative z-10">End Session</span>
                                        </button>
                                    </form>
                                @else
                                    <div class="px-8 py-4 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-semibold rounded-xl flex items-center gap-3">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        Session Completed
                                    </div>
                                @endif
                            </div>

                            <!-- Session Statistics -->
                            <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="hours">0</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Hours</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400" x-text="minutes">0</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Minutes</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" x-text="seconds">0</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Seconds</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Session Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl">
                        <div class="p-8">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                                <i class="fas fa-clipboard-list text-blue-600"></i>
                                Session Information
                            </h3>

                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</dt>
                                    <dd>
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                                            {{ $counselingSession->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                               ($counselingSession->status === 'ongoing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 
                                               'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400') }}">
                                            <i class="fas fa-{{ $counselingSession->status === 'pending' ? 'clock' : ($counselingSession->status === 'ongoing' ? 'play' : 'check') }} mr-1"></i>
                                            {{ ucfirst($counselingSession->status) }}
                                        </span>
                                    </dd>
                                </div>

                                @if($counselingSession->started_at)
                                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Started At</dt>
                                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            <i class="fas fa-calendar-alt text-green-600 mr-2"></i>
                                            {{ $counselingSession->started_at->format('F d, Y g:i A') }}
                                        </dd>
                                    </div>
                                @endif

                                @if($counselingSession->ended_at)
                                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Ended At</dt>
                                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            <i class="fas fa-calendar-check text-red-600 mr-2"></i>
                                            {{ $counselingSession->ended_at->format('F d, Y g:i A') }}
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Duration</dt>
                                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            <i class="fas fa-hourglass-half text-blue-600 mr-2"></i>
                                            @if($counselingSession->duration !== null)
                                                @if($counselingSession->duration > 0)
                                                    {{ $counselingSession->formatted_duration }}
                                                @else
                                                    {{ $counselingSession->started_at ? $counselingSession->started_at->diffInSeconds($counselingSession->ended_at) : 0 }} seconds
                                                @endif
                                            @else
                                                Not completed yet
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                                <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Category</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                                        <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border-l-4 border-blue-500">
                                            {{ $counselingSession->category ? $counselingSession->category->name : 'N/A' }}
                                        </div>
                                    </dd>
                                </div>

                                <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Primary Concern</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                                        <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border-l-4 border-blue-500">
                                            {{ $counselingSession->concern }}
                                        </div>
                                    </dd>
                                </div>

                                <div 
                                    x-data="{ editing: false, note: @js($counselingSession->notes ?? '') }"
                                    class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl"
                                >
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex justify-between items-center">
                                        <span>Session Notes</span>

                                        <!-- Edit Button -->
                                        <button 
                                            @click="editing = true" 
                                            x-show="!editing"
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm font-medium"
                                        >
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </button>
                                    </dt>

                                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                                        <!-- View Mode -->
                                        <div 
                                            x-show="!editing" 
                                            @click="editing = true"
                                            class="bg-white dark:bg-gray-800 p-4 rounded-lg min-h-[100px] border-l-4 border-purple-500 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                        >
                                            <div class="whitespace-pre-wrap" x-text="note || 'Click here to add notes...'"></div>
                                        </div>

                                        <!-- Edit Mode -->
                                        <form 
                                            x-show="editing" 
                                            method="POST" 
                                            action="{{ route('counselor.counseling-sessions.update', $counselingSession) }}"
                                            class="bg-white dark:bg-gray-800 p-4 rounded-lg border-l-4 border-purple-500"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <textarea 
                                                name="notes"
                                                x-model="note"
                                                class="w-full p-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring focus:ring-purple-400 dark:bg-gray-900 dark:text-gray-100"
                                                rows="5"
                                            ></textarea>

                                            <input type="hidden" name="status" value="{{ $counselingSession->status }}">

                                            <div class="mt-3 flex gap-2">
                                                <x-primary-button>
                                                    <i class="fas fa-save mr-2"></i> Save
                                                </x-primary-button>

                                                <x-secondary-button type="button" @click="editing = false">
                                                    Cancel
                                                </x-secondary-button>
                                            </div>
                                        </form>
                                    </dd>
                                </div>

                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Student Information -->
                <div class="order-2 lg:order-3">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl sticky top-6">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                                <i class="fas fa-user-graduate text-pink-600"></i>
                                Student Information
                            </h3>
                            
                            <div class="text-center mb-6">
                                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center shadow-lg">
                                    <i class="fas fa-user-graduate text-2xl text-white"></i>
                                </div>
                                <div class="font-bold text-lg text-gray-900 dark:text-gray-100">
                                    {{ $counselingSession->student->user->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $counselingSession->student->student_number }}
                                </div>
                            </div>

                            <!-- Student Quick Info -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Email</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $counselingSession->student->user->email }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Session Type</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        Individual Counseling
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function sessionTimer(initialRunning = false, startedAtISO = '') {
            return {
                isRunning: initialRunning,
                sessionStartedAt: startedAtISO,
                elapsedTime: 0,
                timerInterval: null,
                hours: '00',
                minutes: '00',
                seconds: '00',

                initTimer() {
                    if (this.sessionStartedAt && this.isRunning) {
                        const start = new Date(this.sessionStartedAt);
                        const now = new Date();
                        this.elapsedTime = now - start;
                        this.startTimer();
                    }
                },

                startTimer() {
                    this.updateDisplay();
                    this.timerInterval = setInterval(() => {
                        this.elapsedTime += 1000;
                        this.updateDisplay();
                    }, 1000);
                },

                updateDisplay() {
                    const totalSeconds = Math.floor(this.elapsedTime / 1000);
                    this.hours = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
                    this.minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
                    this.seconds = String(totalSeconds % 60).padStart(2, '0');
                },

                formatTime() {
                    return `${this.hours}:${this.minutes}:${this.seconds}`;
                },

                statusText() {
                    if (!this.isRunning) return 'Ready to Start';
                    return this.elapsedTime > 0 ? 'Session in Progress' : 'Starting...';
                },

                destroy() {
                    if (this.timerInterval) {
                        clearInterval(this.timerInterval);
                    }
                }
            };
        }
    </script>
</x-app-layout>