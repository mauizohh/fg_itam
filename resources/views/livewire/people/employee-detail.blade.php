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
            :value="count($assignedAssets) ?: 3"
            subtitle="Current custody"
        />
        <x-stat-card
            title="Asset Value"
            :value="'RM ' . number_format($assetValue)"
            subtitle="Purchase value"
        />
        <x-stat-card
            title="Pending Returns"
            :value="$employee->pending_returns_count"
            subtitle="No outstanding items"
        />
        <x-stat-card
            title="Exceptions"
            :value="$employee->exceptions_count"
            subtitle="No audit issues"
        />
    </div>

    <!-- Assigned Equipment Table -->
    <x-table-section title="Assigned Equipment">
        <x-slot:actions>
            <a href="{{ route('transactions.return') }}?employee={{ urlencode($employee->name) }}" class="btn-primary">
                Start Return
            </a>
        </x-slot:actions>

        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Asset ID</th>
                    <th>Asset</th>
                    <th>Assigned Date</th>
                    <th>Condition</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignedAssets as $item)
                <tr>
                    <td>
                        <a href="{{ route('assets.detail', $item->id) }}" class="font-bold text-[#a92d31] hover:underline font-heading">
                            {{ $item->id }}
                        </a>
                    </td>
                    <td class="font-bold text-gray-800">{{ $item->name }}</td>
                    <td class="text-xs text-gray-600">{{ $item->assigned_date ? \Carbon\Carbon::parse($item->assigned_date)->format('d M Y') : '10 Aug 2026' }}</td>
                    <td>
                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                            {{ $item->condition ?: 'Good' }}
                        </span>
                    </td>
                    <td>
                        <span class="pill normal">Assigned</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td>
                        <a href="{{ route('assets.detail', 'IT-LAP-2026-00123') }}" class="font-bold text-[#a92d31] hover:underline font-heading">
                            IT-LAP-2026-00123
                        </a>
                    </td>
                    <td class="font-bold text-gray-800">Dell Latitude 7450</td>
                    <td class="text-xs text-gray-600">10 Aug 2026</td>
                    <td><span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">Good</span></td>
                    <td><span class="pill normal">Assigned</span></td>
                </tr>
                <tr>
                    <td>
                        <a href="{{ route('assets.detail', 'IT-MON-2026-00391') }}" class="font-bold text-[#a92d31] hover:underline font-heading">
                            IT-MON-2026-00391
                        </a>
                    </td>
                    <td class="font-bold text-gray-800">Dell 27 Monitor</td>
                    <td class="text-xs text-gray-600">10 Aug 2026</td>
                    <td><span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">Good</span></td>
                    <td><span class="pill normal">Assigned</span></td>
                </tr>
                <tr>
                    <td>
                        <a href="{{ route('assets.detail', 'IT-ACC-2026-00678') }}" class="font-bold text-[#a92d31] hover:underline font-heading">
                            IT-ACC-2026-00678
                        </a>
                    </td>
                    <td class="font-bold text-gray-800">USB-C Dock</td>
                    <td class="text-xs text-gray-600">10 Aug 2026</td>
                    <td><span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">Good</span></td>
                    <td><span class="pill normal">Assigned</span></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-section>
</div>
