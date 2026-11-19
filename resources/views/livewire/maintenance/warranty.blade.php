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

    <!-- Table: Upcoming Warranty Expiry -->
    <x-table-section title="Upcoming Warranty Expiry">
        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Asset ID</th>
                    <th>Asset</th>
                    <th>Provider</th>
                    <th>End Date</th>
                    <th>Days Left</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $ast)
                @php
                    $daysLeft = $ast->warranty_end ? (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($ast->warranty_end), false) : 0;
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('assets.detail', $ast->id) }}" class="font-bold text-[#a92d31] hover:underline font-heading">
                            {{ $ast->id }}
                        </a>
                    </td>
                    <td class="font-bold text-gray-800">{{ $ast->name }}</td>
                    <td class="font-semibold text-gray-700">{{ $ast->warranty_provider ?: 'Vendor Support' }}</td>
                    <td class="text-xs text-gray-600">{{ $ast->warranty_end ? \Carbon\Carbon::parse($ast->warranty_end)->format('d M Y') : '—' }}</td>
                    <td>
                        <span class="font-bold {{ $daysLeft < 15 ? 'text-red-600' : 'text-amber-600' }}">
                            {{ $daysLeft > 0 ? "{$daysLeft} days" : 'Expired' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('assets.detail', $ast->id) }}" class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded bg-gray-50 hover:bg-[#a92d31] hover:text-white border border-gray-200 transition">
                                Review
                            </a>
                            @if (auth()->user()?->hasPermission('maintenance.manage') || auth()->user()?->hasPermission('assets.edit'))
                                <button type="button" wire:click="openWarrantyModal('{{ $ast->id }}')" class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded bg-white hover:bg-gray-100 text-gray-700 border border-gray-200 transition">
                                    Update
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </x-table-section>

    <!-- Warranty Modal -->
    @if ($showWarrantyModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="warranty-modal-title">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 id="warranty-modal-title" class="font-heading font-bold text-lg text-[#a92d31]">Add / Update Equipment Warranty</h3>
                    <button type="button" wire:click="cancelWarrantyModal" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
                </div>
                <form wire:submit.prevent="saveWarranty" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Asset *</label>
                        <select wire:model="warrantyAssetId" class="itam-select" required>
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

                    <div class="flex items-center justify-end gap-2 pt-3 border-t">
                        <button type="button" wire:click="cancelWarrantyModal" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Save Warranty</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
