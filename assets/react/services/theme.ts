export type ThemeMode = 'light' | 'dark';

const STORAGE_KEY = 'diabcare-theme';

function systemPrefersDark(): boolean {
    if (typeof window === 'undefined') return false;
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
}

export function getStoredTheme(): ThemeMode | null {
    try {
        const value = localStorage.getItem(STORAGE_KEY);
        if (value === 'light' || value === 'dark') return value;
    } catch {
        // localStorage indisponible (mode privé, etc.) → silencieux
    }
    return null;
}

export function setStoredTheme(theme: ThemeMode): void {
    try {
        localStorage.setItem(STORAGE_KEY, theme);
    } catch {
        // silencieux
    }
}

export function resolveTheme(saved?: ThemeMode | null): ThemeMode {
    return saved ?? (systemPrefersDark() ? 'dark' : 'light');
}

export function applyTheme(theme: ThemeMode): void {
    document.documentElement.setAttribute('data-theme', theme);
}

export function initTheme(): ThemeMode {
    const theme = resolveTheme(getStoredTheme());
    applyTheme(theme);
    return theme;
}