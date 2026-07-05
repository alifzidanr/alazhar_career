<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg tracking-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-ui.card>
                @include('profile.partials.update-profile-information-form')
            </x-ui.card>

            <x-ui.card>
                @include('profile.partials.update-password-form')
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
