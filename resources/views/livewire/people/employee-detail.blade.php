<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Employee Detail</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Show all equipment assigned to an employee and support onboarding or offboarding clearance.</p>
        </div>
        <a href="{{ route('people.employees') }}" class="btn-secondary">
            Back to Directory
        </a>
    </div>

    <!-- Employee Banner Card -->
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-xl font-heading font-extrabold text-gray-900">{{ $employee->name }}</h2>
            <div class="text-xs text-gray-500">{{ $employee->department }} &bull; {{ $employee->location }}</div>
        </div>
        <div>
            <span class="pill low font-bold">Active</span>
        </div>
    </div>

    <!-- 4 Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-[18px]">
        <x-stat-card
            title="Assigned Assets"
            :value="count($assignedAssets)"
            subtitle="Current custody"
        />
        @if (auth()->user()?->canViewFinancialData())
            <x-stat-card
                title="Asset Value"
                :value="'RM ' . number_format($assetValue)"
                subtitle="Purchase value"
            />
        @endif
        <x-stat-card
            title="Pending Returns"
            :value="$pendingReturnsCount"
            :subtitle="$pendingReturnsCount > 0 ? $pendingReturnsCount . ' return(s) due' : 'No outstanding items'"
        />
        <x-stat-card
            title="Exceptions"
            :value="$exceptionsCount"
            :subtitle="$exceptionsCount > 0 ? $exceptionsCount . ' issue(s) flagged' : 'No audit issues'"
        />
    </div>

    <!-- Assigned Equipment Table -->
    <x-table-section title="Assigned Equipment">
        <x-slot:actions>
            @if (count($assignedAssets) > 0)
                <a href="{{ route('transactions.return') }}?employee={{ urlencode($employee->name) }}&mode=all" class="btn-primary">
                    Return All
                </a>
            @else
                <button type="button" disabled class="btn-secondary opacity-50 cursor-not-allowed" title="No equipment currently in custody">
                    Return All
                </button>
            @endif
        </x-slot:actions>

        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Asset ID</th>
                    <th>Asset</th>
                    <th>Assigned Date</th>
                    <th class="text-center">Condition</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignedAssets as $item)
                <tr>
                    <td>
                        <a href="{{ route('assets.detail', $item->id) }}" class="asset-id-link">
                            {{ $item->id }}
                        </a>
                    </td>
                    <td>
                        <div class="table-primary-text">{{ $item->name }}</div>
                        <div class="table-subtext">{{ $item->serial_number }}</div>
                    </td>
                    <td class="table-date">{{ $item->assigned_date ? \Carbon\Carbon::parse($item->assigned_date)->format('d M Y') : '10 Aug 2026' }}</td>
                    <td class="text-center">
                        <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $item->condition === 'New' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $item->condition ?: 'Good' }}
                        </span>
                    </td>
                    <td>
                        <span class="{{ $item->status_pill_class }}">{{ $item->status }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-400">
                        No equipment currently assigned to this employee.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-section>

    <!-- Recent Activity Table for Employee Assets -->
    <x-table-section title="Recent Activity" description="Audited asset lifecycle events, assignments, transfers, and maintenance for {{ $employee->name }}">
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th class="w-28">Time</th>
                        <th>Asset</th>
                        <th>Action</th>
                        <th class="w-44">Action By</th>
                        <th class="w-44">Employee</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentActivities as $activity)
                        @php
                            $asset = $activityAssets->get($activity->object_id);
                            $employeeName = $activity->resolveEmployeeName($asset);
                        @endphp
                        <tr>
                            <td class="font-bold text-gray-700 text-xs whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($activity->timestamp)->format('d M H:i') }}
                            </td>
                            <td>
                                @if (auth()->user()?->hasPermission('assets.view'))
                                    <a href="{{ route('assets.detail', $activity->object_id) }}" class="text-[#a92d31] hover:underline font-bold font-heading">
                                        {{ $activity->object_id }}
                                    </a>
                                @else
                                    <span class="font-mono font-bold text-[#a92d31]">{{ $activity->object_id }}</span>
                                @endif
                                @if ($asset)
                                    <div class="text-[11px] text-gray-500 truncate max-w-xs">{{ $asset->name }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="font-semibold text-gray-800 text-xs block">{{ $activity->action }}</span>
                                @if ($activity->after_state)
                                    <span class="text-[11px] text-gray-500 font-mono block truncate max-w-sm">{{ $activity->after_state }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 font-semibold whitespace-nowrap">
                                    {{ $activity->clean_user }}
                                </span>
                            </td>
                            <td>
                                @if ($employeeName !== '—')
                                    <span class="font-medium text-gray-900 text-xs">{{ $employeeName }}</span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-xs text-gray-400">
                                No recent activity recorded for assets under this employee.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-table-section>
</div>
