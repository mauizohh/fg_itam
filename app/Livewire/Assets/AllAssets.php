<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Services\AssetIdGeneratorService;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AllAssets extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'All';

    public string $categoryFilter = 'All';

    public string $locationFilter = 'All';

    public string $sortField = 'id';

    public string $sortDirection = 'asc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingLocationFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    // Modal state for Add Asset
    public bool $showAddModal = false;

    public string $newId = '';

    public string $newCategory = 'LAP';

    public string $newName = '';

    public string $newBrand = '';

    public string $newModel = '';

    public string $newSerial = '';

    public string $newLocation = 'Jitra Office';

    public string $newStatus = 'Available';

    public ?float $newCost = null;

    public ?string $newPurchaseDate = null;

    public ?string $newWarrantyProvider = null;

    public ?string $newWarrantyStart = null;

    public ?string $newWarrantyEnd = null;

    public ?string $newReplacementDue = null;

    public ?string $initialPurchaseDate = null;

    // Modal state for Edit Asset
    public bool $showEditModal = false;

    public string $editId = '';

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

    public function mount()
    {
        if (request()->has('search')) {
            $this->search = request()->query('search');
        }
    }

    public function openAddModal()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.create'), 403);

        $this->newId = AssetIdGeneratorService::generate($this->newCategory);
        $this->newPurchaseDate = now()->format('Y-m-d');
        $this->initialPurchaseDate = $this->newPurchaseDate;
        $this->newWarrantyStart = $this->newPurchaseDate;
        $this->newReplacementDue = null;
        $this->showAddModal = true;
    }

    public function updatedNewPurchaseDate(?string $val): void
    {
        if (empty($this->newWarrantyStart) || $this->newWarrantyStart === $this->initialPurchaseDate) {
            $this->newWarrantyStart = $val;
        }
        $this->initialPurchaseDate = $val;
    }

    public function updatedNewCategory($val)
    {
        $this->newId = AssetIdGeneratorService::generate($val);
    }

    public function saveAsset()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.create'), 403);

        $this->validate([
            'newId' => 'required|string',
            'newName' => 'required|string|max:255',
            'newSerial' => 'required|string|unique:fg_itam_assets,serial_number',
            'newLocation' => ['required', 'string', 'in:'.implode(',', Location::OFFICE_OPTIONS)],
            'newPurchaseDate' => 'nullable|date',
            'newWarrantyStart' => 'nullable|date',
            'newWarrantyEnd' => 'nullable|date',
            'newWarrantyProvider' => 'nullable|string|max:150',
            'newReplacementDue' => 'nullable|date',
        ]);

        $cat = Category::find($this->newCategory);

        $asset = Asset::create([
            'id' => $this->newId,
            'asset_tag' => 'TAG-'.substr($this->newId, 3),
            'name' => $this->newName,
            'category_id' => $this->newCategory,
            'category_name' => $cat ? $cat->name : 'Equipment',
            'brand' => $this->newBrand ?: 'Standard',
            'model' => $this->newModel ?: $this->newName,
            'serial_number' => $this->newSerial,
            'status' => $this->newStatus,
            'assigned_location' => $this->newLocation,
            'purchase_date' => $this->newPurchaseDate ?: null,
            'purchase_cost' => $this->newCost ?: 0.00,
            'warranty_start' => $this->newWarrantyStart ?: null,
            'warranty_end' => $this->newWarrantyEnd ?: null,
            'warranty_provider' => $this->newWarrantyProvider ?: null,
            'replacement_due' => $this->newReplacementDue ?: null,
            'verification_status' => 'Verified',
            'last_physical_verification' => now(),
        ]);

        AuditLogService::record('Register asset', 'Asset', $asset->id, null, "Registered in {$this->newLocation}");

        $this->showAddModal = false;
        $this->reset(['newName', 'newBrand', 'newModel', 'newSerial', 'newCost', 'newPurchaseDate', 'newWarrantyProvider', 'newWarrantyStart', 'newWarrantyEnd', 'newReplacementDue']);
        session()->flash('success', "Asset {$asset->id} registered successfully!");
    }

    public function openEditModal(string $id): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.edit'), 403);

        $asset = Asset::findOrFail($id);
        $this->editId = $asset->id;
        $this->editName = (string) $asset->name;
        $this->editBrand = (string) ($asset->brand ?? '');
        $this->editModel = (string) ($asset->model ?? '');
        $this->editSerial = (string) $asset->serial_number;
        $this->editLocation = in_array($asset->assigned_location, Location::OFFICE_OPTIONS, true)
            ? $asset->assigned_location
            : Location::OFFICE_OPTIONS[0];
        $this->editCondition = (string) ($asset->condition ?: 'Good');
        $this->editPurchaseDate = $asset->purchase_date ? substr((string) $asset->purchase_date, 0, 10) : null;
        $this->initialPurchaseDate = $this->editPurchaseDate;
        $this->editPurchaseCost = $asset->purchase_cost ? (float) $asset->purchase_cost : null;
        $this->editWarrantyProvider = (string) ($asset->warranty_provider ?? '');
        $this->editWarrantyStart = $asset->warranty_start
            ? substr((string) $asset->warranty_start, 0, 10)
            : ($this->editPurchaseDate ?: null);
        $this->editWarrantyEnd = $asset->warranty_end ? substr((string) $asset->warranty_end, 0, 10) : null;

        $parsedDue = null;
        if (! empty($asset->replacement_due)) {
            try {
                $parsedDue = Carbon::parse($asset->replacement_due)->format('Y-m-d');
            } catch (\Throwable) {
                $parsedDue = null;
            }
        }
        $this->editReplacementDue = $parsedDue;
        $this->editRemarks = (string) ($asset->remarks ?? '');

        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function updatedEditPurchaseDate(?string $val): void
    {
        if (empty($this->editWarrantyStart) || $this->editWarrantyStart === $this->initialPurchaseDate) {
            $this->editWarrantyStart = $val;
        }
        $this->initialPurchaseDate = $val;
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
            'editSerial' => 'required|string|max:100|unique:fg_itam_assets,serial_number,'.$this->editId.',id',
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

        $asset = Asset::findOrFail($this->editId);

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

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.view'), 403);

        $query = Asset::query();
        $actor->scopeDepartmentQuery($query, 'department');

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('id', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%")
                    ->orWhere('serial_number', 'like', "%{$this->search}%")
                    ->orWhere('assigned_user', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== 'All') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter !== 'All') {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->locationFilter !== 'All') {
            $query->where('assigned_location', 'like', "%{$this->locationFilter}%");
        }

        $allowedSorts = ['id', 'name', 'serial_number', 'assigned_user', 'assigned_location', 'status'];
        if (in_array($this->sortField, $allowedSorts, true)) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        $assets = $query->paginate(15);
        $categories = Category::all();

        return view('livewire.assets.all-assets', compact('assets', 'categories'))
            ->layout('layouts.app', ['title' => 'All Assets', 'breadcrumb' => 'IT Asset Management / Assets']);
    }
}
