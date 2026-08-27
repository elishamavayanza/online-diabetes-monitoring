import { useState } from 'react';
import { usePatients } from '../hooks/usePatients';
import { PatientsFilter } from '../components/PatientsFilter';
import { PatientsTable } from '../components/PatientsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/admin/patients/_patients.scss';

export function PatientsPage() {
    const { patients, filters, setFilters, isLoading, error } = usePatients();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openAddModal = () => {
        setIsAddModalOpen(true);
        pushAction(() => setIsAddModalOpen(false));
    };

    const handleFilterChange = (newFilters: typeof filters) => {
        const previousFilters = filters;
        setFilters(newFilters);
        pushAction(() => setFilters(previousFilters));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="patients-page">
            {/* Bloc titre uniquement */}
            <div className="patients-page__header">
                <h1>Patients</h1>
                <p>Gérez les patients de votre organisation</p>
            </div>

            {/* Bloc actions séparé, en dessous */}
            <div className="patients-page__actions">
                <Button variant="primary" onClick={openAddModal}>
                    + Ajouter un patient
                </Button>
            </div>

            <PatientsFilter filters={filters} onChange={handleFilterChange} />
            <PatientsTable patients={patients} />

            {isAddModalOpen && (
                <Modal isOpen={isAddModalOpen} onClose={() => setIsAddModalOpen(false)}>
                    <p>Formulaire d'ajout de patient (à implémenter).</p>
                </Modal>
            )}
        </div>
    );
}
