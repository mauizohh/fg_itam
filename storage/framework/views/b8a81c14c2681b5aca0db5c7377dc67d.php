<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Transfer Asset</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Move asset responsibility between users, departments or locations while preserving history.</p>
    </div>

    <!-- Transfer Details Form matching UI 11 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
        <h3 class="font-heading font-bold text-base text-[#3b4048] pb-2 border-b">Transfer Details</h3>

        <form wire:submit.prevent="submit" class="space-y-5 text-xs">
            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Asset *</label>
                <select wire:model="assetId" class="itam-select h-11" required>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ast): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ast->id); ?>"><?php echo e($ast->id); ?> - <?php echo e($ast->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Current Custodian</label>
                    <input type="text" wire:model="currentCustodian" class="itam-input h-11 bg-gray-50 text-gray-600" readonly>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Current Location</label>
                    <input type="text" wire:model="currentLocation" class="itam-input h-11 bg-gray-50 text-gray-600" readonly>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Transfer To *</label>
                    <input type="text" wire:model="transferTo" placeholder="New Custodian Name" class="itam-input h-11" required>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">New Department</label>
                    <select wire:model="newDepartment" class="itam-select h-11">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($dept->name); ?>"><?php echo e($dept->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">New Location</label>
                    <input type="text" wire:model="newLocation" placeholder="e.g. KL Office - Engineering" class="itam-input h-11">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Effective Date</label>
                    <input type="date" wire:model="effectiveDate" class="itam-input h-11">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Reason</label>
                <input type="text" wire:model="reason" placeholder="e.g. Role transfer, project assignment" class="itam-input h-11">
            </div>

            <div class="p-3 bg-red-50/50 rounded-lg border border-red-100 text-[11px] text-[#a92d31]">
                <strong>History preserved:</strong> previous assignment will remain visible in the asset timeline and audit logs.
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <a href="<?php echo e(route('assets.index')); ?>" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Transfer Asset</button>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/transactions/transfer-asset.blade.php ENDPATH**/ ?>