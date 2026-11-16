<?php

namespace App\Livewire\Procurement;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Purchases extends Component
{
    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('procurement.view'), 403);

        $purchases = Purchase::orderByDesc('purchase_date')->get();

        return view('livewire.procurement.purchases', compact('purchases'))
            ->layout('layouts.app', ['title' => 'Purchases', 'breadcrumb' => 'IT Asset Management / Procurement / Purchases']);
    }
}
