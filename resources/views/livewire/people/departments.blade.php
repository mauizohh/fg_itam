<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Departments</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Summarize asset ownership, value and accountability by department.</p>
    </div>

    <!-- 4 Department Metric Cards matching UI 08 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($departments as $dept)
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition">
            <h3 class="font-heading font-bold text-sm text-[#a92d31]">{{ $dept->name }}</h3>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-2">{{ $dept->active_assets_count }} assets</strong>
            <div class="text-xs text-gray-500 font-semibold mt-1">RM {{ number_format($dept->purchase_value) }} purchase value</div>
            <div class="text-[11px] font-bold text-amber-600 mt-1">{{ $dept->exceptions_count }} {{ Str::plural('exception', $dept->exceptions_count) }}</div>
        </div>
        @endforeach
    </div>

    <!-- Department Summary Table matching UI 08 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Department Summary</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Owner</th>
                        <th>Active Assets</th>
                        <th>Available</th>
                        <th>Repair</th>
                        <th>Missing</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departments as $dept)
                    <tr>
                        <td class="font-bold text-gray-900">{{ $dept->name }}</td>
                        <td class="font-semibold text-gray-700">{{ $dept->owner_title }}</td>
                        <td class="font-bold font-heading text-gray-800">{{ $dept->active_assets_count }}</td>
                        <td class="text-emerald-700 font-semibold">{{ $dept->available_count }}</td>
                        <td class="text-amber-700 font-semibold">{{ $dept->repair_count }}</td>
                        <td class="{{ $dept->missing_count > 0 ? 'text-red-600 font-bold' : 'text-gray-500' }}">{{ $dept->missing_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
