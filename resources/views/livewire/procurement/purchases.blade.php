<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Purchases</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Track received purchases, invoice details and asset creation progress.</p>
    </div>

    <!-- 4 Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-[18px]">
        <x-stat-card
            title="YTD Purchases"
            value="RM 1.84M"
            subtitle="IT assets and software"
        />
        <x-stat-card
            title="This Month"
            value="RM 146K"
            subtitle="Approved purchases"
        />
        <x-stat-card
            title="Assets Pending Register"
            value="18"
            subtitle="Received but not tagged"
        />
        <x-stat-card
            title="Invoices Pending"
            value="6"
            subtitle="Finance review"
        />
    </div>

    <!-- Purchase Register Table -->
    <x-table-section title="Purchase Register">
        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Purchase</th>
                    <th>Supplier</th>
                    <th>Invoice</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Assets</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchases as $p)
                <tr>
                    <td class="font-bold text-[#a92d31] font-heading">{{ $p->id }}</td>
                    <td class="font-semibold text-gray-900">{{ $p->supplier_name }}</td>
                    <td class="font-mono text-xs text-gray-600">{{ $p->invoice_number }}</td>
                    <td class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($p->purchase_date)->format('d M') }}</td>
                    <td class="font-bold font-heading text-gray-800">RM {{ number_format($p->amount) }}</td>
                    <td>{{ $p->assets_description }}</td>
                    <td>
                        <span class="pill {{ $p->status === 'Received' ? 'high' : 'low' }}">{{ $p->status }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </x-table-section>
</div>
