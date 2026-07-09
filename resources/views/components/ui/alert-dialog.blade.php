<div
    x-data="{
        show: false,
        title: '',
        description: '',
        confirmText: 'Lanjutkan',
        cancelText: 'Batal',
        destructive: false,
        _form: null,
        open(detail) {
            this.title = detail.title ?? 'Apakah Anda yakin?';
            this.description = detail.description ?? '';
            this.confirmText = detail.confirmText ?? 'Lanjutkan';
            this.cancelText = detail.cancelText ?? 'Batal';
            this.destructive = detail.destructive ?? false;
            this._form = detail.form ?? null;
            this.show = true;
        },
        confirm() {
            this.show = false;
            this._form?.submit();
        },
    }"
    x-on:confirm-dialog.window="open($event.detail)"
    x-cloak
>
    <div x-show="show" class="fixed inset-0 z-[90] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="show = false"></div>
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-md rounded-lg border bg-background p-6 shadow-lg"
            @keydown.escape.window="show = false"
        >
            <h3 class="text-base font-semibold" x-text="title"></h3>
            <p class="mt-1.5 text-sm text-muted-foreground" x-show="description" x-text="description"></p>

            <div class="mt-5 flex justify-end gap-2">
                <x-ui.button type="button" variant="outline" @click="show = false" x-text="cancelText"></x-ui.button>
                <span x-show="!destructive">
                    <x-ui.button type="button" @click="confirm()" x-text="confirmText"></x-ui.button>
                </span>
                <span x-show="destructive">
                    <x-ui.button type="button" variant="destructive" @click="confirm()" x-text="confirmText"></x-ui.button>
                </span>
            </div>
        </div>
    </div>
</div>
