<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">All Assets</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Search, filter and manage the complete IT asset register.</p>
        </div>
        @if (auth()->user()?->hasPermission('assets.create'))
            <div>
                <button wire:click="openAddModal" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add Asset</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Filters Bar matching UI 02 -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Filter ID, serial, user..."
                class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</label>
            <select wire:model.live="statusFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="Assigned">Assigned</option>
                <option value="Available">Available</option>
                <option value="In Repair">In Repair</option>
                <option value="Retired">Retired</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Category</label>
            <select wire:model.live="categoryFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Location</label>
            <select wire:model.live="locationFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                @foreach (\App\Models\Location::OFFICE_OPTIONS as $office)
                    <option value="{{ $office }}">{{ $office }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Assets Register Table -->
    <x-table-section title="Asset Register">
        <x-slot:badge>
            Total: <strong class="text-primary-600">{{ $assets->total() }}</strong> records
        </x-slot:badge>

        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <x-sortable-th field="id" :current-field="$sortField" :current-direction="$sortDirection">Asset ID</x-sortable-th>
                    <x-sortable-th field="name" :current-field="$sortField" :current-direction="$sortDirection">Asset</x-sortable-th>
                    <x-sortable-th field="serial_number" :current-field="$sortField" :current-direction="$sortDirection">Serial No.</x-sortable-th>
                    <x-sortable-th field="assigned_user" :current-field="$sortField" :current-direction="$sortDirection">Assigned To</x-sortable-th>
                    <x-sortable-th field="assigned_location" :current-field="$sortField" :current-direction="$sortDirection">Location</x-sortable-th>
                    <x-sortable-th field="status" :current-field="$sortField" :current-direction="$sortDirection">Status</x-sortable-th>
                    <th class="text-center w-24">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                <tr>
                    <td>
                        <a href="{{ route('assets.detail', $asset->id) }}" class="font-bold text-[#a92d31] hover:underline font-heading">
                            {{ $asset->id }}
                        </a>
                    </td>
                    <td>
                        <div class="font-bold text-gray-800">{{ $asset->name }}</div>
                        <div class="text-[11px] text-gray-400 font-normal">{{ $asset->category_name }} &bull; {{ $asset->brand }}</div>
                    </td>
                    <td class="font-mono text-xs text-gray-600">{{ $asset->serial_number }}</td>
                    <td>{{ $asset->display_assigned_user ?: '-' }}</td>
                    <td>{{ $asset->assigned_location }}</td>
                    <td>
                        <span class="{{ $asset->status_pill_class }}">{{ $asset->status }}</span>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center">
                            @if (auth()->user()?->hasPermission('assets.edit'))
                                <x-table-dropdown>
                                    <x-slot:trigger>
                                        <button type="button" class="kebab-btn" title="Actions">
                                            <x-icon name="ellipsis-vertical" class="w-5 h-5 text-gray-500 hover:text-gray-900" />
                                        </button>
                                    </x-slot:trigger>
                                    <x-table-dropdown.item href="{{ route('assets.detail', $asset->id) }}" label="View Details" icon="eye" />
                                    <x-table-dropdown.item label="Edit" icon="pencil" @click="close(); $wire.openEditModal('{{ $asset->id }}')" />
                                </x-table-dropdown>
                            @else
                                <a href="{{ route('assets.detail', $asset->id) }}" class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded border border-gray-200 bg-gray-50 hover:bg-[#a92d31] hover:text-white transition">
                                    View
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-400">
                        No matching assets found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <x-slot:pagination>
            {{ $assets->links() }}
        </x-slot:pagination>
    </x-table-section>

    @if (auth()->user()?->hasPermission('assets.create') && $showAddModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl max-w-xl w-full max-h-[90vh] flex flex-col shadow-2xl my-auto">
            <div class="flex items-center justify-between border-b px-6 py-4 flex-shrink-0">
                <h3 class="font-heading font-bold text-lg text-[#a92d31]">Add New IT Asset</h3>
                <button wire:click="$set('showAddModal', false)" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
            </div>
            <form wire:submit.prevent="saveAsset" class="flex flex-col flex-1 min-h-0">
                <div class="p-6 overflow-y-auto space-y-4 text-xs flex-1">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Asset ID (Auto-Generated Format)</label>
                            <input type="text" wire:model="newId" class="itam-input font-mono font-bold bg-gray-50 text-[#a92d31]" readonly>
                            <span class="text-[10px] text-gray-400">Compliant with FG IT Helpdesk</span>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Category</label>
                            <select wire:model.live="newCategory" class="itam-select">
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->prefix }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Asset Name / Title *</label>
                        <input type="text" wire:model="newName" placeholder="e.g. Dell Latitude 7450" class="itam-input" required>
                        @error('newName') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Brand</label>
                            <input type="text" wire:model="newBrand" placeholder="e.g. Dell, HP, Apple" class="itam-input">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Model</label>
                            <input type="text" wire:model="newModel" placeholder="e.g. Latitude 7450" class="itam-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Serial Number *</label>
                            <input type="text" wire:model="newSerial" placeholder="e.g. DL7450-93821" class="itam-input" required>
                            @error('newSerial') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Purchase Date</label>
                            <input type="date" wire:model.live="newPurchaseDate" class="itam-input">
                            @error('newPurchaseDate') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Purchase Cost (MYR)</label>
                            <input type="number" step="0.01" wire:model="newCost" placeholder="0.00" class="itam-input">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Warranty Provider</label>
                            <input type="text" wire:model="newWarrantyProvider" placeholder="e.g. Dell Support, AppleCare" class="itam-input">
                            @error('newWarrantyProvider') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Warranty Start Date</label>
                            <input type="date" wire:model="newWarrantyStart" class="itam-input">
                            @error('newWarrantyStart') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Warranty End Date</label>
                            <input type="date" wire:model="newWarrantyEnd" class="itam-input">
                            @error('newWarrantyEnd') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Replacement Due</label>
                            <input type="date" wire:model="newReplacementDue" class="itam-input">
                            @error('newReplacementDue') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Initial Location</label>
                            <select wire:model="newLocation" class="itam-select">
                                @foreach (\App\Models\Location::OFFICE_OPTIONS as $office)
                                    <option value="{{ $office }}">{{ $office }}</option>
                                @endforeach
                            </select>
                            @error('newLocation') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Initial Status</label>
                        <select wire:model="newStatus" class="itam-select">
                            <option value="Available">Available</option>
                            <option value="Assigned">Assigned</option>
                            <option value="In Repair">In Repair</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t bg-gray-50 rounded-b-2xl flex-shrink-0">
                    <button type="button" wire:click="$set('showAddModal', false)" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save Asset</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Edit Asset Modal -->
    @if ($showEditModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl my-auto">
            <div class="flex items-center justify-between border-b px-6 py-4 flex-shrink-0">
                <div>
                    <h3 class="font-heading font-bold text-lg text-[#a92d31]">Edit Asset Details</h3>
                    <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $editId }}</p>
                </div>
                <button type="button" wire:click="cancelEditModal" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
            </div>
            <form wire:submit.prevent="saveAssetEdit" class="flex flex-col flex-1 min-h-0">
                <div class="p-6 overflow-y-auto space-y-4 text-xs flex-1">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="block font-bold text-gray-700 mb-1">Asset Name / Title *</label>
                            <input type="text" wire:model="editName" class="itam-input" required>
                            @error('editName') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Brand</label>
                            <input type="text" wire:model="editBrand" class="itam-input">
                            @error('editBrand') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Model</label>
                            <input type="text" wire:model="editModel" class="itam-input">
                            @error('editModel') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Serial Number *</label>
                            <input type="text" wire:model="editSerial" class="itam-input font-mono" required>
                            @error('editSerial') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Condition</label>
                            <select wire:model="editCondition" class="itam-select">
                                <option value="New">New</option>
                                <option value="Good">Good</option>
                                <option value="Fair">Fair</option>
                                <option value="Damaged">Damaged</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Assigned Location</label>
                            <select wire:model="editLocation" class="itam-select">
                                @foreach (\App\Models\Location::OFFICE_OPTIONS as $office)
                                    <option value="{{ $office }}">{{ $office }}</option>
                                @endforeach
                            </select>
                            @error('editLocation') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Purchase Date</label>
                            <input type="date" wire:model.live="editPurchaseDate" class="itam-input">
                            @error('editPurchaseDate') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        @if (auth()->user()?->canViewFinancialData())
                            <div>
                                <label class="block font-bold text-gray-700 mb-1">Purchase Cost (MYR)</label>
                                <input type="number" step="0.01" wire:model="editPurchaseCost" class="itam-input">
                            </div>
                        @endif
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Warranty Provider</label>
                            <input type="text" wire:model="editWarrantyProvider" class="itam-input">
                            @error('editWarrantyProvider') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Replacement Due</label>
                            <input type="date" wire:model="editReplacementDue" class="itam-input">
                            @error('editReplacementDue') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Warranty Start Date</label>
                            <input type="date" wire:model="editWarrantyStart" class="itam-input">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Warranty End Date</label>
                            <input type="date" wire:model="editWarrantyEnd" class="itam-input">
                            @error('editWarrantyEnd') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Remarks</label>
                        <textarea wire:model="editRemarks" rows="2" class="itam-input py-2"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t bg-gray-50 rounded-b-2xl flex-shrink-0">
                    <button type="button" wire:click="cancelEditModal" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
