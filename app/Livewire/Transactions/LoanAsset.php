<?php

namespace App\Livewire\Transactions;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\Transaction;
use App\Services\AuditLogService;
use Livewire\Component;

class LoanAsset extends Component
{
    public string $assetId = 'IT-LAP-2026-00418';
    public string $borrower = 'Aisha Noor';
    public string $purpose = 'Client presentation';
    public string $checkoutDate = '2026-08-14';
    public string $dueDate = '2026-08-16';
    public string $condition = 'Good';
    public string $accessories = 'Charger';
    public string $notes = 'Temporary loan - not permanent assignment';

    public function submit()
    {
        $this->validate([
            'assetId' => 'required',
            'borrower' => 'required',
            'dueDate' => 'required',
        ]);

        $asset = Asset::find($this->assetId);
        if ($asset) {
            $asset->update([
                'status' => 'Loan',
                'assigned_user' => $this->borrower,
                'expected_return_date' => $this->dueDate,
                'remarks' => "Loan: {$this->purpose}",
            ]);

            Transaction::create([
                'type' => 'Loan',
                'asset_id' => $asset->id,
                'to_user' => $this->borrower,
                'checkout_date' => $this->checkoutDate,
                'due_date' => $this->dueDate,
                'condition_at_time' => $this->condition,
                'accessories' => $this->accessories,
                'remarks' => $this->notes,
                'performed_by' => 'Support',
            ]);

            AuditLogService::record('Loan asset', 'Asset', $asset->id, 'Available', "Loaned to {$this->borrower} (Due: {$this->dueDate})");
        }

        session()->flash('success', "Asset {$this->assetId} checked out on temporary loan!");
        return redirect()->route('assets.index');
    }

    public function render()
    {
        $assets = Asset::all();
        $employees = Employee::where('status', 'Active')->get();

        return view('livewire.transactions.loan-asset', compact('assets', 'employees'))
            ->layout('layouts.app', ['title' => 'Loan Asset', 'breadcrumb' => 'IT Asset Management / Transactions / Loan Asset']);
    }
}
