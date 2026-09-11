import { STORAGE_KEY } from './translations';

export function localeCode(): 'fr' | 'en' {
    try {
        if (window.localStorage.getItem(STORAGE_KEY) === 'en') return 'en';
    } catch {
        // stockage indisponible : repli sur le français
    }
    return 'fr';
}

export function getDateFormatLocale(): 'fr-FR' | 'en-US' {
    return localeCode() === 'en' ? 'en-US' : 'fr-FR';
}