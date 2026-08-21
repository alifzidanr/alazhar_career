@props(['loker'])

@php
    $jenjangNama = $loker->jenjang->nama_jenjang ?? null;
    $isBaru = $loker->start_time && $loker->start_time->gte(now()->subDays(7));
    $isSegera = $loker->end_time && $loker->end_time->between(now(), now()->addDays(7));
    $daysLeft = $loker->end_time ? (int) now()->startOfDay()->diffInDays($loker->end_time->copy()->startOfDay(), false) : null;
@endphp

<x-ui.card class="hover:border-brand-navy-100 hover:shadow-md transition-all h-full flex flex-col bg-white">
    <div class="flex-1">
    <div class="flex items-start justify-between gap-3">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-navy-50 text-brand-navy-600">
            @if ($jenjangNama && str_contains($jenjangNama, 'Guru'))
                {{-- academic cap --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443" /></svg>
            @elseif ($jenjangNama === 'Satpam')
                {{-- shield check --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.573-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
            @elseif ($jenjangNama === 'Driver')
                {{-- truck --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25h5.34l4.318 5.31a1.5 1.5 0 0 1 .318.936v3.879a1.125 1.125 0 0 1-1.125 1.125H18M4.5 14.25h6a.75.75 0 0 0 .75-.75V6a.75.75 0 0 0-.75-.75H4.5A.75.75 0 0 0 3.75 6v7.5a.75.75 0 0 0 .75.75Z" /></svg>
            @elseif ($jenjangNama === 'Tata Usaha')
                {{-- clipboard list --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" /></svg>
            @elseif ($jenjangNama === 'Teknisi')
                {{-- wrench & screwdriver --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L1.5 3l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" /></svg>
            @else
                {{-- generic staff (users) --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
            @endif
        </div>

        <div class="text-right shrink-0">
            @if ($isBaru)
                <x-ui.badge variant="success">Lowongan Baru</x-ui.badge>
            @elseif ($isSegera)
                <x-ui.badge variant="destructive">Segera Berakhir</x-ui.badge>
                @if ($daysLeft !== null)
                    <p class="mt-1 text-[11px] font-semibold text-destructive">{{ $daysLeft <= 0 ? 'Hari ini' : 'H-'.$daysLeft }}</p>
                @endif
            @elseif ($loker->end_time)
                <x-ui.badge variant="muted">Sampai {{ $loker->end_time->translatedFormat('d F') }}</x-ui.badge>
            @endif
        </div>
    </div>

    <h3 class="mt-4 font-semibold text-base leading-snug">{{ $loker->judul_loker }}</h3>

    @if ($loker->wilayah || $jenjangNama)
        <p class="mt-1.5 flex items-center gap-1.5 text-sm text-muted-foreground">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-4 w-4 shrink-0 text-brand-navy-600"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
            {{ $loker->wilayah }}{{ $loker->wilayah && $jenjangNama ? ' — ' : '' }}{{ $jenjangNama }}
        </p>
    @endif

    @if ($loker->deskripsi_loker)
        <p class="mt-3 text-sm text-muted-foreground/90 line-clamp-2">{{ Str::limit($loker->deskripsi_loker, 100) }}</p>
    @endif
    </div>

    <div class="mt-4 flex items-center justify-between gap-2">
        @if ($jenjangNama)
            <span class="inline-flex items-center rounded-md bg-muted px-2 py-1 text-[11px] font-semibold tracking-wide text-muted-foreground uppercase">{{ $jenjangNama }}</span>
        @else
            <span></span>
        @endif
        <span class="inline-flex items-center gap-1 text-sm font-semibold text-brand-navy-600">
            Lihat Detail
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" /></svg>
        </span>
    </div>
</x-ui.card>
