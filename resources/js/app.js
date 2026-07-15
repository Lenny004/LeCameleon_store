import Alpine from 'alpinejs';
import './modules/theme.js';
import { toggleTheme } from './modules/theme.js';
import { initAdminCharts } from './modules/admin-charts.js';

window.Alpine = Alpine;

Alpine.data('themeToggle', () => ({
    toggle() {
        toggleTheme();
    },
}));

Alpine.data('mobileNav', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    },
    close() {
        this.open = false;
    },
}));

Alpine.data('adminSidebar', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    },
    close() {
        this.open = false;
    },
}));

Alpine.data('filterPanel', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    },
}));

Alpine.data('productGallery', (images = []) => ({
    images,
    active: 0,
    select(index) {
        this.active = index;
    },
}));

/**
 * Live shipping quote fetcher (calculator + checkout).
 * @param {{ calculateUrl: string, flatRate?: number }} options
 */
Alpine.data('shippingQuote', (options = {}) => ({
    municipalityId: '',
    loading: false,
    error: null,
    quote: null,
    flatRate: Number(options.flatRate ?? 0),
    calculateUrl: options.calculateUrl ?? '',

    async fetchQuote() {
        if (!this.municipalityId) {
            this.quote = null;
            this.error = null;
            return;
        }

        this.loading = true;
        this.error = null;

        try {
            const url = new URL(this.calculateUrl, window.location.origin);
            url.searchParams.set('destination_municipality_id', this.municipalityId);

            const response = await fetch(url.toString(), {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                const payload = await response.json().catch(() => ({}));
                const message = payload.message
                    ?? payload.errors?.destination_municipality_id?.[0]
                    ?? 'No se pudo calcular el envío.';
                throw new Error(message);
            }

            this.quote = await response.json();
        } catch (err) {
            this.quote = null;
            this.error = err instanceof Error ? err.message : 'Error de conexión.';
        } finally {
            this.loading = false;
        }
    },

    get displayFee() {
        if (this.quote && typeof this.quote.fee === 'number') {
            return this.quote.fee;
        }

        return this.flatRate;
    },

    formatMoney(amount) {
        return `$${Number(amount).toFixed(2)}`;
    },

    formatEta(hours) {
        const value = Number(hours);
        if (!value || Number.isNaN(value)) {
            return 'Por confirmar';
        }
        if (value >= 48) {
            const days = Math.ceil(value / 24);
            return `~${days} día${days === 1 ? '' : 's'} hábil${days === 1 ? '' : 'es'}`;
        }
        return `~${value} horas`;
    },

    formatDispatch(iso) {
        if (!iso) {
            return 'Por confirmar';
        }
        try {
            return new Date(iso).toLocaleString('es-SV', {
                dateStyle: 'medium',
                timeStyle: 'short',
            });
        } catch {
            return 'Por confirmar';
        }
    },
}));

Alpine.data('quantityInput', (initial = 1, max = 99) => ({
    qty: initial,
    max,
    increment() {
        if (this.qty < this.max) {
            this.qty++;
        }
    },
    decrement() {
        if (this.qty > 1) {
            this.qty--;
        }
    },
}));

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initAdminCharts();
});
