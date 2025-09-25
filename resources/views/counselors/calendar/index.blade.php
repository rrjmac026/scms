<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <div class="flex items-center justify-center w-12 h-12 rounded-2xl shadow-lg" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);">
                <i class="fas fa-calendar-check text-white text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white">
                    {{ __('Appointments Calendar') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage and view all appointment schedules</p>
            </div>
        </div>
    </x-slot>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
    
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Calendar Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                    <div class="absolute top-0 right-0 w-20 h-20 -mt-10 -mr-10 bg-white opacity-10 rounded-full"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium">Pending</p>
                                <p class="text-white text-2xl font-bold" id="pendingCount">{{ $appointments->where('extendedProps.status', 'pending')->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                    <div class="absolute top-0 right-0 w-20 h-20 -mt-10 -mr-10 bg-white opacity-10 rounded-full"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium">Approved</p>
                                <p class="text-white text-2xl font-bold" id="approvedCount">{{ $appointments->where('extendedProps.status', 'approved')->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-white text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="absolute top-0 right-0 w-20 h-20 -mt-10 -mr-10 bg-white opacity-10 rounded-full"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium">Completed</p>
                                <p class="text-white text-2xl font-bold" id="completedCount">{{ $appointments->where('extendedProps.status', 'completed')->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calendar-check text-white text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);">
                    <div class="absolute top-0 right-0 w-20 h-20 -mt-10 -mr-10 bg-white opacity-10 rounded-full"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm font-medium">Total</p>
                                <p class="text-white text-2xl font-bold" id="totalCount">{{ $appointments->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-white text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Container -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl p-6">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-1 h-6 rounded-full" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);"></div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Schedule Overview</h3>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2 text-sm">
                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                            <span class="text-gray-600 dark:text-gray-400">Pending</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            <span class="text-gray-600 dark:text-gray-400">Approved</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="text-gray-600 dark:text-gray-400">Completed</span>
                        </div>
                    </div>
                </div>
                <div id="calendar" class="rounded-xl overflow-hidden"></div>
            </div>
        </div>
    </div>
    
    <!-- Enhanced Modal for appointment details -->
    <div id="appointmentModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50 transition-opacity duration-300">
        <div class="relative top-20 mx-auto p-0 border-0 w-full max-w-lg">
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
                <!-- Modal Header -->
                <div class="relative p-6 text-white" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);">
                    <div class="absolute top-0 right-0 w-32 h-32 -mt-16 -mr-16 bg-white opacity-10 rounded-full"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 -mb-12 -ml-12 bg-white opacity-10 rounded-full"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-calendar-alt text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">Appointment Details</h3>
                                <p class="text-white/80 text-sm">View appointment information</p>
                            </div>
                        </div>
                        <button onclick="closeModal()" class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm hover:bg-white/30 transition-colors">
                            <i class="fas fa-times text-white"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="p-6">
                    <div id="modalContent" class="space-y-4"></div>
                    <div class="flex justify-end mt-6 space-x-3">
                        <button onclick="closeModal()" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-colors duration-200">
                            Close
                        </button>
                        <button id="viewDetailsBtn" class="px-6 py-3 text-white font-medium rounded-xl shadow-lg transform hover:scale-105 transition-all duration-200" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            View Full Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var appointments = @json($appointments);
            console.log('Appointments data:', appointments);

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                events: appointments,
                eventClick: function(info) {
                    showAppointmentDetails(info.event);
                },
                height: 650,
                eventDisplay: 'block',
                displayEventTime: true,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                },
                dayMaxEvents: 3,
                moreLinkClick: 'popover',
                eventDidMount: function(info) {
                    info.el.style.cursor = 'pointer';
                    info.el.style.transition = 'all 0.2s ease';

                    info.el.addEventListener('mouseenter', function() {
                        this.style.transform = 'scale(1.05)';
                        this.style.zIndex = '10';
                        this.style.boxShadow = '0 8px 25px -8px rgba(0,0,0,0.3)';
                    });

                    info.el.addEventListener('mouseleave', function() {
                        this.style.transform = 'scale(1)';
                        this.style.zIndex = '1';
                        this.style.boxShadow = 'none';
                    });
                },
                dayCellClassNames: function() {
                    return 'hover:bg-pink-50 dark:hover:bg-pink-900/10 transition-colors duration-200';
                }
            });

            calendar.render();
        });

        function showAppointmentDetails(event) {
            const props = event.extendedProps;
            const modalContent = document.getElementById('modalContent');

            modalContent.innerHTML = `
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%);">
                            <i class="fas fa-user-graduate text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Student</p>
                            <p class="font-semibold text-gray-900 dark:text-white">${props.student}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-info-circle text-purple-500"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full ${getStatusColor(props.status)}">
                                ${props.status.charAt(0).toUpperCase() + props.status.slice(1)}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Date</p>
                            <p class="font-semibold text-gray-900 dark:text-white">${event.start.toLocaleDateString()}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Time</p>
                            <p class="font-semibold text-gray-900 dark:text-white">${event.start.toLocaleTimeString()}</p>
                        </div>
                    </div>

                    ${props.description ? `
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Description</p>
                            <p class="text-gray-900 dark:text-white">${props.description}</p>
                        </div>
                    ` : ''}
                </div>
            `;

            // Set up view details button for counselor
            document.getElementById('viewDetailsBtn').onclick = function() {
                window.location.href = `/counselor/appointments/${event.id}`;
            };

            document.getElementById('appointmentModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('appointmentModal').classList.add('hidden');
        }

        function getStatusColor(status) {
            switch(status) {
                case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400';
                case 'approved': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400';
                case 'completed': return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
                default: return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('appointmentModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
        </script>


    <!-- Custom Calendar Styles -->
    <style>
        .fc-theme-standard .fc-scrollgrid {
            border-radius: 12px !important;
            overflow: hidden;
        }
        
        .fc-theme-standard td, .fc-theme-standard th {
            border-color: #e5e7eb !important;
        }
        
        .fc-col-header-cell {
            background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%) !important;
            color: white !important;
            font-weight: 600 !important;
            padding: 12px 8px !important;
        }
        
        .fc-daygrid-day:hover {
            background-color: rgba(255, 146, 194, 0.05) !important;
        }
        
        .fc-event {
            border-radius: 8px !important;
            border: none !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            padding: 2px 6px !important;
            margin: 1px !important;
        }
        
        .fc-button-primary {
            background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%) !important;
            border: none !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
        }
        
        .fc-button-primary:hover {
            background: linear-gradient(135deg, #e879a5 0%, #d1477a 100%) !important;
            transform: translateY(-1px);
        }
        
        .fc-today-button, .fc-prev-button, .fc-next-button {
            border-radius: 8px !important;
            font-weight: 500 !important;
        }
        
        .fc-toolbar-title {
            color: #1f2937 !important;
            font-weight: 700 !important;
        }
        
        .dark .fc-toolbar-title {
            color: #f9fafb !important;
        }
        
        .fc-daygrid-day-number {
            color: #374151 !important;
            font-weight: 500 !important;
            padding: 8px !important;
        }
        
        .dark .fc-daygrid-day-number {
            color: #d1d5db !important;
        }
        
        .fc-day-today {
            background-color: rgba(255, 146, 194, 0.1) !important;
        }
        
        .fc-day-today .fc-daygrid-day-number {
            background: linear-gradient(135deg, #FF92C2 0%, #e879a5 100%) !important;
            color: white !important;
            border-radius: 8px !important;
            width: 28px !important;
            height: 28px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    </style>
</x-app-layout>