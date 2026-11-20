<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Assets in Repair</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Monitor devices that are unavailable due to repair or maintenance.</p>
        </div>
        @if (auth()->user()?->hasPermission('maintenance.manage'))
            <a href="{{ route('maintenance.repairs') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>New Repair Case</span>
            </a>
        @endif
    </div>

    <!-- 4 Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-[18px]">
        <x-stat-card
            title="Open Repairs"
            :value="$openRepairs"
            subtitle="Current"
        />
        <x-stat-card
            title="Overdue"
            :value="$overdueCount"
            subtitle="Past target date"
        />
        <x-stat-card
            title="Awaiting Quote"
            :value="$awaitingQuote"
            subtitle="Vendor response"
        />
        @if (auth()->user()?->canViewFinancialData())
            <x-stat-card
                title="Repair Spend"
                :value="'RM ' . number_format($repairSpend)"
                subtitle="This year"
            />
        @endif
    </div>

    <!-- Table: Repair Queue -->
    <x-table-section title="Repair Queue">
        <x-slot:badge>
            <strong class="text-primary-600">{{ count($repairs) }}</strong> active repair cases
        </x-slot:badge>

        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Asset ID</th>
                    <th>Asset</th>
                    <th>Issue</th>
                    <th>Vendor</th>
                    <th>Sent</th>
                    <th>Target Return</th>
                    <th class="text-center w-28">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($repairs as $rpr)
                <tr>
                    <td>
                        <a href="{{ route('assets.detail', $rpr->asset_id) }}" class="asset-id-link">
                            {{ $rpr->asset_id }}
                        </a>
                    </td>
                    <td>
                        @php $asset = \App\Models\Asset::find($rpr->asset_id); @endphp
                        <div class="table-primary-text">{{ $asset ? $asset->name : 'IT Equipment' }}</div>
                        @if($asset && $asset->serial_number)
                            <div class="table-subtext">{{ $asset->serial_number }}</div>
                        @endif
                    </td>
                    <td>{{ $rpr->issue }}</td>
                    <td>{{ $rpr->vendor }}</td>
                    <td class="table-date">{{ \Carbon\Carbon::parse($rpr->opened_date)->format('d M') }}</td>
                    <td class="table-date {{ $rpr->status === 'Overdue' ? 'text-red-600 font-semibold' : '' }}">
                        {{ $rpr->target_return_date ? \Carbon\Carbon::parse($rpr->target_return_date)->format('d M') : '-' }}
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center">
                            <span class="{{ $rpr->status_pill_class }}">{{ $rpr->status }}</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </x-table-section>
</div>
