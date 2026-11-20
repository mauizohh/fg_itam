<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Retired Assets</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Maintain retirement, disposal and data-sanitization records.</p>
        </div>
        @if (auth()->user()?->hasPermission('assets.dispose'))
            <button wire:click="$set('showRetireModal', true)" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>New Retirement</span>
            </button>
        @endif
    </div>

    <!-- Filters Bar matching UI 06 -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search retired register..."
                class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Disposition</label>
            <select wire:model.live="dispositionFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="Recycle">Recycle</option>
                <option value="Destroy">Destroy</option>
                <option value="Sale">Sale</option>
                <option value="Donation">Donation</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Year</label>
            <select wire:model.live="yearFilter" class="itam-select text-xs h-10">
                <option value="2026">2026</option>
                <option value="2025">2025</option>
            </select>
        </div>
    </div>

    <!-- Retirement Register Table -->
    <x-table-section title="Retirement Register">
        <x-slot:badge>
            <strong class="text-primary-600">{{ count($retired) }}</strong> decommissioned assets
        </x-slot:badge>

        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Asset ID</th>
                    <th>Asset</th>
                    <th>Retired Date</th>
                    <th>Reason</th>
                    <th>Disposition</th>
                    <th>Data Sanitization</th>
                </tr>
            </thead>
            <tbody>
                @forelse($retired as $asset)
                <tr>
                    <td>
                        <a href="{{ route('assets.detail', $asset->id) }}" class="asset-id-link">
                            {{ $asset->id }}
                        </a>
                    </td>
                    <td>
                        <div class="table-primary-text">{{ $asset->name }}</div>
                        @if($asset->serial_number)
                            <div class="table-subtext">{{ $asset->serial_number }}</div>
                        @endif
                    </td>
                    <td class="table-date">{{ $asset->retired_date ? \Carbon\Carbon::parse($asset->retired_date)->format('d M Y') : '31 Jul 2026' }}</td>
                    <td>{{ $asset->retirement_reason ?: 'End of life' }}</td>
                    <td>
                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                            {{ $asset->disposition ?: 'Recycle' }}
                        </span>
                    </td>
                    <td>
                        <span class="pill {{ $asset->data_sanitization === 'Verified' ? 'low' : 'closed' }}">
                            {{ $asset->data_sanitization ?: 'Verified' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-400">
                        No retired assets found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-section>

    <!-- Modal: New Retirement -->
    @if($showRetireModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-heading font-bold text-lg text-[#a92d31]">Decommission & Retire Asset</h3>
                <button wire:click="$set('showRetireModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form wire:submit.prevent="retireAsset" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Select Asset to Retire *</label>
                    <select wire:model="retireAssetId" class="itam-select" required>
                        <option value="">-- Choose Asset --</option>
                        @foreach($availableForRetire as $cand)
                            <option value="{{ $cand->id }}">{{ $cand->id }} - {{ $cand->name }} ({{ $cand->serial_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Retirement Reason *</label>
                    <select wire:model="retireReason" class="itam-select">
                        <option value="End of life">End of life</option>
                        <option value="Obsolete">Obsolete</option>
                        <option value="Beyond repair">Beyond repair</option>
                        <option value="Security risk">Security risk</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Disposition Method</label>
                        <select wire:model="retireDisposition" class="itam-select">
                            <option value="Recycle">Recycle</option>
                            <option value="Destroy">Destroy</option>
                            <option value="Donation">Donation</option>
                            <option value="Sale">Sale</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Data Sanitization</label>
                        <select wire:model="retireSanitization" class="itam-select">
                            <option value="Verified">Verified (NIST 800-88 Wipe)</option>
                            <option value="Pending">Pending Wipe</option>
                            <option value="N/A">N/A (No storage)</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" wire:click="$set('showRetireModal', false)" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Confirm Retirement</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
