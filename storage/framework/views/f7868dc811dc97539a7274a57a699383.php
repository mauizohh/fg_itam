<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Assets in Repair</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Monitor devices that are unavailable due to repair or maintenance.</p>
        </div>
        <a href="<?php echo e(route('maintenance.repairs')); ?>" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>New Repair Case</span>
        </a>
    </div>

    <!-- 4 Summary Cards matching UI 05 -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">OPEN REPAIRS</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1"><?php echo e($openRepairs); ?></strong>
            <small class="block text-xs text-amber-600 font-semibold mt-0.5">Current</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-red-200 shadow-sm bg-red-50/20">
            <span class="block text-[11px] font-bold text-red-600 uppercase tracking-wider font-heading">OVERDUE</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#b91c1c] mt-1"><?php echo e($overdueCount); ?></strong>
            <small class="block text-xs text-red-500 font-semibold mt-0.5">Past target date</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">AWAITING QUOTE</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1"><?php echo e($awaitingQuote); ?></strong>
            <small class="block text-xs text-gray-400 mt-0.5">Vendor response</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">REPAIR SPEND</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">RM <?php echo e(number_format($repairSpend)); ?></strong>
            <small class="block text-xs text-gray-400 mt-0.5">This year</small>
        </div>
    </div>

    <!-- Table: Repair Queue -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Repair Queue</h3>
            <span class="text-xs text-gray-400"><?php echo e(count($repairs)); ?> active repair cases</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Asset</th>
                        <th>Issue</th>
                        <th>Vendor</th>
                        <th>Sent</th>
                        <th>Target Return</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $repairs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rpr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('assets.detail', $rpr->asset_id)); ?>" class="font-bold text-[#a92d31] hover:underline font-heading">
                                <?php echo e($rpr->asset_id); ?>

                            </a>
                        </td>
                        <td class="font-bold text-gray-800">
                            <?php $asset = \App\Models\Asset::find($rpr->asset_id); ?>
                            <?php echo e($asset ? $asset->name : 'IT Equipment'); ?>

                        </td>
                        <td><?php echo e($rpr->issue); ?></td>
                        <td class="font-semibold text-gray-700"><?php echo e($rpr->vendor); ?></td>
                        <td class="text-xs text-gray-600"><?php echo e(\Carbon\Carbon::parse($rpr->opened_date)->format('d M')); ?></td>
                        <td class="text-xs font-semibold <?php echo e($rpr->status === 'Overdue' ? 'text-red-600' : 'text-gray-600'); ?>">
                            <?php echo e($rpr->target_return_date ? \Carbon\Carbon::parse($rpr->target_return_date)->format('d M') : '-'); ?>

                        </td>
                        <td>
                            <span class="<?php echo e($rpr->status_pill_class); ?>"><?php echo e($rpr->status); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/assets/assets-in-repair.blade.php ENDPATH**/ ?>