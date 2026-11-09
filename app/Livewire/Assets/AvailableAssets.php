<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Category;
use Livewire\Component;

class AvailableAssets extends Component
{
    public string $search = '';
    public string $categoryFilter = 'All';
    public string $locationFilter = 'IT Store';

    public function render()
    {
        $laptopCount = Asset::where('status', 'Available')->where('category_id', 'LAP')->count();
        $monitorCount = Asset::where('status', 'Available')->where('category_id', 'MON')->count();
        $phoneCount = Asset::where('status', 'Available')->where('category_id', 'MOB')->count();
        $networkCount = Asset::where('status', 'Available')->where('category_id', 'NET')->count();

        $query = Asset::where('status', 'Available');

        if (!empty($this->search)) {
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

        if (!empty($this->locationFilter)) {
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
