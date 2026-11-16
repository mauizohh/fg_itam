<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Dashboard</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Executive overview of inventory, accountability, risk and upcoming actions.</p>
        </div>
        <div class="flex items-center gap-2">
            @if (auth()->user()?->hasPermission('assets.create'))
                <a href="{{ route('assets.index') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add Asset</span>
                </a>
            @endif
            @if (auth()->user()?->hasPermission('audits.scan'))
                <a href="{{ route('audits.new') }}" class="btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    <span>Audit Scan</span>
                </a>
            @endif
        </div>
    </div>

    <!-- 4 KPI Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[18px]">
        <x-stat-card
            title="Total Assets"
            :value="number_format($totalAssets)"
            subtitle="Within your authorized scope"
        />
        <x-stat-card
            title="Assigned"
            :value="number_format($assigned)"
            :subtitle="$assignedPercent . '% of total'"
        />
        <x-stat-card
            title="Available"
            :value="number_format($available)"
            subtitle="Ready for allocation"
        />
        <x-stat-card
            title="Attention Needed"
            :value="number_format($attentionNeeded)"
            subtitle="Repair, missing or damaged"
        />
    </div>

    <!-- Middle Split: Asset Status & Upcoming Actions -->
    @php
        $otherCount = max(0, $totalAssets - $assigned - $available - $repair);
        $otherPercent = $totalAssets > 0 ? max(0, round(100 - $assignedPercent - $availablePercent - $repairPercent, 1)) : 0;
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Asset Status Breakdown -->
        <div class="lg:col-span-2 bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading font-bold text-base text-[#3b4048]">Asset Status</h3>
                <span class="text-xs text-gray-400">Real-time health breakdown</span>
            </div>

            <!-- Multi-color Progress Bar -->
            <div class="h-4 w-full bg-gray-100 rounded-full overflow-hidden flex shadow-inner">
                <div style="width: {{ $assignedPercent }}%" class="bg-[#388bfd] h-full" title="Assigned: {{ $assigned }} ({{ $assignedPercent }}%)"></div>
                <div style="width: {{ $availablePercent }}%" class="bg-[#2ea043] h-full" title="Available: {{ $available }} ({{ $availablePercent }}%)"></div>
                <div style="width: {{ $repairPercent }}%" class="bg-[#d29922] h-full" title="Repair: {{ $repair }} ({{ $repairPercent }}%)"></div>
                <div style="width: {{ $otherPercent }}%" class="bg-[#64748b] h-full" title="Other: {{ $otherCount }} ({{ $otherPercent }}%)"></div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-4 border-t border-gray-100 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#388bfd]"></span>
                    <div>
                        <span class="text-gray-500 block">Assigned</span>
                        <strong class="text-gray-900 font-heading text-sm">{{ number_format($assigned) }}</strong>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#2ea043]"></span>
                    <div>
                        <span class="text-gray-500 block">Available</span>
                        <strong class="text-gray-900 font-heading text-sm">{{ number_format($available) }}</strong>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#d29922]"></span>
                    <div>
                        <span class="text-gray-500 block">In Repair</span>
                        <strong class="text-gray-900 font-heading text-sm">{{ number_format($repair) }}</strong>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#64748b]"></span>
                    <div>
                        <span class="text-gray-500 block">Other/Retired</span>
                        <strong class="text-gray-900 font-heading text-sm">{{ number_format($otherCount) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Actions -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <h3 class="font-heading font-bold text-base text-[#3b4048] mb-4">Quick Navigation</h3>
            <div class="space-y-3.5">
                @if (auth()->user()?->hasPermission('maintenance.view'))
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-red-100 text-[#a92d31] font-heading font-extrabold flex items-center justify-center text-xs">{{ $expiringWarranties }}</span>
                            <span class="text-xs text-gray-700 font-medium">Tracked warranty records</span>
                        </div>
                        <a href="{{ route('maintenance.warranty') }}" class="text-[11px] font-bold text-[#a92d31] hover:underline">View</a>
                    </div>
                @endif
                @if (auth()->user()?->hasPermission('transactions.manage'))
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 font-heading font-extrabold flex items-center justify-center text-xs">{{ $attentionNeeded }}</span>
                            <span class="text-xs text-gray-700 font-medium">Loan &amp; return workflows</span>
                        </div>
                        <a href="{{ route('transactions.loan') }}" class="text-[11px] font-bold text-[#a92d31] hover:underline">View</a>
                    </div>
                @endif
                @if (auth()->user()?->hasPermission('software.view'))
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 font-heading font-extrabold flex items-center justify-center text-xs">&rarr;</span>
                            <span class="text-xs text-gray-700 font-medium">Software renewals &amp; licenses</span>
                        </div>
                        <a href="{{ route('software.renewals') }}" class="text-[11px] font-bold text-[#a92d31] hover:underline">View</a>
                    </div>
                @endif
                @if (auth()->user()?->hasPermission('my_assets.view'))
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 font-heading font-extrabold flex items-center justify-center text-xs">&bull;</span>
                            <span class="text-xs text-gray-700 font-medium">My assigned equipment</span>
                        </div>
                        <a href="{{ route('my-assets') }}" class="text-[11px] font-bold text-[#a92d31] hover:underline">View</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Activity Table -->
    @if ($recentActivities->isNotEmpty())
        <x-table-section title="Recent Activity">
            @if (auth()->user()?->hasPermission('admin.audit_logs'))
                <x-slot:actions>
                    <a href="{{ route('admin.audit-logs') }}" class="text-xs font-bold text-[#a92d31] hover:underline">View All Logs</a>
                </x-slot:actions>
            @endif

            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th class="w-32">Time</th>
                        <th>Object</th>
                        <th>Action</th>
                        <th class="w-40">User</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentActivities as $activity)
                        <tr>
                            <td class="font-bold text-gray-700 text-xs">{{ \Carbon\Carbon::parse($activity->timestamp)->format('d M H:i') }}</td>
                            <td>
                                @if ($activity->object_type === 'Asset')
                                    <a href="{{ route('assets.detail', $activity->object_id) }}" class="text-[#a92d31] hover:underline font-bold font-heading">
                                        {{ $activity->object_id }}
                                    </a>
                                @else
                                    <span class="font-mono text-xs text-gray-700">{{ $activity->object_type }} #{{ $activity->object_id }}</span>
                                @endif
                            </td>
                            <td class="text-xs text-gray-700">{{ $activity->action }}@if($activity->after_state) &mdash; {{ $activity->after_state }}@endif</td>
                            <td><span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 font-semibold">{{ $activity->user }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-table-section>
    @endif
</div>
