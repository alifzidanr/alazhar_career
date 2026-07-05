<div
    x-data="{
        toasts: [],
        show(toast) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, ...toast });
            setTimeout(() => this.dismiss(id), 4000);
        },
        dismiss(id) {
            this.toasts = this.toasts.filter((t) => t.id !== id);
        },
    }"
    x-init="
        @if (session('status'))
            show({ variant: 'success', message: @js(session('status')) });
        @endif
        @if ($errors->any())
            show({ variant: 'destructive', message: @js($errors->count() > 1 ? 'Terdapat '.$errors->count().' error pada formulir.' : $errors->first()) });
        @endif
    "
    class="pointer-events-none fixed bottom-0 right-0 z-[100] flex w-full max-w-sm flex-col gap-2 p-4 sm:bottom-4 sm:right-4"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto flex w-full items-start gap-3 rounded-lg border bg-background p-4 shadow-lg"
            :class="toast.variant === 'destructive' ? 'border-destructive/40' : 'border-border'"
        >
            <template x-if="toast.variant === 'success'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </template>
            <template x-if="toast.variant === 'destructive'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mt-0.5 h-5 w-5 shrink-0 text-destructive">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </template>
            <p class="flex-1 text-sm font-medium text-foreground" x-text="toast.message"></p>
            <button type="button" @click="dismiss(toast.id)" class="shrink-0 text-muted-foreground hover:text-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
