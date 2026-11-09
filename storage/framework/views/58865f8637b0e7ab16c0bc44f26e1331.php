<div class="fixed bottom-6 right-6 z-50">
    <!-- Floating toggle button -->
    <button wire:click="toggle"
            class="h-12 w-12 rounded-full bg-[#a92d31] hover:bg-[#8f2428] text-white shadow-xl flex items-center justify-center transition-transform hover:scale-105 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
    </button>

    <!-- Dialog / Quick Widget -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOpen): ?>
    <div class="absolute bottom-16 right-0 w-80 sm:w-96 bg-white rounded-xl shadow-2xl border border-[#c54f53] overflow-hidden animate-fade-in text-[#3b4048]">
        <div class="bg-[#8f2428] px-4 py-3 flex items-center justify-between text-white">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <span class="font-heading font-bold text-sm">FG Asset & Helpdesk Bridge</span>
            </div>
            <button wire:click="toggle" class="text-white/80 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-4 space-y-3">
            <p class="text-xs text-gray-500">Fast asset identity verification across ITAM & Helpdesk tickets.</p>
            <div>
                <input type="text"
                       wire:model.live.debounce.300ms="searchQuery"
                       placeholder="Scan/Type Asset ID or Serial..."
                       class="w-full text-xs h-9 px-3 border border-[#e3d0d0] rounded-lg focus:border-[#a92d31] focus:ring-1 focus:ring-[#e8cccc] outline-none">
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($results)): ?>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('assets.detail', $res['id'])); ?>" class="block p-2 bg-gray-50 hover:bg-red-50 rounded-lg border border-gray-100 transition">
                            <div class="flex items-center justify-between">
                                <span class="font-heading font-bold text-xs text-[#a92d31]"><?php echo e($res['id']); ?></span>
                                <span class="text-[10px] font-semibold text-gray-500"><?php echo e($res['status']); ?></span>
                            </div>
                            <div class="text-[11px] text-gray-700 truncate"><?php echo e($res['name']); ?></div>
                            <div class="text-[10px] text-gray-400"><?php echo e($res['assigned_user'] ?: 'Unassigned'); ?> &bull; <?php echo e($res['serial_number']); ?></div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="pt-1">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Quick Links</span>
                    <div class="space-y-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentLookups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('assets.detail', $id)); ?>" class="flex items-center justify-between p-1.5 hover:bg-gray-50 rounded text-xs">
                                <span class="font-bold text-[#a92d31] font-heading"><?php echo e($id); ?></span>
                                <span class="text-[10px] text-gray-500 truncate max-w-[150px]"><?php echo e($label); ?></span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/components/quick-chat.blade.php ENDPATH**/ ?>