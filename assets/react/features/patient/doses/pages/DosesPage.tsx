import { useState } from 'react';
import { useDoses } from '../hooks/useDoses';
import { DosesList } from '../components/DosesList';
import { IntakeActionModal } from '../components/IntakeActionModal';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { MedicationIntake, IntakeStatus } from '../types';
import '@/styles/pages/patient/doses/_doses.scss';

export function DosesPage() {
    const { intakes, isLoading, error, recordIntake } = useDoses();
    const [selectedIntake, setSelectedIntake] = useState<MedicationIntake | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    const handleAction = (intake: MedicationIntake, newStatus: IntakeStatus) => {
        // Pour les actions rapides, on ouvre le modal avec le statut prérempli
        setSelectedIntake({ ...intake, statut: newStatus });
    };
    // DosesPage.tsx
    const confirmAction = async (status: IntakeStatus, time: string, quantity: string) => {
        if (!selectedIntake) return;

        // Construire une date du jour avec l'heure choisie
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const dateTime = new Date(`${year}-${month}-${day}T${time}:00`);

        setIsSubmitting(true);
        await recordIntake(
            selectedIntake.prescriptionItemId,
            status,
            dateTime.toISOString(), // envoi en ISO complet
            quantity
        );
        setIsSubmitting(false);
        setSelectedIntake(null);
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

            <DosesList intakes={intakes} onAction={handleAction} />

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Cette page montre les prises de médicaments d'aujourd'hui.</p>
                </Modal>
            )}

            <IntakeActionModal
                isOpen={!!selectedIntake}
                onClose={() => setSelectedIntake(null)}
                intake={selectedIntake}
                onConfirm={confirmAction}
                isSubmitting={isSubmitting}
            />
        </div>
    );
}
