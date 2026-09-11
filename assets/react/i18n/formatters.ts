import { Locale } from './translations';

/**
 * The browser's formatting APIs expect a BCP 47 locale. Keeping this mapping
 * here prevents screens from pinning dates and times to French.
 */
export function formattingLocale(locale: Locale): string {
    return locale === 'en' ? 'en-GB' : 'fr-FR';
}

export function formatDate(
    value: string | Date,
    locale: Locale,
    options: Intl.DateTimeFormatOptions = { day: '2-digit', month: '2-digit', year: 'numeric' }
): string {
    const date = value instanceof Date ? value : new Date(value);
    if (Number.isNaN(date.getTime())) return typeof value === 'string' ? value : '';
    return date.toLocaleDateString(formattingLocale(locale), options);
}
