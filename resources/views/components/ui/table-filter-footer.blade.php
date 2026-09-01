<div class="flex flex-col gap-3 mt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-4">
        <p class="text-muted-foreground" x-show="total > 0" x-text="`Menampilkan ${rangeStart}–${rangeEnd} dari ${total}`"></p>
        <p class="text-muted-foreground" x-show="total === 0">Tidak ada hasil yang cocok.</p>

        <label class="flex items-center gap-2 text-muted-foreground">
            Baris:
            <x-ui.select x-model.number="perPage" class="!h-8 !w-auto py-0">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </x-ui.select>
        </label>
    </div>

    <div class="flex items-center gap-2" x-show="totalPages > 1">
        <button type="button" @click="prevPage()" :disabled="page === 1" class="inline-flex items-center justify-center rounded-md border border-input bg-background px-3 py-1.5 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground disabled:pointer-events-none disabled:opacity-50">&larr; Sebelumnya</button>
        <span class="text-xs text-muted-foreground" x-text="`Hal ${page} / ${totalPages}`"></span>
        <button type="button" @click="nextPage()" :disabled="page === totalPages" class="inline-flex items-center justify-center rounded-md border border-input bg-background px-3 py-1.5 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground disabled:pointer-events-none disabled:opacity-50">Selanjutnya &rarr;</button>
    </div>
</div>
