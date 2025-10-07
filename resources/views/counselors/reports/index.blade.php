<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Generate Report') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Report Generation Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                    <div class="flex items-center">
                        <i class="fas fa-file-alt text-3xl mr-4"></i>
                        <div>
                            <h3 class="text-xl font-semibold">Configure Report Parameters</h3>
                            <p class="text-blue-100 text-sm mt-1">Select date range and counselor to generate detailed reports</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('counselor.reports.generate') }}" id="generateReportForm">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Start Date -->
                            <div>
                                <x-input-label for="start_date" value="{{ __('Start Date') }}" class="required" />
                                <x-text-input 
                                    type="date" 
                                    name="start_date" 
                                    id="start_date"
                                    value="{{ old('start_date', $filters['start_date']) }}" 
                                    class="w-full mt-1" 
                                    required 
                                />
                                @error('start_date')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div>
                                <x-input-label for="end_date" value="{{ __('End Date') }}" class="required" />
                                <x-text-input 
                                    type="date" 
                                    name="end_date" 
                                    id="end_date"
                                    value="{{ old('end_date', $filters['end_date']) }}" 
                                    class="w-full mt-1" 
                                    required 
                                />
                                @error('end_date')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Counselor Filter -->
                            <div>
                                <x-input-label for="counselor_id" value="{{ __('Counselor (Optional)') }}" />
                                <select 
                                    name="counselor_id" 
                                    id="counselor_id"
                                    class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Counselors</option>
                                    @foreach($counselors as $counselor)
                                        <option value="{{ $counselor->id }}" @selected(old('counselor_id', $filters['counselor_id']) == $counselor->id)>
                                            {{ $counselor->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3">
                            <button 
                                type="button"
                                onclick="exportReport('pdf')"
                                class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition"
                            >
                                <i class="fas fa-file-pdf mr-2"></i>
                                Export as PDF
                            </button>

                            <button 
                                type="button"
                                onclick="exportReport('excel')"
                                class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition"
                            >
                                <i class="fas fa-file-excel mr-2"></i>
                                Export as Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Information Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 text-2xl mr-4"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Report Includes</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Appointments, sessions, feedbacks, and statistics
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <i class="fas fa-filter text-green-500 text-2xl mr-4"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Flexible Filtering</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Filter by date range and specific counselors
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-purple-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <i class="fas fa-download text-purple-500 text-2xl mr-4"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Multiple Formats</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Export to PDF or Excel for easy sharing
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function exportReport(format) {
            // Validate dates
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (!startDate || !endDate) {
                alert('Please select both start and end dates.');
                return;
            }
            
            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date must be before or equal to end date.');
                return;
            }
            
            // Get counselor value
            const counselorId = document.getElementById('counselor_id').value;
            
            // Build query parameters
            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate
            });
            
            // Add counselor_id only if it has a value
            if (counselorId) {
                params.append('counselor_id', counselorId);
            }
            
            // Build export URL based on format
            const baseUrl = format === 'pdf' 
                ? '{{ route("counselor.reports.export.pdf") }}'
                : '{{ route("counselor.reports.export.excel") }}';
            
            const exportUrl = baseUrl + '?' + params.toString();
            
            console.log('Exporting to:', exportUrl); // Debug log
            
            // Trigger download
            window.location.href = exportUrl;
        }

        // Set max date to today for both date inputs
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_date').setAttribute('max', today);
            document.getElementById('end_date').setAttribute('max', today);
            
            // Update end date min when start date changes
            document.getElementById('start_date').addEventListener('change', function() {
                document.getElementById('end_date').setAttribute('min', this.value);
            });
        });
    </script>
</x-app-layout>