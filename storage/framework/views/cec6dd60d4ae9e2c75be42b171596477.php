<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Roles</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Define permission groups and enforce least-privilege access.</p>
        </div>
        <button class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Role</span>
        </button>
    </div>

    <!-- Table: Role Permission Matrix matching UI 28 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Role Permission Matrix</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Permission</th>
                        <th>System Admin</th>
                        <th>IT Admin</th>
                        <th>IT Support</th>
                        <th>Finance</th>
                        <th>Auditor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="font-bold text-gray-900"><?php echo e($p['perm']); ?></td>
                        <td><span class="pill low"><?php echo e($p['admin']); ?></span></td>
                        <td><span class="pill <?php echo e($p['it_admin'] === 'Yes' ? 'low' : 'closed'); ?>"><?php echo e($p['it_admin']); ?></span></td>
                        <td><span class="pill <?php echo e($p['it_support'] === 'Yes' ? 'low' : ($p['it_support'] === 'Limited' ? 'high' : 'closed')); ?>"><?php echo e($p['it_support']); ?></span></td>
                        <td><span class="pill <?php echo e($p['finance'] === 'Yes' ? 'low' : ($p['finance'] === 'Read' ? 'normal' : 'closed')); ?>"><?php echo e($p['finance']); ?></span></td>
                        <td><span class="pill <?php echo e($p['auditor'] === 'Yes' ? 'low' : ($p['auditor'] === 'Read' ? 'normal' : 'closed')); ?>"><?php echo e($p['auditor']); ?></span></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/administration/roles.blade.php ENDPATH**/ ?>