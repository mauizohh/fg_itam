<?php

namespace App\Livewire\Administration;

use App\Models\AuditLog;
use Livewire\Component;

class AuditLogs extends Component
{
    public string $search = '';
    public string $userFilter = 'All';
    public string $actionFilter = 'All';
    public string $dateFilter = 'Today';

    public function render()
    {
        $query = AuditLog::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('object_id', 'like', "%{$this->search}%")
                  ->orWhere('action', 'like', "%{$this->search}%")
                  ->orWhere('user', 'like', "%{$this->search}%");
            });
        }

        if ($this->userFilter !== 'All') {
            $query->where('user', $this->userFilter);
        }

        if ($this->actionFilter !== 'All') {
            $query->where('action', $this->actionFilter);
        }

        $logs = $query->orderByDesc('timestamp')->get();

        return view('livewire.administration.audit-logs', compact('logs'))
            ->layout('layouts.app', ['title' => 'Audit Logs', 'breadcrumb' => 'IT Asset Management / Administration / Audit Logs']);
    }
}
