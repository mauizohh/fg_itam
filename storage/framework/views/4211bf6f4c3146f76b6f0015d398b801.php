<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Purchase Orders</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Create and monitor purchase orders from approval through receipt.</p>
        </div>
        <button wire:click="$set('showModal', true)" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>New PO</span>
        </button>
    </div>

    <!-- Filters Bar matching UI 15 -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search PO number, supplier..."
                class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</label>
            <select wire:model.live="statusFilter" class="itam-select text-xs h-10">
                <option value="Open">Open</option>
                <option value="Approved">Approved</option>
                <option value="Ordered">Ordered</option>
                <option value="Partial">Partial</option>
                <option value="All">All</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Supplier</label>
            <select wire:model.live="supplierFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="TechSource">TechSource</option>
                <option value="NetWorks Supply">NetWorks Supply</option>
            </select>
        </div>
    </div>

    <!-- Table: Purchase Orders -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Purchase Orders</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>Requested By</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Delivery</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="font-bold text-[#a92d31] font-heading"><?php echo e($po->id); ?></td>
                        <td class="font-semibold text-gray-900"><?php echo e($po->supplier_name); ?></td>
                        <td><?php echo e($po->requested_by); ?></td>
                        <td class="text-xs text-gray-600"><?php echo e(\Carbon\Carbon::parse($po->po_date)->format('d M')); ?></td>
                        <td class="font-bold font-heading text-gray-800">RM <?php echo e(number_format($po->amount)); ?></td>
                        <td class="text-xs text-gray-600"><?php echo e($po->delivery_date ? \Carbon\Carbon::parse($po->delivery_date)->format('d M') : '-'); ?></td>
                        <td>
                            <?php
                                $pill = match($po->status) {
                                    'Approved' => 'low',
                                    'Ordered' => 'normal',
                                    'Partial' => 'high',
                                    default => 'normal',
                                };
                            ?>
                            <span class="pill <?php echo e($pill); ?>"><?php echo e($po->status); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- New PO Modal -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModal): ?>
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-heading font-bold text-base text-[#a92d31]">Create Purchase Order</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form wire:submit.prevent="createPo" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">PO Number</label>
                    <input type="text" wire:model="newId" class="itam-input bg-gray-50 font-mono font-bold" required>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Supplier *</label>
                    <input type="text" wire:model="supplierName" class="itam-input" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Requested By</label>
                        <input type="text" wire:model="requestedBy" class="itam-input">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Amount (RM) *</label>
                        <input type="number" step="0.01" wire:model="amount" class="itam-input" required>
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Target Delivery Date</label>
                    <input type="date" wire:model="deliveryDate" class="itam-input">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Submit PO</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/procurement/purchase-orders.blade.php ENDPATH**/ ?>