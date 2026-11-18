<?php

namespace App\Livewire\Auth;

use App\Enums\ItamRole;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Sign In')]
class Login extends Component
{
    public string $username = '';

    public string $password = '';

    /**
     * Authenticate an Admidio employee against FG ITAM.
     */
    public function authenticate(): mixed
    {
        $this->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = trim($this->username);
        $throttleKey = Str::transliterate(Str::lower($loginInput).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('username', "Too many login attempts. Please try again in {$seconds} seconds.");

            return null;
        }

        if (! Auth::attempt(['username' => $loginInput, 'password' => $this->password])) {
            RateLimiter::hit($throttleKey, 60);
            AuditLogService::record(
                'Failed Login',
                'Auth',
                $loginInput,
                null,
                'Invalid credentials or inactive ITAM account',
                $loginInput
            );

            $this->addError('username', 'Invalid Admidio username/email or password, or your ITAM account is inactive.');

            return null;
        }

        RateLimiter::clear($throttleKey);
        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        /** @var User $user */
        $user = Auth::user();

        DB::table('adm_users')
            ->where('usr_id', $user->usr_id)
            ->update([
                'usr_last_login' => now(),
                'usr_number_login' => DB::raw('COALESCE(usr_number_login, 0) + 1'),
            ]);

        AuditLogService::record(
            'Login',
            'Auth',
            (string) $user->usr_id,
            null,
            "Signed in as {$user->role_label} ({$user->username})",
            $user->display_name
        );

        $defaultRoute = $user->hasRole(ItamRole::Employee)
            ? route('my-assets')
            : route('dashboard');

        return $this->redirectIntended(default: $defaultRoute, navigate: false);
    }

    /**
     * Populate credentials for one of the local QA/test accounts.
     */
    public function fillTestAccount(string $username): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        $this->resetValidation();
        $this->username = $username;
        $this->password = 'Test@1234';
    }

    public function render(): View
    {
        return view('livewire.auth.login', [
            'showTestAccounts' => app()->environment('local', 'testing'),
        ]);
    }
}
