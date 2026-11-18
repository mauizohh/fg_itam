<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-[#3b4048]">Asset Transaction History</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Read-only ledger of asset assignments, returns, transfers, and loans
                @if ($user->isDepartmentScoped())
                    &bull; Scoped to: <span class="font-semibold text-[#3b4048]">{{ implode(', ', $user->managedDepartments ?: $user->departments) }}</span>
                @elseif ($user->hasRole(\App\Enums\ItamRole::Employee))
                    &bull; Showing your personal equipment custody records
                @endif
            </p>
        </div>
        @if ($user->hasPermission('transactions.manage'))
            <div class="flex gap-2">
                <a href="{{ route('transactions.assign') }}" class="btn-primary text-xs">Assign Asset</a>
                <a href="{{ route('transactions.return') }}" class="btn-secondary text-xs">Return Asset</a>
            </div>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col sm:flex-row gap-3">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search by Asset ID, equipment, employee, or remarks..."
            class="itam-input flex-1 text-sm"
        />
        <select wire:model.live="typeFilter" class="itam-select w-full sm:w-48 text-sm">
            <option value="">All Transaction Types</option>
            <option value="Assign">Assign</option>
            <option value="Return">Return</option>
            <option value="Transfer">Transfer</option>
            <option value="Loan">Loan</option>
        </select>
    </div>

    <!-- Ledger Table -->
    <x-table-section title="Transaction Ledger" description="Verified chain-of-custody movement history">
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Asset</th>
                        <th>From</th>
                        <th>To / Dept</th>
                        <th>Location</th>
                        <th>Condition</th>
                        <th>Performed By</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $tx)
                        <tr>
                            <td class="text-xs font-mono text-gray-500">
                                {{ $tx->transaction_date ?? $tx->checkout_date ?? $tx->return_date ?? substr((string) $tx->created_at, 0, 10) }}
                            </td>
                            <td>
                                @php
                                    $pill = match ($tx->type) {
                                        'Assign' => 'pill assigned',
                                        'Loan' => 'pill loan',
                                        'Return' => 'pill available',
                                        default => 'pill normal',
                                    };
                                @endphp
                                <span class="{{ $pill }}">{{ $tx->type }}</span>
                            </td>
                            <td>
                                <div class="font-mono font-bold text-[#a92d31] text-xs">{{ $tx->asset_id }}</div>
                                <div class="text-xs text-gray-600">{{ $tx->asset_name ?? '—' }}</div>
                            </td>
                            <td class="text-xs text-gray-600">{{ $tx->from_user ? \App\Models\User::cleanDisplayName($tx->from_user) : 'Jitra Office' }}</td>
                            <td class="text-xs">
                                <div class="font-medium text-[#3b4048]">{{ $tx->to_user ? \App\Models\User::cleanDisplayName($tx->to_user) : 'Jitra Office' }}</div>
                                @if ($tx->to_department || $tx->asset_department)
                                    <div class="text-[11px] text-gray-400">{{ $tx->to_department ?: $tx->asset_department }}</div>
                                @endif
                            </td>
                            <td class="text-xs text-gray-600">{{ $tx->to_location ?: '—' }}</td>
                            <td class="text-xs text-gray-600">{{ $tx->condition_at_time ?: '—' }}</td>
                            <td class="text-xs text-gray-600">{{ $tx->performed_by ? \App\Models\User::cleanDisplayName($tx->performed_by) : 'System' }}</td>
                            <td class="text-xs text-gray-500 max-w-xs">{{ $tx->remarks ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-sm text-gray-400">
                                No transactions matched your filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </x-table-section>
</div>
