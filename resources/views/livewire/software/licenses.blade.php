<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Licenses</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Track purchased, assigned and available software license quantities.</p>
    </div>

    <!-- 4 Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-[18px]">
        <x-stat-card
            title="Purchased Seats"
            :value="number_format($purchasedSeats)"
            subtitle="Across managed titles"
        />
        <x-stat-card
            title="Assigned"
            :value="number_format($assignedSeats)"
            subtitle="87.3% utilization"
        />
        <x-stat-card
            title="Available"
            :value="$availableSeats"
            subtitle="Unassigned"
        />
        <x-stat-card
            title="Overallocated"
            :value="$overallocated"
            subtitle="No compliance risk"
        />
    </div>

    <!-- License Utilization Table -->
    <x-table-section title="License Utilization">
        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Software</th>
                    <th class="text-center">Purchased</th>
                    <th class="text-center">Assigned</th>
                    <th class="text-center">Available</th>
                    <th>Utilization</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($licenses as $lic)
                <tr>
                    <td class="font-bold text-gray-900">{{ $lic->software_name }}</td>
                    <td class="text-center font-bold font-heading text-gray-800">{{ $lic->purchased_seats }}</td>
                    <td class="text-center">
                        <button type="button" 
                                wire:click="openAssetModal({{ $lic->id }})" 
                                class="inline-flex items-center gap-1 font-bold text-[#a92d31] hover:underline cursor-pointer group"
                                title="Click to view assigned asset IDs">
                            <span>{{ $lic->assigned_seats }}</span>
                            <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-[#a92d31] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </button>
                    </td>
                    <td class="text-center font-semibold text-gray-700">{{ $lic->available_seats }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <span class="font-bold font-heading text-xs">{{ $lic->utilization_percent }}%</span>
                            <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#a92d31]" style="width: {{ $lic->utilization_percent }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="{{ $lic->status_pill_class }}">{{ $lic->status }}</span>
                    </td>
                    <td class="text-center">
                        <button type="button" 
                                wire:click="openAssetModal({{ $lic->id }})" 
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded text-xs font-semibold bg-gray-50 hover:bg-[#a92d31] hover:text-white border border-gray-200 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>View Assets</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </x-table-section>

    <!-- Assigned Assets Modal for Selected License Title -->
    @if($showAssetModal && $selectedLicense)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl max-w-3xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b pb-3 shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-heading font-bold text-lg text-[#3b4048]">
                            Assigned Assets &bull; <span class="text-[#a92d31]">{{ $selectedLicense->software_name }}</span>
                        </h3>
                        <span class="{{ $selectedLicense->status_pill_class }} text-[10px]">{{ $selectedLicense->status }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Showing all equipment allocated this software license. Click any Asset ID to view its complete asset profile.
                    </p>
                </div>
                <button type="button" wire:click="closeAssetModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none p-1 cursor-pointer">
                    &times;
                </button>
            </div>

            @if(session()->has('modal_success'))
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-xs flex items-center justify-between">
                    <span>{{ session('modal_success') }}</span>
                </div>
            @endif

            <!-- Modal Search & License Summary -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0 bg-gray-50 p-3 rounded-xl border border-gray-200">
                <div class="relative flex-1 max-w-sm">
                    <input type="text"
                           wire:model.live.debounce.250ms="assetSearch"
                           placeholder="Filter by Asset ID, Device, Custodian, Dept..."
                           class="itam-input h-9 text-xs pl-8 pr-3 w-full bg-white">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div class="flex items-center gap-3 text-xs shrink-0">
                    <span class="text-gray-500 font-medium">
                        Showing <strong class="text-gray-900">{{ $assignedAssets->count() }}</strong> assigned device(s)
                    </span>
                    <span class="text-gray-300">&bull;</span>
                    <span class="text-gray-500">
                        Purchased: <strong class="text-gray-900">{{ $selectedLicense->purchased_seats }}</strong>
                    </span>
                </div>
            </div>

            <!-- Modal Assets Table -->
            <div class="border border-gray-200 rounded-xl overflow-y-auto flex-1 bg-white">
                <table class="w-full text-left itam-table">
                    <thead>
                        <tr>
                            <th>Asset ID</th>
                            <th>Asset Name</th>
                            <th>Custodian</th>
                            <th>Location</th>
                            <th>Status</th>
                            @if(auth()->user()?->hasPermission('software.manage'))
                            <th class="text-center w-20">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignedAssets as $ast)
                        <tr wire:key="lic-ast-{{ $ast->id }}">
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
                            @if(auth()->user()?->hasPermission('software.manage'))
                            <td class="text-center">
                                <button type="button" 
                                        wire:click="unlinkAsset('{{ $ast->id }}')"
                                        wire:confirm="Remove software license from {{ $ast->id }}?"
                                        class="p-1 text-gray-400 hover:text-red-600 rounded transition" 
                                        title="Remove license from this device">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()?->hasPermission('software.manage') ? '6' : '5' }}" class="text-center py-8 text-xs text-gray-400">
                                No devices found matching "{{ $assetSearch }}".
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Quick Assign Asset Section for Managers -->
            @if(auth()->user()?->hasPermission('software.manage') && $availableAssets->isNotEmpty())
            <div class="pt-3 border-t flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-2 w-full sm:w-auto flex-1">
                    <label class="text-xs font-bold text-gray-600 shrink-0">Assign to Asset:</label>
                    <select wire:model="newAssetId" class="itam-select h-9 text-xs flex-1">
                        <option value="">-- Choose asset from inventory --</option>
                        @foreach($availableAssets as $av)
                            <option value="{{ $av->id }}">
                                {{ $av->id }} - {{ $av->name }} ({{ $av->display_assigned_user ?: 'Available' }})
                            </option>
                        @endforeach
                    </select>
                    <button type="button" 
                            wire:click="assignAsset" 
                            class="btn-primary text-xs h-9 shrink-0 {{ empty($newAssetId) ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ empty($newAssetId) ? 'disabled' : '' }}>
                        Assign Asset
                    </button>
                </div>
                <button type="button" wire:click="closeAssetModal" class="btn-secondary text-xs h-9">
                    Done
                </button>
            </div>
            @else
            <div class="pt-3 border-t flex justify-end shrink-0">
                <button type="button" wire:click="closeAssetModal" class="btn-secondary text-xs">
                    Close
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
