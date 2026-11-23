<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Departments</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Summarize asset ownership, value and accountability by department.</p>
    </div>

    <!-- Top Department Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[18px]">
        @foreach($topDepartments as $dept)
        <x-stat-card
            :title="$dept->name"
            :value="$dept->active_assets_count . ' assets'"
            :subtitle="(auth()->user()?->canViewFinancialData() ? 'RM ' . number_format($dept->purchase_value) . ' &bull; ' : '') . $dept->exceptions_count . ' ' . Str::plural('exception', $dept->exceptions_count)"
        />
        @endforeach
    </div>

    <!-- Department Summary Table -->
    <x-table-section title="Department Summary">
        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Department</th>
                    <th class="text-center">Active Assets</th>
                    <th class="text-center">Available</th>
                    <th class="text-center">Repair</th>
                    <th class="text-center">Missing</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $dept)
                <tr>
                    <td class="font-bold text-gray-900">{{ $dept->name }}</td>
                    <td class="text-center font-bold font-heading text-gray-800">{{ $dept->active_assets_count }}</td>
                    <td class="text-center text-emerald-700 font-semibold">{{ $dept->available_count }}</td>
                    <td class="text-center text-amber-700 font-semibold">{{ $dept->repair_count }}</td>
                    <td class="text-center {{ $dept->missing_count > 0 ? 'text-red-600 font-bold' : 'text-gray-500' }}">{{ $dept->missing_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </x-table-section>
</div>
