import { useSettings } from '../hooks/useSettings';
import { GeneralSettings } from '../components/GeneralSettings';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/root/settings/_settings.scss';

export function SettingsPage() {
    const { settings, isLoading, isSaving, error, save } = useSettings();

    if (isLoading) {
        return <Spinner />;
    }

    if (error || !settings) {
        return <Alert variant="error">{error ?? 'Paramètres indisponibles.'}</Alert>;
    }

    return (
        <div className="settings-page">
            <div className="settings-page__header">
                <h1>Configuration</h1>
                <p>Gérez les paramètres globaux de la plateforme</p>
            </div>
            <GeneralSettings settings={settings} onSave={save} isSaving={isSaving} />
        </div>
    );
}
