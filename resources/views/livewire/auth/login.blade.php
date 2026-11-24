<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#8f2428] to-[#360a0c] p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        {{-- Logo --}}
        <div class="flex flex-col items-center mb-8">
            <img src="{{ asset('images/fg_logo.png') }}" alt="FG Logo" class="h-16 mb-4">
            <h1 class="text-2xl font-heading font-bold text-fg-red text-center">FG IT ASSET MANAGEMENT</h1>
            <p class="text-muted text-sm mt-1">Sign in to continue</p>
        </div>

        {{-- Error --}}
        @if ($errors->any())
            <div role="alert" aria-live="polite" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm font-medium">
                {{ $errors->first('username') ?: $errors->first('password') }}
            </div>
        @endif

        {{-- Form --}}
        <form wire:submit="authenticate" method="POST" class="space-y-5">
            <x-input
                label="Username"
                placeholder="Enter your username"
                wire:model="username"
                icon="user"
            />

            <x-input
                label="Password"
                type="password"
                placeholder="Enter your password"
                wire:model="password"
                icon="lock-closed"
            />

            <x-button
                type="submit"
                class="w-full bg-fg-red hover:bg-fg-red-dark text-white font-semibold py-3 rounded-xl transition"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Sign In</span>
                <span wire:loading>
                    <x-icon name="arrow-path" class="w-5 h-5 animate-spin" /> Signing in...
                </span>
            </x-button>
        </form>
    </div>
</div>
