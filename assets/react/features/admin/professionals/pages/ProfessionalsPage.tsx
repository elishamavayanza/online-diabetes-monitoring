import { useState } from 'react';
import { useProfessionals } from '../hooks/useProfessionals';
import { ProfessionalsTable } from '../components/ProfessionalsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/admin/professionals/_professionals.scss';

export function ProfessionalsPage() {
    const { professionals, isLoading, error } = useProfessionals();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openAddModal = () => {
        setIsAddModalOpen(true);
        pushAction(() => setIsAddModalOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="professionals-page">
            <div className="professionals-page__header">
                <h1>Professionnels</h1>
                <Button variant="primary" onClick={openAddModal}>
                    + Ajouter un professionnel
                </Button>
            </div>
            <ProfessionalsTable professionals={professionals} />

            {isAddModalOpen && (
                <Modal isOpen={isAddModalOpen} onClose={() => setIsAddModalOpen(false)}>
                    <p>Formulaire d'ajout de professionnel (à implémenter).</p>
                </Modal>
            )}
        </div>
    );
}
