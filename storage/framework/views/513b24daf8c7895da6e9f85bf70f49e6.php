<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Settings</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Configure system-wide numbering, notifications, retention and tracking behavior.</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="$refresh" class="btn-secondary">Reset</button>
            <button wire:click="saveSettings" class="btn-primary">Save Settings</button>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('success')): ?>
        <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-semibold">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- 4 Setting Blocks matching UI 29 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        <!-- Asset Numbering -->
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-base text-[#3b4048] pb-2 border-b">Asset Numbering</h3>
            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Asset ID Format</label>
                <input type="text" wire:model="assetIdFormat" class="itam-input font-mono font-bold text-[#a92d31]">
                <span class="text-[10px] text-gray-400 mt-0.5 block">Default pattern: IT-[DEVICE 3-LETTERS]-[YEAR]-[NUMBER 5 digits]</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Next Number</label>
                    <input type="text" wire:model="nextNumber" class="itam-input font-mono">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Default Currency</label>
                    <input type="text" wire:model="defaultCurrency" class="itam-input">
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-base text-[#3b4048] pb-2 border-b">Notifications</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                    <span class="font-semibold text-gray-700">Warranty expiry alerts</span>
                    <span class="pill low">ON</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                    <span class="font-semibold text-gray-700">Overdue loan alerts</span>
                    <span class="pill low">ON</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                    <span class="font-semibold text-gray-700">Audit deadline alerts</span>
                    <span class="pill low">ON</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                    <span class="font-semibold text-gray-700">Repair overdue alerts</span>
                    <span class="pill low">ON</span>
                </div>
            </div>
        </div>

        <!-- Tracking -->
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-base text-[#3b4048] pb-2 border-b">Tracking Controls</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Last Seen Threshold</label>
                    <input type="text" wire:model="lastSeenThreshold" class="itam-input">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Physical Audit Frequency</label>
                    <input type="text" wire:model="auditFrequency" class="itam-input">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Require Serial Number</label>
                    <select wire:model="requireSerial" class="itam-select">
                        <option value="Yes">Yes (Mandatory)</option>
                        <option value="No">No</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Require Return Inspection</label>
                    <select wire:model="requireReturnInspection" class="itam-select">
                        <option value="Yes">Yes (3-point Checklist)</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Data Retention -->
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-base text-[#3b4048] pb-2 border-b">Data Retention</h3>
            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Audit Log Retention</label>
                <input type="text" wire:model="auditRetention" class="itam-input">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Disposed Asset Records</label>
                    <input type="text" wire:model="disposedRetention" class="itam-input">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Attachments Retention</label>
                    <input type="text" wire:model="attachmentRetention" class="itam-input">
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/administration/settings.blade.php ENDPATH**/ ?>