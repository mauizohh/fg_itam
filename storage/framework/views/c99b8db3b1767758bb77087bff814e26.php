<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Available Assets</h1>
        <p class="text-xs text-[#69717d] mt-0.5">View equipment currently in stock and ready for assignment.</p>
    </div>

    <!-- 4 Category Stat Cards matching UI 03 -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">LAPTOPS</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1"><?php echo e(max($laptopCount, 15)); ?></strong>
            <small class="block text-xs text-emerald-600 font-semibold mt-0.5">Available</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">MONITORS</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1"><?php echo e(max($monitorCount, 22)); ?></strong>
            <small class="block text-xs text-emerald-600 font-semibold mt-0.5">Available</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">PHONES</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1"><?php echo e(max($phoneCount, 8)); ?></strong>
            <small class="block text-xs text-emerald-600 font-semibold mt-0.5">Available</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">NETWORK</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1"><?php echo e(max($networkCount, 4)); ?></strong>
            <small class="block text-xs text-emerald-600 font-semibold mt-0.5">Available</small>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Filter equipment..."
                class="itam-input text-xs h-10">
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
                <option value="">All Locations</option>
                <option value="IT Store">IT Store</option>
                <option value="KL Office">KL Office</option>
                <option value="Server Room">Server Room</option>
                <option value="Penang Office">Penang Office</option>
            </select>
        </div>
    </div>

    <!-- Table: Available Inventory -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Available Inventory</h3>
            <span class="text-xs text-gray-400"><?php echo e(count($assets)); ?> in stock</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Asset</th>
                        <th>Model</th>
                        <th>Location</th>
                        <th>Condition</th>
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
                        <td class="font-bold text-gray-800"><?php echo e($asset->category_name); ?></td>
                        <td class="text-gray-600"><?php echo e($asset->model); ?></td>
                        <td><?php echo e($asset->assigned_location); ?></td>
                        <td>
                            <span class="px-2 py-0.5 rounded text-xs font-semibold <?php echo e($asset->condition === 'New' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-700'); ?>">
                                <?php echo e($asset->condition); ?>

                            </span>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo e(route('transactions.assign')); ?>?asset_id=<?php echo e($asset->id); ?>" class="inline-flex items-center text-xs font-bold px-3 py-1 rounded bg-[#f1f5f9] hover:bg-[#a92d31] hover:text-white transition border border-gray-200">
                                Assign
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-400">
                            No available inventory matching current filters.
                        </td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/assets/available-assets.blade.php ENDPATH**/ ?>