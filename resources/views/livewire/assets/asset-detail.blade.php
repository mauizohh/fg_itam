<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Asset Detail</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Single source of truth for an individual asset, including identity, custody, lifecycle and history.</p>
        </div>
        <div class="flex items-center gap-2">
            @if (auth()->user()?->hasPermission('transactions.manage'))
                @if ($asset->status === 'Assigned' || ! empty($asset->assigned_user))
                    <a href="{{ route('transactions.transfer') }}?asset_id={{ $asset->id }}" class="btn-primary">
                        Transfer Asset
                    </a>
                @elseif ($asset->status === 'Available')
                    <a href="{{ route('transactions.assign') }}?asset_id={{ $asset->id }}" class="btn-primary">
                        Assign Asset
                    </a>
                @endif
            @endif

            @if (auth()->user()?->hasPermission('assets.edit'))
                <button type="button" wire:click="openEditModal" class="btn-secondary inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <span>Edit Asset</span>
                </button>
            @endif

            <a href="{{ route('assets.index') }}" class="btn-secondary">
                Back to Register
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div role="status" aria-live="polite" class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Master Header Card matching UI 31 -->
    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-[#a92d31] font-heading">{{ $asset->id }}</span>
            <h2 class="text-2xl font-heading font-black text-gray-900 mt-0.5">{{ $asset->name }}</h2>
            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 mt-1 font-mono">
                <span>Serial: <strong>{{ $asset->serial_number }}</strong></span>
                <span>&bull;</span>
                <span>Brand: <strong>{{ $asset->brand ?: 'Standard' }}</strong></span>
                <span>&bull;</span>
                <span>Model: <strong>{{ $asset->model ?: $asset->name }}</strong></span>
                @if($asset->hostname)
                    <span>&bull;</span>
                    <span>Host: {{ $asset->hostname }}</span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="{{ $asset->status_pill_class }} text-xs px-3 py-1">{{ $asset->status }}</span>
        </div>
    </div>

    <!-- 3 Detailed Group Panels matching UI 31 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Panel 1: Custody -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-sm text-[#3b4048] pb-2 border-b border-gray-100 flex items-center justify-between">
                <span>Custody</span>
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            </h3>
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-gray-400 block text-[11px]">Assigned To</span>
                    <strong class="text-gray-900 text-sm font-semibold">{{ $asset->display_assigned_user ?: 'Unassigned' }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Department</span>
                    <span class="text-gray-800 font-medium">{{ $asset->department ?: 'Finance' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Location</span>
                    <span class="text-gray-800 font-medium">{{ $asset->assigned_location ?: 'Jitra Office' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Assigned Date</span>
                    <span class="text-gray-800 font-medium">{{ $asset->assigned_date ? \Carbon\Carbon::parse($asset->assigned_date)->format('d M Y') : '10 Aug 2026' }}</span>
                </div>
            </div>
        </div>

        <!-- Panel 2: Lifecycle -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-sm text-[#3b4048] pb-2 border-b border-gray-100 flex items-center justify-between">
                <span>Lifecycle</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </h3>
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-gray-400 block text-[11px]">Purchase Date</span>
                    <span class="text-gray-800 font-medium">{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d M Y') : '01 Aug 2026' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Purchase Cost</span>
                    @if (auth()->user()?->canViewFinancialData())
                        <strong class="text-gray-900 text-sm font-semibold">RM {{ number_format($asset->purchase_cost ?: 5200, 2) }}</strong>
                    @else
                        <span class="text-xs text-gray-400 italic">Restricted</span>
                    @endif
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Warranty Provider</span>
                    <span class="text-gray-800 font-medium">{{ $asset->warranty_provider ?: '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Warranty End</span>
                    <span class="text-gray-800 font-medium">{{ $asset->warranty_end ? \Carbon\Carbon::parse($asset->warranty_end)->format('d M Y') : '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Replacement</span>
                    <span class="text-gray-800 font-medium">
                        @if ($asset->replacement_due)
                            @php
                                try {
                                    $formattedDue = \Carbon\Carbon::parse($asset->replacement_due)->format('d M Y');
                                } catch (\Throwable) {
                                    $formattedDue = $asset->replacement_due;
                                }
                            @endphp
                            {{ $formattedDue }}
                        @else
                            —
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Panel 3: Tracking & Verification -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-sm text-[#3b4048] pb-2 border-b border-gray-100 flex items-center justify-between">
                <span>Tracking</span>
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            </h3>
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-gray-400 block text-[11px]">Digital Identity</span>
                    <span class="text-gray-800 font-medium">Serial + hostname</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Last Seen</span>
                    <span class="text-emerald-700 font-semibold">Today 10:30 (Endpoint inventory)</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Last Physical Audit</span>
                    <span class="text-gray-800 font-medium">{{ $asset->last_physical_verification ? \Carbon\Carbon::parse($asset->last_physical_verification)->format('d M Y') : '15 Jun 2026' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Verification Status</span>
                    <span class="pill low">Confirmed</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset Timeline -->
    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
        <h3 class="font-heading font-bold text-base text-[#3b4048] mb-4">Asset Timeline</h3>
        <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200">
            @forelse ($timelineEvents as $index => $event)
                <div class="relative">
                    <span class="absolute -left-6 top-1 w-3 h-3 rounded-full {{ $index === 0 ? 'bg-[#a92d31] ring-4 ring-red-100' : 'bg-gray-400 ring-4 ring-gray-100' }}"></span>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-gray-900">{{ $event['date'] }}</span>
                        @if (! empty($event['actor']))
                            <span class="text-[11px] text-gray-500 font-medium">&bull; By {{ $event['actor'] }}</span>
                        @endif
                    </div>
                    <div class="text-xs font-semibold text-gray-800 mt-0.5">{{ $event['title'] }}</div>
                    @if (! empty($event['description']))
                        <div class="text-xs text-gray-500 mt-0.5">{{ $event['description'] }}</div>
                    @endif
                </div>
            @empty
                <div class="text-xs text-gray-400 py-2">
                    No activity recorded for this asset yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Edit Asset Modal -->
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="edit-asset-title">
            <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl my-auto">
                <div class="flex items-center justify-between border-b px-6 py-4 flex-shrink-0">
                    <div>
                        <h3 id="edit-asset-title" class="font-heading font-bold text-lg text-[#a92d31]">Edit Asset Details</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Asset ID: <strong class="font-mono text-[#a92d31]">{{ $assetId }}</strong></p>
                    </div>
                    <button type="button" wire:click="cancelEditModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="saveAssetEdit" class="flex flex-col flex-1 min-h-0">
                    <div class="p-6 overflow-y-auto space-y-4 text-xs flex-1">
                        <!-- General Details -->
                        <div class="space-y-3">
                            <h4 class="font-heading font-bold text-xs uppercase tracking-wider text-gray-600 border-b pb-1">1. Equipment Identification</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block font-bold text-gray-700 mb-1">Asset Name / Title *</label>
                                    <input type="text" wire:model="editName" class="itam-input text-xs" required>
                                    @error('editName') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Brand</label>
                                    <input type="text" wire:model="editBrand" placeholder="e.g. Dell, HP, Apple" class="itam-input text-xs">
                                    @error('editBrand') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Model</label>
                                    <input type="text" wire:model="editModel" placeholder="e.g. Latitude 7450" class="itam-input text-xs">
                                    @error('editModel') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Serial Number *</label>
                                    <input type="text" wire:model="editSerial" class="itam-input font-mono text-xs" required>
                                    @error('editSerial') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Condition</label>
                                    <select wire:model="editCondition" class="itam-select text-xs">
                                        <option value="New">New</option>
                                        <option value="Good">Good</option>
                                        <option value="Fair">Fair</option>
                                        <option value="Damaged">Damaged</option>
                                    </select>
                                    @error('editCondition') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="space-y-3 pt-2">
                            <h4 class="font-heading font-bold text-xs uppercase tracking-wider text-gray-600 border-b pb-1">2. Location</h4>
                            <div>
                                <label class="block font-bold text-gray-700 mb-1">Assigned Location</label>
                                <select wire:model="editLocation" class="itam-select text-xs">
                                    @foreach (\App\Models\Location::OFFICE_OPTIONS as $office)
                                        <option value="{{ $office }}">{{ $office }}</option>
                                    @endforeach
                                </select>
                                @error('editLocation') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Lifecycle & Warranty -->
                        <div class="space-y-3 pt-2">
                            <h4 class="font-heading font-bold text-xs uppercase tracking-wider text-gray-600 border-b pb-1">3. Procurement &amp; Warranty</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Purchase Date</label>
                                    <input type="date" wire:model.live="editPurchaseDate" class="itam-input text-xs">
                                    @error('editPurchaseDate') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>
                                @if (auth()->user()?->canViewFinancialData())
                                    <div>
                                        <label class="block font-bold text-gray-700 mb-1">Purchase Cost (MYR)</label>
                                        <input type="number" step="0.01" wire:model="editPurchaseCost" placeholder="0.00" class="itam-input text-xs">
                                        @error('editPurchaseCost') <span class="text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Warranty Provider</label>
                                    <input type="text" wire:model="editWarrantyProvider" placeholder="e.g. Dell Support, AppleCare, Vendor Support" class="itam-input text-xs">
                                    @error('editWarrantyProvider') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Replacement Due</label>
                                    <input type="date" wire:model="editReplacementDue" class="itam-input text-xs">
                                    @error('editReplacementDue') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Warranty Start Date</label>
                                    <input type="date" wire:model="editWarrantyStart" class="itam-input text-xs">
                                    @error('editWarrantyStart') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Warranty End Date</label>
                                    <input type="date" wire:model="editWarrantyEnd" class="itam-input text-xs">
                                    @error('editWarrantyEnd') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="space-y-2 pt-2">
                            <label class="block font-bold text-gray-700 mb-1">Operational Remarks / Notes</label>
                            <textarea wire:model="editRemarks" rows="2" placeholder="Lifecycle notes, upgrades, or remarks..." class="itam-input text-xs py-2"></textarea>
                            @error('editRemarks') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 px-6 py-4 border-t bg-gray-50 rounded-b-2xl flex-shrink-0">
                        <button type="button" wire:click="cancelEditModal" class="btn-secondary text-xs">Cancel</button>
                        <button type="submit" class="btn-primary text-xs">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
