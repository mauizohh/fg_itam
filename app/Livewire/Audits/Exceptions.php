<?php

namespace App\Livewire\Audits;

use App\Models\AuditException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Exceptions extends Component
{
    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('audits.exceptions.view'), 403);

        $query = $actor->scopeDepartmentQuery(AuditException::query(), 'owner_department');

        $exceptions = (clone $query)->orderByDesc('id')->get();
        $openExceptions = (clone $query)->where('status', 'Open')->count();
        $missingCount = (clone $query)->where('exception_type', 'Not Found')->count();
        $wrongLocationCount = (clone $query)->where('exception_type', 'Wrong Location')->count();
        $damagedCount = (clone $query)->where('exception_type', 'Damaged')->count();

        return view('livewire.audits.exceptions', compact(
            'exceptions',
            'openExceptions',
            'missingCount',
            'wrongLocationCount',
            'damagedCount'
        ))->layout('layouts.app', ['title' => 'Audit Exceptions', 'breadcrumb' => 'IT Asset Management / Audits / Exceptions']);
    }
}
