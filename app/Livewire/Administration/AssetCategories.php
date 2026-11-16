<?php

namespace App\Livewire\Administration;

use App\Models\Category;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AssetCategories extends Component
{
    public bool $showModal = false;

    public string $categoryId = '';

    public string $categoryName = '';

    public string $prefix = '';

    public int $usefulLife = 4;

    public string $trackingFields = 'Serial, Hostname';

    public function addCategory(): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('admin.categories'), 403);

        $this->validate([
            'categoryId' => 'required|max:10|unique:fg_itam_categories,id',
            'categoryName' => 'required',
            'prefix' => 'required',
        ]);

        $created = Category::create([
            'id' => strtoupper($this->categoryId),
            'name' => $this->categoryName,
            'prefix' => strtoupper($this->prefix),
            'useful_life_years' => $this->usefulLife,
            'tracking_fields' => $this->trackingFields,
            'status' => 'Active',
        ]);

        AuditLogService::record('Add category', 'Category', (string) $created->id, null, $created->name);

        $this->showModal = false;
        $this->reset(['categoryId', 'categoryName', 'prefix']);
        session()->flash('success', 'Category added!');
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('admin.categories'), 403);

        $categories = Category::all();

        return view('livewire.administration.asset-categories', compact('categories'))
            ->layout('layouts.app', ['title' => 'Asset Categories', 'breadcrumb' => 'IT Asset Management / Administration / Categories']);
    }
}
