<?php

namespace App\Livewire\Procurement;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PurchaseOrders extends Component
{
    public string $search = '';

    public string $statusFilter = 'Open';

    public string $supplierFilter = 'All';

    public bool $showModal = false;

    public string $newId = 'PO-2026-0091';

    public string $supplierName = 'TechSource';

    public string $requestedBy = 'IT Operations';

    public float $amount = 25000.00;

    public string $deliveryDate = '2026-08-25';

    public function createPo()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('procurement.manage'), 403);

        $this->validate([
            'newId' => 'required',
            'supplierName' => 'required',
            'amount' => 'required|numeric',
        ]);

        PurchaseOrder::create([
            'id' => $this->newId,
            'supplier_name' => $this->supplierName,
            'requested_by' => $this->requestedBy,
            'po_date' => now(),
            'amount' => $this->amount,
            'delivery_date' => $this->deliveryDate,
            'status' => 'Approved',
        ]);

        $this->showModal = false;
        session()->flash('success', "PO {$this->newId} created successfully!");
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('procurement.view'), 403);

        $query = PurchaseOrder::query();

        if (! empty($this->search)) {
            $query->where(function ($q): void {
                $q->where('id', 'like', "%{$this->search}%")
                    ->orWhere('supplier_name', 'like', "%{$this->search}%");
            });
        }
        if ($this->statusFilter === 'Open') {
            $query->whereIn('status', ['Approved', 'Ordered', 'Partial']);
        } elseif ($this->statusFilter !== 'All') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->supplierFilter !== 'All') {
            $query->where('supplier_name', $this->supplierFilter);
        }

        $orders = $query->orderByDesc('po_date')->get();
        $suppliers = Supplier::all();

        return view('livewire.procurement.purchase-orders', compact('orders', 'suppliers'))
            ->layout('layouts.app', ['title' => 'Purchase Orders', 'breadcrumb' => 'IT Asset Management / Procurement / Purchase Orders']);
    }
}
