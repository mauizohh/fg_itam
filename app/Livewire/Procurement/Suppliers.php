<?php

namespace App\Livewire\Procurement;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Suppliers extends Component
{
    public string $search = '';

    public string $statusFilter = 'Active';

    public string $categoryFilter = 'All';

    public bool $showModal = false;

    public string $name = '';

    public string $category = 'Hardware';

    public string $contactEmail = '';

    public string $contactPhone = '';

    public function addSupplier()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('procurement.manage'), 403);

        $this->validate([
            'name' => 'required|string',
            'contactEmail' => 'required|email',
        ]);

        Supplier::create([
            'name' => $this->name,
            'category' => $this->category,
            'contact_email' => $this->contactEmail,
            'contact_phone' => $this->contactPhone,
            'status' => 'Active',
        ]);

        $this->showModal = false;
        $this->reset(['name', 'contactEmail', 'contactPhone']);
        session()->flash('success', 'Supplier created successfully!');
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('procurement.view'), 403);

        $query = Supplier::query();

        if (! empty($this->search)) {
            $query->where('name', 'like', "%{$this->search}%");
        }
        if ($this->statusFilter !== 'All') {
            $query->where('status', $this->statusFilter);
        }
        if ($this->categoryFilter !== 'All') {
            $query->where('category', $this->categoryFilter);
        }

        $suppliers = $query->orderBy('name')->get();

        return view('livewire.procurement.suppliers', compact('suppliers'))
            ->layout('layouts.app', ['title' => 'Suppliers', 'breadcrumb' => 'IT Asset Management / Procurement / Suppliers']);
    }
}
