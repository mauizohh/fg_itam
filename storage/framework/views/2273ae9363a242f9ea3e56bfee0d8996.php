<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">All Assets</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Search, filter and manage the complete IT asset register.</p>
        </div>
        <div>
            <button wire:click="openAddModal" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Asset</span>
            </button>
        </div>
    </div>

    <!-- Filters Bar matching UI 02 -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Filter ID, serial, user..."
                class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</label>
            <select wire:model.live="statusFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="Assigned">Assigned</option>
                <option value="Available">Available</option>
                <option value="In Repair">In Repair</option>
                <option value="Retired">Retired</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Category</label>
            <select wire:model.live="categoryFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Location</label>
            <select wire:model.live="locationFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="KL Office">KL Office</option>
                <option value="IT Store">IT Store</option>
                <option value="Server Room">Server Room</option>
                <option value="Penang Office">Penang Office</option>
                <option value="Repair Vendor">Repair Vendor</option>
            </select>
        </div>
    </div>

    <!-- Assets Register Table matching UI 02 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Asset Register</h3>
            <span class="text-xs text-gray-400">Total: <?php echo e($assets->total()); ?> records</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Asset</th>
                        <th>Serial No.</th>
                        <th>Assigned To</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('assets.detail', $asset->id)); ?>" class="font-bold text-[#a92d31] hover:underline font-heading">
                                <?php echo e($asset->id); ?>

                            </a>
                        </td>
                        <td>
                            <div class="font-bold text-gray-800"><?php echo e($asset->name); ?></div>
                            <div class="text-[11px] text-gray-400 font-normal"><?php echo e($asset->category_name); ?> &bull; <?php echo e($asset->brand); ?></div>
                        </td>
                        <td class="font-mono text-xs text-gray-600"><?php echo e($asset->serial_number); ?></td>
                        <td><?php echo e($asset->assigned_user ?: '-'); ?></td>
                        <td><?php echo e($asset->assigned_location); ?></td>
                        <td>
                            <span class="<?php echo e($asset->status_pill_class); ?>"><?php echo e($asset->status); ?></span>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo e(route('assets.detail', $asset->id)); ?>" class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded border border-gray-200 bg-gray-50 hover:bg-[#a92d31] hover:text-white transition">
                                View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-400">
                            No matching assets found.
                        </td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            <?php echo e($assets->links()); ?>

        </div>
    </div>

    <!-- Modal: Add Asset -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAddModal): ?>
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-heading font-bold text-lg text-[#a92d31]">Add New IT Asset</h3>
                <button wire:click="$set('showAddModal', false)" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
            </div>
            <form wire:submit.prevent="saveAsset" class="space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Asset ID (Auto-Generated Format)</label>
                        <input type="text" wire:model="newId" class="itam-input font-mono font-bold bg-gray-50 text-[#a92d31]" readonly>
                        <span class="text-[10px] text-gray-400">Compliant with FG IT Helpdesk</span>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Category</label>
                        <select wire:model.live="newCategory" class="itam-select">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?> (<?php echo e($c->prefix); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Asset Name / Title *</label>
                    <input type="text" wire:model="newName" placeholder="e.g. Dell Latitude 7450" class="itam-input" required>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Brand</label>
                        <input type="text" wire:model="newBrand" placeholder="e.g. Dell, HP, Apple" class="itam-input">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Model</label>
                        <input type="text" wire:model="newModel" placeholder="e.g. Latitude 7450" class="itam-input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Serial Number *</label>
                        <input type="text" wire:model="newSerial" placeholder="e.g. DL7450-93821" class="itam-input" required>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newSerial'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Purchase Cost (MYR)</label>
                        <input type="number" step="0.01" wire:model="newCost" placeholder="0.00" class="itam-input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Initial Location</label>
                        <select wire:model="newLocation" class="itam-select">
                            <option value="IT Store">IT Store</option>
                            <option value="KL Office - Level 5">KL Office - Level 5</option>
                            <option value="KL Office - Level 6">KL Office - Level 6</option>
                            <option value="Server Room">Server Room</option>
                            <option value="Penang Office">Penang Office</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Initial Status</label>
                        <select wire:model="newStatus" class="itam-select">
                            <option value="Available">Available</option>
                            <option value="Assigned">Assigned</option>
                            <option value="In Repair">In Repair</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t">
                    <button type="button" wire:click="$set('showAddModal', false)" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save Asset</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/assets/all-assets.blade.php ENDPATH**/ ?>