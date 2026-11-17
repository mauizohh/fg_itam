<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Audit Logs</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Provide an immutable record of important user and system changes.</p>
    </div>

    <!-- Filters Bar matching UI 30 -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search log object or user..." class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">User</label>
            <select wire:model.live="userFilter" class="itam-select text-xs h-10">
                <option value="All">All Users</option>
                @foreach ($availableUsers as $uName)
                    <option value="{{ $uName }}">{{ $uName }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Action</label>
            <select wire:model.live="actionFilter" class="itam-select text-xs h-10">
                <option value="All">All Actions</option>
                @foreach ($availableActions as $act)
                    <option value="{{ $act }}">{{ $act }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Date</label>
            <select wire:model.live="dateFilter" class="itam-select text-xs h-10">
                <option value="Today">Today</option>
                <option value="This Week">This Week</option>
                <option value="All Time">All Time</option>
            </select>
        </div>
    </div>

    <!-- Table: System Activity Log -->
    <x-table-section title="System Activity Log">
        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Object</th>
                    <th>Before</th>
                    <th>After</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-xs text-gray-600 font-medium">
                        {{ \Carbon\Carbon::parse($log->timestamp)->format('d M H:i') }}
                    </td>
                    <td class="font-semibold text-gray-900">{{ $log->user }}</td>
                    <td>
                        <span class="font-bold text-gray-800">{{ $log->action }}</span>
                    </td>
                    <td>
                        @if ($log->object_type === 'Asset')
                            <a href="{{ route('assets.detail', $log->object_id) }}" class="font-bold text-[#a92d31] font-heading hover:underline">
                                {{ $log->object_id }}
                            </a>
                        @else
                            <span class="text-xs font-mono text-gray-700">{{ $log->object_type }} #{{ $log->object_id }}</span>
                        @endif
                    </td>
                    <td class="text-xs text-gray-500 font-mono">{{ $log->before_state ?: '-' }}</td>
                    <td class="text-xs text-gray-800 font-mono font-medium">{{ $log->after_state ?: '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-400">No activity logs recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <x-slot:pagination>
            {{ $logs->links() }}
        </x-slot:pagination>
    </x-table-section>
</div>
