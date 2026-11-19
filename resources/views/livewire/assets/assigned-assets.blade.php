<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Assigned Assets</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Track assets currently under employee, department or location custody.</p>
    </div>

    <!-- Filters Bar matching UI 04 -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Filter assigned assets..."
                class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Assigned To</label>
            <select wire:model.live="assignedToFilter" class="itam-select text-xs h-10">
                <option value="All">All Custodians</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->name }}">{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Department</label>
            <select wire:model.live="departmentFilter" class="itam-select text-xs h-10">
                <option value="All">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Location</label>
            <select wire:model.live="locationFilter" class="itam-select text-xs h-10">
                <option value="All">All Locations</option>
                @foreach (\App\Models\Location::OFFICE_OPTIONS as $office)
                    <option value="{{ $office }}">{{ $office }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Current Assignments Table -->
    <x-table-section title="Current Assignments">
        <x-slot:badge>
            <strong class="text-primary-600">{{ $assignments->total() }}</strong> active assignments
        </x-slot:badge>

        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Asset ID</th>
                    <th>Asset</th>
                    <th>Assigned To</th>
                    <th>Department</th>
                    <th>Assigned Date</th>
                    <th class="text-center w-28">Condition</th>
                    <th>Status</th>
                    <th class="text-center w-24">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $item)
                <tr>
                    <td>
                        <a href="{{ route('assets.detail', $item->id) }}" class="font-bold text-[#a92d31] hover:underline font-heading">
                            {{ $item->id }}
                        </a>
                    </td>
                    <td>
                        <div class="font-bold text-gray-800">{{ $item->name }}</div>
                        <div class="text-[11px] text-gray-400">{{ $item->serial_number }}</div>
                    </td>
                    <td class="font-semibold text-gray-800">{{ $item->display_assigned_user }}</td>
                    <td>{{ $item->department ?: 'General' }}</td>
                    <td class="text-xs text-gray-600">{{ $item->assigned_date ? \Carbon\Carbon::parse($item->assigned_date)->format('d M Y') : '10 Aug 2026' }}</td>
                    <td class="text-center">
                        <div class="flex items-center justify-center">
                            <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $item->condition === 'New' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $item->condition ?: 'Good' }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <span class="pill normal">Assigned</span>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center">
                            <x-table-dropdown>
                                <x-slot:trigger>
                                    <button type="button" class="kebab-btn" title="Actions">
                                        <x-icon name="ellipsis-vertical" class="w-5 h-5 text-gray-500 hover:text-gray-900" />
                                    </button>
                                </x-slot:trigger>
                                <x-table-dropdown.item href="{{ route('assets.detail', $item->id) }}" label="View Details" icon="eye" />
                                <x-table-dropdown.item href="{{ route('transactions.transfer') }}?asset_id={{ $item->id }}" label="Transfer" icon="arrows-right-left" />
                                <x-table-dropdown.item href="{{ route('transactions.return') }}?asset_id={{ $item->id }}" label="Return" icon="arrow-uturn-left" />
                            </x-table-dropdown>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-8 text-gray-400">
                        No assignments matching filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <x-slot:pagination>
            {{ $assignments->links() }}
        </x-slot:pagination>
    </x-table-section>
</div>
