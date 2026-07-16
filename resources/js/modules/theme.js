const STORAGE_KEY = 'lecameleon-theme-v2';

export function getStoredTheme() {
    return localStorage.getItem(STORAGE_KEY);
}

export function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem(STORAGE_KEY, theme);
    window.dispatchEvent(new CustomEvent('themechange', { detail: { theme } }));
}

export function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    setTheme(next);
    return next;
}

export function initTheme() {
    // Light is the product default. Only honor an explicit user choice.
    const stored = getStoredTheme();
    const theme = stored === 'dark' || stored === 'light' ? stored : 'light';
    document.documentElement.setAttribute('data-theme', theme);

    window.addEventListener('storage', (event) => {
        if (event.key !== STORAGE_KEY) {
            return;
        }

        const next = event.newValue === 'dark' || event.newValue === 'light'
            ? event.newValue
            : 'light';

        document.documentElement.setAttribute('data-theme', next);
        window.dispatchEvent(new CustomEvent('themechange', { detail: { theme: next } }));
    });
}

initTheme();
