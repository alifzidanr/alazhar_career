<div class="flex flex-col gap-3 mt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
    <p class="text-muted-foreground" x-show="total > 0" x-text="`Menampilkan ${rangeStart}–${rangeEnd} dari ${total}`"></p>
    <p class="text-muted-foreground" x-show="total === 0">Tidak ada hasil yang cocok.</p>

    <div class="flex items-center gap-2" x-show="totalPages > 1">
        <button type="button" @click="prevPage()" :disabled="page === 1" class="inline-flex items-center justify-center rounded-md border border-input bg-background px-3 py-1.5 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground disabled:pointer-events-none disabled:opacity-50">&larr; Sebelumnya</button>
        <span class="text-xs text-muted-foreground" x-text="`Hal ${page} / ${totalPages}`"></span>
        <button type="button" @click="nextPage()" :disabled="page === totalPages" class="inline-flex items-center justify-center rounded-md border border-input bg-background px-3 py-1.5 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground disabled:pointer-events-none disabled:opacity-50">Selanjutnya &rarr;</button>
    </div>
</div>
