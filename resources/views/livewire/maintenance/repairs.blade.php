<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Repairs</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Record incidents, repair vendors, costs and turnaround performance.</p>
        </div>
        <button wire:click="$set('showModal', true)" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>New Repair</span>
        </button>
    </div>

    <!-- Filters Bar matching UI 16 -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search case or issue..." class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</label>
            <select wire:model.live="statusFilter" class="itam-select text-xs h-10">
                <option value="Open">Open</option>
                <option value="Repairing">Repairing</option>
                <option value="Overdue">Overdue</option>
                <option value="Awaiting Part">Awaiting Part</option>
                <option value="All">All</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Vendor</label>
            <select wire:model.live="vendorFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="TechCare">TechCare</option>
                <option value="PrintWorks">PrintWorks</option>
            </select>
        </div>
    </div>

    <!-- Table: Repair Cases matching UI 16 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Repair Cases</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Case</th>
                        <th>Asset</th>
                        <th>Issue</th>
                        <th>Opened</th>
                        <th>Vendor</th>
                        <th>Cost</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($repairs as $rpr)
                    <tr>
                        <td class="font-bold text-[#a92d31] font-heading">{{ $rpr->id }}</td>
                        <td>
                            <a href="{{ route('assets.detail', $rpr->asset_id) }}" class="font-semibold text-gray-800 hover:text-[#a92d31]">
                                {{ $rpr->asset_id }}
                            </a>
                        </td>
                        <td>{{ $rpr->issue }}</td>
                        <td class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($rpr->opened_date)->format('d M') }}</td>
                        <td class="font-semibold text-gray-700">{{ $rpr->vendor }}</td>
                        <td class="font-bold font-heading text-gray-800">RM {{ number_format($rpr->cost) }}</td>
                        <td>
                            <span class="{{ $rpr->status_pill_class }}">{{ $rpr->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: New Repair -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-heading font-bold text-base text-[#a92d31]">Open Repair Ticket</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form wire:submit.prevent="createRepair" class="space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Case Number</label>
                        <input type="text" wire:model="newCaseId" class="itam-input bg-gray-50 font-mono font-bold" required>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Vendor</label>
                        <input type="text" wire:model="vendor" class="itam-input" required>
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Select Asset *</label>
                    <select wire:model="assetId" class="itam-select" required>
                        @foreach($assets as $a)
                            <option value="{{ $a->id }}">{{ $a->id }} - {{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Issue Description *</label>
                    <input type="text" wire:model="issue" placeholder="e.g. Battery failure, Broken hinges" class="itam-input" required>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Estimated Cost (RM)</label>
                    <input type="number" step="0.01" wire:model="cost" class="itam-input">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Submit Repair</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
