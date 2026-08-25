import { SettingsData } from '../types';

export async function fetchSettings(): Promise<SettingsData> {
    await new Promise((resolve) => setTimeout(resolve, 500));
    return {
        platformName: 'OnlineDIAB',
        maintenanceMode: false,
        allowRegistration: true,
        defaultLanguage: 'fr',
    };
}

export async function saveSettings(settings: SettingsData): Promise<void> {
    // Simulation de sauvegarde
    await new Promise((resolve) => setTimeout(resolve, 500));
    console.log('Paramètres sauvegardés', settings);
}
