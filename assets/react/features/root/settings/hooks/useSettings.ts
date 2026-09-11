import { useEffect, useState } from 'react';
import { fetchSettings, saveSettings } from '../services/settingsService';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { SettingsData } from '../types';

export function useSettings() {
    const { showToast } = useToast();
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
                const message = (err as Error)?.message ?? 'Impossible de charger les paramètres.';
                setError(message);
                showToast({ type: 'error', message });
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, [showToast]);

    const save = async (newSettings: SettingsData, logoFile?: File | null) => {
        setIsSaving(true);
        setError(null);
        try {
            const saved = await saveSettings(newSettings, logoFile);
            setSettings(saved);
            showToast({
                type: 'success',
                message: 'Configuration système enregistrée avec succès.',
            });
            return true;
        } catch (err) {
            const message = (err as Error)?.message ?? 'Erreur lors de la sauvegarde.';
            setError(message);
            showToast({ type: 'error', message });
            return false;
        } finally {
            setIsSaving(false);
        }
    };

    return { settings, isLoading, isSaving, error, save };
}