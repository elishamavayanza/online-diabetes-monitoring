import { useOrganizationSettings } from '../hooks/useOrganizationSettings';
import { OrganizationSettingsForm } from '../components/OrganizationSettingsForm';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/admin/settings/_settings.scss';

export function AdminSettingsPage() {
    const { settings, isLoading, isSaving, error, save } = useOrganizationSettings();

    if (isLoading) {
        return <Spinner />;
    }

    if (error || !settings) {
        return <Alert variant="error">{error ?? 'Paramètres indisponibles.'}</Alert>;
    }

    return (
        <div className="admin-settings-page">
            <div className="admin-settings-page__header">
                <h1>Paramètres</h1>
                <p>Configuration de votre organisation</p>
            </div>
            <OrganizationSettingsForm settings={settings} onSave={save} isSaving={isSaving} />
        </div>
    );
}
