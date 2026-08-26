import { useState } from 'react';
import { useTreatments } from '../hooks/useTreatments';
import { TreatmentsList } from '../components/TreatmentsList';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/patient/treatments/_treatments.scss';

export function TreatmentsPage() {
    const { treatments, isLoading, error } = useTreatments();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="treatments-page">
            <div className="treatments-page__header">
                <h1>Mes traitements</h1>
                <p>Ce qui vous est prescrit</p>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>
            <TreatmentsList treatments={treatments} />

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Cette page liste vos traitements prescrits.</p>
                </Modal>
            )}
        </div>
    );
}
