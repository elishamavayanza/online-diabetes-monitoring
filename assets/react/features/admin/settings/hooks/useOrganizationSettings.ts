import { useEffect, useState } from 'react';
import { fetchOrganizationSettings, saveOrganizationSettings } from '../services/organizationSettingsService';
import { OrganizationSettings } from '../types';

export function useOrganizationSettings() {
    const [settings, setSettings] = useState<OrganizationSettings | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchOrganizationSettings();
                setSettings(data);
            } catch (err) {
                setError('Impossible de charger les paramètres.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    const save = async (newSettings: OrganizationSettings) => {
        setIsSaving(true);
        try {
            await saveOrganizationSettings(newSettings);
            setSettings(newSettings);
        } catch (err) {
            setError('Erreur lors de la sauvegarde.');
        } finally {
            setIsSaving(false);
        }
    };

    return { settings, isLoading, isSaving, error, save };
}
