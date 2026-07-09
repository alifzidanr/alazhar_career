

import Alpine from 'alpinejs';
import Viewer from 'viewerjs';

window.Alpine = Alpine;
window.Viewer = Viewer;

Alpine.data('tableFilter', (perPage = 15, initialFilters = {}, sortModes = {}) => ({
    search: '',
    filters: { ...initialFilters },
    sort: Object.keys(sortModes)[0] || '',
    sortModes,
    page: 1,
    perPage,

    init() {
        this.$watch('search', () => { this.page = 1; });
        this.$watch('filters', () => { this.page = 1; }, { deep: true });
        this.$watch('sort', () => { this.page = 1; });
    },

    reset() {
        this.search = '';
        for (const key in this.filters) this.filters[key] = '';
        this.sort = Object.keys(this.sortModes)[0] || '';
        this.page = 1;
    },

    matches(el) {
        const text = el.dataset.search || '';
        if (this.search && !text.includes(this.search.toLowerCase())) return false;

        for (const key in this.filters) {
            const value = this.filters[key];
            if (value && !(el.dataset[key] || '').split(' ').includes(value)) return false;
        }

        return true;
    },

    matchingRows() {
        const rows = Array.from(this.$refs.tbody?.querySelectorAll('[data-row]') || []).filter(el => this.matches(el));

        const mode = this.sortModes[this.sort];
        if (mode) {
            rows.sort((a, b) => {
                const av = Number(a.dataset[mode.field] ?? 0);
                const bv = Number(b.dataset[mode.field] ?? 0);
                return mode.dir === 'asc' ? av - bv : bv - av;
            });
        }

        return rows;
    },

    isVisible(el) {
        const idx = this.matchingRows().indexOf(el);
        if (idx === -1) return false;

        const start = (this.page - 1) * this.perPage;

        return idx >= start && idx < start + this.perPage;
    },

    get total() {
        return this.matchingRows().length;
    },

    get totalPages() {
        return Math.max(1, Math.ceil(this.total / this.perPage));
    },

    get rangeStart() {
        return this.total === 0 ? 0 : (this.page - 1) * this.perPage + 1;
    },

    get rangeEnd() {
        return Math.min(this.page * this.perPage, this.total);
    },

    prevPage() {
        if (this.page > 1) this.page--;
    },

    nextPage() {
        if (this.page < this.totalPages) this.page++;
    },
}));

Alpine.start();
