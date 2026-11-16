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
                <option value="KL Office">KL Office</option>
                <option value="IT Store">IT Store</option>
                <option value="Server Room">Server Room</option>
                <option value="Penang Office">Penang Office</option>
                <option value="Repair Vendor">Repair Vendor</option>
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
                    <th>Asset ID</th>
                    <th>Asset</th>
                    <th>Serial No.</th>
                    <th>Assigned To</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
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
                    <td>{{ $asset->assigned_user ?: '-' }}</td>
                    <td>{{ $asset->assigned_location }}</td>
                    <td>
                        <span class="{{ $asset->status_pill_class }}">{{ $asset->status }}</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('assets.detail', $asset->id) }}" class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded border border-gray-200 bg-gray-50 hover:bg-[#a92d31] hover:text-white transition">
                            View
                        </a>
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
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-heading font-bold text-lg text-[#a92d31]">Add New IT Asset</h3>
                <button wire:click="$set('showAddModal', false)" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
            </div>
            <form wire:submit.prevent="saveAsset" class="space-y-4 text-xs">
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
                        <label class="block font-bold text-gray-700 mb-1">Purchase Cost (MYR)</label>
                        <input type="number" step="0.01" wire:model="newCost" placeholder="0.00" class="itam-input">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Initial Location</label>
                        <select wire:model="newLocation" class="itam-select">
                            <option value="IT Store">IT Store</option>
                            <option value="KL Office - Level 5">KL Office - Level 5</option>
                            <option value="KL Office - Level 6">KL Office - Level 6</option>
                            <option value="Server Room">Server Room</option>
                            <option value="Penang Office">Penang Office</option>
                        </select>
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

                <div class="flex items-center justify-end gap-2 pt-3 border-t">
                    <button type="button" wire:click="$set('showAddModal', false)" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save Asset</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
