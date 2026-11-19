<?php

namespace App\Livewire\Transactions;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LoanAsset extends Component
{
    public string $assetId = '';

    public string $assetSearch = '';

    public string $borrower = 'Aisha Noor';

    public string $purpose = 'Client presentation';

    public string $checkoutDate = '';

    public string $dueDate = '';

    public string $condition = 'Good';

    public string $accessories = 'Charger';

    public string $notes = 'Temporary loan - not permanent assignment';

    public function mount(): void
    {
        $this->checkoutDate = now()->format('Y-m-d');
        $this->dueDate = now()->addDays(7)->format('Y-m-d');

        $requestedId = (string) request()->query('asset_id', '');
        if ($requestedId !== '') {
            $asset = Asset::find($requestedId);
            if ($asset) {
                $this->assetId = $asset->id;
                if ($asset->condition) {
                    $this->condition = $asset->condition;
                }
            }
        } else {
            $firstAvailable = Asset::where('status', 'Available')->orderBy('id')->first();
            if ($firstAvailable) {
                $this->assetId = $firstAvailable->id;
                if ($firstAvailable->condition) {
                    $this->condition = $firstAvailable->condition;
                }
            }
        }
    }

    public function updatedAssetId(string $value): void
    {
        $asset = Asset::find($value);
        if ($asset && $asset->condition) {
            $this->condition = $asset->condition;
        }
    }

    public function submit()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('transactions.manage'), 403);

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
                'performed_by' => $actor->display_name,
            ]);

            AuditLogService::record('Loan asset', 'Asset', $asset->id, 'Available', "Loaned to {$this->borrower} (Due: {$this->dueDate})");
        }

        session()->flash('success', "Asset {$this->assetId} checked out on temporary loan!");

        return redirect()->route('assets.index');
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('transactions.manage'), 403);

        $query = Asset::where('status', 'Available');
        if (trim($this->assetSearch) !== '') {
            $term = trim($this->assetSearch);
            $query->where(function ($sq) use ($term) {
                $sq->where('id', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('brand', 'like', "%{$term}%")
                    ->orWhere('model', 'like', "%{$term}%")
                    ->orWhere('serial_number', 'like', "%{$term}%");
            });
        }

        $assets = $query->orderBy('id')->get();
        $selectedAsset = $this->assetId ? Asset::find($this->assetId) : null;
        $employees = Employee::where('status', 'Active')->get();

        return view('livewire.transactions.loan-asset', compact('assets', 'selectedAsset', 'employees'))
            ->layout('layouts.app', ['title' => 'Loan Asset', 'breadcrumb' => 'IT Asset Management / Transactions / Loan Asset']);
    }
}
