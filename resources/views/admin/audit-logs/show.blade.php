<x-app-layout>
    <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-lg flex items-center justify-center"
                     style="background: linear-gradient(135deg,#FF92C2 0%,#f97316 100%); box-shadow: 0 10px 30px rgba(249,115,22,0.06);">
                    <i class="fas fa-clipboard-list text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-white leading-tight"><?php echo e(__('Audit Log Details')); ?></h2>
                    <p class="text-xs text-white/75">Detailed event record</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('admin.audit-logs.index')); ?>" class="inline-flex items-center px-3 py-2 rounded-lg bg-white/10 text-white hover:bg-white/20 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md sm:rounded-2xl p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <div class="flex items-start gap-4">
                            <div class="h-16 w-16 rounded-xl flex items-center justify-center"
                                 style="background: linear-gradient(135deg,#FF92C2 0%,#f97316 100%); box-shadow: 0 8px 20px rgba(249,115,22,0.06);">
                                <i class="fas fa-user-shield text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    <?php if($auditLog->user): ?>
                                        <?php echo e($auditLog->user->first_name); ?> <?php echo e($auditLog->user->last_name); ?>
                                    <?php else: ?>
                                        System
                                    <?php endif; ?>
                                </h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <?php echo e($auditLog->user->email ?? 'system@local'); ?>
                                </div>
                                <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-pink-50 text-pink-700">
                                    <i class="fas fa-exchange-alt mr-2"></i> <?php echo e($auditLog->action); ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-300">Description</h4>
                            <p class="mt-2 text-gray-900 dark:text-gray-100 whitespace-pre-wrap"><?php echo e($auditLog->description ?? '—'); ?></p>
                        </div>
                    </div>

                    <div>
                        <div class="p-4 rounded-lg border border-gray-100 dark:border-gray-700 bg-gradient-to-b from-white/50 to-white/30 dark:from-gray-800/60 dark:to-gray-800/40">
                            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-300">Metadata</h4>
                            <div class="mt-3 space-y-3 text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">IP</span>
                                    <span class="font-medium"><?php echo e($auditLog->ip_address ?? '—'); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">User Agent</span>
                                    <span class="font-medium break-words"><?php echo e($auditLog->user_agent ?? '—'); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Timestamp</span>
                                    <span class="font-medium"><?php echo e($auditLog->created_at->toDayDateTimeString()); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
