<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Reports & Analytics') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.reports.export.pdf', request()->all()) }}" 
                   class="inline-flex items-center px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Export PDF
                </a>
                <a href="{{ route('admin.reports.export.excel', request()->all()) }}" 
                   class="inline-flex items-center px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export Excel
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
                    <div>
                        <x-input-label for="category" value="{{ __('Category') }}"/>
                        <select name="category" class="w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" @selected($filters['category'] == $category)>
                                    {{ $category }}
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

                <!-- Average Rating -->
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
                                        <i class="fas fa-star {{ $i <= round($analytics['kpis']['average_feedback_rating']) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </small>
                            </div>
                            <div>
                                <i class="fas fa-star fa-2x text-gray-300"></i>
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
                    <div class="p-6">
                        <canvas id="sessionsPerMonthChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Appointments by Status Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-cyan-600 text-white">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-chart-pie mr-2"></i>Appointments by Status
                        </h3>
                    </div>
                    <div class="p-6">
                        <canvas id="appointmentStatusChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Feedback Trends Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-yellow-600 text-white">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-chart-line mr-2"></i>Feedback Rating Trends
                        </h3>
                    </div>
                    <div class="p-6">
                        <canvas id="feedbackTrendsChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Top Offenses Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-red-600 text-white">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Top Offenses/Issues
                        </h3>
                    </div>
                    <div class="p-6">
                        <canvas id="topOffensesChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Counselor Workload Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-green-600 text-white">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-user-tie mr-2"></i>Counselor Workload
                        </h3>
                    </div>
                    <div class="p-6">
                        <canvas id="counselorWorkloadChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Category Distribution Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-purple-600 text-white">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-sitemap mr-2"></i>Category Distribution
                        </h3>
                    </div>
                    <div class="p-6">
                        <canvas id="categoryDistributionChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Auto-submit form on filter change
        document.querySelectorAll('#filterForm select, #filterForm input').forEach(el => {
            el.addEventListener('change', () => document.getElementById('filterForm').submit());
        });

        // Chart.js initialization
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($analytics['charts']);

            // Sessions Per Month Chart
            if (chartData.sessionsPerMonth) {
                new Chart(document.getElementById('sessionsPerMonthChart'), {
                    type: 'bar',
                    data: {
                        labels: chartData.sessionsPerMonth.labels,
                        datasets: [{
                            label: 'Sessions',
                            data: chartData.sessionsPerMonth.data,
                            backgroundColor: 'rgba(59, 130, 246, 0.6)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            // Appointments Status Chart
            if (chartData.appointmentsByStatus) {
                new Chart(document.getElementById('appointmentStatusChart'), {
                    type: 'pie',
                    data: {
                        labels: chartData.appointmentsByStatus.labels,
                        datasets: [{
                            data: chartData.appointmentsByStatus.data,
                            backgroundColor: [
                                'rgba(34, 197, 94, 0.6)',
                                'rgba(234, 179, 8, 0.6)',
                                'rgba(239, 68, 68, 0.6)',
                                'rgba(156, 163, 175, 0.6)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // Feedback Trends Chart
            if (chartData.feedbackTrends) {
                new Chart(document.getElementById('feedbackTrendsChart'), {
                    type: 'line',
                    data: {
                        labels: chartData.feedbackTrends.labels,
                        datasets: [{
                            label: 'Average Rating',
                            data: chartData.feedbackTrends.data,
                            borderColor: 'rgba(234, 179, 8, 1)',
                            backgroundColor: 'rgba(234, 179, 8, 0.2)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { 
                                beginAtZero: true,
                                max: 5
                            }
                        }
                    }
                });
            }

            // Top Offenses Chart
            if (chartData.topOffenses) {
                new Chart(document.getElementById('topOffensesChart'), {
                    type: 'doughnut',
                    data: {
                        labels: chartData.topOffenses.labels,
                        datasets: [{
                            data: chartData.topOffenses.data,
                            backgroundColor: [
                                'rgba(239, 68, 68, 0.6)',
                                'rgba(59, 130, 246, 0.6)',
                                'rgba(234, 179, 8, 0.6)',
                                'rgba(34, 197, 94, 0.6)',
                                'rgba(168, 85, 247, 0.6)',
                                'rgba(251, 146, 60, 0.6)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // Counselor Workload Chart
            if (chartData.counselorWorkload) {
                new Chart(document.getElementById('counselorWorkloadChart'), {
                    type: 'bar',
                    data: {
                        labels: chartData.counselorWorkload.labels,
                        datasets: [{
                            label: 'Sessions Handled',
                            data: chartData.counselorWorkload.data,
                            backgroundColor: 'rgba(34, 197, 94, 0.6)',
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        scales: {
                            x: { beginAtZero: true }
                        }
                    }
                });
            }

            // Category Distribution Chart
            if (chartData.categoryDistribution) {
                new Chart(document.getElementById('categoryDistributionChart'), {
                    type: 'bar',
                    data: {
                        labels: chartData.categoryDistribution.labels,
                        datasets: [{
                            label: 'Sessions',
                            data: chartData.categoryDistribution.data,
                            backgroundColor: 'rgba(168, 85, 247, 0.6)',
                            borderColor: 'rgba(168, 85, 247, 1)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>