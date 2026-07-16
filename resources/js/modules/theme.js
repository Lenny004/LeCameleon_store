const STORAGE_KEY = 'lecameleon-theme';

export function getStoredTheme() {
    return localStorage.getItem(STORAGE_KEY);
}

export function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem(STORAGE_KEY, theme);
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
}

initTheme();
