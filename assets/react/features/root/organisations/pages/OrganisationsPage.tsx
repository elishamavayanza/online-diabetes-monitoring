import { useState } from 'react';
import { useOrganisations } from '../hooks/useOrganisations';
import { OrganisationsTable } from '../components/OrganisationsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/root/organisations/_organisations.scss';

export function OrganisationsPage() {
    const { organisations, isLoading, error } = useOrganisations();
    const [modalOpen, setModalOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openAddModal = () => {
        setModalOpen(true);
        // Action inverse : fermer la modale
        pushAction(() => setModalOpen(false));
    };

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="organisations-page">
            <div className="organisations-page__header">
                <h1>Organisations</h1>
                <p>Liste de toutes les organisations de la plateforme</p>
                <Button onClick={openAddModal}>Ajouter une organisation</Button>
            </div>
            <OrganisationsTable organisations={organisations} />

            {modalOpen && (
                <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)}>
                    <p>Formulaire d'ajout (exemple).</p>
                </Modal>
            )}
        </div>
    );
}
