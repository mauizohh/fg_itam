<?php

namespace App\Livewire\Software;

use App\Models\Software;
use Livewire\Component;

class SoftwareCatalog extends Component
{
    public string $search = '';
    public string $typeFilter = 'All';
    public string $vendorFilter = 'All';

    public bool $showModal = false;
    public string $name = '';
    public string $vendor = '';
    public string $type = 'Subscription';
    public int $managedDevices = 0;
    public string $ownerDepartment = 'IT Operations';

    public function addSoftware()
    {
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
        $query = Software::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', "%{$this->search}%");
        }
        if ($this->typeFilter !== 'All') {
            $query->where('type', $this->typeFilter);
        }
        if ($this->vendorFilter !== 'All') {
            $query->where('vendor', $this->vendorFilter);
        }

        $software = $query->orderBy('name')->get();

        return view('livewire.software.software-catalog', compact('software'))
            ->layout('layouts.app', ['title' => 'Software Catalog', 'breadcrumb' => 'IT Asset Management / Software']);
    }
}
