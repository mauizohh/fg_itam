<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">New Audit</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Create a physical audit, define scope and capture scan or verification results.</p>
    </div>

    <!-- Active Campaign Card matching UI 21 -->
    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-heading font-extrabold text-gray-900"><?php echo e($audit->name); ?></h2>
                <div class="text-xs text-gray-500 mt-0.5">Scope: <?php echo e($audit->scope); ?></div>
            </div>
            <!-- Percentage Badge -->
            <div class="w-16 h-16 rounded-full border-4 border-[#a92d31] flex items-center justify-center font-heading font-black text-base text-[#a92d31] shadow-inner bg-red-50/50">
                <?php echo e($percent); ?>%
            </div>
        </div>

        <!-- 4 Scope Metrics -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-gray-100">
            <div>
                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider font-heading">IN SCOPE</span>
                <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1"><?php echo e($audit->total_in_scope); ?></strong>
                <small class="block text-xs text-gray-400 mt-0.5">Assets</small>
            </div>
            <div>
                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider font-heading">VERIFIED</span>
                <strong class="block text-2xl font-heading font-extrabold text-emerald-600 mt-1"><?php echo e($audit->verified_count); ?></strong>
                <small class="block text-xs text-emerald-600 font-semibold mt-0.5">Found</small>
            </div>
            <div>
                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider font-heading">EXCEPTIONS</span>
                <strong class="block text-2xl font-heading font-extrabold text-[#b91c1c] mt-1"><?php echo e($audit->exceptions_count); ?></strong>
                <small class="block text-xs text-red-500 font-semibold mt-0.5">Needs review</small>
            </div>
            <div>
                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider font-heading">REMAINING</span>
                <strong class="block text-2xl font-heading font-extrabold text-gray-500 mt-1"><?php echo e($remaining); ?></strong>
                <small class="block text-xs text-gray-400 mt-0.5">Not checked</small>
            </div>
        </div>
    </div>

    <!-- Verification Station Box matching UI 21 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
        <h3 class="font-heading font-bold text-base text-[#3b4048]">Verification Station</h3>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($feedbackMessage): ?>
            <div class="p-3 rounded-lg text-xs font-semibold <?php echo e($feedbackType === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200'); ?>">
                <?php echo e($feedbackMessage); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Scanner Dashed Box -->
        <div class="border-2 border-dashed border-[#c54f53] bg-red-50/20 rounded-2xl p-8 flex flex-col items-center justify-center text-center space-y-3">
            <svg class="w-12 h-12 text-[#a92d31]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            <div>
                <span class="block font-heading font-black text-sm tracking-widest text-[#a92d31] uppercase">SCAN</span>
                <span class="text-xs text-gray-500">Scan QR / RFID or enter Asset ID</span>
            </div>
            <div class="w-full max-w-sm">
                <input type="text"
                       wire:model="scanInput"
                       placeholder="e.g. IT-LAP-2026-00123"
                       class="itam-input text-center font-heading font-black text-base text-[#a92d31] h-12 tracking-wider uppercase">
            </div>
        </div>

        <!-- 4 Verification Action Buttons matching UI 21 -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
            <button wire:click="recordResult('Found')"
                    class="h-11 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-heading font-bold text-xs shadow transition flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Found</span>
            </button>
            <button wire:click="recordResult('Wrong Location')"
                    class="h-11 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-heading font-bold text-xs shadow transition flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Wrong Location</span>
            </button>
            <button wire:click="recordResult('Damaged')"
                    class="h-11 rounded-xl bg-red-600 hover:bg-red-700 text-white font-heading font-bold text-xs shadow transition flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Damaged</span>
            </button>
            <button wire:click="recordResult('Not Found')"
                    class="h-11 rounded-xl bg-gray-600 hover:bg-gray-700 text-white font-heading font-bold text-xs shadow transition flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>Not Found</span>
            </button>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/audits/new-audit.blade.php ENDPATH**/ ?>