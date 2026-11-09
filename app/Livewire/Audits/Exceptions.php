<?php

namespace App\Livewire\Audits;

use App\Models\AuditException;
use Livewire\Component;

class Exceptions extends Component
{
    public function render()
    {
        $exceptions = AuditException::orderByDesc('id')->get();
        $openExceptions = 17;
        $missingCount = 5;
        $wrongLocationCount = 8;
        $damagedCount = 4;

        return view('livewire.audits.exceptions', compact(
            'exceptions',
            'openExceptions',
            'missingCount',
            'wrongLocationCount',
            'damagedCount'
        ))->layout('layouts.app', ['title' => 'Audit Exceptions', 'breadcrumb' => 'IT Asset Management / Audits / Exceptions']);
    }
}
