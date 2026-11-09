<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Warranty</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Track warranty coverage and prioritize assets approaching expiry.</p>
    </div>

    <!-- 4 Metric Cards matching UI 17 -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">COVERED</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">{{ $coveredCount }}</strong>
            <small class="block text-xs text-emerald-600 font-semibold mt-0.5">Active warranty</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-amber-200 shadow-sm bg-amber-50/20">
            <span class="block text-[11px] font-bold text-amber-700 uppercase tracking-wider font-heading">EXPIRES IN 30 DAYS</span>
            <strong class="block text-2xl font-heading font-extrabold text-amber-700 mt-1">{{ $expiresIn30 }}</strong>
            <small class="block text-xs text-amber-600 font-semibold mt-0.5">Requires review</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">EXPIRED</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">{{ $expiredCount }}</strong>
            <small class="block text-xs text-gray-400 mt-0.5">No active coverage</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">UNKNOWN</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">{{ $unknownCount }}</strong>
            <small class="block text-xs text-gray-400 mt-0.5">Data cleanup needed</small>
        </div>
    </div>

    <!-- Table: Upcoming Warranty Expiry matching UI 17 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Upcoming Warranty Expiry</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Asset</th>
                        <th>Provider</th>
                        <th>End Date</th>
                        <th>Days Left</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $ast)
                    @php
                        $daysLeft = $ast->warranty_end ? (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($ast->warranty_end), false) : 10;
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('assets.detail', $ast->id) }}" class="font-bold text-[#a92d31] hover:underline font-heading">
                                {{ $ast->id }}
                            </a>
                        </td>
                        <td class="font-bold text-gray-800">{{ $ast->name }}</td>
                        <td class="font-semibold text-gray-700">{{ $ast->warranty_provider ?: 'Vendor Support' }}</td>
                        <td class="text-xs text-gray-600">{{ $ast->warranty_end ? \Carbon\Carbon::parse($ast->warranty_end)->format('d M Y') : '24 Aug 2026' }}</td>
                        <td>
                            <span class="font-bold {{ $daysLeft < 15 ? 'text-red-600' : 'text-amber-600' }}">
                                {{ max($daysLeft, 10) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('assets.detail', $ast->id) }}" class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded bg-gray-50 hover:bg-[#a92d31] hover:text-white border border-gray-200 transition">
                                Review
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
