<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Warranty</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Track warranty coverage and prioritize assets approaching expiry.</p>
        </div>
        @if (auth()->user()?->hasPermission('maintenance.manage') || auth()->user()?->hasPermission('assets.edit'))
            <button type="button" wire:click="openWarrantyModal()" class="btn-primary inline-flex items-center gap-1.5 text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add / Update Warranty</span>
            </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div role="status" aria-live="polite" class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- 4 Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-[18px]">
        <x-stat-card
            title="Covered"
            :value="$coveredCount"
            subtitle="Active warranty"
        />
        <x-stat-card
            title="Expires in 30 Days"
            :value="$expiresIn30"
            subtitle="Requires review"
        />
        <x-stat-card
            title="Expired"
            :value="$expiredCount"
            subtitle="No active coverage"
        />
        <x-stat-card
            title="Unknown"
            :value="$unknownCount"
            subtitle="Data cleanup needed"
        />
    </div>

    <!-- Filters Bar matching UI standard -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text"
                   wire:model.live.debounce.250ms="search"
                   placeholder="Search asset, serial, provider..."
                   class="itam-input text-xs h-10 w-full">
        </div>

        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Warranty Status</label>
            <select wire:model.live="statusFilter" class="itam-select text-xs h-10 w-full">
                <option value="all">All Warranties</option>
                <option value="active">Active Coverage</option>
                <option value="expiring_30">Expiring in 30 Days</option>
                <option value="expired">Expired Coverage</option>
                <option value="unknown">Unspecified End</option>
            </select>
        </div>

        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Sort By</label>
            <select wire:model.live="sortField" class="itam-select text-xs h-10 w-full">
                <option value="warranty_default">Upcoming Expiry First (Expired at Bottom)</option>
                <option value="warranty_end">Sort by End Date</option>
                <option value="id">Sort by Asset ID</option>
                <option value="name">Sort by Asset Name</option>
                <option value="warranty_provider">Sort by Provider</option>
            </select>
        </div>
    </div>

    <!-- Table: Upcoming Warranty Expiry -->
    <x-table-section title="Upcoming Warranty Expiry">
        <x-slot:badge>
            Total: <strong class="text-primary-600">{{ $assets->total() }}</strong> records
        </x-slot:badge>

        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <x-sortable-th field="id" :current-field="$sortField" :current-direction="$sortDirection">Asset ID</x-sortable-th>
                    <x-sortable-th field="name" :current-field="$sortField" :current-direction="$sortDirection">Asset</x-sortable-th>
                    <x-sortable-th field="warranty_provider" :current-field="$sortField" :current-direction="$sortDirection">Provider</x-sortable-th>
                    <x-sortable-th field="warranty_end" :current-field="$sortField" :current-direction="$sortDirection">End Date</x-sortable-th>
                    <th>Days Left</th>
                    <th class="text-center w-24">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $ast)
                @php
                    $daysLeft = $ast->warranty_end ? (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($ast->warranty_end), false) : 0;
                    $isExpired = $ast->warranty_end && \Carbon\Carbon::parse($ast->warranty_end)->isPast();
                @endphp
                <tr class="{{ $isExpired ? 'bg-gray-50/50' : '' }}">
                    <td>
                        <a href="{{ route('assets.detail', $ast->id) }}" class="font-bold text-[#a92d31] hover:underline font-heading">
                            {{ $ast->id }}
                        </a>
                    </td>
                    <td class="font-bold text-gray-800">{{ $ast->name }}</td>
                    <td class="font-semibold text-gray-700">{{ $ast->warranty_provider ?: 'Vendor Support' }}</td>
                    <td class="text-xs text-gray-600">{{ $ast->warranty_end ? \Carbon\Carbon::parse($ast->warranty_end)->format('d M Y') : '—' }}</td>
                    <td>
                        @if (empty($ast->warranty_end))
                            <span class="text-gray-400 font-medium">Unspecified</span>
                        @elseif ($isExpired)
                            <span class="font-bold text-red-600">Expired</span>
                        @elseif ($daysLeft <= 30)
                            <span class="font-bold text-amber-600">{{ $daysLeft }} days left</span>
                        @else
                            <span class="font-bold text-emerald-700">{{ $daysLeft }} days</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center">
                            @if (auth()->user()?->hasPermission('maintenance.manage') || auth()->user()?->hasPermission('assets.edit'))
                                <x-table-dropdown>
                                    <x-slot:trigger>
                                        <button type="button" class="kebab-btn" title="Actions">
                                            <x-icon name="ellipsis-vertical" class="w-5 h-5 text-gray-500 hover:text-gray-900" />
                                        </button>
                                    </x-slot:trigger>
                                    <x-table-dropdown.item href="{{ route('assets.detail', $ast->id) }}" label="Review" icon="eye" />
                                    <x-table-dropdown.item label="Update" icon="pencil" @click="close(); $wire.openWarrantyModal('{{ $ast->id }}')" />
                                </x-table-dropdown>
                            @else
                                <a href="{{ route('assets.detail', $ast->id) }}" class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded bg-gray-50 hover:bg-[#a92d31] hover:text-white border border-gray-200 transition">
                                    Review
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-gray-400 py-6 text-xs">
                        No warranty records match the current filter criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <x-slot:pagination>
            {{ $assets->links() }}
        </x-slot:pagination>
    </x-table-section>

    <!-- Warranty Modal -->
    @if ($showWarrantyModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="warranty-modal-title">
            <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl my-auto">
                <div class="flex items-center justify-between border-b px-6 py-4 flex-shrink-0">
                    <h3 id="warranty-modal-title" class="font-heading font-bold text-lg text-[#a92d31]">Add / Update Equipment Warranty</h3>
                    <button type="button" wire:click="cancelWarrantyModal" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
                </div>
                <form wire:submit.prevent="saveWarranty" class="flex flex-col flex-1 min-h-0">
                    <div class="p-6 overflow-y-auto space-y-4 text-xs flex-1">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Asset *</label>
                            <select wire:model.live="warrantyAssetId" class="itam-select" required>
                                <option value="">Select Asset...</option>
                                @foreach ($allCandidateAssets as $cand)
                                    <option value="{{ $cand->id }}">{{ $cand->id }} - {{ $cand->name }} ({{ $cand->serial_number }})</option>
                                @endforeach
                            </select>
                            @error('warrantyAssetId') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Warranty Provider *</label>
                            <input type="text" wire:model="warrantyProvider" placeholder="e.g. Dell Support, AppleCare, HP Care Pack" class="itam-input" required>
                            @error('warrantyProvider') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-gray-700 mb-1">Warranty Start Date</label>
                                <input type="date" wire:model="warrantyStart" class="itam-input">
                                <span class="text-[10px] text-gray-400 block mt-0.5">Defaults to purchase date</span>
                                @error('warrantyStart') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block font-bold text-gray-700 mb-1">Warranty End Date *</label>
                                <input type="date" wire:model="warrantyEnd" class="itam-input" required>
                                @error('warrantyEnd') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Support Contract / SLA Details</label>
                            <input type="text" wire:model="supportContract" placeholder="e.g. 4-Hour Onsite Response, Next Business Day" class="itam-input">
                            @error('supportContract') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 px-6 py-4 border-t bg-gray-50 rounded-b-2xl flex-shrink-0">
                        <button type="button" wire:click="cancelWarrantyModal" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Save Warranty</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
