<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Renewals</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Manage renewal dates, owners, costs and approval status for renewable services.</p>
        </div>
        <button wire:click="$set('showModal', true)" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Renewal</span>
        </button>
    </div>

    <!-- Table: Renewal Calendar matching UI 20 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Renewal Calendar</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Renewal</th>
                        <th>Service</th>
                        <th>Owner</th>
                        <th>Renewal Date</th>
                        <th>Annual Cost</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($renewals as $ren)
                    <tr>
                        <td class="font-bold text-[#a92d31] font-heading">{{ $ren->id }}</td>
                        <td class="font-bold text-gray-800">{{ $ren->service_name }}</td>
                        <td>{{ $ren->owner }}</td>
                        <td class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($ren->renewal_date)->format('d M Y') }}</td>
                        <td class="font-bold font-heading text-gray-800">RM {{ number_format($ren->annual_cost) }}</td>
                        <td>
                            <span class="{{ $ren->status_pill_class }}">{{ $ren->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-heading font-bold text-base text-[#a92d31]">Add Subscription Renewal</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form wire:submit.prevent="createRenewal" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Contract ID</label>
                    <input type="text" wire:model="newId" class="itam-input bg-gray-50 font-mono font-bold" required>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Service / Title *</label>
                    <input type="text" wire:model="serviceName" placeholder="e.g. AWS Cloud Services" class="itam-input" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Owner</label>
                        <input type="text" wire:model="owner" class="itam-input">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Annual Cost (RM) *</label>
                        <input type="number" step="0.01" wire:model="annualCost" class="itam-input" required>
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Renewal Due Date *</label>
                    <input type="date" wire:model="renewalDate" class="itam-input" required>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save Renewal</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
