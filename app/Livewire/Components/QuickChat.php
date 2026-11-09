<?php

namespace App\Livewire\Components;

use App\Models\Asset;
use Livewire\Component;

class QuickChat extends Component
{
    public bool $isOpen = false;
    public string $searchQuery = '';
    public array $results = [];
    public array $recentLookups = [
        'IT-DSK-2026-00001' => 'HP ProDesk 4 Tower G1 (Safety)',
        'IT-LAP-2026-00123' => 'Dell Latitude 7450 (Sarah Lim)',
        'IT-NET-2026-00201' => 'Cisco Switch 48P (Server Room)',
    ];

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function updatedSearchQuery()
    {
        $q = trim($this->searchQuery);
        if (strlen($q) >= 2) {
            $this->results = Asset::where('id', 'like', "%{$q}%")
                ->orWhere('name', 'like', "%{$q}%")
                ->orWhere('serial_number', 'like', "%{$q}%")
                ->orWhere('assigned_user', 'like', "%{$q}%")
                ->limit(5)
                ->get()
                ->toArray();
        } else {
            $this->results = [];
        }
    }

    public function render()
    {
        return view('livewire.components.quick-chat');
    }
}
