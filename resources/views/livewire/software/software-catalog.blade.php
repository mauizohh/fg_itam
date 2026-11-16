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
                    <th>Managed Devices</th>
                    <th>Owner</th>
                    <th>Status</th>
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
                    <td class="font-bold font-heading text-gray-800">{{ $s->managed_devices_count }}</td>
                    <td>{{ $s->owner_department }}</td>
                    <td><span class="pill low">Active</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </x-table-section>

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
