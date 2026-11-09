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
                <option value="KL Office">KL Office</option>
                <option value="Server Room">Server Room</option>
                <option value="Penang Office">Penang Office</option>
            </select>
        </div>
    </div>

    <!-- Current Assignments Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Current Assignments</h3>
            <span class="text-xs text-gray-400">{{ $assignments->total() }} active assignments</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Asset</th>
                        <th>Assigned To</th>
                        <th>Department</th>
                        <th>Assigned Date</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
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
                        <td class="font-semibold text-gray-800">{{ $item->assigned_user }}</td>
                        <td>{{ $item->department ?: 'General' }}</td>
                        <td class="text-xs text-gray-600">{{ $item->assigned_date ? \Carbon\Carbon::parse($item->assigned_date)->format('d M Y') : '10 Aug 2026' }}</td>
                        <td>
                            <span class="pill normal">Assigned</span>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('transactions.return') }}?asset_id={{ $item->id }}" class="text-xs font-semibold px-2.5 py-1 rounded border border-gray-200 hover:bg-red-50 text-gray-700 hover:text-[#a92d31] transition">
                                    Return
                                </a>
                                <a href="{{ route('transactions.transfer') }}?asset_id={{ $item->id }}" class="text-xs font-semibold px-2.5 py-1 rounded border border-gray-200 hover:bg-gray-100 text-gray-700 transition">
                                    Transfer
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-400">
                            No assignments matching filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $assignments->links() }}
        </div>
    </div>
</div>
