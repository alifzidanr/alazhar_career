<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg tracking-tight">Manajemen Kriteria</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-ui.card title="Tambah Kriteria" description="Kriteria ini akan tersedia sebagai pilihan dropdown saat menambahkan kriteria pada sebuah loker.">
                <form method="POST" action="{{ route('admin.kriteria.store') }}" class="flex gap-2">
                    @csrf
                    <x-ui.input type="text" name="teks_kriteria" placeholder="cth. S1 Pendidikan Bahasa Inggris" value="{{ old('teks_kriteria') }}" required class="flex-1" />
                    <x-ui.button type="submit">Tambah</x-ui.button>
                </form>
                <x-input-error :messages="$errors->get('teks_kriteria')" class="mt-2" />
            </x-ui.card>

            <x-ui.card :padded="false" class="overflow-hidden">
                <table class="min-w-full divide-y text-sm">
                    <thead class="bg-muted/50">
                        <tr class="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            <th class="px-4 py-3">Kriteria</th>
                            <th class="px-4 py-3">Dipakai</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($kriteriaList as $k)
                            <tr class="hover:bg-muted/30">
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.kriteria.update', $k) }}" class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <x-ui.input type="text" name="teks_kriteria" value="{{ $k->teks_kriteria }}" class="h-8 text-sm" />
                                        <x-ui.button type="submit" variant="outline" size="sm">Simpan</x-ui.button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">{{ $k->kriteria_loker_count }}x</td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.kriteria.destroy', $k) }}" onsubmit="return confirm('Hapus kriteria ini?')">
                                        @csrf @method('DELETE')
                                        <x-ui.button type="submit" variant="ghost" size="sm" class="text-destructive hover:text-destructive">Hapus</x-ui.button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-muted-foreground">Belum ada kriteria.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
