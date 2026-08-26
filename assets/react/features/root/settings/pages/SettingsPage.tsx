import { useState } from 'react';
import { useSettings } from '../hooks/useSettings';
import { GeneralSettings } from '../components/GeneralSettings';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/root/settings/_settings.scss';

export function SettingsPage() {
    const { settings, isLoading, isSaving, error, save } = useSettings();
    const [modalOpen, setModalOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setModalOpen(true);
        // Action inverse : fermer la modale
        pushAction(() => setModalOpen(false));
    };

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
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>
            <GeneralSettings settings={settings} onSave={save} isSaving={isSaving} />

            {modalOpen && (
                <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)}>
                    <p>Informations d'aide sur la configuration.</p>
                </Modal>
            )}
        </div>
    );
}
