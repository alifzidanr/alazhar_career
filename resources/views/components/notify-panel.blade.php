@props(['pelamar'])

@php
    $templates = \App\Support\NotifikasiTemplates::all();
    $suggested = \App\Support\NotifikasiTemplates::suggestedKey($pelamar);
    $rendered = collect($templates)->mapWithKeys(fn ($t, $key) => [$key => \App\Support\NotifikasiTemplates::render($key, $pelamar)]);
@endphp

<div x-data="{
    template: '{{ $suggested ?? '' }}',
    subject: '',
    body: '',
    templates: {{ $rendered->toJson() }},
    applyTemplate() {
        if (this.template && this.templates[this.template]) {
            this.subject = this.templates[this.template].subject;
            this.body = this.templates[this.template].body;
        } else {
            this.subject = '';
            this.body = '';
        }
    }
}" x-init="applyTemplate()">
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="space-y-3">
            <div>
                <x-ui.label for="notify-template">Template Pesan</x-ui.label>
                <x-ui.select id="notify-template" x-model="template" @change="applyTemplate()">
                    <option value="">-- Kosong / Tulis Manual --</option>
                    @foreach ($templates as $key => $t)
                        <option value="{{ $key }}">{{ $t['label'] }}</option>
                    @endforeach
                </x-ui.select>
            </div>

            <div>
                <x-ui.label for="notify-subject">Subjek (email)</x-ui.label>
                <x-ui.input id="notify-subject" type="text" x-model="subject" />
            </div>
        </div>

        <div>
            <x-ui.label for="notify-body">Isi Pesan</x-ui.label>
            <x-ui.textarea id="notify-body" x-model="body" rows="6" class="h-[calc(100%-1.75rem)]"></x-ui.textarea>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.pelamar.notify', $pelamar) }}" class="mt-4 flex gap-2">
        @csrf
        <input type="hidden" name="template" :value="template">
        <input type="hidden" name="subject" :value="subject">
        <input type="hidden" name="body" :value="body">

        <x-ui.button type="submit" name="channel" value="whatsapp" @click.prevent="$dispatch('confirm-dialog', { title: 'Buka WhatsApp untuk mengirim pesan ini?', form: $el.closest('form') })"
            class="bg-emerald-600 text-white shadow-sm hover:bg-emerald-500"
            :disabled="! $pelamar->no_hp">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M12.04 2c-5.46 0-9.9 4.44-9.9 9.9 0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.9-4.44 9.9-9.9s-4.44-9.9-9.9-9.9Zm0 18.1a8.2 8.2 0 0 1-4.17-1.14l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 1 1 6.96 3.85Zm4.5-6.15c-.25-.12-1.46-.72-1.69-.8-.23-.08-.39-.12-.56.12-.16.25-.64.8-.78.96-.14.16-.29.18-.53.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.24-1.47-1.38-1.72-.15-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.15.16-.25.24-.41.08-.16.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.42-.14-.01-.31-.01-.47-.01a.9.9 0 0 0-.66.31c-.23.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.16 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.46-.6 1.66-1.17.21-.58.21-1.08.14-1.18-.06-.1-.22-.16-.47-.28Z"/></svg>
            Kirim via WhatsApp
        </x-ui.button>

        <x-ui.button type="submit" name="channel" value="email" @click.prevent="$dispatch('confirm-dialog', { title: 'Buka aplikasi email untuk mengirim pesan ini?', form: $el.closest('form') })"
            class="bg-blue-600 text-white shadow-sm hover:bg-blue-500"
            :disabled="! $pelamar->email">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M2.25 6.75A2.25 2.25 0 0 1 4.5 4.5h15a2.25 2.25 0 0 1 2.25 2.25v10.5A2.25 2.25 0 0 1 19.5 19.5h-15a2.25 2.25 0 0 1-2.25-2.25V6.75Zm2.4-.75 7.35 5.51L19.35 6H4.65Zm15.6 1.29-7.03 5.27a1.5 1.5 0 0 1-1.79 0L4.35 7.29V17.25h15.15V7.29Z"/></svg>
            Kirim via Email
        </x-ui.button>
    </form>
</div>
