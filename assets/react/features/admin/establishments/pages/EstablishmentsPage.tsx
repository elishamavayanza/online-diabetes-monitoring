import { useState } from 'react';
import { useEstablishments } from '../hooks/useEstablishments';
import { EstablishmentsTable } from '../components/EstablishmentsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/admin/establishments/_establishments.scss';

export function EstablishmentsPage() {
    const { establishments, isLoading, error } = useEstablishments();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openAddModal = () => {
        setIsAddModalOpen(true);
        pushAction(() => setIsAddModalOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="establishments-page">
            {/* Bloc titre uniquement */}
            <div className="establishments-page__header">
                <h1>Établissements</h1>
                <p>Gérez les établissements de votre organisation</p>
            </div>

            {/* Bloc actions séparé, en dessous */}
            <div className="establishments-page__actions">
                <Button variant="primary" onClick={openAddModal}>
                    + Ajouter un établissement
                </Button>
            </div>

            <EstablishmentsTable establishments={establishments} />

            {isAddModalOpen && (
                <Modal isOpen={isAddModalOpen} onClose={() => setIsAddModalOpen(false)}>
                    <p>Formulaire d'ajout d'établissement (à implémenter).</p>
                </Modal>
            )}
        </div>
    );
}
