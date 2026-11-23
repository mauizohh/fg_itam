<?php

namespace App\Livewire\Software;

use App\Models\Asset;
use App\Models\License;
use App\Models\Software;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SoftwareCatalog extends Component
{
    public string $search = '';

    public string $typeFilter = 'All';

    public string $vendorFilter = 'All';

    public bool $showModal = false;

    public bool $showDeviceModal = false;

    public ?string $selectedSoftwareTitle = null;

    public string $deviceSearch = '';

    public string $name = '';

    public string $vendor = '';

    public string $type = 'Subscription';

    public int $managedDevices = 0;

    public string $ownerDepartment = 'IT Operations';

    public function openDeviceModal(string $softwareTitle): void
    {
        $this->selectedSoftwareTitle = $softwareTitle;
        $this->deviceSearch = '';
        $this->showDeviceModal = true;
    }

    public function closeDeviceModal(): void
    {
        $this->showDeviceModal = false;
        $this->selectedSoftwareTitle = null;
        $this->deviceSearch = '';
    }

    public function addSoftware()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('software.manage'), 403);

        $this->validate([
            'name' => 'required',
            'vendor' => 'required',
        ]);

        Software::create([
            'name' => $this->name,
            'vendor' => $this->vendor,
            'type' => $this->type,
            'managed_devices_count' => $this->managedDevices,
            'owner_department' => $this->ownerDepartment,
            'status' => 'Active',
        ]);

        $this->showModal = false;
        session()->flash('success', "Software {$this->name} added!");
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('software.view'), 403);

        $query = Software::query();

        if (! empty($this->search)) {
            $query->where('name', 'like', "%{$this->search}%");
        }
        if ($this->typeFilter !== 'All') {
            $query->where('type', $this->typeFilter);
        }
        if ($this->vendorFilter !== 'All') {
            $query->where('vendor', $this->vendorFilter);
        }

        $software = $query->orderBy('name')->get();

        $deviceAssets = collect();
        if ($this->selectedSoftwareTitle) {
            $license = License::where('software_name', $this->selectedSoftwareTitle)->first();
            if ($license) {
                $allAssets = $license->getAssignedAssets();
            } else {
                $allAssets = Asset::whereIn('category_id', ['LAP', 'DSK'])->take(4)->get();
            }

            if (! empty(trim($this->deviceSearch))) {
                $q = strtolower(trim($this->deviceSearch));
                $deviceAssets = $allAssets->filter(function ($a) use ($q) {
                    return str_contains(strtolower($a->id), $q)
                        || str_contains(strtolower($a->name), $q)
                        || str_contains(strtolower($a->assigned_user ?? ''), $q)
                        || str_contains(strtolower($a->department ?? ''), $q)
                        || str_contains(strtolower($a->assigned_location ?? ''), $q);
                })->values();
            } else {
                $deviceAssets = $allAssets;
            }
        }

        return view('livewire.software.software-catalog', compact('software', 'deviceAssets'))
            ->layout('layouts.app', ['title' => 'Software Catalog', 'breadcrumb' => 'IT Asset Management / Software']);
    }
}
