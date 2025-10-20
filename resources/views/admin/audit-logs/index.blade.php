<x-app-layout>
    <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="h-10 w-10 rounded-xl flex items-center justify-center"
                     style="background: linear-gradient(135deg,#FF92C2 0%,#f97316 100%); box-shadow: 0 8px 20px rgba(249,115,22,0.08);">
                    <i class="fas fa-shield-alt text-white"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">
                        <?php echo e(__('Audit Logs')); ?>
                    </h2>
                    <p class="font-medium text-gray-900 dark:text-gray-100">System activity and security events</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="#" class="inline-flex items-center px-3 py-2 rounded-lg bg-white/10 text-white/90 hover:bg-white/20 transition">
                    <i class="fas fa-filter mr-2 text-white/90"></i>Filters
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search -->
            <div class="mb-6 p-4 rounded-xl shadow-sm" style="background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));">
                <form class="flex gap-4" method="GET">
                    <div class="flex-1">
                        <input type="text" name="search" value="<?php echo e($search ?? ''); ?>"
                               class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white/80 dark:bg-gray-900/60 px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-200"
                               placeholder="Search by user, action, ip or description...">
                    </div>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-3 rounded-lg text-white shadow-sm hover:opacity-95 transition"
                        style="background: linear-gradient(90deg, #FF92C2 0%, #ff9ec9 50%, #FF92C2 100%);">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>
            </div>

            <!-- Logs Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="" style="background: linear-gradient(90deg, rgba(255,146,194,0.06), rgba(249,115,22,0.03));">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-pink-600 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-pink-600 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-pink-600 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-pink-600 uppercase tracking-wider">IP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-pink-600 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-pink-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($logs as $log)
                                    <tr class="hover:shadow-sm transition-shadow">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($log->user)
                                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $log->user->first_name }} {{ $log->user->last_name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->user->email }}</div>
                                            @else
                                                <div class="text-sm text-gray-500 dark:text-gray-400">System</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-pink-600">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-pink-50 text-pink-700 text-xs font-semibold">
                                                <i class="fas fa-exchange-alt mr-2 text-pink-500"></i>{{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ Str::limit($log->description, 80) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->ip_address ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->created_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.audit-logs.show', $log) }}" 
                                               class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-white shadow-sm hover:opacity-95 transition"
                                               style="background: linear-gradient(90deg, #FF92C2 0%, #ff9ec9 50%, #FF92C2 100%);">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">No audit logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 flex items-center justify-end">
                        <div class="bg-white dark:bg-gray-800 px-4 py-2 rounded-lg shadow-sm">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
