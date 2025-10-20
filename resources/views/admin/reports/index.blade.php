<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Reports & Analytics') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.reports.generate', request()->all()) }}" 
                   class="inline-flex items-center px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-file-excel mr-2"></i>
                    Generate Report Page
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <form id="filterForm" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <x-input-label for="start_date" value="{{ __('Start Date') }}" />
                        <x-text-input type="date" name="start_date" value="{{ $filters['start_date'] }}" class="w-full" />
                    </div>
                    <div>
                        <x-input-label for="end_date" value="{{ __('End Date') }}" />
                        <x-text-input type="date" name="end_date" value="{{ $filters['end_date'] }}" class="w-full" />
                    </div>
                    <div>
                        <x-input-label for="counselor_id" value="{{ __('Counselor') }}" />
                        <select name="counselor_id" class="w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm">
                            <option value="">All Counselors</option>
                            @foreach($counselors as $counselor)
                                <option value="{{ $counselor->id }}" @selected($filters['counselor_id'] == $counselor->id)>
                                    {{ $counselor->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Appointments -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase mb-1">
                                    Total Appointments
                                </div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ number_format($analytics['kpis']['total_appointments']) }}
                                </div>
                                <small class="text-gray-500 dark:text-gray-400">
                                    <span class="text-green-600">{{ $analytics['kpis']['completed_appointments'] }} completed</span> | 
                                    <span class="text-red-600">{{ $analytics['kpis']['canceled_appointments'] }} canceled</span>
                                </small>
                            </div>
                            <div>
                                <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Sessions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-green-600 dark:text-green-400 uppercase mb-1">
                                    Total Sessions
                                </div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ number_format($analytics['kpis']['total_sessions']) }}
                                </div>
                                <small class="text-gray-500 dark:text-gray-400">
                                    Counseling sessions conducted
                                </small>
                            </div>
                            <div>
                                <i class="fas fa-comments fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students Counseled -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-cyan-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-cyan-600 dark:text-cyan-400 uppercase mb-1">
                                    Students Counseled
                                </div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ number_format($analytics['kpis']['total_students_counseled']) }}
                                </div>
                                <small class="text-gray-500 dark:text-gray-400">
                                    {{ $analytics['kpis']['active_counselors'] }} active counselors
                                </small>
                            </div>
                            <div>
                                <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Average Rating with Chart Line Icon -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-yellow-600 dark:text-yellow-400 uppercase mb-1">
                                    Average Rating
                                </div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ number_format($analytics['kpis']['average_feedback_rating'], 2) }}/5.0
                                </div>
                                <small class="text-gray-500 dark:text-gray-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-chart-line {{ $i <= round($analytics['kpis']['average_feedback_rating']) ? 'text-green-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </small>
                            </div>
                            <div>
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Sessions Per Month Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-blue-600 text-white">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-chart-bar mr-2"></i>Sessions Per Month
                        </h3>
                    </div>
                    <div class="p-6" style="height: 350px;">
                        <canvas id="sessionsPerMonthChart"></canvas>
                    </div>
                </div>

                <!-- Feedback Trends Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-yellow-600 text-white">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-chart-line mr-2"></i>Feedback Rating Trends
                        </h3>
                    </div>
                    <div class="p-6" style="height: 350px;">
                        <canvas id="feedbackTrendsChart"></canvas>
                    </div>
                </div>

                <!-- Counselor Workload Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-green-600 text-white">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-user-tie mr-2"></i>Counselor Workload
                        </h3>
                    </div>
                    <div class="p-6" style="height: 350px;">
                        <canvas id="counselorWorkloadChart"></canvas>
                    </div>
                </div>

                <!-- Category Distribution Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-purple-600 text-white">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-sitemap mr-2"></i>Category Distribution
                        </h3>
                    </div>
                    <div class="p-6" style="height: 350px;">
                        <canvas id="categoryDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    console.log('=== CHART DEBUG START ===');
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded');
        
        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is NOT loaded!');
            alert('Chart.js failed to load. Check your internet connection.');
            return;
        }
        console.log('✓ Chart.js is loaded:', Chart.version);

        // Auto-submit form on filter change
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            const inputs = filterForm.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    filterForm.submit();
                });
            });
        }

        // Chart data from backend
        const chartData = @json($analytics['charts']);
        console.log('Chart Data received:', chartData);

        // Check if data exists
        if (!chartData) {
            console.error('No chart data available!');
            return;
        }

        // Helper function to generate colors
        const generateColors = (count) => {
            const colors = [
                'rgba(59, 130, 246, 0.8)',   // blue
                'rgba(34, 197, 94, 0.8)',     // green
                'rgba(239, 68, 68, 0.8)',     // red
                'rgba(234, 179, 8, 0.8)',     // yellow
                'rgba(168, 85, 247, 0.8)',    // purple
                'rgba(251, 146, 60, 0.8)',    // orange
                'rgba(6, 182, 212, 0.8)',     // cyan
                'rgba(236, 72, 153, 0.8)',    // pink
            ];
            return colors.slice(0, count);
        };

        // Function to safely create charts
        const createChart = (canvasId, config) => {
            console.log(`Creating chart: ${canvasId}`);
            const canvas = document.getElementById(canvasId);
            
            if (!canvas) {
                console.error(`❌ Canvas element ${canvasId} not found!`);
                return null;
            }
            
            console.log(`✓ Canvas found: ${canvasId}`);
            
            try {
                const ctx = canvas.getContext('2d');
                const chart = new Chart(ctx, config);
                console.log(`✓ Chart created successfully: ${canvasId}`);
                return chart;
            } catch (error) {
                console.error(`❌ Error creating chart ${canvasId}:`, error);
                return null;
            }
        };

        // Sessions Per Month Chart (Bar)
        console.log('Creating Sessions Per Month Chart...');
        if (chartData.sessionsPerMonth && chartData.sessionsPerMonth.labels && chartData.sessionsPerMonth.data) {
            console.log('Data:', chartData.sessionsPerMonth);
            createChart('sessionsPerMonthChart', {
                type: 'bar',
                data: {
                    labels: chartData.sessionsPerMonth.labels,
                    datasets: [{
                        label: 'Sessions',
                        data: chartData.sessionsPerMonth.data,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        } else {
            console.warn('❌ Sessions Per Month data missing or invalid');
        }

        // Appointments by Status Chart (Pie)
        console.log('Creating Appointments by Status Chart...');
        if (chartData.appointmentsByStatus && chartData.appointmentsByStatus.labels && chartData.appointmentsByStatus.data) {
            console.log('Data:', chartData.appointmentsByStatus);
            createChart('appointmentStatusChart', {
                type: 'pie',
                data: {
                    labels: chartData.appointmentsByStatus.labels,
                    datasets: [{
                        data: chartData.appointmentsByStatus.data,
                        backgroundColor: generateColors(chartData.appointmentsByStatus.labels.length),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        } else {
            console.warn('❌ Appointments by Status data missing or invalid');
        }

        // Feedback Trends Chart (Line)
        console.log('Creating Feedback Trends Chart...');
        if (chartData.feedbackTrends && chartData.feedbackTrends.labels && chartData.feedbackTrends.data) {
            console.log('Data:', chartData.feedbackTrends);
            createChart('feedbackTrendsChart', {
                type: 'line',
                data: {
                    labels: chartData.feedbackTrends.labels,
                    datasets: [{
                        label: 'Average Rating',
                        data: chartData.feedbackTrends.data,
                        backgroundColor: 'rgba(234, 179, 8, 0.2)',
                        borderColor: 'rgba(234, 179, 8, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        } else {
            console.warn('❌ Feedback Trends data missing or invalid');
        }

        // Top Offenses Chart (Doughnut)
        console.log('Creating Top Offenses Chart...');
        if (chartData.topOffenses && chartData.topOffenses.labels && chartData.topOffenses.data) {
            console.log('Data:', chartData.topOffenses);
            createChart('topOffensesChart', {
                type: 'doughnut',
                data: {
                    labels: chartData.topOffenses.labels,
                    datasets: [{
                        data: chartData.topOffenses.data,
                        backgroundColor: generateColors(chartData.topOffenses.labels.length),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        } else {
            console.warn('❌ Top Offenses data missing or invalid');
        }

        // Counselor Workload Chart (Horizontal Bar)
        console.log('Creating Counselor Workload Chart...');
        if (chartData.counselorWorkload && chartData.counselorWorkload.labels && chartData.counselorWorkload.data) {
            console.log('Data:', chartData.counselorWorkload);
            createChart('counselorWorkloadChart', {
                type: 'bar',
                data: {
                    labels: chartData.counselorWorkload.labels,
                    datasets: [{
                        label: 'Sessions',
                        data: chartData.counselorWorkload.data,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        } else {
            console.warn('❌ Counselor Workload data missing or invalid');
        }

        // Category Distribution Chart (Bar)
        console.log('Creating Category Distribution Chart...');
        if (chartData.categoryDistribution && chartData.categoryDistribution.labels && chartData.categoryDistribution.data) {
            console.log('Data:', chartData.categoryDistribution);
            createChart('categoryDistributionChart', {
                type: 'bar',
                data: {
                    labels: chartData.categoryDistribution.labels,
                    datasets: [{
                        label: 'Appointments',
                        data: chartData.categoryDistribution.data,
                        backgroundColor: 'rgba(168, 85, 247, 0.8)',
                        borderColor: 'rgba(168, 85, 247, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        } else {
            console.warn('❌ Category Distribution data missing or invalid');
        }

        console.log('=== CHART DEBUG END ===');
    });
</script>
</x-app-layout>