import { useState } from 'react';
import { useDoses } from '../hooks/useDoses';
import { DosesList } from '../components/DosesList';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/patient/doses/_doses.scss';

export function DosesPage() {
    const { intakes, isLoading, error } = useDoses();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="doses-page">
            <div className="doses-page__header">
                <h1>Mes prises</h1>
                <p>Historique réel de vos prises</p>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>
            <DosesList intakes={intakes} />

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Cette page montre les prises de médicaments d'aujourd'hui.</p>
                </Modal>
            )}
        </div>
    );
}
