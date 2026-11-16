<div class="w-full max-w-md space-y-6">
    <!-- Brand Card Header -->
    <div class="text-center space-y-2">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white border border-gray-200 shadow-sm">
            <img src="{{ asset('images/fg_logo.png') }}" alt="FG ITAM Logo" class="h-9 w-9 object-contain" onerror="this.style.display='none'">
        </div>
        <div>
            <h1 class="text-2xl font-heading font-bold text-[#3b4048]">FG <span class="text-[#a92d31]">ITAM</span></h1>
            <p class="text-xs text-gray-500 mt-0.5">Sign in with your Admidio employee account</p>
        </div>
    </div>

    <!-- Sign-In Form Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 sm:p-8">
        <form wire:submit="authenticate" class="space-y-5" x-data="{ showPassword: false }" novalidate>
            @if ($errors->any())
                <div role="alert" aria-live="polite" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700 font-medium">
                    {{ $errors->first('username') ?: $errors->first('password') }}
                </div>
            @endif

            <div>
                <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-[#3b4048] mb-1.5">
                    Admidio Username or Email
                </label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    wire:model="username"
                    autocomplete="username"
                    spellcheck="false"
                    autocorrect="off"
                    autocapitalize="none"
                    enterkeyhint="next"
                    required
                    @if ($errors->has('username'))
                        aria-invalid="true"
                        aria-describedby="username-error"
                    @endif
                    placeholder="e.g. admin or test.manager"
                    class="w-full min-h-[48px] px-3.5 py-2.5 text-sm bg-white border rounded-lg text-[#3b4048] placeholder-gray-400 focus:outline-none focus-visible:outline-2 focus-visible:outline-[#a92d31] focus:border-[#a92d31] {{ $errors->has('username') ? 'border-red-400' : 'border-gray-300' }}"
                />
                @error('username')
                    <p id="username-error" class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[#3b4048] mb-1.5">
                    Password
                </label>
                <div class="relative flex items-center">
                    <input
                        id="password"
                        name="password"
                        :type="showPassword ? 'text' : 'password'"
                        wire:model="password"
                        autocomplete="current-password"
                        enterkeyhint="done"
                        required
                        @if ($errors->has('password'))
                            aria-invalid="true"
                            aria-describedby="password-error"
                        @endif
                        placeholder="Enter your password"
                        class="w-full min-h-[48px] pl-3.5 pr-20 py-2.5 text-sm bg-white border rounded-lg text-[#3b4048] placeholder-gray-400 focus:outline-none focus-visible:outline-2 focus-visible:outline-[#a92d31] focus:border-[#a92d31] {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }}"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        :aria-pressed="showPassword.toString()"
                        aria-label="Toggle password visibility"
                        class="absolute right-1.5 min-h-[40px] px-2.5 text-xs font-semibold text-gray-600 hover:text-[#a92d31] rounded focus:outline-none focus-visible:outline-2 focus-visible:outline-[#a92d31]"
                    >
                        <span x-text="showPassword ? 'Hide' : 'Show'">Show</span>
                    </button>
                </div>
                @error('password')
                    <p id="password-error" class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-full min-h-[48px] btn-primary flex items-center justify-center gap-2 text-sm font-semibold focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#a92d31]"
            >
                <span wire:loading.remove wire:target="authenticate">Sign In</span>
                <span wire:loading wire:target="authenticate">Signing In...</span>
            </button>
        </form>
    </div>

    @if ($showTestAccounts)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xs font-heading font-bold uppercase tracking-wider text-gray-600">Role Testing Accounts</h2>
                <span class="text-[11px] font-mono text-gray-500">Password: <strong class="text-[#3b4048]">Test@1234</strong></span>
            </div>
            <p class="text-[11px] text-gray-500 mb-3">Click any role account below to fill credentials for role-based testing:</p>
            <div class="grid grid-cols-2 gap-2">
                @php
                    $testLogins = [
                        ['username' => 'test.sysadmin', 'role' => 'System & IT Admin', 'hint' => 'Override (IT Nazrul)'],
                        ['username' => 'test.itsupport', 'role' => 'IT Support', 'hint' => 'Information Technology'],
                        ['username' => 'test.manager', 'role' => 'Department Manager', 'hint' => 'Creative Leader'],
                        ['username' => 'test.finance', 'role' => 'Finance', 'hint' => 'Account Dept'],
                        ['username' => 'test.employee', 'role' => 'Employee', 'hint' => 'Quality Department'],
                        ['username' => 'nazrul', 'role' => 'Auditor', 'hint' => 'Read-Only Auditor'],
                    ];
                @endphp
                @foreach ($testLogins as $acct)
                    <button
                        type="button"
                        wire:click="fillTestAccount('{{ $acct['username'] }}')"
                        class="text-left p-2 rounded-lg border border-gray-200 hover:border-[#a92d31] hover:bg-red-50/40 transition focus:outline-none focus-visible:outline-2 focus-visible:outline-[#a92d31]"
                    >
                        <div class="text-xs font-bold text-[#3b4048]">{{ $acct['role'] }}</div>
                        <div class="text-[11px] font-mono text-[#a92d31]">{{ $acct['username'] }}</div>
                        <div class="text-[10px] text-gray-400">{{ $acct['hint'] }}</div>
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>
