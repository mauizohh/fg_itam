<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Licenses</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Track purchased, assigned and available software license quantities.</p>
    </div>

    <!-- 4 Metrics matching UI 19 -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">PURCHASED SEATS</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">{{ number_format($purchasedSeats) }}</strong>
            <small class="block text-xs text-gray-400 mt-0.5">Across managed titles</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">ASSIGNED</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">{{ number_format($assignedSeats) }}</strong>
            <small class="block text-xs text-emerald-600 font-semibold mt-0.5">87.3% utilization</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">AVAILABLE</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">{{ $availableSeats }}</strong>
            <small class="block text-xs text-gray-400 mt-0.5">Unassigned</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">OVERALLOCATED</span>
            <strong class="block text-2xl font-heading font-extrabold text-emerald-600 mt-1">{{ $overallocated }}</strong>
            <small class="block text-xs text-emerald-600 font-semibold mt-0.5">No compliance risk</small>
        </div>
    </div>

    <!-- License Utilization Table matching UI 19 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">License Utilization</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Software</th>
                        <th>Purchased</th>
                        <th>Assigned</th>
                        <th>Available</th>
                        <th>Utilization</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($licenses as $lic)
                    <tr>
                        <td class="font-bold text-gray-900">{{ $lic->software_name }}</td>
                        <td class="font-bold font-heading text-gray-800">{{ $lic->purchased_seats }}</td>
                        <td class="font-semibold text-gray-700">{{ $lic->assigned_seats }}</td>
                        <td class="font-semibold text-gray-700">{{ $lic->available_seats }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="font-bold font-heading text-xs">{{ $lic->utilization_percent }}%</span>
                                <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#a92d31]" style="width: {{ $lic->utilization_percent }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="{{ $lic->status_pill_class }}">{{ $lic->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
