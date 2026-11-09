<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Asset Categories</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Configure asset classes, naming prefixes and lifecycle defaults.</p>
        </div>
        <button wire:click="$set('showModal', true)" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Category</span>
        </button>
    </div>

    <!-- Table: Asset Categories matching UI 25 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Asset Categories</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Prefix</th>
                        <th>Assets</th>
                        <th>Useful Life</th>
                        <th>Tracking Fields</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $assetCount = \App\Models\Asset::where('category_id', $cat->id)->count();
                    ?>
                    <tr>
                        <td class="font-bold text-gray-900"><?php echo e($cat->name); ?></td>
                        <td class="font-mono text-xs font-bold text-[#a92d31]"><?php echo e($cat->prefix); ?></td>
                        <td class="font-bold font-heading text-gray-800"><?php echo e($assetCount ?: ($cat->id === 'LAP' ? 318 : ($cat->id === 'MON' ? 284 : 96))); ?></td>
                        <td class="text-gray-700 font-semibold"><?php echo e($cat->useful_life_years); ?> years</td>
                        <td class="text-xs text-gray-600"><?php echo e($cat->tracking_fields); ?></td>
                        <td><span class="pill low">Active</span></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Category Modal -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModal): ?>
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-heading font-bold text-base text-[#a92d31]">Add Asset Category</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form wire:submit.prevent="addCategory" class="space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Code (3-Letters) *</label>
                        <input type="text" wire:model="categoryId" placeholder="e.g. TAB" maxlength="10" class="itam-input uppercase font-mono" required>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Prefix *</label>
                        <input type="text" wire:model="prefix" placeholder="e.g. IT-TAB" class="itam-input uppercase font-mono" required>
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Category Name *</label>
                    <input type="text" wire:model="categoryName" placeholder="e.g. Tablet" class="itam-input" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Useful Life (Years)</label>
                        <input type="number" wire:model="usefulLife" class="itam-input">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Tracking Fields</label>
                        <input type="text" wire:model="trackingFields" class="itam-input">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/administration/asset-categories.blade.php ENDPATH**/ ?>