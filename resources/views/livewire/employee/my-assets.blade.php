<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-[#3b4048]">My Assigned Equipment</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Signed in as <span class="font-semibold text-[#3b4048]">{{ $user->name }}</span> ({{ $user->primary_department }})
                &bull; View assigned assets, acknowledge receipt, and track return obligations.
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Assigned Items</span>
            <span class="text-2xl font-heading font-bold text-[#3b4048] mt-1">{{ $myAssets->count() }}</span>
        </div>
        <div class="stat-card">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Acknowledged</span>
            <span class="text-2xl font-heading font-bold text-emerald-700 mt-1">{{ $acknowledgements->count() }}</span>
        </div>
        <div class="stat-card">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Return Obligations</span>
            <span class="text-2xl font-heading font-bold text-amber-600 mt-1">{{ $returnObligationsCount }}</span>
        </div>
    </div>

    <!-- Assigned Equipment Table -->
    <x-table-section title="Equipment Under Your Custody" description="Acknowledge receipt to confirm custody and equipment return terms">
        <div class="overflow-x-auto">
            <table class="itam-table">
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Equipment</th>
                        <th>Serial No.</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Return Obligation</th>
                        <th>Acknowledgement</th>
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
                            <td class="text-xs text-gray-600">{{ $asset->assigned_location ?: 'KL Office' }}</td>
                            <td class="text-xs">
                                @if ($asset->expected_return_date)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 font-medium">
                                        Due {{ $asset->expected_return_date }}
                                    </span>
                                @elseif ($asset->status === 'Loan')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-medium">
                                        Temporary Loan Return Required
                                    </span>
                                @else
                                    <span class="text-gray-500">Return upon offboarding / role transfer</span>
                                @endif
                            </td>
                            <td>
                                @if ($ack)
                                    <span class="pill normal">Acknowledged</span>
                                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $ack->acknowledged_at?->format('Y-m-d H:i') }}</div>
                                @else
                                    <span class="pill high">Pending</span>
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
                                No active IT equipment is currently assigned under your name ({{ $user->name }}).
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

    <!-- Recent Custody History -->
    <x-table-section title="My Recent Equipment Transactions" description="Check-in, assignment, loan, and transfer activity involving you">
        <div class="overflow-x-auto">
            <table class="itam-table">
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
                            <td class="text-xs text-gray-600">{{ $tx->from_user ?: 'IT Store' }}</td>
                            <td class="text-xs text-gray-700 font-medium">{{ $tx->to_user ?: ($tx->to_location ?: 'IT Store') }}</td>
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
