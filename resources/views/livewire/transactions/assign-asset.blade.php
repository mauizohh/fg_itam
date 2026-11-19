<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Assign Asset</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Issue equipment to an employee, department, location or project with acknowledgement.</p>
    </div>

    <!-- Assignment Details Form matching UI 09 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <h3 class="font-heading font-bold text-base text-[#3b4048] mb-6 pb-2 border-b">Assignment Details</h3>

        <form wire:submit.prevent="submit" class="space-y-5 text-xs">
            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Asset *</label>
                <select wire:model.live="assetId" class="itam-select h-11" required>
                    @foreach($availableAssets as $ast)
                        <option value="{{ $ast->id }}">{{ $ast->id }} - {{ $ast->name }} ({{ $ast->serial_number }}) - [{{ $ast->status }}]</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Assign To *</label>
                    <input type="text" wire:model="assignTo" placeholder="Employee Name" class="itam-input h-11" required>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Department</label>
                    <select wire:model="department" class="itam-select h-11">
                        @foreach($departments as $d)
                            <option value="{{ $d->name }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Location</label>
                    <select wire:model="location" class="itam-select h-11">
                        @foreach (\App\Models\Location::OFFICE_OPTIONS as $office)
                            <option value="{{ $office }}">{{ $office }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Assigned Date</label>
                    <input type="date" wire:model="assignedDate" class="itam-input h-11">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Expected Return</label>
                    <input type="text" wire:model="expectedReturn" placeholder="Not required or date" class="itam-input h-11">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Condition at Issue</label>
                    <select wire:model="condition" class="itam-select h-11">
                        <option value="New">New</option>
                        <option value="Good">Good</option>
                        <option value="Fair">Fair</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Accessories</label>
                <input type="text" wire:model="accessories" placeholder="Charger, Dock, Case, Cable" class="itam-input h-11">
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Remarks</label>
                <textarea wire:model="remarks" rows="3" class="itam-input py-2.5" placeholder="Operational notes..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <a href="{{ route('assets.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Assign Asset</button>
            </div>
        </form>
    </div>
</div>
