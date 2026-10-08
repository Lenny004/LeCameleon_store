import Alpine from 'alpinejs';
import './modules/theme.js';
import { toggleTheme } from './modules/theme.js';
import { initAdminCharts } from './modules/admin-charts.js';

window.Alpine = Alpine;

Alpine.data('themeToggle', () => ({
    isDark: document.documentElement.getAttribute('data-theme') === 'dark',
    sync() {
        this.isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    },
    toggle() {
        toggleTheme();
        this.sync();
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
    paymentMethod: options.paymentMethod ?? '',
    loading: false,
    error: null,
    quote: null,
    flatRate: Number(options.flatRate ?? 0),
    calculateUrl: options.calculateUrl ?? '',
    transferAvailable: Boolean(options.transferAvailable),
    codEnabled: Boolean(options.codEnabled),
    codMunicipalityIds: (options.codMunicipalityIds ?? []).map((id) => String(id)),
    codMaxAmount: options.codMaxAmount === null || options.codMaxAmount === undefined
        ? null
        : Number(options.codMaxAmount),
    stripeAvailable: Boolean(options.stripeAvailable),

    codVisible() {
        const municipalityAllowed = this.codMunicipalityIds.length === 0
            || this.codMunicipalityIds.includes(String(this.municipalityId));
        const total = Number(options.subtotal ?? 0) + this.displayFee;
        const amountAllowed = this.codMaxAmount === null || total <= this.codMaxAmount;

        return this.codEnabled && municipalityAllowed && amountAllowed;
    },

    syncPaymentMethod() {
        if (this.paymentMethod === 'cod' && !this.codVisible()) {
            this.paymentMethod = this.transferAvailable ? 'transfer' : (this.stripeAvailable ? 'stripe' : '');
        }

        if (!this.paymentMethod && this.codVisible()) {
            this.paymentMethod = 'cod';
        }
    },

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
            this.syncPaymentMethod();
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

Alpine.data('addressBook', (addresses = []) => ({
    addresses,
    fillAddress(id) {
        const address = this.addresses.find((item) => String(item.id) === String(id));
        if (!address) return;
        const fields = ['first_name', 'last_name', 'line1', 'line2', 'city', 'state', 'postal_code', 'country', 'phone'];
        fields.forEach((field) => {
            const input = document.getElementById(`shipping_${field}`);
            if (input) input.value = address[field] ?? '';
        });
        const municipality = document.getElementById('destination_municipality_id');
        if (municipality && address.municipality_id) {
            municipality.value = address.municipality_id;
            municipality.dispatchEvent(new Event('change', { bubbles: true }));
        }
    },
}));

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initAdminCharts();
});
