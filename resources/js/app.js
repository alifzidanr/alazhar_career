

import Alpine from 'alpinejs';
import Viewer from 'viewerjs';

window.Alpine = Alpine;
window.Viewer = Viewer;

// Forms across the app do full-page POST/PATCH submits, which reload the
// page at the top by default. Remember the scroll position right before a
// submit and restore it once the redirected page loads, so admins editing
// a record mid-page (or mid-list) aren't dropped back to the top.
(function () {
    const scrollKey = () => 'scrollPos:' + location.pathname + location.search;

    const restoreScroll = () => {
        const saved = sessionStorage.getItem(scrollKey());
        if (saved === null) return;
        sessionStorage.removeItem(scrollKey());
        const y = parseInt(saved, 10);
        // The app sets a global `scroll-behavior: smooth` on <html>, which would
        // otherwise turn this into an animated scroll that can be cut short by
        // layout shifts (fonts/images) still happening while the page loads.
        if (!Number.isNaN(y)) window.scrollTo({ top: y, left: 0, behavior: 'instant' });
    };

    if (document.readyState === 'complete') {
        restoreScroll();
    } else {
        window.addEventListener('load', restoreScroll);
    }

    document.addEventListener('submit', (e) => {
        if (e.target instanceof HTMLFormElement) {
            sessionStorage.setItem(scrollKey(), String(window.scrollY));
        }
    }, true);
})();

// Live thousand-separator formatting for currency-style inputs opted in via
// data-format="ribuan" (e.g. typing "1000" displays as "1.000"). The dots
// are stripped back to plain digits right before the form submits, so the
// backend still receives a plain integer.
(function () {
    const toDigits = (value) => value.replace(/\D/g, '');
    const format = (digits) => (digits === '' ? '' : Number(digits).toLocaleString('id-ID'));

    document.addEventListener('input', (e) => {
        const el = e.target;
        if (!(el instanceof HTMLInputElement) || el.dataset.format !== 'ribuan') return;
        el.value = format(toDigits(el.value));
    });

    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        form.querySelectorAll('input[data-format="ribuan"]').forEach((el) => {
            el.value = toDigits(el.value);
        });
    }, true);
})();

Alpine.data('tableFilter', (perPage = 15, initialFilters = {}, sortModes = {}) => ({
    search: '',
    filters: { ...initialFilters },
    sort: Object.keys(sortModes)[0] || '',
    sortModes,
    page: 1,
    perPage,
    selectedIds: [],

    init() {
        this.$watch('search', () => { this.page = 1; });
        this.$watch('filters', () => { this.page = 1; }, { deep: true });
        this.$watch('sort', () => { this.page = 1; this.reorder(); });
        this.$watch('perPage', () => { this.page = 1; });
        this.reorder();
    },

    // x-show only hides non-matching/off-page rows, it never moves them, so
    // sorting alone wouldn't visibly reorder rows already on the same page.
    // Physically re-append rows (in sorted order) to make sort visible.
    reorder() {
        const tbody = this.$refs.tbody;
        if (!tbody || !this.sortModes[this.sort]) return;
        this.matchingRows().forEach((el) => tbody.appendChild(el));
    },

    reset() {
        this.search = '';
        for (const key in this.filters) this.filters[key] = '';
        this.sort = Object.keys(this.sortModes)[0] || '';
        this.page = 1;
        this.selectedIds = [];
    },

    // Rows selectable via a checkbox carry `data-id`. Selection is tracked by
    // id (not DOM element) so it survives re-sorting/re-filtering.
    visibleIds() {
        return this.matchingRows().filter((el) => this.isVisible(el)).map((el) => el.dataset.id).filter(Boolean);
    },

    isSelected(id) {
        return this.selectedIds.includes(String(id));
    },

    toggleSelect(id, checked) {
        id = String(id);
        if (checked) {
            if (!this.selectedIds.includes(id)) this.selectedIds.push(id);
        } else {
            this.selectedIds = this.selectedIds.filter((v) => v !== id);
        }
    },

    toggleSelectAll(checked) {
        const visible = this.visibleIds();
        if (checked) {
            this.selectedIds = [...new Set([...this.selectedIds, ...visible])];
        } else {
            this.selectedIds = this.selectedIds.filter((id) => !visible.includes(id));
        }
    },

    get allVisibleSelected() {
        const visible = this.visibleIds();
        return visible.length > 0 && visible.every((id) => this.selectedIds.includes(id));
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
