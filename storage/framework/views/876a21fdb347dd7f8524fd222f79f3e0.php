<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Departments</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Summarize asset ownership, value and accountability by department.</p>
    </div>

    <!-- 4 Department Metric Cards matching UI 08 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition">
            <h3 class="font-heading font-bold text-sm text-[#a92d31]"><?php echo e($dept->name); ?></h3>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-2"><?php echo e($dept->active_assets_count); ?> assets</strong>
            <div class="text-xs text-gray-500 font-semibold mt-1">RM <?php echo e(number_format($dept->purchase_value)); ?> purchase value</div>
            <div class="text-[11px] font-bold text-amber-600 mt-1"><?php echo e($dept->exceptions_count); ?> <?php echo e(Str::plural('exception', $dept->exceptions_count)); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Department Summary Table matching UI 08 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Department Summary</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Owner</th>
                        <th>Active Assets</th>
                        <th>Available</th>
                        <th>Repair</th>
                        <th>Missing</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="font-bold text-gray-900"><?php echo e($dept->name); ?></td>
                        <td class="font-semibold text-gray-700"><?php echo e($dept->owner_title); ?></td>
                        <td class="font-bold font-heading text-gray-800"><?php echo e($dept->active_assets_count); ?></td>
                        <td class="text-emerald-700 font-semibold"><?php echo e($dept->available_count); ?></td>
                        <td class="text-amber-700 font-semibold"><?php echo e($dept->repair_count); ?></td>
                        <td class="<?php echo e($dept->missing_count > 0 ? 'text-red-600 font-bold' : 'text-gray-500'); ?>"><?php echo e($dept->missing_count); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/people/departments.blade.php ENDPATH**/ ?>