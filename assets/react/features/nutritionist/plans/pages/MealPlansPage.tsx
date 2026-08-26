import { useState } from 'react';
import { useMealPlans } from '../hooks/useMealPlans';
import { MealPlansTable } from '../components/MealPlansTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/nutritionist/plans/_plans.scss';

export function MealPlansPage() {
    const { plans, isLoading, error } = useMealPlans();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openAddModal = () => {
        setIsAddModalOpen(true);
        // Enregistre l'action inverse : fermer la modale
        pushAction(() => setIsAddModalOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="meal-plans-page">
            <div className="meal-plans-page__header">
                <h1>Plans alimentaires</h1>
                <Button variant="primary" onClick={openAddModal}>+ Nouveau plan</Button>
            </div>
            <MealPlansTable plans={plans} />

            {isAddModalOpen && (
                <Modal isOpen={isAddModalOpen} onClose={() => setIsAddModalOpen(false)}>
                    <p>Formulaire d'ajout de plan alimentaire (à implémenter).</p>
                </Modal>
            )}
        </div>
    );
}
