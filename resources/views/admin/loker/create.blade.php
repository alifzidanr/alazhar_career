<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg tracking-tight">Loker Baru</h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <x-ui.card>
                <form method="POST" action="{{ route('admin.loker.store') }}" class="space-y-5">
                    @csrf
                    @include('admin.loker._form')

                    <x-ui.button type="submit">
                        Simpan &amp; Lanjut Atur Kriteria
                    </x-ui.button>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
