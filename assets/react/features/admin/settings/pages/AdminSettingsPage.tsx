import { useState } from 'react';
import { useOrganizationSettings } from '../hooks/useOrganizationSettings';
import { OrganizationSettingsForm } from '../components/OrganizationSettingsForm';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useI18n } from '@/react/i18n/I18nContext';
import '@/styles/pages/admin/settings/_settings.scss';

export function AdminSettingsPage() {
    const { settings, isLoading, isSaving, error, save } = useOrganizationSettings();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();
    const { t } = useI18n();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error || !settings) return <Alert variant="error">{error ?? t('Paramètres indisponibles.')}</Alert>;

    return (
        <div className="admin-settings-page">
            <div className="admin-settings-page__header">
                <h1>{t('Paramètres')}</h1>
                <p>{t('Configuration de votre organisation')}</p>
                <Button variant="secondary" onClick={openHelp}>{t('Aide')}</Button>
            </div>
            <OrganizationSettingsForm settings={settings} onSave={save} isSaving={isSaving} />

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>{t('Cette page permet de configurer les informations de votre organisation.')}</p>
                </Modal>
            )}
        </div>
    );
}
