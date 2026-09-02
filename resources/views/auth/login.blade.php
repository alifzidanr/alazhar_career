<x-guest-layout>
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
</x-guest-layout>
