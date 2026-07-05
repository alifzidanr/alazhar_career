<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg tracking-tight">Manajemen Pelamar</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex flex-wrap gap-2">
                @foreach ($tahapOptions as $t)
                    <a href="{{ route('admin.pelamar.index', ['tahap' => $t->id_tahap_rekrutmen]) }}"
                       class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium border transition-colors {{ $tahapAktif === $t->id_tahap_rekrutmen ? 'bg-primary text-primary-foreground border-primary' : 'bg-background text-muted-foreground border-input hover:bg-accent hover:text-accent-foreground' }}">
                        {{ $t->id_tahap_rekrutmen }}. {{ $t->tahap_rekrutmen }}
                        <span class="text-xs opacity-75">({{ $counts[$t->id_tahap_rekrutmen] ?? 0 }})</span>
                    </a>
                @endforeach
            </div>

            <x-ui.card :padded="false" class="overflow-x-auto">
                <table class="min-w-full divide-y text-sm">
                    <thead class="bg-muted/50">
                        <tr class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Loker</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tgl Apply</th>
                            <th class="px-4 py-3">Catatan</th>
                            <th class="px-4 py-3">Aksi Cepat</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($pelamarList as $p)
                            <tr class="hover:bg-muted/30">
                                <td class="px-4 py-3 font-medium whitespace-nowrap">{{ $p->namaLengkap() }}</td>
                                <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->loker->judul_loker }}</td>
                                <td class="px-4 py-3 whitespace-nowrap"><x-status-badge :status="$p->statusPelamar" /></td>
                                <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $p->tanggal_apply->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.pelamar.catatan', $p) }}" class="flex items-center gap-1">
                                        @csrf @method('PATCH')
                                        <x-ui.input type="text" name="catatan" value="{{ $p->catatan }}" placeholder="Catatan..." class="!h-7 w-32 text-xs" />
                                        <x-ui.button type="submit" size="sm" variant="outline" title="Simpan Catatan" class="!h-7 !px-2">💾</x-ui.button>
                                    </form>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <form method="POST" action="{{ route('admin.pelamar.status', $p) }}" onsubmit="return confirm('Tandai lolos?')">
                                            @csrf @method('PATCH')
                                            <x-ui.button type="submit" name="id_status_pelamar" value="{{ \App\Models\StatusPelamar::LOLOS }}" size="sm" variant="outline" title="Tandai Lolos" class="!h-7 !px-2 text-emerald-700 border-emerald-200 bg-emerald-50 hover:bg-emerald-100">L</x-ui.button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.pelamar.status', $p) }}" onsubmit="return confirm('Tandai tidak lolos?')">
                                            @csrf @method('PATCH')
                                            <x-ui.button type="submit" name="id_status_pelamar" value="{{ \App\Models\StatusPelamar::TIDAK_LOLOS }}" size="sm" variant="outline" title="Tandai Tidak Lolos" class="!h-7 !px-2">TL</x-ui.button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.pelamar.status', $p) }}" onsubmit="return confirm('Tolak pelamar ini?')">
                                            @csrf @method('PATCH')
                                            <x-ui.button type="submit" name="id_status_pelamar" value="{{ \App\Models\StatusPelamar::DITOLAK }}" size="sm" variant="outline" title="Tandai Ditolak" class="!h-7 !px-2 text-destructive border-destructive/30 bg-destructive/5 hover:bg-destructive/10">DT</x-ui.button>
                                        </form>
                                        @if ($p->id_tahap_rekrutmen > \App\Models\TahapRekrutmen::SELEKSI_BERKAS)
                                            <form method="POST" action="{{ route('admin.pelamar.mundur', $p) }}" onsubmit="return confirm('Mundurkan ke tahap sebelumnya?')">
                                                @csrf @method('PATCH')
                                                <x-ui.button type="submit" size="sm" variant="outline" title="Mundurkan ke Tahap Sebelumnya" class="!h-7 !px-2">&larr;</x-ui.button>
                                            </form>
                                        @endif
                                        @if ($p->id_status_pelamar === \App\Models\StatusPelamar::LOLOS && $p->id_tahap_rekrutmen < \App\Models\TahapRekrutmen::MIGRASI_DATA)
                                            <form method="POST" action="{{ route('admin.pelamar.lanjut', $p) }}" onsubmit="return confirm('Lanjutkan ke tahap berikutnya?')">
                                                @csrf @method('PATCH')
                                                <x-ui.button type="submit" size="sm" variant="outline" title="Lanjutkan ke Tahap Berikutnya" class="!h-7 !px-2">&rarr;</x-ui.button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.pelamar.notify', $p) }}" onsubmit="return confirm('Buka WhatsApp untuk hubungi {{ $p->namaLengkap() }}?')">
                                            @csrf
                                            <input type="hidden" name="body" value="">
                                            <x-ui.button type="submit" name="channel" value="whatsapp" size="sm" title="Hubungi via WhatsApp" class="!h-7 !px-2 bg-emerald-600 hover:bg-emerald-500" :disabled="! $p->no_hp">WA</x-ui.button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.pelamar.notify', $p) }}" onsubmit="return confirm('Buka Email untuk hubungi {{ $p->namaLengkap() }}?')">
                                            @csrf
                                            <input type="hidden" name="body" value="">
                                            <input type="hidden" name="subject" value="">
                                            <x-ui.button type="submit" name="channel" value="email" size="sm" title="Hubungi via Email" class="!h-7 !px-2 bg-blue-600 hover:bg-blue-500" :disabled="! $p->email">✉</x-ui.button>
                                        </form>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <x-ui.button :href="route('admin.pelamar.show', $p)" variant="ghost" size="sm">Detail &rarr;</x-ui.button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-muted-foreground">Belum ada pelamar pada tahap ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
