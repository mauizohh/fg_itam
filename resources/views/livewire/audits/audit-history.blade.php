<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Audit History</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Review completed and active audit campaigns with summary results.</p>
        </div>
        <a href="{{ route('audits.new') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>New Audit</span>
        </a>
    </div>

    <!-- Filters Bar matching UI 22 -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search campaign..." class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Year</label>
            <select wire:model.live="yearFilter" class="itam-select text-xs h-10">
                <option value="2026">2026</option>
                <option value="2025">2025</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</label>
            <select wire:model.live="statusFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select>
        </div>
    </div>

    <!-- Table: Audit Campaigns matching UI 22 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Audit Campaigns</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Audit</th>
                        <th>Scope</th>
                        <th>Period</th>
                        <th>Verified</th>
                        <th>Exceptions</th>
                        <th>Accuracy</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($audits as $aud)
                    <tr>
                        <td class="font-bold text-[#a92d31] font-heading">{{ $aud->id }}</td>
                        <td class="font-bold text-gray-800">{{ $aud->scope }}</td>
                        <td class="text-xs text-gray-600">{{ $aud->period }}</td>
                        <td class="font-semibold text-gray-800">{{ $aud->verified_count }} / {{ $aud->total_in_scope }}</td>
                        <td class="{{ $aud->exceptions_count > 0 ? 'text-amber-600 font-bold' : 'text-gray-500' }}">
                            {{ $aud->exceptions_count }}
                        </td>
                        <td class="font-semibold text-emerald-700">
                            {{ $aud->accuracy_rate ? $aud->accuracy_rate . '%' : '-' }}
                        </td>
                        <td>
                            <span class="{{ $aud->status_pill_class }}">{{ $aud->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
