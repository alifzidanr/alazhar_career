<x-layouts.public :title="'Lowongan Kerja - Rekrutmen YPI Al Azhar'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-semibold tracking-tight mb-1">Seluruh Lowongan</h1>
        <p class="text-muted-foreground mb-8">Bergabunglah bersama YPI Al Azhar. Berikut lowongan yang sedang dibuka.</p>

        @if ($lokerList->isEmpty())
            <x-ui.card class="text-center text-muted-foreground">
                Belum ada lowongan yang dibuka saat ini. Silakan cek kembali nanti.
            </x-ui.card>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($lokerList as $loker)
                    <a href="{{ route('loker.show', $loker) }}" class="block">
                        <x-ui.card class="hover:border-foreground/30 hover:shadow-md transition-all h-full">
                            <div class="flex items-start justify-between gap-2">
                                <h2 class="font-semibold text-base">{{ $loker->judul_loker }}</h2>
                                <x-ui.badge variant="success" class="shrink-0">Dibuka</x-ui.badge>
                            </div>
                            @if ($loker->lokasi)
                                <p class="text-sm text-muted-foreground mt-1">📍 {{ $loker->lokasi }}</p>
                            @endif
                            @if ($loker->deskripsi_loker)
                                <p class="text-sm text-muted-foreground mt-3 line-clamp-3">{{ $loker->deskripsi_loker }}</p>
                            @endif
                            @if ($loker->end_time)
                                <p class="text-xs text-muted-foreground/70 mt-3">Batas lamaran: {{ $loker->end_time->format('d/m/Y') }}</p>
                            @endif
                        </x-ui.card>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.public>
