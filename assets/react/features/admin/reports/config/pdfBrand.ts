export const PDF_BRAND = {
    name: 'DiabCare',
    tagline: 'Plateforme de suivi du diabète',
    primary: '#2C7A7B',
    secondary: '#76B8B8',
    background: '#E9EEF3',
    surface: '#F5F8FA',
    text: '#1E2A32',
    accent: '#F4A261',
    muted: '#5C6B73',
    border: '#C5D0D8',
    white: '#FFFFFF',
} as const;

export function hexToRgb(hex: string): [number, number, number] {
    const normalized = hex.replace('#', '');

    return [
        Number.parseInt(normalized.slice(0, 2), 16),
        Number.parseInt(normalized.slice(2, 4), 16),
        Number.parseInt(normalized.slice(4, 6), 16),
    ];
}
