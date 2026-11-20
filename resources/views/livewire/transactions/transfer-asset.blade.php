<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Transfer Asset</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Move asset responsibility between users, departments or locations while preserving history.</p>
    </div>

    <!-- Transfer Details Form matching UI 11 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
        <h3 class="font-heading font-bold text-base text-[#3b4048] pb-2 border-b">Transfer Details</h3>

        <form wire:submit.prevent="submit" class="space-y-5 text-xs">
            @php
                $assetOptions = $assets->map(fn($a) => [
                    'id' => $a->id,
                    'name' => $a->name,
                    'serial' => $a->serial_number ?: '',
                    'custodian' => $a->display_assigned_user ?: ($a->assigned_user ?: 'Assigned'),
                    'department' => $a->department ?: '',
                    'location' => $a->assigned_location ?: '',
                    'status' => $a->status ?: 'Assigned',
                ])->values();
                if ($selectedAsset && ! $assets->contains('id', $selectedAsset->id)) {
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
                    <label class="block font-bold text-gray-700">Select Assigned Asset *</label>
                    <span class="text-[11px] text-gray-500 font-medium">
                        {{ $assets->count() }} asset(s) available for transfer
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
                                    <span class="text-gray-500 text-[11px]" x-text="'(Current: ' + selectedAsset.custodian + ')'"></span>
                                    <span class="pill normal text-[10px] shrink-0" x-text="selectedAsset.status"></span>
                                </div>
                            </template>
                            <template x-if="!selectedAsset">
                                <span class="text-gray-400 text-xs">-- Select Assigned Asset (browse list or search) --</span>
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
                                            <span>Current: <strong x-text="ast.custodian"></strong></span>
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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Current Custodian</label>
                    <input type="text" wire:model="currentCustodian" class="itam-input h-11 bg-gray-50 text-gray-600" readonly>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Current Location</label>
                    <input type="text" wire:model="currentLocation" class="itam-input h-11 bg-gray-50 text-gray-600" readonly>
                </div>
            </div>

            <!-- Transfer To (New Custodian) (Single Full-Width Searchable Selection) -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block font-bold text-gray-700">Transfer To (New Custodian)</label>
                    <span class="text-[11px] text-gray-400 font-normal">Optional for department transfer</span>
                </div>

                <div x-data="{
                    open: false,
                    search: '',
                    selectedName: @entangle('transferTo').live,
                    department: @entangle('newDepartment').live,
                    employees: {{ json_encode($employeeOptions) }},
                    get selectedEmployee() {
                        return this.employees.find(e => e.name === this.selectedName) || null;
                    },
                    get filteredEmployees() {
                        let list = this.employees;
                        if (this.department && this.department.trim()) {
                            list = list.filter(e => e.department && e.department.toLowerCase() === this.department.toLowerCase().trim());
                        }
                        if (!this.search || !this.search.trim()) return list;
                        const q = this.search.toLowerCase().trim();
                        return list.filter(e =>
                            e.name.toLowerCase().includes(q) ||
                            (e.department && e.department.toLowerCase().includes(q)) ||
                            (e.email && e.email.toLowerCase().includes(q))
                        );
                    },
                    select(emp) {
                        this.selectedName = emp.name;
                        if (emp.department) {
                            this.department = emp.department;
                        }
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
                    <div @click="open = !open; if(open) $nextTick(() => $refs.transferEmpSearchInput.focus())"
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
                                <span class="text-gray-400 text-xs">-- Select New Custodian (or search by name/dept) --</span>
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
                                   x-ref="transferEmpSearchInput"
                                   x-model="search"
                                   @keydown.enter.prevent="if (search.trim()) { select({ name: search.trim(), department: department }); }"
                                   placeholder="Type to filter or enter custom name..."
                                   class="itam-input h-9 text-xs pl-3 pr-7 w-full bg-gray-50 focus:bg-white">
                            <button type="button" x-show="search && search.length > 0" @click="search = ''" class="absolute right-2.5 top-2 text-gray-400 hover:text-gray-600 text-xs font-bold">
                                &times;
                            </button>
                        </div>

                        <!-- Custom Name option if user types non-matching name -->
                        <template x-if="search.trim() && !filteredEmployees.some(e => e.name.toLowerCase() === search.toLowerCase().trim())">
                            <button type="button"
                                    @click="select({ name: search.trim(), department: department })"
                                    class="w-full text-left p-2.5 hover:bg-red-50/50 transition text-xs text-gray-700 italic rounded-md border border-dashed border-gray-200">
                                <span>Use custom name: <strong class="not-italic text-gray-900" x-text="search.trim()"></strong></span>
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
                                    No matching employees found<span x-show="department" x-text="' in ' + department"></span>.
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                @error('transferTo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- New Department & New Location in the same row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">New Department *</label>
                    <select wire:model.live="newDepartment" class="itam-select h-11" required>
                        <option value="">-- All Departments --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    @error('newDepartment') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">New Location</label>
                    <select wire:model="newLocation" class="itam-select h-11">
                        @foreach (\App\Models\Location::OFFICE_OPTIONS as $office)
                            <option value="{{ $office }}">{{ $office }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Effective Date & Reason in the same row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Effective Date</label>
                    <input type="date" wire:model="effectiveDate" class="itam-input h-11">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Reason</label>
                    <input type="text" wire:model="reason" placeholder="e.g. Role transfer, project assignment" class="itam-input h-11">
                </div>
            </div>

            <div class="p-3 bg-red-50/50 rounded-lg border border-red-100 text-[11px] text-[#a92d31]">
                <strong>History preserved:</strong> previous assignment will remain visible in the asset timeline and audit logs.
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <a href="{{ route('assets.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Transfer Asset</button>
            </div>
        </form>
    </div>
</div>
