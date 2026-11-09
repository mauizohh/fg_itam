<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Exceptions</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Investigate discrepancies such as missing equipment, wrong user or wrong location.</p>
    </div>

    <!-- 4 Metric Cards matching UI 23 -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">OPEN EXCEPTIONS</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1"><?php echo e($openExceptions); ?></strong>
            <small class="block text-xs text-gray-400 mt-0.5">Across audits</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-red-200 shadow-sm bg-red-50/20">
            <span class="block text-[11px] font-bold text-red-600 uppercase tracking-wider font-heading">MISSING</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#b91c1c] mt-1"><?php echo e($missingCount); ?></strong>
            <small class="block text-xs text-red-500 font-semibold mt-0.5">High priority</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-amber-200 shadow-sm bg-amber-50/20">
            <span class="block text-[11px] font-bold text-amber-700 uppercase tracking-wider font-heading">WRONG LOCATION</span>
            <strong class="block text-2xl font-heading font-extrabold text-amber-700 mt-1"><?php echo e($wrongLocationCount); ?></strong>
            <small class="block text-xs text-amber-600 font-semibold mt-0.5">Reconcile</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">DAMAGED</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1"><?php echo e($damagedCount); ?></strong>
            <small class="block text-xs text-gray-400 mt-0.5">Maintenance follow-up</small>
        </div>
    </div>

    <!-- Table: Open Exceptions matching UI 23 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Open Exceptions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Exception</th>
                        <th>Expected</th>
                        <th>Observed</th>
                        <th>Audit</th>
                        <th>Owner</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $exceptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('assets.detail', $ex->asset_id)); ?>" class="font-bold text-[#a92d31] hover:underline font-heading">
                                <?php echo e($ex->asset_id); ?>

                            </a>
                        </td>
                        <td>
                            <span class="<?php echo e($ex->pill_class); ?>"><?php echo e($ex->exception_type); ?></span>
                        </td>
                        <td><?php echo e($ex->expected_info); ?></td>
                        <td class="font-semibold text-gray-700"><?php echo e($ex->observed_info); ?></td>
                        <td class="font-mono text-xs text-gray-500"><?php echo e($ex->audit_id); ?></td>
                        <td><?php echo e($ex->owner_department); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/audits/exceptions.blade.php ENDPATH**/ ?>