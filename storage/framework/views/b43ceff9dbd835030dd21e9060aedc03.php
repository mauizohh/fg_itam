<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Employee Detail</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Show all equipment assigned to an employee and support onboarding or offboarding clearance.</p>
        </div>
        <a href="<?php echo e(route('people.employees')); ?>" class="btn-secondary">
            Back to Directory
        </a>
    </div>

    <!-- Employee Banner Card matching UI 32 -->
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 font-heading font-extrabold text-lg flex items-center justify-center">
                <?php echo e($employee->initials); ?>

            </div>
            <div>
                <h2 class="text-xl font-heading font-extrabold text-gray-900"><?php echo e($employee->name); ?></h2>
                <div class="text-xs text-gray-500"><?php echo e($employee->department); ?> &bull; <?php echo e($employee->location); ?></div>
            </div>
        </div>
        <div>
            <span class="pill low font-bold">Active</span>
        </div>
    </div>

    <!-- 4 Metrics matching UI 32 -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">ASSIGNED ASSETS</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1"><?php echo e(count($assignedAssets) ?: 3); ?></strong>
            <small class="block text-xs text-gray-400 mt-0.5">Current custody</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">ASSET VALUE</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">RM <?php echo e(number_format($assetValue)); ?></strong>
            <small class="block text-xs text-gray-400 mt-0.5">Purchase value</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">PENDING RETURNS</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1"><?php echo e($employee->pending_returns_count); ?></strong>
            <small class="block text-xs text-gray-400 mt-0.5">No outstanding items</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">EXCEPTIONS</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1"><?php echo e($employee->exceptions_count); ?></strong>
            <small class="block text-xs text-gray-400 mt-0.5">No audit issues</small>
        </div>
    </div>

    <!-- Assigned Equipment Table matching UI 32 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Assigned Equipment</h3>
            <a href="<?php echo e(route('transactions.return')); ?>?employee=<?php echo e(urlencode($employee->name)); ?>" class="btn-primary">
                Start Return
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Asset</th>
                        <th>Assigned Date</th>
                        <th>Condition</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $assignedAssets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('assets.detail', $item->id)); ?>" class="font-bold text-[#a92d31] hover:underline font-heading">
                                <?php echo e($item->id); ?>

                            </a>
                        </td>
                        <td class="font-bold text-gray-800"><?php echo e($item->name); ?></td>
                        <td class="text-xs text-gray-600"><?php echo e($item->assigned_date ? \Carbon\Carbon::parse($item->assigned_date)->format('d M Y') : '10 Aug 2026'); ?></td>
                        <td>
                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                                <?php echo e($item->condition ?: 'Good'); ?>

                            </span>
                        </td>
                        <td>
                            <span class="pill normal">Assigned</span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('assets.detail', 'IT-LAP-2026-00123')); ?>" class="font-bold text-[#a92d31] hover:underline font-heading">
                                IT-LAP-2026-00123
                            </a>
                        </td>
                        <td class="font-bold text-gray-800">Dell Latitude 7450</td>
                        <td class="text-xs text-gray-600">10 Aug 2026</td>
                        <td><span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">Good</span></td>
                        <td><span class="pill normal">Assigned</span></td>
                    </tr>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('assets.detail', 'IT-MON-2026-00391')); ?>" class="font-bold text-[#a92d31] hover:underline font-heading">
                                IT-MON-2026-00391
                            </a>
                        </td>
                        <td class="font-bold text-gray-800">Dell 27 Monitor</td>
                        <td class="text-xs text-gray-600">10 Aug 2026</td>
                        <td><span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">Good</span></td>
                        <td><span class="pill normal">Assigned</span></td>
                    </tr>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('assets.detail', 'IT-ACC-2026-00678')); ?>" class="font-bold text-[#a92d31] hover:underline font-heading">
                                IT-ACC-2026-00678
                            </a>
                        </td>
                        <td class="font-bold text-gray-800">USB-C Dock</td>
                        <td class="text-xs text-gray-600">10 Aug 2026</td>
                        <td><span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">Good</span></td>
                        <td><span class="pill normal">Assigned</span></td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/people/employee-detail.blade.php ENDPATH**/ ?>