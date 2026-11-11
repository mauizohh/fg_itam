<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Warranty</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Track warranty coverage and prioritize assets approaching expiry.</p>
    </div>

    <!-- 4 Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-[18px]">
        <x-stat-card
            title="Covered"
            :value="$coveredCount"
            subtitle="Active warranty"
        />
        <x-stat-card
            title="Expires in 30 Days"
            :value="$expiresIn30"
            subtitle="Requires review"
        />
        <x-stat-card
            title="Expired"
            :value="$expiredCount"
            subtitle="No active coverage"
        />
        <x-stat-card
            title="Unknown"
            :value="$unknownCount"
            subtitle="Data cleanup needed"
        />
    </div>

    <!-- Table: Upcoming Warranty Expiry -->
    <x-table-section title="Upcoming Warranty Expiry">
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
    </x-table-section>
</div>
