<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Employees</h1>
            <p class="text-xs text-[#69717d] mt-0.5">View employee custody and identify all equipment assigned to each person.</p>
        </div>
    </div>

    <!-- Filters Bar matching UI 07 -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search employee..."
                class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Department</label>
            <select wire:model.live="departmentFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($dept->name); ?>"><?php echo e($dept->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</label>
            <select wire:model.live="statusFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="Active">Active</option>
                <option value="Offboarded">Offboarded</option>
            </select>
        </div>
    </div>

    <!-- Employee Directory Table matching UI 07 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Employee Directory</h3>
            <span class="text-xs text-gray-400"><?php echo e(count($employees)); ?> staff members</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Location</th>
                        <th>Assigned Assets</th>
                        <th>Pending Returns</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="font-bold text-gray-900">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-full bg-red-100 text-[#a92d31] font-bold font-heading text-xs flex items-center justify-center">
                                    <?php echo e($emp->initials); ?>

                                </div>
                                <span><?php echo e($emp->name); ?></span>
                            </div>
                        </td>
                        <td><?php echo e($emp->department); ?></td>
                        <td><?php echo e($emp->location); ?></td>
                        <td class="font-bold font-heading text-gray-800"><?php echo e($emp->assigned_assets_count); ?></td>
                        <td>
                            <span class="<?php echo e($emp->pending_returns_count > 0 ? 'text-amber-600 font-bold' : 'text-gray-500'); ?>">
                                <?php echo e($emp->pending_returns_count); ?>

                            </span>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo e(route('people.employee.detail', $emp->id)); ?>" class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded bg-gray-50 hover:bg-[#a92d31] hover:text-white border border-gray-200 transition">
                                View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/people/employees.blade.php ENDPATH**/ ?>