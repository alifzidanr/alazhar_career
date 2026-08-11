<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg tracking-tight">Dashboard Statistik Pelamar</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stat tiles -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-ui.card>
                    <p class="text-sm text-muted-foreground">Total Pelamar</p>
                    <p class="mt-1 text-3xl font-bold tracking-tight">{{ number_format($totalPelamar) }}</p>
                </x-ui.card>
                <x-ui.card>
                    <p class="text-sm text-muted-foreground">Lowongan Aktif</p>
                    <p class="mt-1 text-3xl font-bold tracking-tight">{{ number_format($totalLokerAktif) }}<span class="text-base font-normal text-muted-foreground"> / {{ number_format($totalLoker) }}</span></p>
                </x-ui.card>
                <x-ui.card>
                    <p class="text-sm text-muted-foreground">Pelamar 30 Hari Terakhir</p>
                    <p class="mt-1 text-3xl font-bold tracking-tight">{{ number_format($pelamarBaru30Hari) }}</p>
                </x-ui.card>
                <x-ui.card>
                    <p class="text-sm text-muted-foreground">Rasio Diterima</p>
                    <p class="mt-1 text-3xl font-bold tracking-tight">{{ $rasioDiterima }}<span class="text-base font-normal text-muted-foreground">%</span></p>
                </x-ui.card>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Status breakdown -->
                <x-ui.card title="Pelamar per Status">
                    @php
                        $variantBar = [
                            'success' => 'bg-emerald-600',
                            'warning' => 'bg-amber-500',
                            'destructive' => 'bg-red-600',
                            'info' => 'bg-blue-600',
                            'muted' => 'bg-slate-500',
                        ];
                        $statusMax = max(1, $statusBreakdown->max('total'));
                    @endphp
                    @if ($statusBreakdown->isEmpty())
                        <p class="text-sm text-muted-foreground">Belum ada data pelamar.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($statusBreakdown as $row)
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-1">
                                        <span class="font-medium">{{ $row['label'] }}</span>
                                        <span class="text-muted-foreground">{{ $row['total'] }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-muted overflow-hidden">
                                        <div class="h-full rounded-full {{ $variantBar[$row['variant']] }}" style="width: {{ round($row['total'] / $statusMax * 100) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.card>

                <!-- Tahap funnel -->
                <x-ui.card title="Pelamar per Tahap Rekrutmen">
                    @php
                        $tahapMax = max(1, $tahapBreakdown->max('total'));
                    @endphp
                    <div class="space-y-3">
                        @foreach ($tahapBreakdown as $row)
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="font-medium">{{ $row['label'] }}</span>
                                    <span class="text-muted-foreground">{{ $row['total'] }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-muted overflow-hidden">
                                    <div class="h-full rounded-full bg-brand-navy-600" style="width: {{ round($row['total'] / $tahapMax * 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            </div>

            <!-- Top loker -->
            <x-ui.card title="5 Lowongan dengan Pelamar Terbanyak">
                @if ($topLoker->isEmpty() || $topLoker->first()->pelamar_count === 0)
                    <p class="text-sm text-muted-foreground">Belum ada pelamar.</p>
                @else
                    @php($topLokerMax = max(1, $topLoker->max('pelamar_count')))
                    <div class="space-y-3">
                        @foreach ($topLoker as $loker)
                            @continue($loker->pelamar_count === 0)
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <a href="{{ route('admin.pelamar.index', ['loker' => $loker->id_loker]) }}" class="font-medium hover:underline">{{ $loker->judul_loker }}</a>
                                    <span class="text-muted-foreground">{{ $loker->pelamar_count }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-muted overflow-hidden">
                                    <div class="h-full rounded-full bg-brand-navy-600" style="width: {{ round($loker->pelamar_count / $topLokerMax * 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            <!-- 30-day trend -->
            <x-ui.card title="Tren Lamaran Masuk (30 Hari Terakhir)">
                <div x-data="{ hover: null }" class="relative">
                    <div class="flex items-end gap-1 h-32">
                        @foreach ($tren as $key => $row)
                            <div
                                class="flex-1 flex flex-col items-center justify-end h-full relative"
                                @mouseenter="hover = @js($key)"
                                @mouseleave="hover = null"
                            >
                                <div
                                    x-show="hover === @js($key)"
                                    x-cloak
                                    class="absolute bottom-full mb-1.5 z-10 rounded-md bg-foreground text-background text-xs px-2 py-1 whitespace-nowrap pointer-events-none shadow"
                                >{{ $row['tanggal']->translatedFormat('d M') }}: {{ $row['total'] }} pelamar</div>
                                <div
                                    class="w-full rounded-t transition-colors"
                                    :class="hover === @js($key) ? 'bg-brand-navy-700' : 'bg-brand-navy-600'"
                                    style="height: {{ $row['total'] > 0 ? max(4, round($row['total'] / $trenMax * 100)) : 2 }}%"
                                ></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between text-xs text-muted-foreground mt-2">
                        <span>{{ $tren->first()['tanggal']->translatedFormat('d M Y') }}</span>
                        <span>{{ $tren->last()['tanggal']->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
