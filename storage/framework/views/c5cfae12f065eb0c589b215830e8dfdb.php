<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Reports</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Generate operational, financial, lifecycle and compliance reports.</p>
    </div>

    <!-- 6 Report Cards Grid matching UI 24 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div class="space-y-2">
                <h3 class="font-heading font-bold text-base text-[#3b4048]"><?php echo e($r['title']); ?></h3>
                <p class="text-xs text-gray-500 leading-relaxed"><?php echo e($r['description']); ?></p>
            </div>
            <div class="pt-6 mt-4 border-t border-gray-100 flex items-center gap-2">
                <a href="<?php echo e(route('reports.pdf', $r['id'])); ?>" class="btn-primary flex-1 text-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Download PDF</span>
                </a>
                <a href="<?php echo e(route('reports.csv', $r['id'])); ?>" class="btn-secondary px-3" title="Export CSV">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/reports/reports-index.blade.php ENDPATH**/ ?>