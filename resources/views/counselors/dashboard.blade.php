<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Counselor Dashboard') }}
            </h2>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome Banner -->
            <div class="overflow-hidden shadow-lg rounded-xl" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%); box-shadow: 0 10px 25px -5px rgba(255, 146, 194, 0.25);">
                <div class="relative p-8 text-white">
                    <div class="absolute top-0 right-0 w-32 h-32 -mt-16 -mr-16 bg-white opacity-5 rounded-full"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 -mb-12 -ml-12 bg-white opacity-5 rounded-full"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold mb-2">
                                Welcome back, Dr. {{ auth()->user()->name }}! 👨‍⚕️
                            </h1>
                            <p class="text-pink-100 text-lg">
                                Employee ID: {{ auth()->user()->counselor->employee_number ?? 'N/A' }}
                            </p>
                            <p class="text-pink-100">
                                Specialization: {{ auth()->user()->counselor->specialization ?? 'General Counseling' }}
                            </p>
                        </div>
                        <div class="hidden md:block">
                            <div class="w-24 h-24 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg border-l-4" style="border-left-color: #FF92C2;">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Appointments</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    {{ auth()->user()->counselor->appointments()->count() ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg border-l-4" style="border-left-color: #FF92C2;">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sessions Completed</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    {{ auth()->user()->counselor->counselingSessions()->where('status', 'completed')->count() ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg border-l-4" style="border-left-color: #FF92C2;">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Today's Appointment</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    {{ auth()->user()->counselor->appointments()
                                        ->whereDate('preferred_date', today())
                                        ->distinct('student_id')
                                        ->count() ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg border-l-4" style="border-left-color: #FF92C2;">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Students</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    {{ auth()->user()->counselor->counselingSessions()->distinct('student_id')->count() ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Quick Actions -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <a href="{{ route('counselor.appointments.index') }}" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-lg flex items-center justify-center transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    View Schedule
                                </a>
                                <a href="#" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-lg flex items-center justify-center transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                    </svg>
                                    Generate Reports
                                </a>
                                <a href="{{ route('profile.edit') }}" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-3 rounded-lg flex items-center justify-center transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                    </svg>
                                    Update Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Schedule & Recent Activity -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Today's Schedule -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Today's Schedule</h3>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('F j, Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Tools & Resources -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Professional Tools & Resources</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 rounded-lg" style="background: linear-gradient(135deg, rgba(255, 146, 194, 0.1) 0%, rgba(232, 121, 165, 0.1) 100%);">
                                    <h4 class="font-medium" style="color: #e879a5;">Assessment Tools</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Psychological assessment forms and questionnaires</p>
                                    <a href="#" class="text-pink-500 hover:text-pink-600 text-sm mt-2 inline-block">Access Tools →</a>
                                </div>
                                <div class="p-4 rounded-lg" style="background: linear-gradient(135deg, rgba(255, 146, 194, 0.1) 0%, rgba(232, 121, 165, 0.1) 100%);">
                                    <h4 class="font-medium" style="color: #e879a5;">Treatment Plans</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Create and manage treatment plans</p>
                                    <a href="#" class="text-pink-500 hover:text-pink-600 text-sm mt-2 inline-block">Create Plan →</a>
                                </div>
                                <div class="p-4 rounded-lg" style="background: linear-gradient(135deg, rgba(255, 146, 194, 0.1) 0%, rgba(232, 121, 165, 0.1) 100%);">
                                    <h4 class="font-medium" style="color: #e879a5;">Reference Materials</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Clinical guidelines and best practices</p>
                                    <a href="#" class="text-pink-500 hover:text-pink-600 text-sm mt-2 inline-block">Browse Library →</a>
                                </div>
                                <div class="p-4 rounded-lg" style="background: linear-gradient(135deg, rgba(255, 146, 194, 0.1) 0%, rgba(232, 121, 165, 0.1) 100%);">
                                    <h4 class="font-medium" style="color: #e879a5;">Continuing Education</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Professional development resources</p>
                                    <a href="#" class="text-pink-500 hover:text-pink-600 text-sm mt-2 inline-block">Learn More →</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Professional Notes -->
            @if(auth()->user()->counselor->specialization)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-l-4 border-blue-400 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            <strong>Specialization Focus:</strong> Your expertise in {{ auth()->user()->counselor->specialization }} 
                            helps provide targeted support to students with specific needs.
                        </p>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>