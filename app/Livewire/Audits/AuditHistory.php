<?php

namespace App\Livewire\Audits;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AuditHistory extends Component
{
    public string $search = '';

    public string $yearFilter = '2026';

    public string $statusFilter = 'All';

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('audits.history.view'), 403);

        $query = Audit::query();

        if (! empty($this->search)) {
            $query->where(function ($q): void {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('scope', 'like', "%{$this->search}%");
            });
        }
        if ($this->statusFilter !== 'All') {
            $query->where('status', $this->statusFilter);
        }

        $audits = $query->orderByDesc('id')->get();

        return view('livewire.audits.audit-history', compact('audits'))
            ->layout('layouts.app', ['title' => 'Audit History', 'breadcrumb' => 'IT Asset Management / Audits / History']);
    }
}
