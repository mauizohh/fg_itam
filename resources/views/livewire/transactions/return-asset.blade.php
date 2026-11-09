<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Return Asset</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Check equipment back into IT, record condition and determine the next action.</p>
    </div>

    <!-- Return Details Card matching UI 10 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
        <h3 class="font-heading font-bold text-base text-[#3b4048] pb-2 border-b">Return Details</h3>

        <form wire:submit.prevent="completeReturn" class="space-y-5 text-xs">
            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Asset *</label>
                <select wire:model="assetId" class="itam-select h-11" required>
                    @foreach($assignedAssets as $ast)
                        <option value="{{ $ast->id }}">{{ $ast->id }} - {{ $ast->name }} (Assigned to: {{ $ast->assigned_user }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Returned By *</label>
                    <input type="text" wire:model="returnedBy" class="itam-input h-11" required>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Return Date</label>
                    <input type="date" wire:model="returnDate" class="itam-input h-11">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Return Location</label>
                    <select wire:model="returnLocation" class="itam-select h-11">
                        <option value="IT Store">IT Store</option>
                        <option value="KL Office">KL Office</option>
                        <option value="Server Room">Server Room</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Condition</label>
                    <select wire:model="condition" class="itam-select h-11">
                        <option value="Good">Good</option>
                        <option value="Fair">Fair</option>
                        <option value="Damaged">Damaged / Needs Repair</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Next Action</label>
                    <select wire:model="nextAction" class="itam-select h-11">
                        <option value="Available for reassignment">Available for reassignment</option>
                        <option value="Maintenance">Send to Maintenance / Vendor</option>
                        <option value="Retired">Decommission / Retire</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Missing Accessories</label>
                    <input type="text" wire:model="missingAccessories" placeholder="None or list items" class="itam-input h-11">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Remarks</label>
                <textarea wire:model="remarks" rows="3" class="itam-input py-2.5"></textarea>
            </div>

            <!-- Inspection Checklist (Pass / Fail Pills) matching UI 10 -->
            <div class="pt-4 border-t border-gray-100">
                <span class="block font-heading font-bold text-gray-700 mb-3">Return Inspection Checklist</span>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-gray-50/50">
                        <span class="font-semibold text-gray-700">Device powers on</span>
                        <span class="pill low">PASS</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-gray-50/50">
                        <span class="font-semibold text-gray-700">Charger returned</span>
                        <span class="pill low">PASS</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-gray-50/50">
                        <span class="font-semibold text-gray-700">Data handling completed</span>
                        <span class="pill low">PASS</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <a href="{{ route('assets.index') }}" class="btn-secondary">Save Draft</a>
                <button type="submit" class="btn-primary">Complete Return</button>
            </div>
        </form>
    </div>
</div>
