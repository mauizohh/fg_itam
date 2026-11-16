<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AvailableAssets extends Component
{
    public string $search = '';

    public string $categoryFilter = 'All';

    public string $locationFilter = 'IT Store';

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.view'), 403);

        $baseQuery = fn () => $actor->scopeDepartmentQuery(Asset::where('status', 'Available'), 'department');

        $laptopCount = $baseQuery()->where('category_id', 'LAP')->count();
        $monitorCount = $baseQuery()->where('category_id', 'MON')->count();
        $phoneCount = $baseQuery()->where('category_id', 'MOB')->count();
        $networkCount = $baseQuery()->where('category_id', 'NET')->count();

        $query = $baseQuery();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('id', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%")
                    ->orWhere('model', 'like', "%{$this->search}%")
                    ->orWhere('serial_number', 'like', "%{$this->search}%");
            });
        }

        if ($this->categoryFilter !== 'All') {
            $query->where('category_id', $this->categoryFilter);
        }

        if (! empty($this->locationFilter)) {
            $query->where('assigned_location', 'like', "%{$this->locationFilter}%");
        }

        $assets = $query->orderBy('id')->get();
        $categories = Category::all();

        return view('livewire.assets.available-assets', compact(
            'laptopCount',
            'monitorCount',
            'phoneCount',
            'networkCount',
            'assets',
            'categories'
        ))->layout('layouts.app', ['title' => 'Available Assets', 'breadcrumb' => 'IT Asset Management / Assets / Available']);
    }
}
