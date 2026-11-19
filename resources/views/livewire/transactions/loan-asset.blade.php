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
            @php
                $assetOptions = $assets->map(fn($a) => [
                    'id' => $a->id,
                    'name' => $a->name,
                    'serial' => $a->serial_number ?: '',
                    'brand' => $a->brand ?: '',
                    'location' => $a->assigned_location ?: '',
                    'status' => $a->status ?: 'Available',
                ])->values();
                if ($selectedAsset && ! $assets->contains('id', $selectedAsset->id)) {
                    $assetOptions->prepend([
                        'id' => $selectedAsset->id,
                        'name' => $selectedAsset->name,
                        'serial' => $selectedAsset->serial_number ?: '',
                        'brand' => $selectedAsset->brand ?: '',
                        'location' => $selectedAsset->assigned_location ?: '',
                        'status' => $selectedAsset->status ?: 'Available',
                    ]);
                }
            @endphp

            <!-- Select Asset Component (Single Unified Searchable Selection) -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block font-bold text-gray-700">Asset *</label>
                    <span class="text-[11px] text-gray-500 font-medium">
                        {{ $assets->count() }} asset(s) available for loan
                    </span>
                </div>

                <div x-data="{
                    open: false,
                    search: @entangle('assetSearch').live,
                    selectedId: @entangle('assetId').live,
                    assets: {{ json_encode($assetOptions) }},
                    get selectedAsset() {
                        return this.assets.find(a => a.id === this.selectedId) || null;
                    },
                    get filteredAssets() {
                        if (!this.search || !this.search.trim()) return this.assets;
                        const q = this.search.toLowerCase().trim();
                        return this.assets.filter(a => 
                            a.id.toLowerCase().includes(q) ||
                            a.name.toLowerCase().includes(q) ||
                            (a.serial && a.serial.toLowerCase().includes(q)) ||
                            (a.brand && a.brand.toLowerCase().includes(q)) ||
                            (a.location && a.location.toLowerCase().includes(q))
                        );
                    },
                    select(id) {
                        this.selectedId = id;
                        this.open = false;
                        this.search = '';
                    },
                    clear() {
                        this.selectedId = '';
                        this.search = '';
                    }
                }"
                @click.outside="open = false"
                @keydown.escape.window="open = false"
                class="relative">
                    <!-- Single Combobox Button / Display -->
                    <div @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
                         class="itam-input h-11 flex items-center justify-between cursor-pointer bg-white px-3.5 border border-gray-300 rounded-lg hover:border-[#a92d31] transition">
                        <div class="flex items-center gap-2 truncate">
                            <template x-if="selectedAsset">
                                <div class="flex items-center gap-2 truncate text-xs">
                                    <span class="font-mono font-bold text-[#a92d31]" x-text="selectedAsset.id"></span>
                                    <span class="font-semibold text-gray-800 truncate" x-text="selectedAsset.name"></span>
                                    <span class="text-gray-500 font-mono text-[11px]" x-text="'(SN: ' + (selectedAsset.serial || '—') + ')'"></span>
                                    <span class="pill low text-[10px] shrink-0" x-text="selectedAsset.status"></span>
                                </div>
                            </template>
                            <template x-if="!selectedAsset">
                                <span class="text-gray-400 text-xs">-- Select Available Asset (browse list or search) --</span>
                            </template>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 text-gray-400">
                            <template x-if="selectedAsset">
                                <button type="button" @click.stop="clear()" class="p-1 hover:text-gray-600 rounded text-xs font-bold leading-none" title="Clear selection">
                                    &times;
                                </button>
                            </template>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Dropdown Menu with Search and Option List -->
                    <div x-show="open"
                         x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-2xl p-2 space-y-2">
                        <!-- Search Input inside dropdown -->
                        <div class="relative">
                            <input type="text"
                                   x-ref="searchInput"
                                   x-model="search"
                                   placeholder="Type to filter by Asset ID, Name, Serial, Brand..."
                                   class="itam-input h-9 text-xs pl-3 pr-7 w-full bg-gray-50 focus:bg-white">
                            <button type="button" x-show="search && search.length > 0" @click="search = ''" class="absolute right-2.5 top-2 text-gray-400 hover:text-gray-600 text-xs font-bold">
                                &times;
                            </button>
                        </div>

                        <!-- Scrollable Option List -->
                        <div class="max-h-60 overflow-y-auto divide-y divide-gray-100 rounded-lg">
                            <template x-for="ast in filteredAssets" :key="ast.id">
                                <button type="button"
                                        @click="select(ast.id)"
                                        class="w-full text-left p-2.5 hover:bg-red-50/50 transition flex items-center justify-between gap-3 text-xs rounded-md"
                                        :class="{ 'bg-red-50/70 border-l-2 border-[#a92d31]': selectedId === ast.id }">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-[#a92d31]" x-text="ast.id"></span>
                                            <span class="font-semibold text-gray-900 truncate" x-text="ast.name"></span>
                                        </div>
                                        <div class="text-[11px] text-gray-500 mt-0.5 truncate">
                                            <span>SN: <strong class="font-mono" x-text="ast.serial || '—'"></strong></span>
                                            <span x-show="ast.brand" x-text="' • Brand: ' + ast.brand"></span>
                                            <span x-show="ast.location" x-text="' • Loc: ' + ast.location"></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="pill low text-[10px]" x-text="ast.status"></span>
                                        <span x-show="selectedId === ast.id" class="text-[#a92d31] font-bold text-sm">✓</span>
                                    </div>
                                </button>
                            </template>
                            <template x-if="filteredAssets.length === 0">
                                <div class="py-6 text-center text-xs text-gray-400">
                                    No matching assets found.
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="assetId" :value="selectedId">
                @error('assetId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
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
