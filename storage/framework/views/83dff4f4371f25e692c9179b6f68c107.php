<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Suppliers</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Maintain approved supplier records, contacts and purchasing history.</p>
        </div>
        <button wire:click="$set('showModal', true)" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Supplier</span>
        </button>
    </div>

    <!-- Filters Bar matching UI 13 -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search supplier..." class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</label>
            <select wire:model.live="statusFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Category</label>
            <select wire:model.live="categoryFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="Hardware">Hardware</option>
                <option value="Networking">Networking</option>
                <option value="Printers">Printers</option>
            </select>
        </div>
    </div>

    <!-- Table: Supplier Directory -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Supplier Directory</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Category</th>
                        <th>Contact</th>
                        <th>Open POs</th>
                        <th>YTD Spend</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="font-bold text-gray-900"><?php echo e($sup->name); ?></td>
                        <td><?php echo e($sup->category); ?></td>
                        <td class="text-xs text-gray-600 font-mono"><?php echo e($sup->contact_email); ?></td>
                        <td class="font-bold font-heading text-gray-800"><?php echo e($sup->open_pos_count); ?></td>
                        <td class="font-bold font-heading text-gray-800">RM <?php echo e(number_format($sup->ytd_spend)); ?></td>
                        <td><span class="pill low">Active</span></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModal): ?>
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-heading font-bold text-base text-[#a92d31]">Add New Supplier</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form wire:submit.prevent="addSupplier" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Company / Supplier Name *</label>
                    <input type="text" wire:model="name" class="itam-input" required>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Category</label>
                    <select wire:model="category" class="itam-select">
                        <option value="Hardware">Hardware</option>
                        <option value="Networking">Networking</option>
                        <option value="Printers">Printers</option>
                        <option value="Cloud & Software">Cloud & Software</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Contact Email *</label>
                    <input type="email" wire:model="contactEmail" class="itam-input" required>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Phone</label>
                    <input type="text" wire:model="contactPhone" class="itam-input">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/procurement/suppliers.blade.php ENDPATH**/ ?>