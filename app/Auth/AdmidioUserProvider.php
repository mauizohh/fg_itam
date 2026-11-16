<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdmidioUserProvider implements UserProvider
{
    public function __construct(
        private readonly AdmidioRoleResolver $roleResolver
    ) {}

    public function retrieveById($identifier): ?Authenticatable
    {
        $row = DB::table('adm_users')
            ->where('usr_id', (int) $identifier)
            ->where('usr_valid', 1)
            ->first();

        if (! $row) {
            return null;
        }

        $user = $this->hydrateUser($row);

        return $user->isActive ? $user : null;
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token): void
    {
        // Shared adm_users table does not store Laravel remember tokens.
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $login = trim((string) ($credentials['login'] ?? $credentials['username'] ?? $credentials['email'] ?? ''));

        if ($login === '') {
            return null;
        }

        $row = DB::table('adm_users')
            ->where('usr_valid', 1)
            ->whereRaw('LOWER(usr_login_name) = ?', [strtolower($login)])
            ->first();

        if (! $row && str_contains($login, '@')) {
            $emailLower = strtolower($login);

            $directMatches = DB::table('adm_users')
                ->where('usr_valid', 1)
                ->whereRaw('LOWER(usr_email) = ?', [$emailLower])
                ->get();

            if ($directMatches->count() === 1) {
                $row = $directMatches->first();
            } else {
                $profileMatches = DB::table('adm_users as u')
                    ->join('adm_user_data as ud', 'ud.usd_usr_id', '=', 'u.usr_id')
                    ->join('adm_user_fields as uf', 'uf.usf_id', '=', 'ud.usd_usf_id')
                    ->where('u.usr_valid', 1)
                    ->where('uf.usf_name_intern', 'EMAIL')
                    ->whereRaw('LOWER(ud.usd_value) = ?', [$emailLower])
                    ->select('u.*')
                    ->distinct()
                    ->get();

                if ($profileMatches->count() === 1) {
                    $row = $profileMatches->first();
                }
            }
        }

        if (! $row || empty($row->usr_password)) {
            return null;
        }

        $user = $this->hydrateUser($row);

        return $user->isActive ? $user : null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $plain = (string) ($credentials['password'] ?? '');
        $hash = $user->getAuthPassword();

        if ($plain === '' || $hash === '') {
            return false;
        }

        return Hash::check($plain, $hash);
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        // Never overwrite shared Admidio password hashes automatically on login.
    }

    /**
     * Hydrate an Authenticatable User object from adm_users, adm_user_data, and AdmidioRoleResolver.
     */
    private function hydrateUser(object $row): User
    {
        $usrId = (int) $row->usr_id;

        $fields = DB::table('adm_user_data as ud')
            ->join('adm_user_fields as uf', 'uf.usf_id', '=', 'ud.usd_usf_id')
            ->where('ud.usd_usr_id', $usrId)
            ->whereIn('uf.usf_name_intern', ['FIRST_NAME', 'LAST_NAME', 'EMAIL'])
            ->pluck('ud.usd_value', 'uf.usf_name_intern');

        $firstName = trim((string) ($fields['FIRST_NAME'] ?? ''));
        $lastName = trim((string) ($fields['LAST_NAME'] ?? ''));
        $fullName = trim("{$firstName} {$lastName}");
        if ($fullName === '') {
            $fullName = (string) ($row->usr_login_name ?: "User #{$usrId}");
        }

        $email = trim((string) ($row->usr_email ?? ''));
        if ($email === '') {
            $email = trim((string) ($fields['EMAIL'] ?? ''));
        }

        $resolved = $this->roleResolver->resolveForUser($usrId, (string) $row->usr_login_name);
        $scopeDepartments = ! empty($resolved['managed_departments'])
            ? $resolved['managed_departments']
            : $resolved['departments'];

        return new User(
            usr_id: $usrId,
            username: (string) $row->usr_login_name,
            name: $fullName,
            email: $email !== '' ? $email : null,
            passwordHash: (string) ($row->usr_password ?? ''),
            itamRole: $resolved['role'],
            roleSource: $resolved['role_source'],
            isActive: $resolved['is_active'],
            departments: $resolved['departments'],
            managedDepartments: $resolved['managed_departments'],
            expandedDepartmentScope: $this->roleResolver->expandDepartmentAliases($scopeDepartments),
            admidioRoles: $resolved['admidio_roles'],
            lastLogin: $row->usr_last_login ? (string) $row->usr_last_login : null,
        );
    }
}
