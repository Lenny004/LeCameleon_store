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
