<?php

namespace App\Livewire\Transactions;

use App\Enums\ItamRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionHistory extends Component
{
    use WithPagination;

    public string $search = '';

    public string $typeFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasPermission('transactions.history'), 403);

        $query = DB::table('fg_itam_transactions as t')
            ->leftJoin('fg_itam_assets as a', 'a.id', '=', 't.asset_id')
            ->select(
                't.*',
                'a.name as asset_name',
                'a.department as asset_department'
            )
            ->when($this->search !== '', function ($q): void {
                $term = '%'.$this->search.'%';
                $q->where(function ($sub) use ($term): void {
                    $sub->where('t.asset_id', 'like', $term)
                        ->orWhere('a.name', 'like', $term)
                        ->orWhere('t.from_user', 'like', $term)
                        ->orWhere('t.to_user', 'like', $term)
                        ->orWhere('t.remarks', 'like', $term);
                });
            })
            ->when($this->typeFilter !== '', function ($q): void {
                $q->where('t.type', $this->typeFilter);
            });

        if ($user->hasRole(ItamRole::Employee)) {
            $names = array_values(array_unique(array_filter([
                strtolower(trim($user->name)),
                strtolower(trim($user->display_name)),
                strtolower(trim($user->username)),
            ])));
            $query->where(function ($q) use ($names): void {
                foreach ($names as $name) {
                    $q->orWhereRaw('LOWER(t.to_user) = ?', [$name])
                        ->orWhereRaw('LOWER(t.from_user) = ?', [$name])
                        ->orWhereRaw('LOWER(a.assigned_user) = ?', [$name]);
                }
            });
        } elseif ($user->isDepartmentScoped()) {
            $scope = array_map('strtolower', $user->departmentScopeNames());
            if (empty($scope)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(function ($q) use ($scope): void {
                    $placeholders = implode(',', array_fill(0, count($scope), '?'));
                    $q->whereRaw("LOWER(COALESCE(t.to_department, '')) IN ({$placeholders})", $scope)
                        ->orWhereRaw("LOWER(COALESCE(a.department, '')) IN ({$placeholders})", $scope);
                });
            }
        }

        $transactions = $query->orderByDesc('t.id')->paginate(20);

        return view('livewire.transactions.transaction-history', [
            'transactions' => $transactions,
            'user' => $user,
        ])->layout('layouts.app', [
            'title' => 'Transaction History',
            'breadcrumb' => 'IT Asset Management / Transactions / History',
        ]);
    }
}
