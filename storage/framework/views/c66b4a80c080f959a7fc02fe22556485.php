<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Purchases</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Track received purchases, invoice details and asset creation progress.</p>
    </div>

    <!-- 4 Metric Cards matching UI 14 -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">YTD PURCHASES</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">RM 1.84M</strong>
            <small class="block text-xs text-gray-400 mt-0.5">IT assets and software</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">THIS MONTH</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">RM 146K</strong>
            <small class="block text-xs text-gray-400 mt-0.5">Approved purchases</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">ASSETS PENDING REGISTER</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#a92d31] mt-1">18</strong>
            <small class="block text-xs text-amber-600 font-semibold mt-0.5">Received but not tagged</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">INVOICES PENDING</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">6</strong>
            <small class="block text-xs text-gray-400 mt-0.5">Finance review</small>
        </div>
    </div>

    <!-- Purchase Register Table matching UI 14 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Purchase Register</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Purchase</th>
                        <th>Supplier</th>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Assets</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="font-bold text-[#a92d31] font-heading"><?php echo e($p->id); ?></td>
                        <td class="font-semibold text-gray-900"><?php echo e($p->supplier_name); ?></td>
                        <td class="font-mono text-xs text-gray-600"><?php echo e($p->invoice_number); ?></td>
                        <td class="text-xs text-gray-600"><?php echo e(\Carbon\Carbon::parse($p->purchase_date)->format('d M')); ?></td>
                        <td class="font-bold font-heading text-gray-800">RM <?php echo e(number_format($p->amount)); ?></td>
                        <td><?php echo e($p->assets_description); ?></td>
                        <td>
                            <span class="pill <?php echo e($p->status === 'Received' ? 'high' : 'low'); ?>"><?php echo e($p->status); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/procurement/purchases.blade.php ENDPATH**/ ?>