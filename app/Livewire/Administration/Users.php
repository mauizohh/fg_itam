<?php

namespace App\Livewire\Administration;

use App\Auth\AdmidioRoleResolver;
use App\Enums\ItamRole;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public string $search = '';

    public string $roleFilter = 'All';

    public string $statusFilter = 'Active';

    public ?int $editUsrId = null;

    public string $editUserName = '';

    public string $editLoginName = '';

    public string $editRole = 'auto';

    public bool $editIsActive = true;

    public string $editNotes = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Open role assignment modal for an Admidio user.
     */
    public function openEdit(int $usrId, AdmidioRoleResolver $resolver): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('admin.users'), 403);

        $row = DB::table('adm_users')->where('usr_id', $usrId)->first();
        abort_unless($row !== null, 404);

        $fieldIds = DB::table('adm_user_fields')
            ->whereIn('usf_name_intern', ['FIRST_NAME', 'LAST_NAME'])
            ->pluck('usf_id', 'usf_name_intern');

        $profileData = DB::table('adm_user_data')
            ->where('usd_usr_id', $usrId)
            ->whereIn('usd_usf_id', $fieldIds->values()->all())
            ->pluck('usd_value', 'usd_usf_id');

        $first = trim((string) ($profileData->get($fieldIds->get('FIRST_NAME')) ?? ''));
        $last = trim((string) ($profileData->get($fieldIds->get('LAST_NAME')) ?? ''));
        $fullName = trim($first.' '.$last);

        $override = DB::table('fg_itam_user_roles')->where('usr_id', $usrId)->first();
        $resolved = $resolver->resolveForUser($usrId, $row->usr_login_name);

        $this->editUsrId = $usrId;
        $this->editLoginName = (string) ($row->usr_login_name ?? '');
        $this->editUserName = $fullName !== '' ? $fullName : ($this->editLoginName ?: "User #{$usrId}");
        $this->editRole = ($override && ItamRole::tryFromValue($override->role) !== null)
            ? (string) $override->role
            : 'auto';
        $this->editIsActive = $override ? (bool) $override->is_active : true;
        $this->editNotes = (string) ($override->notes ?? '');
        $this->resetValidation();
    }

    /**
     * Close edit modal.
     */
    public function cancelEdit(): void
    {
        $this->editUsrId = null;
        $this->resetValidation();
    }

    /**
     * Save ITAM role override and active status to fg_itam_user_roles.
     */
    public function saveUserRole(AdmidioRoleResolver $resolver): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('admin.users'), 403);

        abort_unless($this->editUsrId !== null, 400);

        $this->validate([
            'editRole' => ['required', 'in:auto,system_it_admin,it_support,department_manager,finance,employee,auditor'],
            'editIsActive' => ['boolean'],
            'editNotes' => ['nullable', 'string', 'max:255'],
        ]);

        $userRow = DB::table('adm_users')->where('usr_id', $this->editUsrId)->first();
        abort_unless($userRow !== null, 404);

        $beforeResolved = $resolver->resolveForUser($this->editUsrId, $userRow->usr_login_name);
        $beforeLabel = $beforeResolved['role']->label().' ('.($beforeResolved['is_active'] ? 'Active' : 'Inactive').')';

        if ($this->editRole === 'auto' && $this->editIsActive) {
            DB::table('fg_itam_user_roles')->where('usr_id', $this->editUsrId)->delete();
        } else {
            $storedRole = $this->editRole === 'auto' ? '' : $this->editRole;
            DB::table('fg_itam_user_roles')->updateOrInsert(
                ['usr_id' => $this->editUsrId],
                [
                    'role' => $storedRole,
                    'is_active' => $this->editIsActive,
                    'assigned_by' => $actor->usr_id,
                    'notes' => trim($this->editNotes) !== '' ? trim($this->editNotes) : null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $afterResolved = $resolver->resolveForUser($this->editUsrId, $userRow->usr_login_name);
        $afterLabel = $afterResolved['role']->label().' ('.($afterResolved['is_active'] ? 'Active' : 'Inactive').", {$afterResolved['role_source']})";

        AuditLogService::record(
            'Update user role',
            'UserRole',
            (string) $this->editUsrId,
            $beforeLabel,
            $afterLabel
        );

        $updatedName = $this->editUserName;
        $this->editUsrId = null;
        session()->flash('success', "Updated ITAM access for {$updatedName}: {$afterLabel}.");
    }

    public function render(AdmidioRoleResolver $resolver): View
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('admin.users'), 403);

        $fieldIds = DB::table('adm_user_fields')
            ->whereIn('usf_name_intern', ['FIRST_NAME', 'LAST_NAME', 'EMAIL'])
            ->pluck('usf_id', 'usf_name_intern');

        $firstFieldId = (int) ($fieldIds->get('FIRST_NAME') ?? 0);
        $lastFieldId = (int) ($fieldIds->get('LAST_NAME') ?? 0);
        $emailFieldId = (int) ($fieldIds->get('EMAIL') ?? 0);

        $rawRows = DB::table('adm_users as u')
            ->leftJoin('adm_user_data as fn', function ($join) use ($firstFieldId): void {
                $join->on('fn.usd_usr_id', '=', 'u.usr_id')->where('fn.usd_usf_id', '=', $firstFieldId);
            })
            ->leftJoin('adm_user_data as ln', function ($join) use ($lastFieldId): void {
                $join->on('ln.usd_usr_id', '=', 'u.usr_id')->where('ln.usd_usf_id', '=', $lastFieldId);
            })
            ->leftJoin('adm_user_data as em', function ($join) use ($emailFieldId): void {
                $join->on('em.usd_usr_id', '=', 'u.usr_id')->where('em.usd_usf_id', '=', $emailFieldId);
            })
            ->leftJoin('fg_itam_user_roles as ir', 'ir.usr_id', '=', 'u.usr_id')
            ->select(
                'u.usr_id',
                'u.usr_login_name',
                'u.usr_valid',
                'u.usr_last_login',
                'fn.usd_value as first_name',
                'ln.usd_value as last_name',
                'em.usd_value as email',
                'ir.role as override_role',
                'ir.is_active as override_active'
            )
            ->orderBy('u.usr_id')
            ->get();

        $searchTerm = strtolower(trim($this->search));

        $mapped = $rawRows->map(function (object $row) use ($resolver): array {
            $usrId = (int) $row->usr_id;
            $loginName = (string) ($row->usr_login_name ?? '');
            $fullName = trim(((string) ($row->first_name ?? '')).' '.((string) ($row->last_name ?? '')));
            $displayName = $fullName !== '' ? $fullName : ($loginName !== '' ? $loginName : "User #{$usrId}");
            $resolved = $resolver->resolveForUser($usrId, $loginName);
            $effectiveActive = ((int) $row->usr_valid === 1) && $resolved['is_active'];

            return [
                'usr_id' => $usrId,
                'name' => $displayName,
                'username' => $loginName,
                'email' => $row->email ?: '—',
                'departments' => ! empty($resolved['departments']) ? implode(', ', $resolved['departments']) : '—',
                'role' => $resolved['role'],
                'role_source' => $resolved['role_source'],
                'last_login' => $row->usr_last_login ?: 'Never',
                'is_active' => $effectiveActive,
                'usr_valid' => (int) $row->usr_valid === 1,
            ];
        })->filter(function (array $entry) use ($searchTerm): bool {
            if ($searchTerm !== '') {
                $haystack = strtolower($entry['name'].' '.$entry['username'].' '.$entry['email'].' '.$entry['departments']);
                if (! str_contains($haystack, $searchTerm)) {
                    return false;
                }
            }

            if ($this->roleFilter !== 'All' && $entry['role']->value !== $this->roleFilter) {
                return false;
            }

            if ($this->statusFilter === 'Active' && ! $entry['is_active']) {
                return false;
            }

            if ($this->statusFilter === 'Inactive' && $entry['is_active']) {
                return false;
            }

            return true;
        })->values();

        $perPage = 15;
        $currentPage = $this->getPage();
        $paginated = new LengthAwarePaginator(
            $mapped->forPage($currentPage, $perPage)->values(),
            $mapped->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.administration.users', [
            'users' => $paginated,
            'roleOptions' => ItamRole::cases(),
        ])->layout('layouts.app', [
            'title' => 'System Users',
            'breadcrumb' => 'IT Asset Management / Administration / Users',
        ]);
    }
}
