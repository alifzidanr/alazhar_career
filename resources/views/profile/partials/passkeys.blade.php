<section id="passkey" class="scroll-mt-24">
    <header>
        <h2 class="text-base font-semibold tracking-tight">Passkey</h2>

        <p class="mt-1 text-sm text-muted-foreground">
            Masuk tanpa password menggunakan sidik jari, Face ID, Windows Hello, atau PIN perangkat. Passkey disimpan di perangkat Anda dan bisa dipakai di halaman login dengan tombol "Masuk dengan Passkey".
        </p>
    </header>

    <div x-data="passkeyRegister" class="mt-6">
        <form @submit.prevent="register()" class="flex flex-col sm:flex-row gap-2">
            <x-ui.input type="text" x-model="alias" placeholder="Nama perangkat (cth. MacBook Kantor)" maxlength="100" class="flex-1 min-w-0" />
            <x-ui.button type="submit" class="shrink-0" x-bind:disabled="busy">
                <span x-text="busy ? 'Menunggu perangkat...' : 'Daftarkan Passkey'">Daftarkan Passkey</span>
            </x-ui.button>
        </form>
        <p x-show="error" x-text="error" x-cloak class="mt-2 text-sm text-destructive"></p>
    </div>

    <div class="mt-6 divide-y rounded-md border">
        @forelse ($passkeys as $passkey)
            <div class="flex items-center justify-between gap-3 px-4 py-3">
                <div class="min-w-0">
                    <p class="text-sm font-medium truncate">{{ $passkey->alias ?: 'Passkey' }}</p>
                    <p class="text-xs text-muted-foreground">
                        Didaftarkan {{ $passkey->created_at->translatedFormat('d M Y H:i') }}
                        @if ($passkey->updated_at && $passkey->updated_at->gt($passkey->created_at))
                            · Terakhir dipakai {{ $passkey->updated_at->diffForHumans() }}
                        @endif
                    </p>
                </div>
                <form method="POST" action="{{ route('webauthn.destroy', $passkey->getKey()) }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: 'Hapus passkey ini?', description: 'Perangkat ini tidak bisa lagi dipakai untuk login dengan passkey.', destructive: true, confirmText: 'Hapus', form: $el })">
                    @csrf @method('DELETE')
                    <x-ui.button type="submit" variant="ghost" size="sm" class="text-destructive hover:text-destructive">Hapus</x-ui.button>
                </form>
            </div>
        @empty
            <p class="px-4 py-6 text-center text-sm text-muted-foreground">Belum ada passkey terdaftar.</p>
        @endforelse
    </div>
</section>
