import React, { createContext, useContext, useEffect, useMemo, useState, useCallback, ReactNode } from 'react';
import { useAuth } from '@/react/app/providers/AuthProvider';
import { Locale, STORAGE_KEY, isLocale, translate } from './translations';

export type TranslateFn = (key: string, params?: Record<string, string | number>) => string;

interface I18nContextValue {
    locale: Locale;
    setLocale: (locale: Locale) => void;
    t: TranslateFn;
}

const I18nContext = createContext<I18nContextValue | undefined>(undefined);

export function I18nProvider({ children }: { children: ReactNode }) {
    const { user } = useAuth();

    const [locale, setLocaleState] = useState<Locale>(() => {
        try {
            const stored = window.localStorage.getItem(STORAGE_KEY);
            if (stored && isLocale(stored)) return stored;
        } catch {
            // stockage indisponible : repli sur le français
        }
        return 'fr';
    });

    const setLocale = useCallback((next: Locale) => {
        if (!isLocale(next)) return;
        setLocaleState(next);
        try {
            window.localStorage.setItem(STORAGE_KEY, next);
        } catch {
            // stockage indisponible : la langue reste active pour la session
        }
    }, []);

    // La préférence du compte (locale utilisateur) prime dès que disponible.
    useEffect(() => {
        if (user?.locale && isLocale(user.locale)) {
            setLocale(user.locale);
        }
    }, [user?.locale, setLocale]);

    useEffect(() => {
        document.documentElement.lang = locale;
    }, [locale]);

    const t = useCallback<TranslateFn>(
        (key, params) => translate(key, locale, params),
        [locale]
    );

    const value = useMemo(() => ({ locale, setLocale, t }), [locale, setLocale, t]);

    return <I18nContext.Provider value={value}>{children}</I18nContext.Provider>;
}

export function useI18n(): I18nContextValue {
    const context = useContext(I18nContext);
    if (!context) {
        throw new Error('useI18n must be used within an I18nProvider');
    }
    return context;
}