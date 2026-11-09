<?php

namespace App\Livewire\Procurement;

use App\Models\Purchase;
use Livewire\Component;

class Purchases extends Component
{
    public function render()
    {
        $purchases = Purchase::orderByDesc('purchase_date')->get();

        return view('livewire.procurement.purchases', compact('purchases'))
            ->layout('layouts.app', ['title' => 'Purchases', 'breadcrumb' => 'IT Asset Management / Procurement / Purchases']);
    }
}
