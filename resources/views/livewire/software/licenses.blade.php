<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Licenses</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Track purchased, assigned and available software license quantities.</p>
    </div>

    <!-- 4 Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-[18px]">
        <x-stat-card
            title="Purchased Seats"
            :value="number_format($purchasedSeats)"
            subtitle="Across managed titles"
        />
        <x-stat-card
            title="Assigned"
            :value="number_format($assignedSeats)"
            subtitle="87.3% utilization"
        />
        <x-stat-card
            title="Available"
            :value="$availableSeats"
            subtitle="Unassigned"
        />
        <x-stat-card
            title="Overallocated"
            :value="$overallocated"
            subtitle="No compliance risk"
        />
    </div>

    <!-- License Utilization Table -->
    <x-table-section title="License Utilization">
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
    </x-table-section>
</div>
