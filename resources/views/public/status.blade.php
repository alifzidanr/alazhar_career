<x-layouts.public :title="'Status Lamaran - Rekrutmen YPI Al Azhar'">
    <div
        x-data="{ open: {{ session('lamaran_sukses') ? 'true' : 'false' }} }"
        class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16"
    >
        @if (session('lamaran_sukses'))
            <div x-show="open" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50"></div>
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="relative w-full max-w-md rounded-lg border bg-background p-6 shadow-lg text-center"
                >
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6 w-6 text-emerald-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold">Lamaran Terkirim</h3>
                    <p class="mt-2 text-sm text-muted-foreground leading-relaxed">{{ session('lamaran_sukses') }}</p>
                    <x-ui.button type="button" class="mt-6 w-full" @click="open = false">OK</x-ui.button>
                </div>
            </div>
        @endif

        <h1 class="text-2xl font-semibold tracking-tight text-center">Cek Status Lamaran</h1>
        <p class="mt-1.5 text-sm text-muted-foreground text-center">Masukkan email atau no. WhatsApp yang Anda gunakan saat mendaftar.</p>

        <x-ui.card class="mt-8">
            <form method="POST" action="{{ route('status.search') }}" class="grid sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <x-ui.label for="email">Email</x-ui.label>
                    <x-ui.input type="email" id="email" name="email" value="{{ old('email') }}" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <x-ui.label for="no_hp">No. WhatsApp</x-ui.label>
                    <x-ui.input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" />
                    <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs text-muted-foreground mb-2">Isi salah satu: email saja atau no. WhatsApp saja.</p>
                    <x-ui.button type="submit" class="w-full sm:w-auto">Cek Status</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        @isset($hasilList)
            <div class="mt-8 space-y-4">
                <h2 class="text-sm font-semibold text-muted-foreground uppercase tracking-wide">
                    {{ $hasilList->count() }} Lamaran Ditemukan
                </h2>
                @foreach ($hasilList as $p)
                    <x-ui.card>
                        <div class="flex items-start justify-between gap-3 flex-wrap">
                            <div>
                                <h3 class="font-semibold">{{ $p->loker->judul_loker }}</h3>
                                <p class="text-sm text-muted-foreground mt-0.5">Diajukan pada {{ $p->tanggal_apply->translatedFormat('d M Y') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-tahap-badge :tahap="$p->tahapRekrutmen" />
                                <x-status-badge :status="$p->statusPelamar" />
                            </div>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @endisset
    </div>
</x-layouts.public>
