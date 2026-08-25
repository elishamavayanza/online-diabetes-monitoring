import { useEffect, useState } from 'react';
import { fetchSettings, saveSettings } from '../services/settingsService';
import { SettingsData } from '../types';

export function useSettings() {
    const [settings, setSettings] = useState<SettingsData | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchSettings();
                setSettings(data);
            } catch (err) {
                setError('Impossible de charger les paramètres.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    const save = async (newSettings: SettingsData) => {
        setIsSaving(true);
        try {
            await saveSettings(newSettings);
            setSettings(newSettings);
        } catch (err) {
            setError('Erreur lors de la sauvegarde.');
        } finally {
            setIsSaving(false);
        }
    };

    return { settings, isLoading, isSaving, error, save };
}
