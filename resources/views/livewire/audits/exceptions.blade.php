<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Exceptions</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Investigate discrepancies such as missing equipment, wrong user or wrong location.</p>
    </div>

    <!-- 4 Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-[18px]">
        <x-stat-card
            title="Open Exceptions"
            :value="$openExceptions"
            subtitle="Across audits"
        />
        <x-stat-card
            title="Missing"
            :value="$missingCount"
            subtitle="High priority"
        />
        <x-stat-card
            title="Wrong Location"
            :value="$wrongLocationCount"
            subtitle="Reconcile"
        />
        <x-stat-card
            title="Damaged"
            :value="$damagedCount"
            subtitle="Maintenance follow-up"
        />
    </div>

    <!-- Table: Open Exceptions -->
    <x-table-section title="Open Exceptions">
        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Asset ID</th>
                    <th>Exception</th>
                    <th>Expected</th>
                    <th>Observed</th>
                    <th>Audit</th>
                    <th>Owner</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exceptions as $ex)
                <tr>
                    <td>
                        <a href="{{ route('assets.detail', $ex->asset_id) }}" class="font-bold text-[#a92d31] hover:underline font-heading">
                            {{ $ex->asset_id }}
                        </a>
                    </td>
                    <td>
                        <span class="{{ $ex->pill_class }}">{{ $ex->exception_type }}</span>
                    </td>
                    <td>{{ $ex->expected_info }}</td>
                    <td class="font-semibold text-gray-700">{{ $ex->observed_info }}</td>
                    <td class="font-mono text-xs text-gray-500">{{ $ex->audit_id }}</td>
                    <td>{{ $ex->owner_department }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </x-table-section>
</div>
