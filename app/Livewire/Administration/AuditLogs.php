<?php

namespace App\Livewire\Administration;

use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogs extends Component
{
    use WithPagination;

    public string $search = '';

    public string $userFilter = 'All';

    public string $actionFilter = 'All';

    public string $dateFilter = 'All Time';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingUserFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActionFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('admin.audit_logs'), 403);

        $query = AuditLog::query();

        if (! empty($this->search)) {
            $query->where(function ($q): void {
                $q->where('object_id', 'like', "%{$this->search}%")
                    ->orWhere('action', 'like', "%{$this->search}%")
                    ->orWhere('user', 'like', "%{$this->search}%")
                    ->orWhere('after_state', 'like', "%{$this->search}%");
            });
        }

        if ($this->userFilter !== 'All') {
            $query->where('user', $this->userFilter);
        }

        if ($this->actionFilter !== 'All') {
            $query->where('action', $this->actionFilter);
        }

        if ($this->dateFilter === 'Today') {
            $query->where('timestamp', '>=', Carbon::today());
        } elseif ($this->dateFilter === 'This Week') {
            $query->where('timestamp', '>=', Carbon::now()->startOfWeek());
        }

        $logs = $query->orderByDesc('timestamp')->paginate(15);

        $availableUsers = AuditLog::query()
            ->whereNotNull('user')
            ->distinct()
            ->orderBy('user')
            ->pluck('user');

        $availableActions = AuditLog::query()
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('livewire.administration.audit-logs', compact('logs', 'availableUsers', 'availableActions'))
            ->layout('layouts.app', ['title' => 'Audit Logs', 'breadcrumb' => 'IT Asset Management / Administration / Audit Logs']);
    }
}
