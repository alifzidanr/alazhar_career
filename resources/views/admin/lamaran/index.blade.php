<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg tracking-tight">Daftar Lamaran (Keseluruhan)</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-ui.card>
                <form method="GET" class="grid sm:grid-cols-5 gap-3">
                    <x-ui.input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama..." />

                    <x-ui.select name="loker">
                        <option value="">Semua Loker</option>
                        @foreach ($lokerOptions as $l)
                            <option value="{{ $l->id_loker }}" @selected(request('loker') == $l->id_loker)>{{ $l->judul_loker }}</option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.select name="tahap">
                        <option value="">Semua Tahap</option>
                        @foreach ($tahapOptions as $t)
                            <option value="{{ $t->id_tahap_rekrutmen }}" @selected(request('tahap') == $t->id_tahap_rekrutmen)>{{ $t->id_tahap_rekrutmen }}. {{ $t->tahap_rekrutmen }}</option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.select name="status">
                        <option value="">Semua Status</option>
                        @foreach ($statusOptions as $s)
                            <option value="{{ $s->id_status_pelamar }}" @selected(request('status') == $s->id_status_pelamar)>{{ ucfirst($s->status_pelamar) }}</option>
                        @endforeach
                    </x-ui.select>

                    <div class="flex gap-2">
                        <x-ui.button type="submit" class="flex-1">Filter</x-ui.button>
                        <x-ui.button :href="route('admin.lamaran.index')" variant="outline">Reset</x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            <x-ui.card :padded="false" class="overflow-hidden">
                <table class="min-w-full divide-y text-sm">
                    <thead class="bg-muted/50">
                        <tr class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Loker</th>
                            <th class="px-4 py-3">Pendidikan</th>
                            <th class="px-4 py-3">Tahap</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tgl Apply</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($pelamarList as $p)
                            <tr class="hover:bg-muted/30">
                                <td class="px-4 py-3 font-medium">{{ $p->namaLengkap() }}</td>
                                <td class="px-4 py-3 text-muted-foreground">{{ $p->loker->judul_loker }}</td>
                                <td class="px-4 py-3 text-muted-foreground">{{ $p->pendidikanTerakhir->pendidikan_terakhir }}</td>
                                <td class="px-4 py-3"><x-tahap-badge :tahap="$p->tahapRekrutmen" /></td>
                                <td class="px-4 py-3"><x-status-badge :status="$p->statusPelamar" /></td>
                                <td class="px-4 py-3 text-muted-foreground">{{ $p->tanggal_apply->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <x-ui.button :href="route('admin.pelamar.show', $p)" variant="ghost" size="sm">Detail &rarr;</x-ui.button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-muted-foreground">Belum ada data lamaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-ui.card>

            {{ $pelamarList->links() }}
        </div>
    </div>
</x-app-layout>
