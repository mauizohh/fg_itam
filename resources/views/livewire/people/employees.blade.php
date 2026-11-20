<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Employees</h1>
            <p class="text-xs text-[#69717d] mt-0.5">View employee custody and identify all equipment assigned to each person.</p>
        </div>
    </div>

    <!-- Filters Bar matching UI 07 -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search employee..."
                class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Department</label>
            <select wire:model.live="departmentFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</label>
            <select wire:model.live="statusFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="Active">Active</option>
                <option value="Offboarded">Offboarded</option>
            </select>
        </div>
    </div>

    <!-- Employee Directory Table -->
    <x-table-section title="Employee Directory">
        <x-slot:badge>
            <strong class="text-primary-600">{{ $employees->total() }}</strong> staff members
        </x-slot:badge>

        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Location</th>
                    <th>Assigned Assets</th>
                    <th>Pending Returns</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                <tr>
                    <td class="table-primary-text">{{ $emp->name }}</td>
                    <td>{{ $emp->department }}</td>
                    <td>{{ $emp->location }}</td>
                    <td class="font-bold text-gray-800">{{ $emp->assigned_assets_count }}</td>
                    <td>
                        <span class="{{ $emp->pending_returns_count > 0 ? 'text-amber-600 font-bold' : 'text-gray-500' }}">
                            {{ $emp->pending_returns_count }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('people.employee.detail', $emp->id) }}" class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded bg-gray-50 hover:bg-[#a92d31] hover:text-white border border-gray-200 transition">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-400">
                        No employees found matching filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <x-slot:pagination>
            {{ $employees->links() }}
        </x-slot:pagination>
    </x-table-section>
</div>
