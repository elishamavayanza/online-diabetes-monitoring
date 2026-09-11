import apiClient from '@/services/api/client';
import { DEFAULT_SETTINGS, SettingsData, SettingsItem } from '../types';

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

function normalize(data: Partial<SettingsData> | null | undefined): SettingsData {
    const base = { ...DEFAULT_SETTINGS };
    if (!data) return base;
    return {
        ...base,
        ...data,
        systemName: data.systemName ?? base.systemName,
        logoUrl: data.logoUrl ?? null,
        heroTitle: data.heroTitle ?? '',
        heroSubtitle: data.heroSubtitle ?? '',
        aboutTitle: data.aboutTitle ?? '',
        aboutContent: data.aboutContent ?? '',
        featuresTitle: data.featuresTitle ?? '',
        features: Array.isArray(data.features) ? normalizeItems(data.features) : [],
        usersTitle: data.usersTitle ?? '',
        users: Array.isArray(data.users) ? normalizeItems(data.users) : [],
        ctaTitle: data.ctaTitle ?? '',
        ctaSubtitle: data.ctaSubtitle ?? '',
        footerTagline: data.footerTagline ?? '',
        footerCopyright: data.footerCopyright ?? '',
    };
}

function normalizeItems(items: SettingsItem[]): SettingsItem[] {
    return items.map((item) => ({
        title: item.title ?? '',
        description: item.description ?? '',
    }));
}

export async function fetchSettings(): Promise<SettingsData> {
    const response = await apiClient.get<ApiFeedback<Partial<SettingsData>>>('/settings');
    return normalize(response.data.data);
}

/** Récupération publique (sans intercepteurs d’auth) pour le branding de l’UI. */
export async function fetchPublicSettings(): Promise<SettingsData> {
    const response = await fetch('/api/settings', {
        headers: { Accept: 'application/json' },
    });
    if (!response.ok) {
        throw new Error('Configuration système indisponible.');
    }
    const body = (await response.json()) as ApiFeedback<Partial<SettingsData>>;
    return normalize(body.data);
}

export async function saveSettings(
    settings: SettingsData,
    logoFile?: File | null,
): Promise<SettingsData> {
    const hasFile = Boolean(logoFile);

    if (hasFile) {
        const formData = buildFormData(settings, logoFile!);
        // PHP ne remplit pas fiablement $_FILES pour un PUT multipart (cf. profileService) :
        // on utilise POST pour l'upload (l'API l'accepte via POST/PUT/PATCH).
        const response = await apiClient.post<ApiFeedback<Partial<SettingsData>>>(
            '/settings',
            formData,
            { headers: { 'Content-Type': undefined } as unknown as Record<string, string> },
        );
        return normalize(response.data.data);
    }

    const response = await apiClient.put<ApiFeedback<Partial<SettingsData>>>('/settings', {
        systemName: settings.systemName,
        heroTitle: settings.heroTitle,
        heroSubtitle: settings.heroSubtitle,
        aboutTitle: settings.aboutTitle,
        aboutContent: settings.aboutContent,
        featuresTitle: settings.featuresTitle,
        features: settings.features,
        usersTitle: settings.usersTitle,
        users: settings.users,
        ctaTitle: settings.ctaTitle,
        ctaSubtitle: settings.ctaSubtitle,
        footerTagline: settings.footerTagline,
        footerCopyright: settings.footerCopyright,
    });
    return normalize(response.data.data);
}

function buildFormData(settings: SettingsData, logoFile: File): FormData {
    const formData = new FormData();
    formData.append('systemName', settings.systemName);
    formData.append('heroTitle', settings.heroTitle);
    formData.append('heroSubtitle', settings.heroSubtitle);
    formData.append('aboutTitle', settings.aboutTitle);
    formData.append('aboutContent', settings.aboutContent);
    formData.append('featuresTitle', settings.featuresTitle);
    formData.append('usersTitle', settings.usersTitle);
    formData.append('ctaTitle', settings.ctaTitle);
    formData.append('ctaSubtitle', settings.ctaSubtitle);
    formData.append('footerTagline', settings.footerTagline);
    formData.append('footerCopyright', settings.footerCopyright);
    settings.features.forEach((item, index) => {
        formData.append(`features[${index}][title]`, item.title);
        formData.append(`features[${index}][description]`, item.description);
    });
    settings.users.forEach((item, index) => {
        formData.append(`users[${index}][title]`, item.title);
        formData.append(`users[${index}][description]`, item.description);
    });
    formData.append('logoFile', logoFile);
    return formData;
}