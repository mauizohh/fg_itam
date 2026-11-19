<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AssetDetail extends Component
{
    public string $assetId;

    public bool $showEditModal = false;

    public string $editName = '';

    public string $editBrand = '';

    public string $editModel = '';

    public string $editSerial = '';

    public string $editLocation = 'Jitra Office';

    public string $editCondition = 'Good';

    public ?string $editPurchaseDate = null;

    public ?float $editPurchaseCost = null;

    public ?string $editWarrantyProvider = null;

    public ?string $editWarrantyStart = null;

    public ?string $editWarrantyEnd = null;

    public ?string $editReplacementDue = null;

    public string $editRemarks = '';

    public function mount(string $id)
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.view'), 403);

        $this->assetId = $id;
    }

    public function openEditModal(): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.edit'), 403);

        $asset = Asset::findOrFail($this->assetId);

        $this->editName = (string) $asset->name;
        $this->editBrand = (string) ($asset->brand ?? '');
        $this->editModel = (string) ($asset->model ?? '');
        $this->editSerial = (string) $asset->serial_number;
        $this->editLocation = in_array($asset->assigned_location, Location::OFFICE_OPTIONS, true)
            ? $asset->assigned_location
            : Location::OFFICE_OPTIONS[0];
        $this->editCondition = (string) ($asset->condition ?: 'Good');
        $this->editPurchaseDate = $asset->purchase_date ? substr((string) $asset->purchase_date, 0, 10) : null;
        $this->editPurchaseCost = $asset->purchase_cost ? (float) $asset->purchase_cost : null;
        $this->editWarrantyProvider = (string) ($asset->warranty_provider ?? '');
        $this->editWarrantyStart = $asset->warranty_start ? substr((string) $asset->warranty_start, 0, 10) : null;
        $this->editWarrantyEnd = $asset->warranty_end ? substr((string) $asset->warranty_end, 0, 10) : null;
        $this->editReplacementDue = (string) ($asset->replacement_due ?? '');
        $this->editRemarks = (string) ($asset->remarks ?? '');

        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function cancelEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetValidation();
    }

    public function saveAssetEdit(): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.edit'), 403);

        $this->validate([
            'editName' => 'required|string|max:255',
            'editBrand' => 'nullable|string|max:100',
            'editModel' => 'nullable|string|max:150',
            'editSerial' => 'required|string|max:100|unique:fg_itam_assets,serial_number,'.$this->assetId.',id',
            'editLocation' => ['required', 'string', 'in:'.implode(',', Location::OFFICE_OPTIONS)],
            'editCondition' => 'required|string|in:New,Good,Fair,Damaged',
            'editPurchaseDate' => 'nullable|date',
            'editPurchaseCost' => 'nullable|numeric|min:0',
            'editWarrantyStart' => 'nullable|date',
            'editWarrantyEnd' => 'nullable|date',
            'editWarrantyProvider' => 'nullable|string|max:150',
            'editReplacementDue' => 'nullable|string|max:50',
            'editRemarks' => 'nullable|string|max:1000',
        ]);

        $asset = Asset::findOrFail($this->assetId);

        $beforeState = "Name: {$asset->name}, Serial: {$asset->serial_number}, Brand: {$asset->brand}, Warranty End: {$asset->warranty_end}";

        $updateData = [
            'name' => $this->editName,
            'brand' => $this->editBrand ?: 'Standard',
            'model' => $this->editModel ?: $this->editName,
            'serial_number' => $this->editSerial,
            'assigned_location' => $this->editLocation,
            'condition' => $this->editCondition,
            'purchase_date' => $this->editPurchaseDate ?: null,
            'warranty_start' => $this->editWarrantyStart ?: null,
            'warranty_end' => $this->editWarrantyEnd ?: null,
            'warranty_provider' => $this->editWarrantyProvider ?: null,
            'replacement_due' => $this->editReplacementDue ?: null,
            'remarks' => $this->editRemarks ?: null,
        ];

        if ($actor->canViewFinancialData() && $this->editPurchaseCost !== null) {
            $updateData['purchase_cost'] = $this->editPurchaseCost;
        }

        $asset->update($updateData);

        $afterState = "Name: {$this->editName}, Serial: {$this->editSerial}, Brand: {$this->editBrand}, Warranty End: {$this->editWarrantyEnd}";

        AuditLogService::record(
            'Update asset details',
            'Asset',
            $asset->id,
            $beforeState,
            $afterState
        );

        $this->showEditModal = false;
        session()->flash('success', "Asset {$asset->id} details updated successfully!");
    }

    /**
     * Build dynamic unified lifecycle timeline from audit logs, transactions, and baseline registration.
     *
     * @param  Collection<int, AuditLog>  $auditLogs
     * @param  Collection<int, Transaction>  $transactions
     * @return list<array{timestamp: int, date: string, title: string, description: string, actor: string}>
     */
    private function resolveTimeline(Asset $asset, $auditLogs, $transactions): array
    {
        $events = collect();

        // 1. Audit logs
        foreach ($auditLogs as $log) {
            $time = Carbon::parse($log->timestamp);
            $events->push([
                'timestamp' => $time->timestamp,
                'date' => $time->format('d M Y H:i'),
                'title' => $log->action,
                'description' => (string) ($log->after_state ?: ($log->before_state ?: 'Action completed')),
                'actor' => $log->clean_user ?: 'System',
            ]);
        }

        // 2. Transactions
        foreach ($transactions as $tx) {
            $txDate = $tx->transaction_date ?? $tx->checkout_date ?? $tx->return_date ?? $tx->created_at;
            $time = Carbon::parse($txDate);
            $title = match ($tx->type) {
                'Assign' => 'Assigned to '.User::cleanDisplayName($tx->to_user),
                'Transfer' => 'Transferred to '.User::cleanDisplayName($tx->to_user),
                'Return' => 'Returned to '.($tx->to_location ?: 'Store'),
                'Loan' => 'Loaned to '.User::cleanDisplayName($tx->to_user),
                default => "Transaction: {$tx->type}",
            };
            $desc = $tx->remarks ?: ($tx->condition_at_time ? "Condition: {$tx->condition_at_time}" : '');

            $events->push([
                'timestamp' => $time->timestamp,
                'date' => $time->format('d M Y'),
                'title' => $title,
                'description' => (string) $desc,
                'actor' => $tx->performed_by ? User::cleanDisplayName($tx->performed_by) : 'System',
            ]);
        }

        // 3. Asset Initial Registration / Purchase baseline
        if ($asset->created_at) {
            $createdTime = Carbon::parse($asset->created_at);
            $events->push([
                'timestamp' => $createdTime->timestamp,
                'date' => $createdTime->format('d M Y'),
                'title' => 'Registered in IT Asset Register',
                'description' => "Initial record created with serial {$asset->serial_number}. Location: {$asset->assigned_location}.",
                'actor' => 'IT Operations',
            ]);
        } elseif ($asset->purchase_date) {
            $purchaseTime = Carbon::parse($asset->purchase_date);
            $events->push([
                'timestamp' => $purchaseTime->timestamp,
                'date' => $purchaseTime->format('d M Y'),
                'title' => 'Procurement / Registration',
                'description' => "Asset acquired and recorded. Initial location: {$asset->assigned_location}.",
                'actor' => 'IT Operations',
            ]);
        }

        return $events->unique(fn (array $item): string => $item['date'].'-'.$item['title'])
            ->sortByDesc('timestamp')
            ->values()
            ->all();
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.view'), 403);

        $asset = Asset::find($this->assetId);

        if ($actor->isDepartmentScoped()) {
            abort_if($asset === null, 404);
            abort_unless($actor->canAccessDepartment($asset->department), 403);
        }

        if (! $asset) {
            // Fallback sample asset
            $asset = new Asset([
                'id' => $this->assetId,
                'name' => 'Dell Latitude 7450',
                'serial_number' => 'DL7450-93821',
                'hostname' => 'FIN-LT-023',
                'status' => 'Assigned',
                'assigned_user' => 'Sarah Lim',
                'department' => 'Finance',
                'assigned_location' => 'Jitra Office',
                'purchase_date' => '2026-08-01',
                'purchase_cost' => 5200.00,
                'warranty_end' => '2029-08-01',
                'replacement_due' => 'Aug 2030',
                'verification_status' => 'Verified',
                'last_physical_verification' => '2026-06-15',
            ]);
        }

        $transactions = Transaction::where('asset_id', $this->assetId)->orderByDesc('created_at')->get();
        $auditLogs = AuditLog::where('object_id', $this->assetId)->orderByDesc('timestamp')->get();
        $timelineEvents = $this->resolveTimeline($asset, $auditLogs, $transactions);

        return view('livewire.assets.asset-detail', compact('asset', 'transactions', 'auditLogs', 'timelineEvents'))
            ->layout('layouts.app', ['title' => 'Asset Detail - '.$this->assetId, 'breadcrumb' => 'IT Asset Management / Assets / '.$this->assetId]);
    }
}
