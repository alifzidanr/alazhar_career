<x-guest-layout>
    <x-slot name="title">Login HR</x-slot>

    <div class="mb-6 flex items-start justify-between gap-3">
        <div>
            <h1 class="text-lg font-semibold tracking-tight">Login HR</h1>
            <p class="text-sm text-muted-foreground mt-1">Masuk untuk mengelola lamaran dan lowongan.</p>
        </div>
        <x-ui.button :href="route('loker.index')" variant="outline" size="sm" class="shrink-0">Lihat Portal Publik</x-ui.button>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-input text-primary shadow-sm focus:ring-ring" name="remember">
                <span class="ms-2 text-sm text-muted-foreground">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end">
            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <div x-data="passkeyLogin" class="mt-6">
        <div class="relative flex items-center gap-3 text-xs uppercase text-muted-foreground">
            <span class="h-px flex-1 bg-border"></span>
            atau
            <span class="h-px flex-1 bg-border"></span>
        </div>

        <x-ui.button type="button" variant="outline" class="mt-4 w-full" @click="login()" x-bind:disabled="busy">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 shrink-0"><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6M15.5 7.5l3 3L22 7l-3-3"/></svg>
            <span x-text="busy ? 'Menunggu perangkat...' : 'Masuk dengan Passkey'">Masuk dengan Passkey</span>
        </x-ui.button>
        <p x-show="error" x-text="error" x-cloak class="mt-2 text-sm text-destructive"></p>
    </div>
</x-guest-layout>
