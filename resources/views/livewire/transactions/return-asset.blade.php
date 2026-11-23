<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Return Asset</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Check equipment back into IT, record condition and determine the next action.</p>
    </div>

    <!-- Return Details Card matching UI 10 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b">
            <div>
                <h3 class="font-heading font-bold text-base text-[#3b4048]">Return Details</h3>
                <p class="text-[11px] text-gray-500 mt-0.5">
                    @if($returnMode === 'all')
                        Return all or selected assets in custody of this employee at once.
                    @else
                        Check an individual asset back into available inventory.
                    @endif
                </p>
            </div>
            <div class="inline-flex items-center gap-1 bg-gray-100 p-1 rounded-lg self-start sm:self-auto">
                <button type="button"
                    wire:click="setReturnMode('single')"
                    class="px-3 py-1 rounded-md text-xs font-semibold transition {{ $returnMode === 'single' ? 'bg-white text-[#a92d31] shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Single Asset
                </button>
                <button type="button"
                    wire:click="setReturnMode('all')"
                    class="px-3 py-1 rounded-md text-xs font-semibold transition {{ $returnMode === 'all' ? 'bg-white text-[#a92d31] shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Return All (Custodian)
                </button>
            </div>
        </div>

        <form wire:submit.prevent="completeReturn" class="space-y-5 text-xs">
            @if ($returnMode === 'single')
            @php
                $assetOptions = $assignedAssets->map(fn($a) => [
                    'id' => $a->id,
                    'name' => $a->name,
                    'serial' => $a->serial_number ?: '',
                    'custodian' => $a->display_assigned_user ?: ($a->assigned_user ?: 'Assigned'),
                    'department' => $a->department ?: '',
                    'location' => $a->assigned_location ?: '',
                    'status' => $a->status ?: 'Assigned',
                ])->values();
                if ($selectedAsset && ! $assignedAssets->contains('id', $selectedAsset->id)) {
                    $assetOptions->prepend([
                        'id' => $selectedAsset->id,
                        'name' => $selectedAsset->name,
                        'serial' => $selectedAsset->serial_number ?: '',
                        'custodian' => $selectedAsset->display_assigned_user ?: ($selectedAsset->assigned_user ?: 'Assigned'),
                        'department' => $selectedAsset->department ?: '',
                        'location' => $selectedAsset->assigned_location ?: '',
                        'status' => $selectedAsset->status ?: 'Assigned',
                    ]);
                }
            @endphp

            <!-- Select Asset Component (Single Unified Searchable Selection) -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block font-bold text-gray-700">Asset *</label>
                    <span class="text-[11px] text-gray-500 font-medium">
                        {{ $assignedAssets->count() }} asset(s) eligible for return
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
                            (a.custodian && a.custodian.toLowerCase().includes(q)) ||
                            (a.department && a.department.toLowerCase().includes(q)) ||
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
                                    <span class="text-gray-500 text-[11px]" x-text="'(Assigned to: ' + selectedAsset.custodian + ')'"></span>
                                    <span class="pill normal text-[10px] shrink-0" x-text="selectedAsset.status"></span>
                                </div>
                            </template>
                            <template x-if="!selectedAsset">
                                <span class="text-gray-400 text-xs">-- Select Asset to Return (browse list or search) --</span>
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
                                   placeholder="Type to filter by Asset ID, Name, Custodian, Dept..."
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
                                            <span>Assigned: <strong x-text="ast.custodian"></strong></span>
                                            <span x-show="ast.department" x-text="' • Dept: ' + ast.department"></span>
                                            <span> • SN: <strong class="font-mono" x-text="ast.serial || '—'"></strong></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="pill normal text-[10px]" x-text="ast.status"></span>
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
            @endif

            <!-- Returned By Component (Single Full-Width Searchable Selection) -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block font-bold text-gray-700">Returned By *</label>
                </div>

                <div x-data="{
                    open: false,
                    search: '',
                    selectedName: @entangle('returnedBy').live,
                    employees: {{ json_encode($employeeOptions) }},
                    get selectedEmployee() {
                        return this.employees.find(e => e.name === this.selectedName) || null;
                    },
                    get filteredEmployees() {
                        if (!this.search || !this.search.trim()) return this.employees;
                        const q = this.search.toLowerCase().trim();
                        return this.employees.filter(e =>
                            e.name.toLowerCase().includes(q) ||
                            (e.department && e.department.toLowerCase().includes(q)) ||
                            (e.email && e.email.toLowerCase().includes(q))
                        );
                    },
                    select(emp) {
                        this.selectedName = emp.name;
                        this.open = false;
                        this.search = '';
                    },
                    clear() {
                        this.selectedName = '';
                        this.search = '';
                    }
                }"
                @click.outside="open = false"
                @keydown.escape.window="open = false"
                class="relative">
                    <!-- Single Combobox Button / Display -->
                    <div @click="open = !open; if(open) $nextTick(() => $refs.returnedBySearchInput.focus())"
                         class="itam-input h-11 flex items-center justify-between cursor-pointer bg-white px-3.5 border border-gray-300 rounded-lg hover:border-[#a92d31] transition">
                        <div class="flex items-center gap-2 truncate">
                            <template x-if="selectedName">
                                <div class="flex items-center gap-2 truncate text-xs">
                                    <span class="font-semibold text-gray-800 truncate" x-text="selectedName"></span>
                                    <template x-if="selectedEmployee && selectedEmployee.department">
                                        <span class="pill normal text-[10px] shrink-0" x-text="selectedEmployee.department"></span>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!selectedName">
                                <span class="text-gray-400 text-xs">-- Select Returnee (or search by name/dept) --</span>
                            </template>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 text-gray-400">
                            <template x-if="selectedName">
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
                                   x-ref="returnedBySearchInput"
                                   x-model="search"
                                   @keydown.enter.prevent="if (search.trim()) { select({ name: search.trim(), department: '' }); }"
                                   placeholder="Type to filter or enter custom returnee name..."
                                   class="itam-input h-9 text-xs pl-3 pr-7 w-full bg-gray-50 focus:bg-white">
                            <button type="button" x-show="search && search.length > 0" @click="search = ''" class="absolute right-2.5 top-2 text-gray-400 hover:text-gray-600 text-xs font-bold">
                                &times;
                            </button>
                        </div>

                        <!-- Custom Name option if user types non-matching name -->
                        <template x-if="search.trim() && !filteredEmployees.some(e => e.name.toLowerCase() === search.toLowerCase().trim())">
                            <button type="button"
                                    @click="select({ name: search.trim(), department: '' })"
                                    class="w-full text-left p-2.5 hover:bg-red-50/50 transition text-xs text-gray-700 italic rounded-md border border-dashed border-gray-200">
                                <span>Use custom returnee: <strong class="not-italic text-gray-900" x-text="search.trim()"></strong></span>
                            </button>
                        </template>

                        <!-- Scrollable Option List -->
                        <div class="max-h-60 overflow-y-auto divide-y divide-gray-100 rounded-lg">
                            <template x-for="emp in filteredEmployees" :key="emp.id || emp.name">
                                <button type="button"
                                        @click="select(emp)"
                                        class="w-full text-left p-2.5 hover:bg-red-50/50 transition flex items-center justify-between gap-3 text-xs rounded-md"
                                        :class="{ 'bg-red-50/70 border-l-2 border-[#a92d31]': selectedName === emp.name }">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-900 truncate" x-text="emp.name"></span>
                                            <span class="text-gray-400 text-[11px]" x-show="emp.email" x-text="'<' + emp.email + '>'"></span>
                                        </div>
                                        <div class="text-[11px] text-gray-500 mt-0.5 truncate">
                                            <span x-show="emp.department" x-text="'Dept: ' + emp.department"></span>
                                            <span x-show="!emp.department" class="text-gray-400 italic">No department assigned</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="pill normal text-[10px]" x-show="emp.department" x-text="emp.department"></span>
                                        <span x-show="selectedName === emp.name" class="text-[#a92d31] font-bold text-sm">✓</span>
                                    </div>
                                </button>
                            </template>
                            <template x-if="filteredEmployees.length === 0">
                                <div class="py-6 text-center text-xs text-gray-400">
                                    No matching employees found.
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                @error('returnedBy') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            @if ($returnMode === 'all')
                <!-- Assigned Equipment to Return for Custodian -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block font-bold text-gray-700">
                                Assigned Equipment in Custody *
                            </label>
                            <span class="text-[11px] text-gray-500">
                                Select all or specific equipment to check back into inventory.
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[11px] font-semibold text-gray-600">
                                <strong class="text-[#a92d31]">{{ count($selectedAssetIds) }}</strong> of {{ $returneeAssets->count() }} selected
                            </span>
                            @if ($returneeAssets->isNotEmpty())
                                <button type="button" wire:click="toggleSelectAll" class="text-xs font-bold text-[#a92d31] hover:underline cursor-pointer">
                                    {{ count($selectedAssetIds) === $returneeAssets->count() ? 'Deselect All' : 'Select All' }}
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                        <table class="w-full text-left itam-table">
                            <thead>
                                <tr>
                                    <th class="w-12 text-center">
                                        <input type="checkbox"
                                            wire:click="toggleSelectAll"
                                            {{ $returneeAssets->isNotEmpty() && count($selectedAssetIds) === $returneeAssets->count() ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-[#a92d31] focus:ring-[#a92d31] cursor-pointer">
                                    </th>
                                    <th>Asset ID</th>
                                    <th>Asset</th>
                                    <th>Serial No.</th>
                                    <th class="text-center w-28">Condition</th>
                                    <th class="w-24">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returneeAssets as $ast)
                                <tr class="hover:bg-gray-50 transition cursor-pointer" wire:key="ast-{{ $ast->id }}">
                                    <td class="text-center" @click.stop>
                                        <input type="checkbox"
                                            value="{{ $ast->id }}"
                                            wire:model.live="selectedAssetIds"
                                            class="rounded border-gray-300 text-[#a92d31] focus:ring-[#a92d31] cursor-pointer">
                                    </td>
                                    <td>
                                        <a href="{{ route('assets.detail', $ast->id) }}" target="_blank" class="asset-id-link">
                                            {{ $ast->id }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="table-primary-text">{{ $ast->name }}</div>
                                        <div class="table-subtext">{{ $ast->category_name }} &bull; {{ $ast->brand }}</div>
                                    </td>
                                    <td class="table-mono">{{ $ast->serial_number ?: '—' }}</td>
                                    <td class="text-center">
                                        <div class="flex items-center justify-center">
                                            <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $ast->condition === 'New' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                                {{ $ast->condition ?: 'Good' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="{{ $ast->status_pill_class }}">{{ $ast->status }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-400">
                                        No active assigned equipment found for {{ $returnedBy ?: 'this custodian' }}.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @error('selectedAssetIds') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- Return Date, Return Location, & Condition in the same row (3 columns) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Return Date</label>
                    <input type="date" wire:model="returnDate" class="itam-input h-11">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Return Location</label>
                    <select wire:model="returnLocation" class="itam-select h-11">
                        @foreach (\App\Models\Location::OFFICE_OPTIONS as $office)
                            <option value="{{ $office }}">{{ $office }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Condition</label>
                    <select wire:model="condition" class="itam-select h-11">
                        <option value="Good">Good</option>
                        <option value="Fair">Fair</option>
                        <option value="Damaged">Damaged / Needs Repair</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Next Action</label>
                    <select wire:model="nextAction" class="itam-select h-11">
                        <option value="Available for reassignment">Available for reassignment</option>
                        <option value="Maintenance">Send to Maintenance / Vendor</option>
                        <option value="Retired">Decommission / Retire</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Missing Accessories</label>
                    <input type="text" wire:model="missingAccessories" placeholder="None or list items" class="itam-input h-11">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Remarks</label>
                <textarea wire:model="remarks" rows="3" class="itam-input py-2.5"></textarea>
            </div>

            <!-- Inspection Checklist matching UI 10 -->
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="block font-heading font-bold text-gray-700">Return Inspection Checklist</span>
                    <span class="text-[11px] text-gray-500 font-medium">Tick items as actions are completed</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="flex items-center justify-between p-3 rounded-xl border transition cursor-pointer select-none {{ $checkPowerOn ? 'border-emerald-300 bg-emerald-50/40 shadow-xs' : 'border-gray-200 bg-gray-50/50 hover:bg-white hover:border-gray-300' }}">
                        <span class="font-semibold text-xs {{ $checkPowerOn ? 'text-emerald-950 font-bold' : 'text-gray-700' }}">Device powers on</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border text-xs font-bold transition {{ $checkPowerOn ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-white text-gray-500 border-gray-300 hover:bg-gray-50' }}">
                            <input type="checkbox"
                                   wire:model.live="checkPowerOn"
                                   class="w-3.5 h-3.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span>{{ $checkPowerOn ? 'Pass' : 'Tick' }}</span>
                        </span>
                    </label>

                    <label class="flex items-center justify-between p-3 rounded-xl border transition cursor-pointer select-none {{ $checkCharger ? 'border-emerald-300 bg-emerald-50/40 shadow-xs' : 'border-gray-200 bg-gray-50/50 hover:bg-white hover:border-gray-300' }}">
                        <span class="font-semibold text-xs {{ $checkCharger ? 'text-emerald-950 font-bold' : 'text-gray-700' }}">Charger returned</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border text-xs font-bold transition {{ $checkCharger ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-white text-gray-500 border-gray-300 hover:bg-gray-50' }}">
                            <input type="checkbox"
                                   wire:model.live="checkCharger"
                                   class="w-3.5 h-3.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span>{{ $checkCharger ? 'Pass' : 'Tick' }}</span>
                        </span>
                    </label>

                    <label class="flex items-center justify-between p-3 rounded-xl border transition cursor-pointer select-none {{ $checkDataHandling ? 'border-emerald-300 bg-emerald-50/40 shadow-xs' : 'border-gray-200 bg-gray-50/50 hover:bg-white hover:border-gray-300' }}">
                        <span class="font-semibold text-xs {{ $checkDataHandling ? 'text-emerald-950 font-bold' : 'text-gray-700' }}">Data handling completed</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border text-xs font-bold transition {{ $checkDataHandling ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-white text-gray-500 border-gray-300 hover:bg-gray-50' }}">
                            <input type="checkbox"
                                   wire:model.live="checkDataHandling"
                                   class="w-3.5 h-3.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span>{{ $checkDataHandling ? 'Pass' : 'Tick' }}</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <a href="{{ route('assets.index') }}" class="btn-secondary">Save Draft</a>
                <button type="submit" class="btn-primary {{ $returnMode === 'all' && count($selectedAssetIds) === 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $returnMode === 'all' && count($selectedAssetIds) === 0 ? 'disabled' : '' }}>
                    @if($returnMode === 'all')
                        Complete Return All ({{ count($selectedAssetIds) }} {{ \Illuminate\Support\Str::plural('Asset', count($selectedAssetIds)) }})
                    @else
                        Complete Return
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>
