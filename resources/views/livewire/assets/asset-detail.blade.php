<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Asset Detail</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Single source of truth for an individual asset, including identity, custody, lifecycle and history.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('transactions.assign') }}?asset_id={{ $asset->id }}" class="btn-primary">
                Assign / Transfer
            </a>
            <a href="{{ route('assets.index') }}" class="btn-secondary">
                Back to Register
            </a>
        </div>
    </div>

    <!-- Master Header Card matching UI 31 -->
    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-[#a92d31] font-heading">{{ $asset->id }}</span>
            <h2 class="text-2xl font-heading font-black text-gray-900 mt-0.5">{{ $asset->name }}</h2>
            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 mt-1 font-mono">
                <span>Serial: <strong>{{ $asset->serial_number }}</strong></span>
                <span>&bull;</span>
                <span>Hostname: <strong>{{ $asset->hostname ?: 'FIN-LT-023' }}</strong></span>
                @if($asset->mac_address)
                    <span>&bull;</span>
                    <span>MAC: {{ $asset->mac_address }}</span>
                @endif
                @if($asset->ip_address)
                    <span>&bull;</span>
                    <span>IP: {{ $asset->ip_address }}</span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="{{ $asset->status_pill_class }} text-xs px-3 py-1">{{ $asset->status }}</span>
        </div>
    </div>

    <!-- 3 Detailed Group Panels matching UI 31 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Panel 1: Custody -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-sm text-[#3b4048] pb-2 border-b border-gray-100 flex items-center justify-between">
                <span>Custody</span>
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            </h3>
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-gray-400 block text-[11px]">Assigned To</span>
                    <strong class="text-gray-900 text-sm font-semibold">{{ $asset->assigned_user ?: 'Unassigned' }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Department</span>
                    <span class="text-gray-800 font-medium">{{ $asset->department ?: 'Finance' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Location</span>
                    <span class="text-gray-800 font-medium">{{ $asset->assigned_location ?: 'KL Office' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Assigned Date</span>
                    <span class="text-gray-800 font-medium">{{ $asset->assigned_date ? \Carbon\Carbon::parse($asset->assigned_date)->format('d M Y') : '10 Aug 2026' }}</span>
                </div>
            </div>
        </div>

        <!-- Panel 2: Lifecycle -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-sm text-[#3b4048] pb-2 border-b border-gray-100 flex items-center justify-between">
                <span>Lifecycle</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </h3>
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-gray-400 block text-[11px]">Purchase Date</span>
                    <span class="text-gray-800 font-medium">{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d M Y') : '01 Aug 2026' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Purchase Cost</span>
                    @if (auth()->user()?->canViewFinancialData())
                        <strong class="text-gray-900 text-sm font-semibold">RM {{ number_format($asset->purchase_cost ?: 5200, 2) }}</strong>
                    @else
                        <span class="text-xs text-gray-400 italic">Restricted</span>
                    @endif
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Warranty End</span>
                    <span class="text-gray-800 font-medium">{{ $asset->warranty_end ? \Carbon\Carbon::parse($asset->warranty_end)->format('d M Y') : '01 Aug 2029' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Replacement</span>
                    <span class="text-gray-800 font-medium">{{ $asset->replacement_due ?: 'Aug 2030' }}</span>
                </div>
            </div>
        </div>

        <!-- Panel 3: Tracking & Verification -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-sm text-[#3b4048] pb-2 border-b border-gray-100 flex items-center justify-between">
                <span>Tracking</span>
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            </h3>
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-gray-400 block text-[11px]">Digital Identity</span>
                    <span class="text-gray-800 font-medium">Serial + hostname</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Last Seen</span>
                    <span class="text-emerald-700 font-semibold">Today 10:30 (Endpoint inventory)</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Last Physical Audit</span>
                    <span class="text-gray-800 font-medium">{{ $asset->last_physical_verification ? \Carbon\Carbon::parse($asset->last_physical_verification)->format('d M Y') : '15 Jun 2026' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[11px]">Verification Status</span>
                    <span class="pill low">Confirmed</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset Timeline matching UI 31 -->
    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
        <h3 class="font-heading font-bold text-base text-[#3b4048] mb-4">Asset Timeline</h3>
        <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200">
            <div class="relative">
                <span class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-[#a92d31] ring-4 ring-red-100"></span>
                <div class="text-xs font-bold text-gray-900">10 Aug 2026</div>
                <div class="text-xs text-gray-600 mt-0.5">Assigned to Sarah Lim</div>
            </div>
            <div class="relative">
                <span class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-gray-400 ring-4 ring-gray-100"></span>
                <div class="text-xs font-bold text-gray-900">02 Aug 2026</div>
                <div class="text-xs text-gray-600 mt-0.5">Registered by IT Operations (Serial DL7450-93821 recorded)</div>
            </div>
            <div class="relative">
                <span class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-gray-400 ring-4 ring-gray-100"></span>
                <div class="text-xs font-bold text-gray-900">01 Aug 2026</div>
                <div class="text-xs text-gray-600 mt-0.5">Received from supplier (TechSource Sdn Bhd, INV-88301)</div>
            </div>
        </div>
    </div>
</div>
