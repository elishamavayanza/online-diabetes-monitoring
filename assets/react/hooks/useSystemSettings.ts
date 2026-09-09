import { useEffect, useState } from 'react';
import { fetchPublicSettings } from '@/react/features/root/settings/services/settingsService';
import { DEFAULT_SETTINGS, SettingsData } from '@/react/features/root/settings/types';

let cachePromise: Promise<SettingsData> | null = null;
let cachedSettings: SettingsData | null = null;

type Listener = () => void;
const listeners = new Set<Listener>();

/** Invalide le cache et notifie les consommateurs après une mise à jour ROOT. */
export function invalidateSystemSettings(): void {
    cachePromise = null;
    cachedSettings = null;
    listeners.forEach((listener) => listener());
}

function loadSettings(): Promise<SettingsData> {
    if (cachedSettings) return Promise.resolve(cachedSettings);
    if (!cachePromise) {
        cachePromise = fetchPublicSettings()
            .then((data) => {
                cachedSettings = data;
                return data;
            })
            .catch((err) => {
                cachePromise = null;
                throw err;
            });
    }
    return cachePromise;
}

/**
 * Fournit la configuration système publique (nom, logo, textes home).
 * Utilisée pour le branding (sidebar, login, homepage).
 * Se rafraîchit automatiquement après un enregistrement ROOT.
 */
export function useSystemSettings() {
    const [settings, setSettings] = useState<SettingsData>(DEFAULT_SETTINGS);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        let mounted = true;

        const load = () => {
            setIsLoading(true);
            loadSettings()
                .then((data) => {
                    if (mounted) setSettings(data);
                })
                .catch(() => {
                    // Repli sur les valeurs par défaut
                })
                .finally(() => {
                    if (mounted) setIsLoading(false);
                });
        };

        load();
        listeners.add(load);

        return () => {
            mounted = false;
            listeners.delete(load);
        };
    }, []);

    return { settings, isLoading };
}