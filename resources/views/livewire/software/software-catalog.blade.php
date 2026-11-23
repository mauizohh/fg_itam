<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Software</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Maintain the organization software catalog and associated installations or subscriptions.</p>
        </div>
        @if (auth()->user()?->hasPermission('software.manage'))
            <button wire:click="$set('showModal', true)" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Software</span>
            </button>
        @endif
    </div>

    <!-- Filters Bar matching UI 18 -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search title..." class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Type</label>
            <select wire:model.live="typeFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="Subscription">Subscription</option>
                <option value="Perpetual">Perpetual</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Vendor</label>
            <select wire:model.live="vendorFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="Cloud Vendor">Cloud Vendor</option>
                <option value="Security Vendor">Security Vendor</option>
            </select>
        </div>
    </div>

    <!-- Software Catalog Table -->
    <x-table-section title="Software Catalog">
        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Software</th>
                    <th>Vendor</th>
                    <th>Type</th>
                    <th class="text-center">Managed Devices</th>
                    <th>Owner</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($software as $s)
                <tr>
                    <td class="font-bold text-gray-900">{{ $s->name }}</td>
                    <td>{{ $s->vendor }}</td>
                    <td>
                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                            {{ $s->type }}
                        </span>
                    </td>
                    <td class="text-center font-bold font-heading text-gray-800">
                        <button type="button" 
                                wire:click="openDeviceModal('{{ addslashes($s->name) }}')"
                                class="inline-flex items-center gap-1 font-bold text-[#a92d31] hover:underline cursor-pointer group"
                                title="Click to view assigned asset IDs">
                            <span>{{ $s->managed_devices_count }}</span>
                            <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-[#a92d31] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </button>
                    </td>
                    <td>{{ $s->owner_department }}</td>
                    <td><span class="pill low">Active</span></td>
                    <td class="text-center">
                        <button type="button" 
                                wire:click="openDeviceModal('{{ addslashes($s->name) }}')" 
                                class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded bg-gray-50 hover:bg-[#a92d31] hover:text-white border border-gray-200 transition cursor-pointer">
                            <span>View Assets</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </x-table-section>

    <!-- Managed Devices Modal -->
    @if($showDeviceModal && $selectedSoftwareTitle)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-3xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between border-b pb-3 shrink-0">
                <div>
                    <h3 class="font-heading font-bold text-lg text-[#3b4048]">
                        Managed Devices &bull; <span class="text-[#a92d31]">{{ $selectedSoftwareTitle }}</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Equipment currently utilizing or assigned this software title. Click any Asset ID to view details.
                    </p>
                </div>
                <button type="button" wire:click="closeDeviceModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none p-1 cursor-pointer">
                    &times;
                </button>
            </div>

            <!-- Search -->
            <div class="flex items-center justify-between gap-3 shrink-0 bg-gray-50 p-3 rounded-xl border border-gray-200">
                <div class="relative flex-1 max-w-sm">
                    <input type="text"
                           wire:model.live.debounce.250ms="deviceSearch"
                           placeholder="Filter by Asset ID, Device, Custodian, Dept..."
                           class="itam-input h-9 text-xs pl-8 pr-3 w-full bg-white">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-500 font-medium shrink-0">
                    Showing <strong class="text-gray-900">{{ $deviceAssets->count() }}</strong> assigned device(s)
                </span>
            </div>

            <!-- Devices Table -->
            <div class="border border-gray-200 rounded-xl overflow-y-auto flex-1 bg-white">
                <table class="w-full text-left itam-table">
                    <thead>
                        <tr>
                            <th>Asset ID</th>
                            <th>Device Name</th>
                            <th>Custodian</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deviceAssets as $ast)
                        <tr wire:key="dev-ast-{{ $ast->id }}">
                            <td>
                                <a href="{{ route('assets.detail', $ast->id) }}" 
                                   target="_blank" 
                                   class="asset-id-link font-mono font-bold text-[#a92d31] hover:underline inline-flex items-center gap-1"
                                   title="Open asset details in new tab">
                                    <span>{{ $ast->id }}</span>
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </td>
                            <td>
                                <div class="table-primary-text">{{ $ast->name }}</div>
                                <div class="table-subtext">{{ $ast->category_name }} &bull; {{ $ast->brand }} {{ $ast->model }}</div>
                            </td>
                            <td>
                                <div class="text-xs font-semibold text-gray-800">
                                    {{ $ast->display_assigned_user ?: ($ast->assigned_user ?: '—') }}
                                </div>
                                @if($ast->department)
                                    <div class="text-[11px] text-gray-500">{{ $ast->department }}</div>
                                @endif
                            </td>
                            <td class="text-xs text-gray-600">
                                {{ $ast->assigned_location ?: '—' }}
                            </td>
                            <td>
                                <span class="{{ $ast->status_pill_class }} text-[11px]">{{ $ast->status }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-xs text-gray-400">
                                No devices found matching filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-3 border-t flex justify-end shrink-0">
                <button type="button" wire:click="closeDeviceModal" class="btn-secondary text-xs">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Add Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-heading font-bold text-base text-[#a92d31]">Add Software Title</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form wire:submit.prevent="addSoftware" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Software Name *</label>
                    <input type="text" wire:model="name" class="itam-input" required>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Vendor *</label>
                    <input type="text" wire:model="vendor" class="itam-input" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">License Type</label>
                        <select wire:model="type" class="itam-select">
                            <option value="Subscription">Subscription</option>
                            <option value="Perpetual">Perpetual</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Owner Department</label>
                        <input type="text" wire:model="ownerDepartment" class="itam-input">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" wire:click="$set('showModal', false)" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save Software</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
