<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-[#3b4048]">My Assigned Equipment</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                View assigned assets, acknowledge receipt, and track return obligations.
            </p>
        </div>
        @if ($user->hasPermission('transactions.history'))
            <a href="{{ route('transactions.history') }}" class="btn-secondary inline-flex items-center gap-2 text-xs">
                View My Transaction History
            </a>
        @endif
    </div>

    @if (session()->has('success'))
        <div role="status" aria-live="polite" class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-[18px]">
        <x-stat-card
            title="Assigned Items"
            :value="number_format($myAssets->count())"
            subtitle="Equipment under custody"
        />
        <x-stat-card
            title="Acknowledged"
            :value="number_format($acknowledgements->count())"
            :subtitle="$myAssets->count() > 0 ? round(($acknowledgements->count() / $myAssets->count()) * 100) . '% confirmed' : 'All clear'"
        />
        <x-stat-card
            title="Return Obligations"
            :value="number_format($returnObligationsCount)"
            :subtitle="$returnObligationsCount > 0 ? 'Action required upon due date' : 'No overdue loans'"
        />
    </div>

    <!-- Assigned Equipment Table -->
    <x-table-section title="Equipment Under Your Custody" description="Acknowledge receipt to confirm custody and equipment return terms">
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Equipment</th>
                        <th>Serial No.</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Return</th>
                        <th class="text-center">Acknowledgement</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($myAssets as $asset)
                        @php
                            $ack = $acknowledgements->get($asset->id);
                        @endphp
                        <tr>
                            <td class="font-mono font-bold text-[#a92d31]">{{ $asset->id }}</td>
                            <td>
                                <div class="font-semibold text-[#3b4048]">{{ $asset->name }}</div>
                                <div class="text-xs text-gray-400">{{ $asset->brand }} {{ $asset->model }} &bull; {{ $asset->category_name }}</div>
                                @if (! empty($asset->accessories))
                                    <div class="text-[11px] text-gray-500 mt-0.5">Accessories: {{ $asset->accessories }}</div>
                                @endif
                            </td>
                            <td class="font-mono text-xs text-gray-600">{{ $asset->serial_number ?: '—' }}</td>
                            <td>
                                @php
                                    $pillClass = match (strtolower($asset->status)) {
                                        'assigned' => 'pill assigned',
                                        'loan' => 'pill loan',
                                        default => 'pill normal',
                                    };
                                @endphp
                                <span class="{{ $pillClass }}">{{ $asset->status }}</span>
                            </td>
                            <td class="text-xs text-gray-600">{{ $asset->assigned_location ?: 'Jitra Office' }}</td>
                            <td class="text-xs text-gray-600">
                                @if ($asset->expected_return_date)
                                    {{ $asset->expected_return_date }}
                                @elseif ($asset->status === 'Loan')
                                    Temporary Loan Return Required
                                @else
                                    Return upon offboarding / role transfer
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($ack)
                                    <div class="flex flex-col items-center justify-center gap-1" title="Acknowledged on {{ $ack->acknowledged_at?->format('Y-m-d H:i') }}">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 shadow-2xs shrink-0" title="Acknowledged">
                                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </span>
                                        <span class="text-[11px] text-gray-600 font-mono whitespace-nowrap">{{ $ack->acknowledged_at?->format('Y-m-d H:i') }}</span>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center gap-1" title="Pending Receipt Acknowledgement">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-100 text-red-700 shadow-2xs shrink-0" title="Not Acknowledged">
                                            <svg class="w-3.5 h-3.5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </span>
                                        <span class="text-xs font-semibold text-red-600">Pending</span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-right">
                                @if ($ack)
                                    <button type="button" wire:click="openAcknowledge('{{ $asset->id }}')" class="text-xs font-semibold text-gray-500 hover:text-[#a92d31]">
                                        Update Note
                                    </button>
                                @else
                                    <button type="button" wire:click="openAcknowledge('{{ $asset->id }}')" class="btn-primary text-xs px-3 py-1.5">
                                        Acknowledge Receipt
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-sm text-gray-400">
                                No active IT equipment is currently assigned under your name.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-table-section>

    <!-- Acknowledgement Modal -->
    @if ($selectedAssetId)
        <div class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="ack-modal-title">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-md p-6 space-y-4">
                <div>
                    <h2 id="ack-modal-title" class="text-lg font-heading font-bold text-[#3b4048]">Acknowledge Equipment Receipt</h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Confirm custody of <span class="font-mono font-bold text-[#a92d31]">{{ $selectedAssetId }}</span> and agree to return it in good condition upon offboarding or due date.
                    </p>
                </div>

                <div>
                    <label for="ack-remarks" class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Remarks (Optional)</label>
                    <textarea id="ack-remarks" wire:model="remarks" rows="3" placeholder="e.g. Received laptop and USB-C charger in good working order" class="itam-input w-full text-sm"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" wire:click="cancelAcknowledge" class="btn-secondary text-xs">Cancel</button>
                    <button type="button" wire:click="acknowledge('{{ $selectedAssetId }}')" class="btn-primary text-xs">Confirm &amp; Acknowledge</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Activity Table (Asset monitoring for employee) -->
    <x-table-section title="Recent Activity" description="Audited asset lifecycle events, assignments, transfers, and maintenance on your equipment">
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th class="w-28">Time</th>
                        <th>Asset</th>
                        <th>Action</th>
                        <th class="w-44">Action By</th>
                        <th class="w-44">Employee</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentActivities as $activity)
                        @php
                            $asset = $activityAssets->get($activity->object_id);
                            $employeeName = $activity->resolveEmployeeName($asset);
                        @endphp
                        <tr>
                            <td class="font-bold text-gray-700 text-xs whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($activity->timestamp)->format('d M H:i') }}
                            </td>
                            <td>
                                @if (auth()->user()?->hasPermission('assets.view'))
                                    <a href="{{ route('assets.detail', $activity->object_id) }}" class="text-[#a92d31] hover:underline font-bold font-heading">
                                        {{ $activity->object_id }}
                                    </a>
                                @else
                                    <span class="font-mono font-bold text-[#a92d31]">{{ $activity->object_id }}</span>
                                @endif
                                @if ($asset)
                                    <div class="text-[11px] text-gray-500 truncate max-w-xs">{{ $asset->name }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="font-semibold text-gray-800 text-xs block">{{ $activity->action }}</span>
                                @if ($activity->after_state)
                                    <span class="text-[11px] text-gray-500 font-mono block truncate max-w-sm">{{ $activity->after_state }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 font-semibold whitespace-nowrap">
                                    {{ $activity->clean_user }}
                                </span>
                            </td>
                            <td>
                                @if ($employeeName !== '—')
                                    <span class="font-medium text-gray-900 text-xs">{{ $employeeName }}</span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-xs text-gray-400">
                                No recent activity recorded for your assigned equipment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-table-section>

    <!-- Recent Custody History -->
    <x-table-section title="My Recent Equipment Transactions" description="Check-in, assignment, loan, and transfer activity involving you">
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Asset ID</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($myTransactions as $tx)
                        <tr>
                            <td class="text-xs text-gray-500 font-mono">{{ $tx->transaction_date ?? $tx->checkout_date ?? $tx->return_date ?? '—' }}</td>
                            <td><span class="pill normal">{{ $tx->type }}</span></td>
                            <td class="font-mono font-semibold text-[#a92d31]">{{ $tx->asset_id }}</td>
                            <td class="text-xs text-gray-600">{{ $tx->from_user ? \App\Models\User::cleanDisplayName($tx->from_user) : 'Jitra Office' }}</td>
                            <td class="text-xs text-gray-700 font-medium">{{ $tx->to_user ? \App\Models\User::cleanDisplayName($tx->to_user) : ($tx->to_location ?: 'Jitra Office') }}</td>
                            <td class="text-xs text-gray-500">{{ $tx->remarks ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-xs text-gray-400">No transaction history recorded for your account yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-table-section>
</div>
