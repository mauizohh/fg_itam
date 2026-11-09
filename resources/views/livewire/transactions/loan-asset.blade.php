<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Loan Asset</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Manage temporary equipment loans with due dates and overdue tracking.</p>
    </div>

    <!-- Loan Details Form matching UI 12 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
        <h3 class="font-heading font-bold text-base text-[#3b4048] pb-2 border-b">Loan Details</h3>

        <form wire:submit.prevent="submit" class="space-y-5 text-xs">
            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Asset *</label>
                <select wire:model="assetId" class="itam-select h-11" required>
                    @foreach($assets as $ast)
                        <option value="{{ $ast->id }}">{{ $ast->id }} - {{ $ast->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Borrower *</label>
                    <input type="text" wire:model="borrower" class="itam-input h-11" required>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Purpose</label>
                    <input type="text" wire:model="purpose" placeholder="e.g. Client presentation, travel" class="itam-input h-11">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Checkout Date</label>
                    <input type="date" wire:model="checkoutDate" class="itam-input h-11">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Due Date *</label>
                    <input type="date" wire:model="dueDate" class="itam-input h-11" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Condition</label>
                    <select wire:model="condition" class="itam-select h-11">
                        <option value="Good">Good</option>
                        <option value="New">New</option>
                        <option value="Fair">Fair</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Accessories</label>
                    <input type="text" wire:model="accessories" placeholder="Charger, Adapter, Sleeve" class="itam-input h-11">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Notes</label>
                <textarea wire:model="notes" rows="3" class="itam-input py-2.5"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <a href="{{ route('assets.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Check Out Asset</button>
            </div>
        </form>
    </div>
</div>
