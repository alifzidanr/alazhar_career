<x-app-layout>
    <x-slot name="title">Kelola Loker: {{ $loker->judul_loker }}</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-lg tracking-tight">Kelola Loker: {{ $loker->judul_loker }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            <x-ui.card title="Detail Loker">
                <form method="POST" action="{{ route('admin.loker.update', $loker) }}" class="space-y-5">
                    @csrf
                    @method('PUT')
                    @include('admin.loker._form', ['loker' => $loker])

                    <x-ui.button type="submit">
                        Simpan Perubahan
                    </x-ui.button>
                </form>
            </x-ui.card>

            <x-ui.card title="Kriteria Loker">
                <div class="space-y-2 mb-5">
                    @php
                        $bobotLabel = ['wajib' => 'Wajib', 'diutamakan' => 'Diutamakan', 'nilai_tambah' => 'Nilai Tambah'];
                        $bobotVariant = ['wajib' => 'destructive', 'diutamakan' => 'warning', 'nilai_tambah' => 'secondary'];
                    @endphp
                    @forelse ($loker->kriteria as $k)
                        <div class="flex items-center justify-between rounded-md border px-3 py-2 text-sm">
                            <span class="flex items-center gap-2 flex-wrap">
                                <x-ui.badge :variant="$bobotVariant[$k->bobot]">{{ $bobotLabel[$k->bobot] }}</x-ui.badge>
                                <span>{{ $k->teksKriteria() ?? '(tanpa kriteria)' }}</span>
                            </span>
                            <form method="POST" action="{{ route('admin.loker.kriteria.destroy', [$loker, $k]) }}" x-data @submit.prevent="$dispatch('confirm-dialog', { title: 'Hapus kriteria ini?', destructive: true, confirmText: 'Hapus', form: $el })">
                                @csrf @method('DELETE')
                                <x-ui.button type="submit" variant="ghost" size="sm" class="text-destructive hover:text-destructive">Hapus</x-ui.button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-muted-foreground">Belum ada kriteria.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('admin.loker.kriteria.store', $loker) }}" class="grid sm:grid-cols-2 gap-3 border-t pt-4">
                    @csrf
                    <div>
                        <x-ui.label for="id_kriteria">Kriteria</x-ui.label>
                        <x-ui.select id="id_kriteria" name="id_kriteria">
                            <option value="">-- (tanpa kriteria) --</option>
                            @foreach ($kriteriaList as $kr)
                                <option value="{{ $kr->id_kriteria }}">{{ $kr->teks_kriteria }}</option>
                            @endforeach
                        </x-ui.select>
                        <p class="text-xs text-muted-foreground mt-1">Belum ada pilihan yang cocok? Tambahkan lewat menu <a href="{{ route('admin.kriteria.index') }}" class="underline">Kriteria</a>.</p>
                    </div>
                    <div>
                        <x-ui.label for="bobot">Bobot</x-ui.label>
                        <x-ui.select id="bobot" name="bobot" required>
                            <option value="wajib">Wajib</option>
                            <option value="diutamakan">Diutamakan</option>
                            <option value="nilai_tambah">Nilai Tambah</option>
                        </x-ui.select>
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.button type="submit" variant="secondary">+ Tambah Kriteria</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
